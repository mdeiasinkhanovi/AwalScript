<?php
error_reporting(0);
require 'head.php';
ob_start();
//require 'servers.php';


if(!is_dir('usr')){ mkdir('usr'); }
chmod("usr", 0770);



$_email=strtolower(trim($_POST["email"]));
$_pass=strtolower(trim($_POST["pass"]));

if((!empty($_pass)&&!empty($_pass))&&strlen($_email)<35&&strlen($_pass)<35){

if($_POST["check"]!=11){echo "Error"; exit;}

if($_POST["reg"]){

$set["name"]=$_email;
$set["pass"]=$_pass;
$set["regtime"]=time();
$set["cookie"]="yes";
$set["download"]="yes";
$set["useragent"]="opera mini android";
$set["vaild"]=3;
$set["server"]=$server_id;
$sets=json_encode($set);


if(is_file("usr/".md5($_email).".set")){
$ERROR="<font color=red>Alrady taken username ".$_email."</font><br>";}

elseif(file_put_contents("usr/".md5($_email).".set",$sets)){
$ERROR="<font color=green>Registration Successful</font><br>";}

else{$ERROR="<font color=red>Something went wrong or registration closed temporary</font><br>"; }


}

if($_POST["login"]){

if(is_file("usr/".md5($_email).".set")&&filesize("usr/".md5($_email).".set")<40){
echo '<meta name="viewport"  content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />There is a problem in your account <a href=fix.php>Fix here</a>';
exit;
}

$getpass=json_decode(file_get_contents("usr/".md5($_email).".set"),true);

$ty=$getpass["time"];
$now=time()-$ty;

if(($getpass["server"]!=$server_id)&&(strtolower($getpass["pass"])==$_pass)){
$yserver=$server_by_code[$getpass["server"]];
$yserverf=$server_f[$yserver];
$server_link="https://0.freebasics.com/addservice/?service_id=".$yserverf."&previous_url=%2Fsearchservices%2F%3Fref%3Dfbs_can_rdr&ref=fbs_can_rdr";

$ERROR="<title>Server changed...</title> <font color=red>Wrong server</font><br>";

if(!empty(trim($yserverf))){
$ERROR.="লগিন করতে <a href='index.php?__browser_open=".urlencode($server_link)."'>এখানে ক্লিক</a>(3.3.0/3.3.1)  অথবা নিচের লিংক কপি করে ডুকেন <br><textarea>$server_link</textarea><br>";
}//not empty
else{ $ERROR.="<font color=red>contact ADMIN</font><BR>"; }
}//wrong server
else if($now<120&&strtolower($getpass["pass"])==$_pass){
$ERROR="<font color=red>Try again after few minutes</font><BR>";
} 
else if(strtolower($getpass["pass"])==$_pass){
$gE=$getpass;
$time=time();
$gE["ltime"]=$time;
$gE["name"]=$_email;
file_put_contents("usr/".md5($_email).".set",json_encode($gE));
setcookie("b_id", md5($_email), time() + (86400 * 7), "/");
setcookie("p_id", md5($_pass), time() + (86400 * 7), "/");
setcookie("t_id", md5($time), time() + (86400 * 7), "/");
header("Location:index.php");
ob_end_flush();
flush();
exit;
}else{
$ERROR="<font color=red>Password is Wrong</font><br>"; }


}



}
?>
<html>
<head><?php
include'header.php';
include'meta.php';
?><title>Login to Browser...</title></head>
<body>
<?=$ERROR?>
Login Security improved <font color=green>3x</font> <br>
<form method="post" action="login.php">
Login Name:<br>
<input type="text" name="email" required>
<br>
Login Pass:<br>
<input type="text" name="pass" required>
<br>
<input type="checkbox" name="check" value="11" required> i agreed to all <a href=tos.html>Terms of Service</a>
<br>
<input type="submit" name="login" value="Log In"> Or, <input type="submit" name="reg" value="Register">
</form>