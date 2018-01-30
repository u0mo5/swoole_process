<?php

require './vendor/autoload.php';
require_once('libs/process_pool.php');
require_once('functions/functions.php');
require_once('configs/configs.php');




use QL\QueryList;





//获取缓存目录
$files=getFile('./data/canshu');
//删除文件
$result = @unlink ('./data/sql/shenyang.sql');
// $sql=<<<SQL
// insert into building   
// (`id`, `city_id`, `sell_state`, `tags`, `name`, `sell_avg_price`, `things`, `developer_name`, `indexaction_id`, `secondaction_id`, `addr`, `sta_time`, `end_time`, `sell_address`, `licence_content`, `build`, `goods`, `volume_ratio`, `greening_rate`, `many`, `property_price`, `property_name`, `car_position_num`, `car_position_ratio`) values 
// SQL;
// writeData("./data/sql/shenyang.sql", $sql.PHP_EOL); 



$info_pk=array(
    
            'id'=>"楼盘ID",
            'city_id'=>"城市ID",
            'sell_state' =>"销售状态", //销售状态  没有这个汉字  不在键值对采
            'tags'=>"楼盘特点",   //标签    没有这个汉字  不在键值对采
            //'index_action'=>'一级区域',
            //'second_action'=>'二级区域',
              
            'name'=>"楼盘名",
            //'sale_tel' => "售楼处电话",
            'price_num'=>"参考单价",
            // 'sell_total_price'=>'楼盘总价',
              'things'=>'物业类型',
                'developer_name'=>'开发商',
               'position'=>'区域位置',//,todo  转换成一级区域 二级区域
               'addr'=>'楼盘地址',

  
    //			 'sale_min_first'=>['#container > div.can-container.clearfix > div.can-left > div:nth-child(2) > div.can-border > ul > li:nth-child(1) > div.des','text'],
    //  			 'sale_mon_pay'=>['#container > div.can-container.clearfix > div.can-left > div:nth-child(2) > div.can-border > ul > li:nth-child(2) > div.des','text'],
    //  			 'sale_promote'=>['#container > div.can-container.clearfix > div.can-left > div:nth-child(2) > div.can-border > ul > li:nth-child(3) > div.des','text'],
  
  			 'sta_time'=>'最新开盘',
  			 'end_time'=>'交房时间',
            'sell_address'=>'售楼处地址',
      		 'licence_content'=>'预售许可证',
      		 
      		 'decoration'=>'装修标准',
             'build'=>'建筑类型',
             'goods'=>'产权年限',
             'volume_ratio'=>'容积率',
             'greening_rate'=>'绿化率',
             'many'=>'规划户数',
                //   'community_lou_ceng'=>'楼层状况',
                    //  'community_jin_du'=>'工程进度',
              'property_price'=>'物业管理费',
              'property_name'=>'物业公司',
              'car_position_num'=>'车位数',
              'car_position_ratio'=>'车位比',
    
    
    
    
    
    
    );


$info_kv=array_flip($info_pk);









$tasks=array();







$task_id=0;
foreach ($files as $file) {
    $task_id+=1;
              $bid=substr( $file , strrpos($file , '-')+1, strrpos($file , '/')-strrpos($file , '-')+1 );                                                               //sy.fang.anjuke.com

    $tasks[]=array(
        'func'=>"deal_loupan",
        'url'=>"./data/canshu/".$file,
        'cityId_Map'=>$CONFIGS['cityId_Map'],
        'info_kv'=>$info_kv,
        'bid'=>$bid,
        'task_id'=>$task_id,
        'place_Map'=>$CONFIGS['place_Map'],
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
function deal_loupan($recv){
    clean_buffer();


                           
                                          
//开始解析首页                                          
$html = file_get_contents($recv->url);
$ql = QueryList::html($html)->rules($rules=[

            'id'=>['#container > div.can-container.clearfix > div.can-left > div:nth-child(1) > div.can-border > ul > li:nth-child(1) > div.des > a','href'],
            'city_id'=>['#container > div.can-container.clearfix > div.can-left > div:nth-child(1) > div.can-border > ul > li:nth-child(1) > div.des > a','href'],
            'name'=>['#j-triggerlayer','html'],
            'sell_state' => ['#container > div.can-container.clearfix > div.can-left > div:nth-child(1) > div.can-border > ul > li:nth-child(1) > div.des > i','text'],
            // 'tags'=>['#container > div.can-container.clearfix > div.can-left > div:nth-child(1) > div.can-border > ul > li:nth-child(2) > div.des','html'],
            
            'basics'=>['#container > div.can-container.clearfix > div.can-left > div:nth-child(1) > div.can-border > ul','html'],
            'sales'=>['#container > div.can-container.clearfix > div.can-left > div:nth-child(2) > div.can-border > ul','html'],
            'communitys'=>['#container > div.can-container.clearfix > div.can-left > div:nth-child(3) > div.can-border > ul','html'],


  

]);
$res = $ql->query()->getData(function($item){

          $tmp_bid=substr( $item['id'] , strrpos($item['id'] , '/')+1 );                                                               //sy.fang.anjuke.com
          $item['id']=substr($tmp_bid,0,-5);
          $item['city_id']=substr( $item['city_id'] ,strrpos($item['city_id'] , '//')+2, strrpos($item['city_id'] , '.fang')-8 );      //sy.fang.anjuke.com
          $item['name']=strFilter($item['name']);

      return $item;
});



$ql=null;
$html=null;


          //$l=array_column($data->all(),"url");
            $datas=$res->all();
            $data=$datas[0];
            
//-------------------------------------------------------------            
//采集楼盘基础信息
$qhtml=$data['basics'].$data['sales'].$data['communitys'];


$ql_basic = QueryList::html($qhtml)->rules([

            'li'=>['li','html'],

]);
$res_basic = $ql_basic->query()->getData(function($bsc){

          
          $bsc = QueryList::html($bsc['li'])->rules([  
              'name' => array('.name','text'),
               'des' => array('.des','html'),                                                
                                                               ])->query()->getData();

  

      
      return $bsc;
});     


$basic_data=$res_basic->all();

 
foreach($basic_data as $bd){
           // print_r($bd[0]);
    
    if(!empty($recv->info_kv->{$bd[0]['name']})){                        //kv  是汉字对应英文
       // echo $recv->info_kv->{$bd[0]['name']}.PHP_EOL;
        $data[$recv->info_kv->{$bd[0]['name']}]=$bd[0]['des'];
    }else{
        
       // print_r($recv->info_kv);
       //         echo "+++++++++++++++++__________".$recv->info_kv->{$bd[0]['name']}.PHP_EOL;
    }
    

}

unset($data['basics']);
unset($data['sales']);
unset($data['communitys']);

//采集楼盘基础信息结束
//------------------------------------------------------------- 




            
          
$datas=null;


          $cityId_Map=$recv->cityId_Map;


      if(   !empty($cityId_Map->{$data['city_id']})   ){
          $data['city_id']=intval($cityId_Map->{$data['city_id']});
      }else{
          $data['city_id']=null;
      }
    

    
//解析首页结束 
 $tmp = (array)$recv->info_kv; //kv  是汉字对应英文 

 $info_pk=array_flip($tmp);    //pk  是英对汉
 

//数组相加处理 对应键值求和
$out=array();          
foreach ($info_pk as $k=>$v) {
    

    if(!empty($data[$k])){
        
        

        
        
        if($k=="position"){
            $res_position = QueryList::html($data[$k])->rules([
            
                'indexaction'=>['a:nth-child(1)','text'],
                'secondaction'=>['a:nth-child(2)','text'],
            ])->query()->getData()->all();


            if(!empty($res_position[0])){
                   $indexaction=trim($res_position[0]['indexaction']);
                   $secondaction=trim($res_position[0]['secondaction']);
            }
        foreach($recv->place_Map as $pm){
                if($indexaction==$pm->name){
                    
                    $out['indexaction_id']=$data['indexaction_id']=$pm->id;
                    
                    foreach($pm->secondaryDistrictList as $sm){
                        if($secondaction==$sm->name){
                           $out['secondaction_id']=$data['secondaction_id']=$sm->id; 
                           continue;     
                        }
                        
                    }
                    continue;    
                }
        }
        
        
        // $out['secondaction_id']=$data['secondaction_id']=trim($tmmp[1]);
        unset($out['position']);
        }elseif($k=="tags"){
        
         $tmmp1=explode(' ',strFilter($data[$k]));
         $out[$k]=$data[$k]=implode(',',$tmmp1);

        }elseif($k=="price_num"){
        
            
            $res_price = QueryList::html($data[$k])->rules([
            
                'price'=>['span.can-spe.can-big.space2 ','text'],
            ])->query()->getData()->all();


            if(!empty($res_price[0])){
                $out[$k]=$res_price[0]['price'];
            }
            

                      


        }elseif($k=="sta_time"||$k=="end_time"){
        
            $data[$k]=$data[$k];

            if(!empty($data[$k])){
                $out[$k]=strtotime($data[$k]);
            }else{
                $out[$k]=0;
            }
            

                      


        }else{
                    $out[$k]=strFilter($data[$k]);
        }
    

    }else{
       $out[$k]="";
    }
    
    // code...
            unset($out['position']);
}


//============================================================
//============================================================
//采集wap详情
//开始解析首页

$wap_html = file_get_contents("./data/wap/".$recv->bid.".html");
$wap_html = str_replace('utf-8', 'ISO-8859-1', $wap_html);


//-------------------------------------------------------------            
//采集楼盘基础信息

$wap_ql = QueryList::html($wap_html)->rules([

            'li'=>['li.info','html'],

]);
$wap_res= $wap_ql->query()->getData(function($bsc){

          
          $bsc = QueryList::html($bsc['li'])->rules([  
              'name' => array('label','text'),
               'des' => array('span','text'),                                                
                                                               ])->query()->getData();

  

      
      return $bsc;
});     


$wap_datas=$wap_res->all();

//  print_r($wap_datas);exit;
foreach($wap_datas as $bd){


    if(!empty($bd[0]['name']) ){ 
        //print_r($bd[0]['name']);//kv  是汉字对应英文
        if( strpos($bd[0]['name'],"装修标准")!==false){
        $out['decoration']=strFilter($bd[0]['des']);
        $out['decoration']=convertStrType($out['decoration'],"TOSBC");
        
        
        }

    }else{

    }
    

}

$wap_source=null;


//采集楼盘基础信息结束
//------------------------------------------------------------- 
//============================================================
//============================================================


//============================================================
//============================================================
//采集wap页面
//开始解析首页

$wap_index_html = file_get_contents("./data/wap_index/".$recv->bid.".html");
$wap_index_html = str_replace('utf-8', 'ISO-8859-1', $wap_index_html);


//-------------------------------------------------------------            
//采集楼盘基础信息

$wap_index_ql = QueryList::html($wap_index_html)->rules([

            'map_url'=>['a.wui-line','href'],

]);
$wap_index_res= $wap_index_ql->query()->getData(function($wi){
            if(!empty($wi['map_url'])){
                $wi_url_arr=parse_url($wi['map_url']);
                if(!empty($wi_url_arr['query'])){
                    $wi_arr=array();
                    parse_str( $wi_url_arr['query'],$wi_arr);
                    if( !empty($wi_arr['lat']) && !empty($wi_arr['lng']) ){
//   print_r($wi_arr['lat']);exit;
                                $wi['lng']=$wi_arr['lng'];
                                $wi['lat']=$wi_arr['lat'];
                    }
                
                }
            }
      
      return $wi;
});     


$wap_index_datas=$wap_index_res->all();

//   print_r($wap_index_datas);exit;
foreach($wap_index_datas as $wr){
        if(!empty($wr['lat']) && !empty($wr['lng']) ){
                $out['longitude']=$wr['lng'];
                $out['latitude']=$wr['lat'];
    }
    
    
}




//采集楼盘基础信息结束
//------------------------------------------------------------- 
//============================================================
//============================================================



$now=time();
//----------------------------------------
         $sql=array_to_sql($out, $type='insert_update', $exclude = array(),$table="building",$insert_ext=array('add_time'=>time()),$update_ext=array(),$ext=" update_time={$now},price_diff =price_num/price_num*(VALUES(price_num)-price_num),");//显示实际的字段名
    //$sql=batch_insert_sql($out,$exclude = array());
    echo $sql;
    writeData("./data/sql/shenyang.sql", $sql.PHP_EOL); 
                                          echo "4.".$x=  shell_exec(" free -h ");
                                          echo "5.".$x=  shell_exec("sync; echo 3 > /proc/sys/vm/drop_caches");
                                          echo "6.".$x=  shell_exec(" free -h ");



}


