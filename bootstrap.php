<?php

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

function autoload($className)
{
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    // echo "$fileName\r\n";
 
    $o = require $fileName;
    // var_dump($o);
}

declare(ticks = 1);
pcntl_signal(SIGINT, function($sig) {
    echo "this is the end my friend";
    Utils\Timer::getTimes();
    exit;
});

set_include_path('/home/ubuntu/workspace/');
spl_autoload_register('autoload');