<?php

require './vendor/autoload.php';
require_once('libs/process_pool.php');
require_once('functions/functions.php');
require_once('configs/configs.php');




use QL\QueryList;





//获取缓存目录
$files=getFile('./data/dynamic_news');
//删除文件
$sql_file='./data/sql/dynamic_news.sql';
$result = @unlink ($sql_file);
// $sql=<<<SQL
// insert into building   
// (`id`, `city_id`, `sell_state`, `tags`, `name`, `sell_avg_price`, `things`, `developer_name`, `indexaction_id`, `secondaction_id`, `addr`, `sta_time`, `end_time`, `sell_address`, `licence_content`, `build`, `goods`, `volume_ratio`, `greening_rate`, `many`, `property_price`, `property_name`, `car_position_num`, `car_position_ratio`) values 
// SQL;
// writeData("./data/sql/shenyang.sql", $sql.PHP_EOL); 











$tasks=array();







$task_id=0;
foreach ($files as $file) {
    $task_id+=1;
              $bid=substr( $file , 0, strrpos($file , '_'));
              $did=substr( $file , strrpos($file , '_')+1, strrpos($file , '@')-strrpos($file , '_')-1);   
              $time=substr( $file , strrpos($file , '@')+1, strrpos($file , '.html')-strrpos($file , '@')-1);  
              //sy.fang.anjuke.com

    $tasks[]=array(
        'func'=>"deal_dynamic_news",
        'url'=>"./data/dynamic_news/".$file,
        'cityId_Map'=>$CONFIGS['cityId_Map'],
        'bid'=>$bid,
        'did'=>$did,
        'time'=>$time,
        'task_id'=>$task_id,
        'sql_file'=>$sql_file,
    );

}
// $tasks=array_slice($tasks,0,1);
//  print_r($tasks);exit;



$p= new Process_pool($tasks,100);



//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function deal_dynamic_news($recv){



                           
                                          


//============================================================
//============================================================
//采集wap页面
//开始解析首页

$wap_html = file_get_contents($recv->url);

// print_r($html);
// $html=characet($html);
// $html = iconv('GBK','UTF-8//IGNORE',$html);

$wap_html = str_replace('utf-8', 'ISO-8859-1', $wap_html);






//-------------------------------------------------------------            
//采集楼盘基础信息


  print_r($wap_html);
$wap_ql = QueryList::html($wap_html)->rules([


            'title'=>['div.dyn_view > div.newhead','text'],
            'content' => [' div.dyn_view > p','text'],


]);
$wap_res= $wap_ql->removeHead()->query()->getData();     


$wap_datas=$wap_res->all();

  print_r($wap_datas);
$out=$wap_datas[0];
$out['bid']=$recv->bid;
$out['id']=$recv->did;
$out['time']=$recv->time;
  print_r($out);

//采集楼盘基础信息结束
//------------------------------------------------------------- 
//============================================================
//============================================================

if(empty($out['title'])||empty($out['content'])){
    
}else{
    


// print_r($out);exit;
// echo "++++++++++++++++".PHP_EOL;


//----------------------------------------
         $sql=array_to_sql($out, $type='insert_update', $exclude = array(),$table="dynamic");//显示实际的字段名
    //$sql=batch_insert_sql($out,$exclude = array());
    echo $sql;
    writeData($recv->sql_file, $sql.PHP_EOL); 
    clean_buffer();

}

}


