<?php
use Apolinux\DaemonAdmin\DaemonAdmin;
use Apolinux\DaemonAdmin\TaskManager;

require __DIR__ .'/../vendor/autoload.php' ;

$procname = substr(basename(__FILE__),0,-4) ;
// @TODO, check this, is failing stopping

$daemon = new DaemonAdmin([
  'pid_file' => __DIR__ ."/var/$procname.pid" ,
  'log_dir' => __DIR__ ."/var"  ,
  'name' => $procname ,
  'task_mode' => TaskManager::MODE_LOOP_CALL_FORK,
  'task' => 'testTask' ,
  'wait_loop_task_time' => 0 ,
  'timeout_after_kill' => 5 ,
  'timeout_after_start' => 1 ,
  //'stop_on_exceptions' => false ,
]);
        
function testTask(){
    echo "start task\n" ;
    $f = tmpfile();
    for($i=1 ; $i<=100000; $i++){
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
    function_notfound(); // FATAL ERROR
}

$daemon->run();