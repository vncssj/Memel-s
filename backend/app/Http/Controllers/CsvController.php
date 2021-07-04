<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CsvController extends Controller
{
    public function animais()
    {
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="Animais.csv";');

        $complicado = app('db')->connection('complicado');
        $animais = $complicado->select("SELECT * FROM Animal");
        $csv = fopen('php://output', 'w');

        foreach ($animais as $animal) {
            $animal = (array) $animal;
            $animal['Nascimento'] = is_null($animal['Nascimento']) ? "00/00/0000" : $animal['Nascimento'];
            fputcsv($csv,  $animal);
        }

        fclose($csv);
    }

    public function clientes()
    {
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="Clientes.csv";');

        $complicado = app('db')->connection('complicado');
        $clientes = $complicado->select("SELECT * FROM Cliente");
        $csv = fopen('php://output', 'w');

        foreach ($clientes as $cliente) {
            $cliente = (array) $cliente;
            $cliente['Telefone2'] = is_null($cliente['Telefone2']) ? "(00) 00000-0000" : $cliente['Telefone2'];
            $cliente['Email'] = is_null($cliente['Telefone2']) ? "" : $cliente['Email'];
            fputcsv($csv,  $cliente);
        }

        fclose($csv);
    }

    public function add(Request $request)
    {
        if ($request->isMethod('post') && $request->hasFile('csv')) {
            echo "post";
        }
    }
}
