<?php

// Doctrine (db)
$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'charset'  => 'utf8',
    'host'     => 'db688769058.db.1and1.com',
    'port'     => '3306',
    'dbname'   => 'db688769058',
    'user'     => 'dbo688769058',
    'password' => 'Alaska2017&',
);

// define log parameters
$app['monolog.level'] = 'WARNING';
