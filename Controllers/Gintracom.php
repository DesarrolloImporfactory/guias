<?php

class Gintracom extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo "Gintracom";
    }

    public function tracking($id)
    {
        $this->model->traqueo($id);
    }
    public function label($id)
    {
        if (!empty($id)) {
            $this->model->labels($id);
        } else {
            http_response_code(400);
            echo "Error: No se recibieron datos";
        }
    }

    public function estado($id)
    {
        if (!empty($id)) {
            $this->model->estado($id);
        } else {
            http_response_code(400);
            echo "Error: No se recibieron datos";
        }
    }
}
