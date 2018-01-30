<?php
$CONFIGS=array();



$CONFIGS['xiangce_type']=array(
    
'effect'=>1,	//效果图
'plan'=>2	,//规划图
'outdoor'=>3,//	实景图
'location'=>4,//	交通图
'templet'=>5,//	样板间
'complete'=>6,//	配套图
    
    
    );
    
$func_cityId_Map = function(){
                        //$file=file_get_contents("40total.txt");
$file=<<<STR
[{"title":"\u6c88\u9633","url":"https:\/\/shen.fang.anjuke.com","index":"https:\/\/sy.anjuke.com","total":"644","cityId":1}]
STR;
                        $lists=json_decode($file);
                        
                        $cityId_Map=array();
                        foreach($lists as $li){
                            //var_dump($li->url);
                            $url=substr($li->url ,strrpos($li->url, '//')+2, strrpos($li->url , '.fang')-8 ); 
                            $cityId_Map[$url]=$li->cityId;
                        }
                        return $cityId_Map;
            };    
$CONFIGS['cityId_Map'] = $func_cityId_Map();

include_once('place.php');