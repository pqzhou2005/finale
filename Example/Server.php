<?php

$basename = basename(__FILE__,".php");
$usage = "Usage: php {$basename} {start|stop|reload}";

if(empty($_SERVER['argv'][1])) exit($usage);
$cmd = $_SERVER['argv'][1];

require_once '../Process.php';
$process = new \Finale\Process([
    'pid_file'=>'finale.pid'
]);

switch($cmd)
{
    case "start":
    case "stop":
    case "reload":
        $process->$cmd();
        break;
    default:
        exit($usage);
}