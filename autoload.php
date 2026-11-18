<?php

require_once __DIR__ . '/Psr4ClassLoader.php';

$loader = new \loader\Psr4ClassLoader(
    'iutnc\\deefy',
    __DIR__ . '/src/classes'
);
$loader->register();
