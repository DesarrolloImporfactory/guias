<?php

class ServientregaModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function visualizarGuia($id)
    {
        // URL del servicio web
        $url = "https://181.39.87.158:7777/api/GuiaDigital/[" . $id . ",'impor.comex','123456']";

        // Inicializar cURL
        $ch = curl_init();

        // Configurar opciones de cURL para la solicitud GET
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Omitir la verificación de SSL (NO recomendado para producción)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);

        // Verificar si ocurrió algún error
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        // Cerrar la sesión cURL
        curl_close($ch);

        // Decodificar la respuesta JSON para obtener la cadena base64 de la imagen
        $responseData = json_decode($response, true);
        $base64String = $responseData['archivoEncriptado'];
        $b64 = base64_decode($base64String);

        if (strpos($b64, "%PDF") !== 0) {
            echo "No es un PDF";
        } else {
        }

        if (
            isset($_SERVER['HTTPS']) &&
            ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
        ) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        $server_url =  "../temp2.pdf";

        file_put_contents($server_url, $b64);

        //abrir el archivo
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=temp2.pdf");
        readfile($server_url);
    }
    public function actualizar_guia($data)
    {
        $token = "ef4983b54cc73a26d69eac01bca287d0a0f4db5a6eb535d41c29d9ce94a7eb6a";

        $cas = json_encode($data);

        $sql = "INSERT INTO test (cas) VALUES (?)";
        $data = array($cas);
        $this->insert($sql, $data);


        $guia = $data['guia'];
        $f_movimiento = $data['f_movimiento'];
        $h_movimiento = $data['h_movimiento'];
        $movimiento   = $data['movimiento'];
        $estado       = $data['estado'];
        $ciudad       = $data['ciudad'];
        $observacion1 = $data['observacion1'];
        $observacion2 = $data['observacion2'];
        $observacion3 = $data['observacion3'];

        $sql = "INSERT INTO servi_data (guia, f_movimiento, h_movimiento, movimiento, estado, ciudad, observacion1, observacion2, observacion3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $data = array($guia, $f_movimiento, $h_movimiento, $movimiento, $estado, $ciudad, $observacion1, $observacion2, $observacion3);
        $this->insert($sql, $data);

        $this->cambioDeEstado($guia, $estado);

        http_response_code(200);
        echo "Recibido correctamente";
    }
    private function cambioDeEstado($guia, $estado)
    {
        $marketplace_db = $this->obtenerConexion('https://marketplace.imporsuit.com');
        $sql_guia = "SELECT * FROM guia_laar where guia_laar = '$guia'";
        $result_guia = mysqli_query($marketplace_db, $sql_guia);
        $row_guia = mysqli_fetch_assoc($result_guia);
        $tienda_venta = $row_guia['tienda_venta'];
        $tienda_proveedor = $row_guia['tienda_proveedor'];

        $tienda_venta_db = $this->obtenerConexion($tienda_venta);
        $tienda_proveedor_db = $this->obtenerConexion($tienda_proveedor);

        $sql_update = "UPDATE guia_laar SET estado_guia = '$estado' WHERE guia_laar = '$guia'";
        $result_update = mysqli_query($marketplace_db, $sql_update);
        $result_update_tienda_venta = mysqli_query($tienda_venta_db, $sql_update);
        $result_update_tienda_proveedor = mysqli_query($tienda_proveedor_db, $sql_update);

        mysqli_close($marketplace_db);
        mysqli_close($tienda_venta_db);
        mysqli_close($tienda_proveedor_db);
        if ($result_update && $result_update_tienda_venta && $result_update_tienda_proveedor) {
            return true;
        } else {
            return false;
        }
    }

    private function obtenerConexion($tienda)
    {
        $archivo_tienda =  $tienda . '/sysadmin/vistas/db1.php';
        $archivo_destino_tienda = "../db_destino_guia.php";
        $contenido_tienda = file_get_contents($archivo_tienda);
        $get_data = json_decode($contenido_tienda, true);
        if (file_put_contents($archivo_destino_tienda, $contenido_tienda) !== false) {
            $host_d = $get_data['DB_HOST'];
            $user_d = $get_data['DB_USER'];
            $pass_d = $get_data['DB_PASS'];
            $base_d = $get_data['DB_NAME'];
            // Conexión a la base de datos de la tienda, establece la hora -5 GTM
            date_default_timezone_set('America/Guayaquil');
            $conexion = mysqli_connect($host_d, $user_d, $pass_d, $base_d);
            if (!$conexion) {
                die("Connection failed: " . mysqli_connect_error());
            }
            return $conexion;
        } else {
            echo "Error al copiar el archivo";
        }
    }
}
