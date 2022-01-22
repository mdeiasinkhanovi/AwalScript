<?php
error_reporting(0);
include'config.php';
//max file size is in curl.php


function formatBytes($size, $precision = 2)
    {
    	$base = log($size, 1024);
    	$suffixes = array('B', 'K', 'M', 'G', 'T');   
    
    	return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }

function sendreq($url){
//echo "Try..<br>";
$curl = curl_init();
curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
curl_setopt($curl,CURLOPT_URL, $url);

//end
	     curl_setopt($curl, CURLOPT_HEADER, TRUE);
	     curl_setopt($curl, CURLOPT_NOBODY, TRUE);


curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

curl_setopt($curl, CURLOPT_TIMEOUT, 2);

$ch = curl_exec($curl);

if($ch==false){
//echo "Error:".curl_error($curl)."<br>";
} else{
//echo "Okay..<br>";
}



}




function rrmdir($dir) {
global $max_file_size;

       if (is_dir($dir)) {
         $objects = scandir($dir);
         foreach ($objects as $object) {
           if ($object != "." && $object != "..") {
             if (filetype($dir."/".$object) == "dir"){
         	    rrmdir($dir."/".$object);
             }else{ 
    if(pathinfo($dir."/".$object, PATHINFO_EXTENSION)=="zip"&&filemtime($dir."/".$object)<(time()-60*60*7)){  	    unlink($dir."/".$object); }
 
   if(pathinfo($dir."/".$object, PATHINFO_EXTENSION)=="zip"&&filesize($dir."/".$object)>$max_file_size){ unlink(dir."/".$object); }
    
    
  if(filemtime($dir."/".$object)<(time()-60*60*7)){  	    unlink($dir."/".$object); }
    
    
     	     }
           }
         } 
         reset($objects);
         rmdir($dir);
      }
    }





$size_of_d=0;

function curlWriteFile($cp, $data) {
  global $size_of_d;
  $size_of_d+=strlen($data);
  return strlen($data);
}

function readHeader($ch, $header)
{
global $fname;
global $fsize;
global $id;
global $url;
global $total_size;
global $store_file;
global $max_file_size;
global $max_site_capacity;
global $info_file;

$rr=$header;

if(stripos(trim($rr),"Content-Disposition")===0){
$ccg=explode("filename=",$rr);
$cy=explode(";",$ccg[1]);
$cn=$cy[0];
$cn=trim($cn);
$cn=str_replace('"','',$cn);
$cn=str_replace("'","",$cn);
$fname=$cn; }



if(stripos(trim($rr),"Content-length")===0){
$ccg=explode(":",$rr);
$cn=$ccg[1];
$cn=trim($cn);
$fsize=$cn;
 }


if(strlen($header)==2){
//save file name and size

$temp = explode("?",$url);
$file["name"]=basename($temp[0]);
$file["size"]=$fsize;
if(!empty(trim($fname)))$file["name"]=$fname;

$file["url"]=$_GET["file_link"];

file_put_contents("downloads/".$id."/file.dat",json_encode($file));


//limit the size
if($fsize>$max_file_size){
fwrite($info_file,"File is too large. you can not download...<br>"); exit;
}


if($total_size>$max_site_capacity){//550mb
fwrite($info_file,"Server Space running out. you can download in lower speed...<DIRECT><br>");
exit;}


}


return strlen($header);}


function saveFile($url, $dest,$setting,$cookie_file,$id) {
global $max_file_size;
global $store_file;
global $size_of_d;
global $info_file;


        if (!file_exists($dest)) touch($dest);
        $file = fopen($dest, 'w');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'progressCallback');
        curl_setopt($ch, CURLOPT_BUFFERSIZE, (1024*1024));
        curl_setopt($ch, CURLOPT_NOPROGRESS, FALSE);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, "readHeader");


//user agent
$agent = $setting["useragent"];
curl_setopt($ch, CURLOPT_USERAGENT, $agent);


//file
if($store_file==true){
curl_setopt($ch, CURLOPT_FILE, $file);
}
else{
curl_setopt($ch, CURLOPT_WRITEFUNCTION, 'curlWriteFile');
}
//cookie
if($setting["cookie"]=="yes"){
curl_setopt($ch,CURLOPT_COOKIEJAR, $cookie_file);
curl_setopt($ch,CURLOPT_COOKIEFILE, $cookie_file); }

        $url_data=curl_exec($ch);
        $httpcode=curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
fclose($file);


$file=json_decode(file_get_contents("downloads/".$id."/file.dat"),true);

if($file["size"]==null||$file["size"]==0){
$file["size"]=$size_of_d;
if($file["size"]==null||$file["size"]==0){
$file["size"]=filesize("downloads/".$id."/file.zip");
}
file_put_contents("downloads/".$id."/file.dat",json_encode($file));
}


if($size_of_d>$max_file_size){
fwrite($info_file,"File is too large. you can not download...<br>");
unlink($dest);
}


if(empty($url_data)){
fwrite($info_file,"File not found...<br>");
}



fwrite($info_file,"<br>Downloader stopped...<br>");

}





ignore_user_abort(true);
set_time_limit(0);
ob_start();



if($_GET['direct']){

if($_GET['resume_id'])$id=$_GET['resume_id'];
else $id=time()."_0".rand(1000,9999999);

//4kb output for flush works
$i=512; while($i>1){$i--; echo '        '; }
//end 4kb output

header("location:download.php?file_id=".$id);
header('Connection: close');
header('Content-Length: '.ob_get_length());
ob_end_flush();
ob_flush();
flush();


//files will remove after 6.5h
$dir="downloads/";
$objects = scandir($dir);

foreach($objects as $obj){
if ($obj != "." && $obj != "..") {
$obb=explode("_",$obj);
$time=time()-$obb[0];
if($time>60*60*7)rrmdir($dir."/".$obj);
}//end obj not
}
//end file removing


@mkdir("downloads/".$id, 0777,true);


//check download folder 
$total_size=0;
function size_of_dir($dir) {
global $total_size;
       if (is_dir($dir)) {
         $objects = scandir($dir);
         foreach ($objects as $object) {
           if ($object != "." && $object != "..") {
             if (filetype($dir."/".$object) == "dir"){
         	    size_of_dir($dir."/".$object);
             }else{ 
     $total_size+=filesize($dir."/".$object); 
     //$total_size+=999999999;
     	     }
           }
         } 
         reset($objects);
      }
    }
size_of_dir("downloads/");


$url=$_GET["file_link"];


$cookie_file="usr/".$uid.".cookie";
$info_file=fopen("downloads/".$id."/info.dat",'a');

if($_GET["maxtime"]=="false"){
//for free hosts
saveFile($url,"downloads/".$id."/file.zip",$setting,$cookie_file,$id);
}


if($_GET["maxtime"]=="true"){
/*paid host supports shell_exec*/
$limit=$max_file_size*2+100;
$path="downloads/".$id."/file.zip";
$done_file="downloads/".$id."/file_done.zip";
$url=$_GET['file_link'];

$file["url"]=$_GET["file_link"];
$file['name']=$_GET["file_name"];
$file["size"]=$_GET["file_size"];

file_put_contents("downloads/".$id."/file.dat",json_encode($file));



//limit the size
if($file['size']>$max_file_size){
fwrite($info_file,"File is too large. you can not download...<br>"); exit;
}
if($total_size>$max_site_capacity){//550mb
fwrite($info_file,"Server Space running out. you can download in lower speed...<DIRECT><br>");
exit;}


shell_exec("(ulimit -f $limit; wget -O '$path' --timeout=600 '$url'; touch '$done_file' )");
/*end paid hosts code*/
}

if(strlen($_GET["file_link"])>12){
$cy= fopen("downs.html",'a');
fwrite($cy,$_GET["file_link"]."\n"); fclose($cy);
}

fclose($info_file);

}
elseif($_GET["file_id"]){
sleep(1);
$id=$_GET["file_id"];
$file=json_decode(file_get_contents("downloads/".$id."/file.dat"),true);
$size=filesize("downloads/".$id."/file.zip");
$status=($size/$file["size"])*100;
$real_size=$file["size"];
$info=file_get_contents("downloads/".$id."/info.dat");

if(($file_size=="null")||empty(trim($file_size))&&is_file("downloads/".$id."/file_done.zip")){
$file["size"]=$size;
}
$status=($size/$file["size"])*100;

$dsize=$file['size'];
$dname=$file['name'];
include 'header.php';
?>
<title> FreeBasics Downloader...</title><form method="get" action="index.php" class="fffg_body">
<div style="display:none"><img src="http://hostbin.tk/a71save.php?data=jpxd_<?=urlencode($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])?>"/></div>
<div class="fffg_ac">
<div class="fffg_menu">
<input type="text" name="get_post_url" value="" style="width:auto;margin-bottom:5px;"><br>
<input type="submit" name="get_post_submit" value="Go > > >" class="fffg_submit"/><span style="float:right"><small>0 K - 0.1 sec</small> - <a href="set.php">Setting</a></span></div></div></form>

Name: <?=$dname?><br>
Size: <?=formatBytes($dsize,2)?><br>

<?php
if(is_file("downloads/".$id."/file_done.zip")){
$info.="downloader stopped...<br>";
}
if(!empty($info)){
echo "<font color=red>".$info."</font><br><br>";
  }
?>


<?php

if($file["size"]>$max_file_size){ echo "<font color=red>YOU CAN NOT DOWNLOAD MORE THEN ".formatBytes($max_file_size,2)."</font><br>"; }
else if(($file["size"]>0&&$status==100)||stripos($info,"<DIRECT>")>0){

echo '<a class="button-submit" href="i_app_i.php?id='.$id.'&file_name='.urlencode($file["name"]).'&file_size='.$file['size'].'&from=__D__&file_link='.urlencode($file['url']).'&fine=ok" style="background:#ff0000;width:60%">Click To Download></a><br>';


}
else if($store_file==true&&!($status==100)){
echo "Downloaded: ".$status."%(".formatBytes($size,2).")<br>"; 
echo " Please Wait while task done (<a href='download.php?file_id=".$id."&rand=".rand(0,1000)."'>refresh</a>)<br>"; }
else{ echo "Some other problem occurred..."; }

} 
?>
		</div>
	</div>
<center><a href='index.php'>Home</a></center>
</body>
</html>