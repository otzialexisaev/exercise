<?php
define('DS', DIRECTORY_SEPARATOR);
define('CLASSES_PATH', $_SERVER['DOCUMENT_ROOT'] . DS . 'classes' . DS );
//require_once ('classes/Parser.php');
require_once ('autoload.php');

echo(Parser::Execute());