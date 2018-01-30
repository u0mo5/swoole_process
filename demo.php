<?php
$dy= (object) array();
$dy->url="https://m.anjuke.com/shen/loupan/253829/news/1306481/";

  $bid=substr($dy->url,strrpos($dy->url,"/loupan/")+8,strrpos($dy->url,"/news")-8-strrpos($dy->url,"/loupan/"));

  $did=substr($dy->url,strrpos($dy->url,"/news/")+6,strrpos($dy->url,"/")-6-strrpos($dy->url,"/news/"));
  
  
  $str='2017年11月20日  14:00';
  $str=str_replace("年","-",$str);
    $str=str_replace("月","-",$str);
        $str=str_replace("日","",$str);
        echo $str;
  echo strtotime($str);
