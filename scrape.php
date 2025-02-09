<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
set_time_limit(9999);
error_reporting(E_ALL);
ini_set('max_execution_time', '1200'); 

include_once 'vendor/autoload.php';
use simplehtmldom\HtmlWeb;
use Koren\Skrejper\Cli;

$cli = new Cli();
echo $cli->selectOption();

