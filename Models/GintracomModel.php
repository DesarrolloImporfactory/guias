<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
class GintracomModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function traqueo($id)
    {
        $url = "https://ec.gintracom.site/web/import-suite/tracking";
        $response = $this->enviar_datos($url, $id);
        echo $response;
    }

    public function estado($id)
    {
        $url = "https://ec.gintracom.site/web/import-suite/estado";
        $response = $this->enviar_datos($url, $id);
        echo $response;
    }

    public function labels($id)
    {
        $url = "https://ec.gintracom.site/web/import-suite/label";
        $response = $this->enviar_datos($url, $id);
        echo $response;
    }

    private function enviar_datos($url, $id)
    {
        //Basic Auth
        $username = 'importsuite';
        $password = "ab5b809caf73b2c1abb0e4586a336c3a";

        $data = array("guia" => $id);
        $data_string = json_encode($data);
        //inicia curl
        $ch = curl_init($url);
        //configura las opciones de curl
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string)
            )
        );
        //ejecuta curl
        $result = curl_exec($ch);
        //cierra curl
        curl_close($ch);
        //decodifica el resultado
        //$response = json_decode($result, true);
        //verifica si hay error
        $response = $result;
        if (isset($response['error'])) {
            echo $response['error'];
        } else {
            $server_url =  "../temp2.pdf";

            file_put_contents($server_url, $response);
            //abrir el archivo
            header("Content-type: application/pdf");
            header("Content-Disposition: inline; filename=temp2.pdf");
            readfile($server_url);
        }

        return $response;
    }
}
