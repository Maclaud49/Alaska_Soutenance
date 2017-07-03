<?php

// Doctrine (db)
$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'charset'  => 'utf8',
    'host'     => '127.0.0.1',  // Mandatory for PHPUnit testing
    'port'     => '3306',
    'dbname'   => 'db688769058',
    'user'     => 'dbo688769058',
    'password' => 'Alaska2017&',
);

// enable the debug mode
$app['debug'] = true;

// define log parameters
$app['monolog.level'] = 'INFO';

