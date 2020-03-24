#!/bin/env php
<?php
use Apolinux\DaemonAdmin\DaemonAdmin;
use Apolinux\DaemonAdmin\TaskManager;

require __DIR__ .'/../vendor/autoload.php' ;
        
$procname = substr(basename(__FILE__), 0, -4);

$daemon = new DaemonAdmin([
  'pid_file' => __DIR__ ."/var/$procname.pid" ,
  'log_dir' => __DIR__ ."/var"  ,
  'name' => $procname ,
  'task_mode' => TaskManager::MODE_LOOP_CALL ,
  'task' => 'testTask' ,
  'wait_loop_task_time' => 1 ,
  'timeout_after_kill' => 15 ,
  'timeout_after_start' => 1 ,
  'stop_on_exceptions' => false ,
]);
        
function testTask(){
    echo "start task\n" ;
    $f = tmpfile();
    for($i=1 ; $i<=40000; $i++){
       fwrite($f, md5(base64_decode(random_bytes(256)))) ;
    }
    echo "file created\n" ;
    fseek($f,0) ;
    while(! feof($f)){
        $null = fgetc($f);
        unset($null);
    }
    fclose($f);
    echo "file closed\n" ;
}

$daemon->run();