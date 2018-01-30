<?php


function convertStrType($str, $type) {
 
    $dbc = array( 
      '０' , '１' , '２' , '３' , '４' , 
      '５' , '６' , '７' , '８' , '９' , 
      'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' , 
      'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' , 
      'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' , 
      'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' , 
      'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' , 
      'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' , 
      'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' , 
      'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' , 
      'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' , 
      'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' , 
      'ｙ' , 'ｚ' , '－' , '　' , '：' ,
      '．' , '，' , '／' , '％' , '＃' ,
      '！' , '＠' , '＆' , '（' , '）' ,
      '＜' , '＞' , '＂' , '＇' , '？' ,
      '［' , '］' , '｛' , '｝' , '＼' ,
      '｜' , '＋' , '＝' , '＿' , '＾' ,
      '￥' , '￣' , '｀'
 
);
 
    $sbc = array( //半角
      '0', '1', '2', '3', '4', 
      '5', '6', '7', '8', '9', 
      'A', 'B', 'C', 'D', 'E', 
      'F', 'G', 'H', 'I', 'J', 
      'K', 'L', 'M', 'N', 'O', 
      'P', 'Q', 'R', 'S', 'T', 
      'U', 'V', 'W', 'X', 'Y', 
      'Z', 'a', 'b', 'c', 'd', 
      'e', 'f', 'g', 'h', 'i', 
      'j', 'k', 'l', 'm', 'n', 
      'o', 'p', 'q', 'r', 's', 
      't', 'u', 'v', 'w', 'x', 
      'y', 'z', '-', ' ', ':',
      '.', ',', '/', '%', ' #',
      '!', '@', '&', '(', ')',
      '<', '>', '"', '\'','?',
      '[', ']', '{', '}', '\\',
      '|', '+', '=', '_', '^',
      '￥','~', '`'
 
);
if($type == 'TODBC'){
return str_replace( $sbc, $dbc, $str ); //半角到全角
}elseif($type == 'TOSBC'){
return str_replace( $dbc, $sbc, $str ); //全角到半角
}else{
return $str;
}
}




/**
 * 总体封装下载页面
 */
function page_download($url,$filename="",$path=""){
                                     if(!file_exists($path.$filename) ||  filesize($path.$filename) < 25 ){
                              			shell_exec("wget -c ".$url." -O {$path}{$filename}");

                              			clean_buffer();
                                     }
                                     if(!file_exists($path.$filename) ||  filesize($path.$filename) < 25 ){

                              			get_page( $url ,$path.$filename);
                              			clean_buffer();
                                     }
}




/**
 *   用php实现下载页面 
 */
function get_page($url,$file_path){
$html_content = file_get_contents($url);//获得网页内容
$fp=fopen($file_path,"w");
if(!file_exists($file_path))
{
        return false;
    }
    fwrite($fp,$html_content);
    fclose($fp);
}

/**
 *获取文件列表 
 */
function getFile($dir) {
    $fileArray[]=NULL;
    if (false != ($handle = opendir ( $dir ))) {
        $i=0;
        while ( false !== ($file = readdir ( $handle )) ) {
            //去掉"“.”、“..”以及带“.xxx”后缀的文件
            if ($file != "." && $file != ".."&&strpos($file,".")) {
                $fileArray[$i]="".$file;
                if($i==1000000){
                    break;
                }
                $i++;
            }
        }
        //关闭句柄
        closedir ( $handle );
    }
    return $fileArray;
}
function array_to_sql($array, $type='insert', $exclude = array(),$table="",$insert_ext=array(),$update_ext=array(),$ext=""){
     
    $sql = '';
    if(count($array) > 0){
      foreach ($exclude as $exkey) {
        unset($array[$exkey]);//剔除不要的key
      }
 
      if('insert' == $type){
  
        $keys = array_keys($array);
        $values = array_values($array);
        $col = implode("`, `", $keys);
        $val = implode("', '", $values);
        $sql= "(`$col`) values('$val')";
      }else if('update' == $type){
        $tempsql = '';
        $temparr = array();
        foreach ($array as $key => $value) {
          $tempsql = "'$key' = '$value'";
          $temparr[] = $tempsql;
        }
 
        $sql = implode(",", $temparr);
      }else if('insert_update' == $type){
          
          
        //insert方式
        $insert_arr=array_merge($array,$insert_ext);
        
    
        $keys = array_keys($insert_arr);
        $values = array_values($insert_arr);
        $col = implode("`, `", $keys);
        $val = implode("', '", $values);
        
         
        //update方式       
        $tempsql = '';
        $temparr = array();
        $update_arr=array_merge($array,$update_ext);
        
        foreach ($update_arr as $key => $value) {
          $tempsql = "$key = VALUES($key)";
          $temparr[] = $tempsql;
        }
        $ups = implode(",", $temparr);
        $sql= "insert into  $table  (`$col`) values('$val') "." ON DUPLICATE KEY  UPDATE  $ext  $ups ;";
        

      }
    }
    return $sql;
}
function batch_insert_sql($array,  $exclude = array()){
     
    $sql = '';
    if(count($array) > 0){
      foreach ($exclude as $exkey) {
        unset($array[$exkey]);//剔除不要的key
      }

        $keys = array_keys($array);
        $values = array_values($array);
        $col = implode("`, `", $keys);
        $val = implode("', '", $values);
        $sql= "('$val'),";
   
    }
    return $sql;
}
  function writeData($filepath, $data) 
{ 
    $fp = fopen($filepath,'a');  
    do{ 
        usleep(100); 
    }while (!flock($fp, LOCK_EX)); 
    
    $res = fwrite($fp, $data."\n"); 
    flock($fp, LOCK_UN); 
    fclose($fp);  
    return $res; 
} 

function getLine($filepath){
$file = fopen($filepath, "r");
$user=array();
$i=0;
//输出文本中所有的行，直到文件结束为止。
while(! feof($file))
{
 $user[$i]= trim(fgets($file));//fgets()函数从文件指针中读取一行
 $i++;
}
fclose($file);
$user=array_filter($user);

return $user;

}

function strFilter($str){
    

    $str=str_replace("[查看详情]","",$str);   //过滤掉  [*****]
    $str=str_replace("[查看地图]","",$str);   //过滤掉  [*****]   
    $str=str_replace("[房贷计算器]","",$str);   //过滤掉  [*****]
    $str=str_replace("[立即报名]","",$str);   //过滤掉  [*****]
    $str=str_replace("[价格走势]","",$str);   //过滤掉  [*****]
    $str = preg_replace("/(\s+)/",' ',$str);   //过滤大量空格为一个空格
    $str = preg_replace( "@<(.*?)>@is", "", $str ); //过滤html标签内所有内容

    
    
    $str=trim($str);
    return $str;
    
}

function clean_buffer(){

    
    shell_exec("sync; echo 3 > /proc/sys/vm/drop_caches");
}
function characet($data){
  if( !empty($data) ){
    $fileType = mb_detect_encoding($data , array('UTF-8','GBK','LATIN1','BIG5')) ;
    if( $fileType != 'UTF-8'){
      $data = mb_convert_encoding($data ,'utf-8' , $fileType);
    }
  }
  return $data;
}

function ch_strtotime($str=''){
// $str='2017年11月20日  14:00'
  $str=str_replace("年","-",$str);
    $str=str_replace("月","-",$str);
        $str=str_replace("日","",$str);
        // echo $str;
  return strtotime($str);

}