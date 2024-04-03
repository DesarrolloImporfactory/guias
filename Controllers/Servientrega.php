<?php

class Servientrega extends Controller
{
    public function index()
    {
        $this->views->render($this, "index");
    }

    public function Guia($id)
    {
        //verificar id sea numerico}
        if (is_numeric($id)) {
            $this->model->visualizarGuia($id);
        } else {
            echo "Guia no valida";
        }
    }
}
