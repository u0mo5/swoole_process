<?php

require './vendor/autoload.php';
require_once('libs/process_pool.php');
require_once('functions/functions.php');
require_once('configs/configs.php');

use QL\QueryList;







// echo  json_encode($cityId_Map);exit;

$files=getFile('./data/canshu');
//删除文件
$sql_file='./data/sql/shenyang_xiangce.sql';
$result = @unlink ($sql_file); 








$tasks=array();






$taskid=0;

foreach ($files as $file) {
    $taskid+=1;
    $bid=str_replace("canshu-","",$file);
    $bid=str_replace(".html","",$bid);
    $tasks[]=array(
        'func'=>"deal_xiangce",
        'url'=>"https://m.anjuke.com/shen/xinfang/loupan/{$bid}/ajaxalbum/",
        'bid'=>$bid,
        'cityId_Map'=>$CONFIGS['cityId_Map'],

        'task_id'=>$taskid,
        'xiangce_type'=>$CONFIGS['xiangce_type']
        
        
    );

}
// $tasks=array_slice($tasks,0,1);
 //print_r($tasks);exit;



$p= new Process_pool($tasks,40);























//============================================================================================================
//============================================================================================================
/**
 * 
 *开始回调 
*/

function deal_xiangce($recv){
    $sql_file='./data/sql/shenyang_xiangce.sql';
echo "2.".$x=  shell_exec(" free -h ");


                           
                                          
//开始解析首页                                          




if(!file_exists("./data/xiangceapi/$recv->bid") ||  filesize("./data/xiangceapi/$recv->bid") < 25){
shell_exec("wget -c ".$recv->url." -O ./data/xiangceapi/{$recv->bid}");
clean_buffer();
}

$data = file_get_contents("./data/xiangceapi/$recv->bid");
    $time=time();


$data=(array)(json_decode($data));



$out=array();
if(!empty($data['images'])){


foreach($data['images'] as $k=>$v){
    
    if($k=="complete"){
    }
        if($k=="outdoor"){
        
    }
        if($k=="effect"){
        
    }
        if($k=="plan"){
        
    }
        if($k=="templet"){
        
    }
        if($k=="location"){
        
    }
    $v=(array)$v;
    // print_r($recv->xiangce_type->{$k} );exit;
    foreach($v as $w){
        $out[]=['type_id'=>($recv->xiangce_type->{$k}),'img_url'=>$w->url,'bid'=>$recv->bid];
    }


    
}

// print_r($out);exit;

    foreach($out as $vv){
            $filename= substr( $vv['img_url'], strrpos($vv['img_url'], '/m/')+3, strrpos($vv['img_url'] , '/')-strrpos($vv['img_url'], '/m/')-3 ).".jpg"; 
            $vv['img_name']="275x206n.jpg";

                      			     if(!file_exists("./data/xiangce/$filename") ||  filesize("./data/xiangce/$filename")/1000 < 1){
                      			        
                                        shell_exec("wget -c ".$vv['img_url']." -O ./data/xiangce/{$filename}");
                                        

                                        
                                        
                                     }
                                     
                                    if(file_exists("./data/xiangce/$filename") && filesize("./data/xiangce/$filename")/1000 > 1){
                                            $sql=array_to_sql($vv, $type='insert', $exclude = array());
                                            echo $sql;
                                            writeData($sql_file, $sql.PHP_EOL); 
                                    }        
    }
    


//采集楼盘基础信息结束
//------------------------------------------------------------- 
echo "task:{$recv->task_id}||bid:{$recv->bid}<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<".PHP_EOL;    
    
print_r($out);

echo "task:{$recv->task_id}||bid:{$recv->bid}>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>".PHP_EOL;

//----------------------------------------
//    



    }

}






















