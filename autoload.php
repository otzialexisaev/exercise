<?php
spl_autoload_register('myAutoloader');

function myAutoloader($className)
{
    $path = './classes/';
    include $path.$className.'.php';
}