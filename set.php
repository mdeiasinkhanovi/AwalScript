<?php
error_reporting(0);
require 'head.php';

$sets=array(
//"image"=>"Image",
"cookie"=>"Cookies",
"screen"=>"Fit to Screen",
//"source"=>"Source",
"download"=>"Download",
"hide_browse_form"=>"Hide Browse Form",
"hide_free_basics"=>"Hide FreeBasics",
//"js"=>"Remove Javascript"
);

if($_POST["save"]){

$_GET=$_POST;


$set=json_decode(file_get_contents("usr/".$uid.".set"),true);

foreach($sets as $key=>$val){
$set[$key]=$_GET[$key]? "yes":"no";
}

$set['useragent']=trim($_GET["useragent"]);

if(strlen(trim($_GET["pass"]))>1){

$set['pass']=trim($_GET["pass"]);
if(!empty($set["bytoken"])){
file_put_contents("token/".$set['bytoken'].".pass", $set["pass"]);}

}//pass

$sett= json_encode($set);

file_put_contents("usr/".$uid.".set",$sett);
}
require 'head.php';
?><html>
<head><?php include'header.php';?></head>
<body>
<form action="set.php" method="post">
<table border=0>


<tr>
<td>UserAgent : </td>
<td><input type="text" name="useragent" value="<?php echo ($setting['useragent'])?>"></td>
</tr>

<?php 
foreach($sets as $key=>$val){

$it=($setting[$key]=="yes")?"checked":"";

echo '<tr><td>'.$val.' : </td>
<td><input type="checkbox" name="'.$key.'" value="1" '.$it.'></td></tr>';
}
?>

<tr>
<td>PassWord : </td>
<td><input type="text" name="pass" value="<?php echo ($setting['pass'])?>"></td>
</tr>

<tr>
<td>
<input type="submit" class="fffg_submit" name="save" value="Save"> </td></tr>
</table>
</form>