# Process and Daemon manager

Create system daemons process. A daemon program is a special system program that executes in background, 
where all input data is avoided and output is saved in log files or similar.

This class lets to control the start and stop of the daemon programs and other commands related to the 
daemon too. It can see the status to check if it is running. Also lets to run the program in foreground
to see any problem with code.

### Details

The program to be daemonized can be one of the following:

1. a function called once
2. a function called repeatedly in a loop
3. a function called once using system fork
4. a command line program running once with pcntl_exec
5. a command line program running once with pcntl_exec and fork
6. a command line program running in a loop with pcntl_exec and fork

### Explanation

* system fork: it is a way to duplicate running process from the moment it is called, so it is possible to
have two copies of the running program. It is used function pcntl_fork().

* pcntl_exec: when is used, the current running program is replaced by the command called.


The child processes can be called from an anonymous function, an existent function or using a php script.

### Example

Running a daemon task once 

code:

    <?php
    use Apolinux\DaemonAdmin\DaemonAdmin;
    use Apolinux\DaemonAdmin\TaskManager;

    require __DIR__ .'/../vendor/autoload.php' ;

    $procname = substr(basename(__FILE__), 0, -4);

    $daemon = new DaemonAdmin([
      'pid_file' => __DIR__ ."/var/$procname.pid" ,
      'log_dir' => __DIR__ ."/var"  ,
      'name' => $procname ,
      'task_mode' => TaskManager::MODE_ONCE_CALL ,
      'task' => 'testTask'
    ]);

    function testTask(){
        while(1){
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
    }

    $daemon->run();

example of use:

    >php examples/DaemonTestOnceCall.php 

    DaemonTestOnceCall Daemon
    Runs a process in background and controlls it
    Usage: DaemonTestOnceCall.php  start|stop|restart|status|help|h
      start  : starts the daemon
      stop   : stops the daemon
      restart: stop, then restart daemon
      status : shows daemon info if it is running
      fg     : start in foreground, no daemon                
      help,h : shows this help
    
    >php examples/DaemonTestOnceCall.php start
    Starting Daemon
    Daemon started

    >php examples/DaemonTestOnceCall.php status
    process is running with pid: 28572

    >php examples/DaemonTestOnceCall.php restart
    Stopping daemon
    Daemon stopped
    Starting Daemon
    Daemon started

    >php examples/DaemonTestOnceCall.php stop
    Stopping daemon
    Daemon stopped

    >php examples/DaemonTestOnceCall.php status
    process is not running

    >php examples/DaemonTestOnceCall.php fg
    [ 2020-03-23 21:57:00.5051 ] Child started with pid:29144
    start task
    file created
    file closed
    start task
    file created
    file closed
    start task
    file created
    ^C
    
### Example list

* DaemonTestException.php    : Run a test task in a loop but a exception is throwed
* DaemonTestFail.php         : Run a test task in a loop but a custom error is raised
* DaemonTestForkWaitFail.php : Run a test task in a forked loop but a custom error is raised
* DaemonTestLoopCallFork.php : Run a test task in a forked loop 
* DaemonTestLoopCall.php     : Run a test task in a loop 
* DaemonTestOnceCall.php     : Run a test task once and finish 
* DaemonTestOnceCmd.php      : Run a test command once and finish 

