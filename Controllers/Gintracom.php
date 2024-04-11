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
}
