<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            $animal['Nascimento'] = is_null($animal['Nascimento']) ? "" : $animal['Nascimento'];
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
            $cliente['Telefone2'] = is_null($cliente['Telefone2']) ? "" : $cliente['Telefone2'];
            $cliente['Email'] = is_null($cliente['Telefone2']) ? "" : $cliente['Email'];
            fputcsv($csv,  $cliente);
        }

        fclose($csv);
    }

    public function add(Request $request)
    {

        if ($request->isMethod('post') && $request->hasFile('csv')) {
            $simples = app('db')->connection('simples');
            $csvs = $request->file('csv');

            foreach ($csvs as $key => $csv) {
                $original = $csv->getClientOriginalName();
                $nome = time() . '-' . $csv->getClientOriginalName();
                $csv->move('csvs',   $nome);
                $csv_file = fopen(app()->basePath('public') . '/csvs/' . $nome, 'r');
                while (!feof($csv_file)) {
                    $row = fgetcsv($csv_file);
                    // KEY 0 = ANIMAIS | KEY 1 = CLIENTES

                    if ($key == 1 && false) {
                        try {
                            $p_id = $row[0];
                            $p_nome = $row[1];
                            $p_telefone1 = $row[2];
                            $p_telefone2 = $row[3];
                            $p_email = $row[4];

                            $simples->insert('Insert into pessoas (id,nome) values (?,?)', [$p_id[0], $p_nome]);
                            if (filter_var($p_email, FILTER_VALIDATE_EMAIL)) {
                                $simples->insert('Insert into contatos (tipo, valor, pessoa_id) values (?,?,?)', ['email', $p_email, $p_id]);
                            }

                            $val1 = $this->phoneValidate($p_telefone1);
                            if ($val1['status']) {
                                $simples->insert('Insert into contatos (tipo, valor, pessoa_id) values (?,?,?)', [$val1['tipo'], $val1['number'], $p_id]);
                            }

                            $val2 = $this->phoneValidate($p_telefone2);
                            if ($val2['status']) {
                                $simples->insert('Insert into contatos (tipo, valor, pessoa_id) values (?,?,?)', [$val2['tipo'], $val2['number'], $p_id]);
                            }
                        } catch (\Exception $e) {
                            // echo $e->getMessage();
                        }
                    } else {
                        try {
                            $a_id = $row[0];
                            $a_cliente = $row[1];
                            $a_nome = $row[2];
                            $a_raca = $row[3];
                            $a_especie = $row[4];
                            $a_historico = $row[5];
                            $a_nascimento = date('Y-m-d', strtotime(str_replace('/', '-', $row[6])));

                            $raca = $simples->select('SELECT * FROM animais_racas WHERE nome like ?', [$a_raca]);
                            if (count($raca) == 0) {
                                $simples->insert('Insert into animais_racas (nome) values (?)', [$a_raca]);
                                $raca = $simples->select('SELECT * FROM animais_racas WHERE nome like "?"', [$a_raca]);
                            }
                            $a_raca = $raca[0]->id;

                            $especie = $simples->select('SELECT * FROM animais_especies WHERE nome like ?', [$a_especie]);
                            if (count($especie) == 0) {
                                $simples->insert('Insert into animais_especies (nome) values (?)', [$a_especie]);
                                $especie = $simples->select('SELECT * FROM animais_especies WHERE nome like "?"', [$a_especie]);
                            }
                            $a_especie = $especie[0]->id;
                            echo '<pre>';
                            // var_dump([$a_id, $a_cliente, $a_nome, $a_raca, $a_especie, $a_historico, $a_nascimento]);
                            $insert = $simples->insert('Insert into animais (id, pessoa_id, nome, raca_id, especie_id, historico, nascimento) values (?, ?, ?, ?, ?, ?, ?)', [$a_id, $a_cliente, $a_nome, $a_raca, $a_especie, $a_historico, $a_nascimento]);
                            var_dump($insert);
                        } catch (\Exception $e) {
                            // echo $e->getMessage();
                        }
                    }
                }

                fclose($csv_file);
                unlink(app()->basePath('public') . '/csvs/' . $nome);

                return redirect('http://127.0.0.1:8080');
            }
        }
    }

    function phoneValidate($numero)
    {
        $regra = '/\((10)|([1-9][1-9])\) [9]?[2-9][0-9]{3}-[0-9]{4}/';

        if (empty($numero)) {
            return ['status' => false, 'number' => $numero];
        }

        // NORMALICAÇÃO DOS DDD's
        if (strpos($numero, '(') === false) {
            $numero = substr($numero, 0, 0) . '(' . substr($numero, 0);
            $numero = substr($numero, 0, 3) . ')' . substr($numero, 3);
        }

        if (strpos(substr($numero, 4), '-') === false) {
            $numero = strlen($numero) == 13 ? substr($numero, 0, 9) . '-' . substr($numero, 9) : substr($numero, 0, 10) . '-' . substr($numero, 10);
        }

        if (preg_match($regra, $numero)) {

            $tipo = in_array($numero[5], [6, 7, 8, 9]) ? 'cel' : 'fixo';
            return ['status' => true, 'tipo' => $tipo, 'number' => $numero];
        } else {
            return ['status' => false, 'tipo' => 'no_preg', 'number' => $numero];
        }
        return ['status' => false, 'number' => $numero];
    }
}
