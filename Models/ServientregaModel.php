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
}
