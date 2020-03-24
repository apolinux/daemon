<?php
use Apolinux\DaemonAdmin\DaemonAdmin;
use Apolinux\DaemonAdmin\TaskManager;

require __DIR__ .'/../vendor/autoload.php' ;
        
$procname = substr(basename(__FILE__), 0, -4);

$daemon = new DaemonAdmin([
  'pid_file' => __DIR__ ."/var/$procname.pid" ,
  'log_dir' => __DIR__ ."/var"  ,
  'name' => $procname ,
  'task_mode' => TaskManager::MODE_ONCE_CMD,
  //'task' => 'testTask',
  'run_path' => __DIR__ .'/test_task.php' ,
  'run_path_args' => ['param_one=niebla','param2="tucutu"'],
  'run_path_env' => ['BLA' =>'FIN'],
  'php_bin' => '/usr/bin/php' ,
  //'log_mode' => Logger::MODE_DEBUG ,
]);
        
$daemon->run();