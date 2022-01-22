<?php
error_reporting(0);
$tuid=$_COOKIE["b_id"];

$ublocked=array();

require 'blocked_users.php';

//blocking part
if(in_array($tuid,$ublocked)){
echo "You are blocked for visiting adult content"; exit;}




$url=$_POST["get_post_url"]?$_POST["get_post_url"]:$_GET["get_post_url"];
$url=trim($url);



include 'blocked.php';
$block_txt=""; if(is_blocked($url)){
$block_txt= "This URL can't get in free mode for some reason...";
$_GET["get_post_url"]=null;
$_POST["get_post_url"]=null;
}



if(strtolower($url)=="http://.setting"||strtolower($url)=="http://.setting/"||strtolower($url)==".setting"){
include'set.php';
exit;
}


if(strtolower($url)=="http://.home"||strtolower($url)=="http://.home/"||strtolower($url)==".home"){
$_GET["get_post_url"]=null;
$_GET["get_post_submit"]=null;
$url=null;
}

if(strtolower($url)=="__u__"){
$_GET["get_post_url"]=null;
$_GET["get_post_submit"]=null;
$url=null;
}



require 'head.php';

if(!$_GET["get_post_url"]&&!$_POST["get_post_url"]){

echo '<html>
<head>';
 include'header.php';
 echo '
 <title>Awal Free Browser...</title></head>
<body>';


echo '<form method="get" action="index.php" class="fffg_body">
<div class="fffg_ac">
<div class="fffg_menu">
<input type="text" name="get_post_url" value="'.$url.'" style="width:auto;margin-bottom:5px;"><br>
<input type="submit" name="get_post_submit" value="Go > > >" class="fffg_submit"/><span style="float:right"><small>0 K - 0.1 sec</small> - <a href="set.php">Setting</a></span></div></div></form>';

echo $block_txt."<br><br><a href=\"index.php?get_post_url=__U__\">LONG PRESS THIS TO SETUP ON YOUR ANDROID APP</a><br>";




$yserver=$server_by_code[$setting["server"]];
$yserverf=$servers_f[$yserver];
$server_link="https://0.freebasics.com/addservice/?service_id=".$yserverf."&previous_url=%2Fsearchservices%2F%3Fref%3Dfbs_can_rdr&ref=fbs_can_rdr";
$ERROR="";
if(!empty(trim($yserverf))){
$ERROR.="লগিন করতে নিচের লিংক কপি করে ডুকেন <br><textarea>$server_link</textarea><br>";
}//not empty
else{ $ERROR.="<font color=red>contact ADMIN for your server link</font><BR>"; }
echo "<br>".$ERROR;



include 'vaild.php';

}

else{
//load the url
//check if protocol is missing
if(stripos($url,"://")==false){
header("location:index.php?get_post_url=".urlencode("https://www.google.com/search?q=".urlencode($url)));
exit;
}


//check if posted
$postfields="";
if($_POST["get_post_url"]) {
//echo "post";
$method="post";
foreach($_POST as $key => $value){
if($key!="get_post_url"&&$key!="get_post_submit"){
$postfields.=$key."=".urlencode($value)."&";}
}

} else{
//echo "get";
foreach($_GET as $key => $value){
if($key!="get_post_url"&&$key!="get_post_submit"){
$postfields.=$key."=".urlencode($value)."&";}
}

}//else

//get the post fields
$postfields=substr($postfields,0,-1);
//get absoulote method
if(!empty($postfields)&&$method!="post"){ $method="get";} elseif($method=="post"){}
else{$method="none";}


//echo $method.$postfields;

//if method is get
if($method=="get"){
$url=$url."?". $postfields;
header("location:index.php?get_post_url=".urlencode($url));
exit;
}

require 'curl.php';
$cookie_file="usr/".$uid.".cookie";

load_url($url,$method,$postfields,$cookie_file,$setting);

}



?>