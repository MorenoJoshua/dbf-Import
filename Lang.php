<?php

class Lang
{
    public $return;

    public function __construct($lang = 'es')
    {
        if ($lang == 'es') {
            $this->return = $this->es();
        } else {
            $this->return = $this->es();
        }
    }

    public function es()
    {
        return [
            'dbf_converter' => 'Convertidor DBF',
            'file_name' => 'Nombre de archivo',
            'file' => 'Archivo',
        ];

    }
}