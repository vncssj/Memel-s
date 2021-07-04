<?php
return [
    'default' => 'mysql',
    'connections' => [
        'simples' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'simplesvet',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'complicado' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'complicadovet',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
    ],
];
