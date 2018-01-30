<?php

require './vendor/autoload.php';
require_once('libs/process_pool.php');
require_once('functions/functions.php');
require_once('configs/configs.php');

use QL\QueryList;




// echo  json_encode($cityId_Map);exit;

$files=getFile('./data/dynamic');
//删除文件
$sql_file='./data/sql/shenyang_dynamic.sql';
$result = @unlink ($sql_file); 








$tasks=array();






$taskid=0;

foreach ($files as $file) {
    $taskid+=1;
    $bid=str_replace(".html","",$bid);
    $tasks[]=array(
        'func'=>"deal_dynamic",
        // 'url'=>"https://m.anjuke.com/shen/xinfang/loupan/{$bid}/ajaxalbum/",
        'url'=>"./data/dynamic/$file",
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

function deal_dynamic($recv){
    $sql_file='./data/sql/shenyang_dynamic.sql';
    clean_buffer();


// $html = file_get_contents($recv->file);  

$wap_html = file_get_contents($recv->url);

// print_r($html);
// $html=characet($html);
// $html = iconv('GBK','UTF-8//IGNORE',$html);

$wap_html = str_replace('utf-8', 'ISO-8859-1', $wap_html);


$ql = QueryList::html($wap_html)->rules($rules=[
"url"=>['a.news-row.g-press','href'],
"time"=>['div.news-time','text'],
]);
$res = $ql->query()->getData();
$dynamic_datas=$res->all();
// print_r($dynamic_datas);exit;


foreach($dynamic_datas as $dy){
  $bid=substr($dy['url'],strrpos($dy['url'],"/loupan/")+8,strrpos($dy['url'],"/news")-8-strrpos($dy['url'],"/loupan/"));
  $did=substr($dy['url'],strrpos($dy['url'],"/news/")+6,strrpos($dy['url'],"/")-6-strrpos($dy['url'],"/news/"));
  $time=ch_strtotime($dy['time']);
    $news_file=$bid."_".$did."@".$time.".html";
        if(!file_exists($news_file) ||  filesize($news_file)/1000 < 5){
        shell_exec("wget -c ".$dy['url']." -O ./data/dynamic_news/{$news_file}");
        clean_buffer();
        }
        
        if(!file_exists($news_file) ||  filesize($news_file)/1000 < 5 ){
        
        get_page( $dy['url'] ,"./data/dynamic_news/{$news_file}");
        clean_buffer();
        
        }
    
    
}                                          
//开始解析首页                                          



}





























