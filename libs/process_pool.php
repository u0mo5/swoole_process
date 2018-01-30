<?php
class Process_pool{
  public $worker_num;
  public $type;
  public $workers;
   
    
   function __construct($tasks=array(),$worker_num=2){ //初始化对象，将初始化值放在括号内
   $this->name=$name;
   $this->worker_num=$worker_num;
   


    
    

for($i = 0; $i < count($tasks); $i+=$worker_num){
      	for($w = 0; $w < $worker_num; $w++)
    {
        $this->init_process();
    }
    
    
echo "I:".$i.PHP_EOL;  
$li=array_slice($tasks,$i,$worker_num);


// print_r($li);
$t=0;
        foreach($this->workers as $pid => $process)
    {
        if($t<count($li)){
        $process->push(json_encode($li[$t]));
        }
        $t++;
    }
  
      for($k = 0; $k < $worker_num; $k++)
    {
        $ret = swoole_process::wait();
        $pid = $ret['pid'];
        unset($this->workers[$pid]);
        //echo "Worker Exit, PID=".$pid.PHP_EOL;
    }
    
	$process->freeQueue();
    echo "==================".PHP_EOL;

  }
    
    
   }   
   
   
   
   function init_process(){
        $process = new swoole_process('callback_function', false, false);
    	$process->freeQueue();
        $process->useQueue();
        $pid = $process->start();
        $this->workers[$pid] = $process;
        echo "Master: new worker, PID=".$pid."\n";
   }
    
}

function callback_function(swoole_process $worker)
{
    //echo "Worker: start. PID=".$worker->pid."\n";
    //recv data from master
    $recv = $worker->pop();

  	$recv=json_decode($recv);

//本地读取方式
//下载列表
call_user_func($recv->func, $recv); 
    
        $worker->exit(0);
}
// function do_this($recv){
//     echo $recv->arg;
// }

// $tasks=array();
// $tasks[]=array(
//     'func'=>"do_this",
//     'arg'=>"hello"
//     );
//     $tasks[]=array(
//     'func'=>"do_this",
//     'arg'=>"hi1"
//     );
//         $tasks[]=array(
//     'func'=>"do_this",
//     'arg'=>"hi2"
//     );
//         $tasks[]=array(
//     'func'=>"do_this",
//     'arg'=>"hi3"
//     );
//         $tasks[]=array(
//     'func'=>"do_this",
//     'arg'=>"hi4"
//     );
//         $tasks[]=array(
//     'func'=>"do_this",
//     'arg'=>"hi5"
//     );

// $p= new Process_pool($tasks,5);