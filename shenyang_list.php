<?php

require './vendor/autoload.php';
require_once('libs/process_pool.php');
require_once('functions/functions.php');
require_once('configs/configs.php');

shell_exec("./data/clean.sh");
sleep(2);

use QL\QueryList;



//步骤一：下载列表文件到本地目录
$file=<<<STR
{"title":"\u6c88\u9633","url":"https:\/\/shen.fang.anjuke.com","index":"https:\/\/sy.anjuke.com","total":"663","cityId":1}
STR;
$lists=json_decode($file);
print_r($lists);

wget_lists($url=$lists->url,$cityId=$lists->cityId,$totalRecord=$lists->total,$pageSize=30);


//步骤二：对所有楼盘详情页面文件进行下载


      $totalPageNum = ($totalRecord  +  $pageSize  - 1) / $pageSize;
                   $tasks=array();
        for($i=1;$i<=$totalPageNum;$i++){
                $tasks[]=array(
        'func'=>"wget_loupan",
        'url'=>"./data/lists/{$cityId}/{$i}.html",
        'cityId'=>$cityId,
        'task_id'=>$i,



    );
        }






// $tasks=array_slice($tasks,0,1);
 //print_r($tasks);exit;



$p= new Process_pool($tasks,10);







//=============================================================
//=============================================================
function wget_lists($url="https://shen.fang.anjuke.com",$cityId,$totalRecord=1287,$pageSize=30){
      $totalPageNum = ($totalRecord  +  $pageSize  - 1) / $pageSize;
      echo "dfdfdf".$totalPageNum.PHP_EOL;
             $out=array();
        for($i=1;$i<=$totalPageNum;$i++){
            if(!file_exists("./data/lists/{$cityId}/{$i}.html") ||  filesize("./data/lists/{$cityId}/{$i}.html")/1000 < 50 ){
                echo filesize("./data/lists/{$cityId}/{$i}.html").":{$cityId}-{$i}".PHP_EOL;   
                            shell_exec("wget -c {$url}/loupan/all/p{$i}_s6/ -O ./data/lists/{$cityId}/{$i}.html");
            }

        }
      
      
      
}

function wget_loupan($recv){
    

$url=$recv->url;
$cityId=$recv->cityId;
// $bid=substr( $r['url'] , strrpos($r['url'] , '/')+1,strrpos($r['url'] , '.html')- strrpos($r['url'] , '/')-1 );


             $out=array();
            $res_path=$url;
            if(file_exists($res_path) &&  filesize($res_path)/1000 > 5 ){
                     $res=array();


//本地读取方式
                                $html = file_get_contents($res_path);
                                $ql = QueryList::html($html)->rules([
                                    'url' => array('.key-list>.item-mod','data-link'),

                                ]);
                                unset($html);
                                $data = $ql->query()->getData();
                              $res=array_merge($data->all(),$res);
                              $out=array_merge($data->all(),$out);
                                //echo json_encode($l); 
                                print_r($res);

                                
                                
                                
                                
                            //   echo "[taskid:{$recv->task_id}][url:{$url}][行号：{".__line__."}]===========================================".PHP_EOL; 
                              foreach($res as $r){
                                  if(empty($r['url'])){
                                      continue;
                                  }
                                  
                                  

                                  
                                $bid=substr( $r['url'] , strrpos($r['url'] , '/')+1,strrpos($r['url'] , '.html')- strrpos($r['url'] , '/')-1 );     

                                
                                
                                     //这个是楼盘pc详情
                                    //  $bid=substr( $r['url'] , strrpos($r['url'] , '/')+1 );
                                     $canshu_url=substr( $r['url'] ,0, strrpos($r['url'] , '/')+1).'canshu-'.substr( $r['url'] , strrpos($r['url'] , '/')+1 );
                                     $canshu_filename='canshu-'.substr( $r['url'] , strrpos($r['url'] , '/')+1 );
                                     page_download($canshu_url,$canshu_filename,$canshu_path="./data/canshu/");

                                     
                                     
                                     
                                     
                                     //这个是楼盘wap首页

                                     $wap_index_url="https://m.anjuke.com/shen/loupan/{$bid}/";
                                     $wap_index_filename=substr( $r['url'] , strrpos($r['url'] , '/')+1 );
                                     page_download($wap_index_url,$wap_index_filename,$wap_index_path="./data/wap_index/");
                                     
                                     //这个是楼盘wap详情

                                     $wap_url="https://m.anjuke.com/shen/loupan/{$bid}/params/";
                                     $wap_filename=substr( $r['url'] , strrpos($r['url'] , '/')+1 );
                                     page_download($wap_url,$wap_filename,$wap_path="./data/wap/");

                                     
                                     
                                     //这个是楼盘wap动态

                                     $dynamic_url="https://m.anjuke.com/shen/loupan/{$bid}/news/?from=ajk_tw_dy_qbdt";
                                     $dynamic_filename=substr( $r['url'] , strrpos($r['url'] , '/')+1 );
                                     page_download($dynamic_url,$dynamic_filename,$dynamic_path="./data/dynamic/");
                                     
                                     
                                     
                                //      //这个是楼盘首页
                                     //  $filename= substr( $r['url'] , strrpos($r['url'] , '/')+1 ); 
                      			   //  if(!file_exists("./data/loupans/$filename") ){
                      			   //      print_r($r);
                            //              //writeData("./data/loupans/{$cityId}.txt", json_encode($r).PHP_EOL); 
                            //          }
                                //      if(!file_exists("./data/loupans/$filename") ||  filesize("./data/loupans/$filename")/1000 < 50 ){

                              		// 	shell_exec("wget -c ".$r['url']." -O ./data/loupans/{$filename}");
                                //      }
                                //      if(!file_exists("./data/loupans/$filename") ||  filesize("./data/loupans/$filename")/1000 < 50 ){

                              		// 	get_page( $r['url'] ,"data/loupans/$filename");
                              		// 	echo "5.".$x=  shell_exec("sync; echo 3 > /proc/sys/vm/drop_caches");

                                //      }
                                     
                                //     //这个是楼盘户型页
                                //      $bid=substr( $r['url'] , strrpos($r['url'] , '/')+1 );
                                //      $huxing_url=substr( $r['url'] ,0, strrpos($r['url'] , '/')+1).'huxing-'.substr( $r['url'] , strrpos($r['url'] , '/')+1 );
                                //      $huxing_filename='huxing-'.substr( $r['url'] , strrpos($r['url'] , '/')+1 );
                                     
                                //      if(!file_exists("./data/huxing/$huxing_filename") ||  filesize("./data/huxing/$huxing_filename")/1000 < 50 ){

                              		// 	shell_exec("wget -c ".$huxing_url." -O ./data/huxing/{$huxing_filename}");
                                //      }
                                //      if(!file_exists("./data/huxing/$huxing_filename") ||  filesize("./data/huxing/$huxing_filename")/1000 < 50 ){

                              		// 	get_page( $huxing_url ,"data/huxing/$huxing_filename");
                              		// 	echo "5.".$x=  shell_exec("sync; echo 3 > /proc/sys/vm/drop_caches");

                                //      }
                                //      //这个是楼盘相册页
                                //      $bid=substr( $r['url'] , strrpos($r['url'] , '/')+1 );
                                //      $xiangce_url=substr( $r['url'] ,0, strrpos($r['url'] , '/')+1).'xiangce-'.substr( $r['url'] , strrpos($r['url'] , '/')+1 );
                                //      $xiangce_filename='xiangce-'.substr( $r['url'] , strrpos($r['url'] , '/')+1 );
                                     
                                //      if(!file_exists("./data/xiangce/$xiangce_filename") ||  filesize("./data/xiangce/$xiangce_filename")/1000 < 50 ){

                              		// 	shell_exec("wget -c ".$xiangce_url." -O ./data/xiangce/{$xiangce_filename}");
                                //      }
                                //      if(!file_exists("./data/xiangce/$xiangce_filename") ||  filesize("./data/xiangce/$xiangce_filename")/1000 < 50 ){

                              		// 	get_page( $xiangce_url ,"data/xiangce/$xiangce_filename");
                              		// 	echo "5.".$x=  shell_exec("sync; echo 3 > /proc/sys/vm/drop_caches");

                                //      }
                                     

                                     
                              


                            
            }

        }
      

      
}




