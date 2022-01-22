<?php

function rel2abs($rel, $base) {
  if (empty($rel)) $rel = ".";
if (parse_url($rel, PHP_URL_SCHEME) != "") return $rel;
if(strpos($rel, "//") === 0) return parse_url($base, PHP_URL_SCHEME).":".$rel; //Return if already an absolute URL
  if ($rel[0] == "#" || $rel[0] == "?") return $base.$rel;//Queries and anchors
  extract(parse_url($base)); //Parse base URL and convert to local variables: $scheme, $host, $path
  $path = isset($path) ? preg_replace("#/[^/]*$#", "", $path) : "/"; //Remove non-directory element from path
  if ($rel[0] == "/") $path = ""; //Destroy path if relative url points to root
  $port = isset($port) && $port != 80 ? ":" . $port : "";
  $auth = "";
  if (isset($user)) {
    $auth = $user;
    if (isset($pass)) {
      $auth .= ":" . $pass;
    }
    $auth .= "@";
  }
  $absp = "$auth$host$port$path/$rel"; //Dirty absolute URL
  
  
$absa=explode("?",$absp,2);
  
  
 $abs=$absa[0];
 $abq=$absa[1];

if(!empty($abq))$abq="?".$abq;


  for ($n = 1; $n > 0; $abs = preg_replace(array("#(/\.?/)#", "#/(?!\.\.)[^/]+/\.\./#"), "/", $abs, -1, $n)) {} //Replace '//' or '/./' or '/foo/../' with '/'
  return $scheme . "://" . $abs.$abq; //Absolute URL is ready.
}




function prepare($data,$url,$browse_form,$setting){

define("browse_form",$browse_form);
define("the_url",$url);

$head_data="";
if($setting["screen"]=="yes"){
$head_data.=file_get_contents('meta.php'); }

if($setting["hide_free_basics"]=="yes"){
$head_data.='<style>body style+div[style="border:none;font-size:0;padding:0;height:45px"]{display:none}</style>'; }

$head_data.=file_get_contents('style.php');

define("head_data",$head_data);

//a href    or every href
$data=preg_replace_callback('/<(a|base)([^>]*)(href="|href=\'|href=)([^"\'>]*)/i',function($d){
global $url;

$d[4]=trim($d[4]);
$d[4]=preg_replace('/&amp;/i',"&",$d[4]);

$hash="";
if(stripos("@".$d[4],"#")>0){

$hj=explode("#",$d[4],2);
$d[4]=$hj[0];
if(!empty($hj[1])){ $hash="#".$hj[1]; }

}

$href="index.php?get_post_url=".urlencode(rel2abs($d[4],$url)).$hash;

if(empty($d[4])){ $href="".$hash; }

return '<'.$d[1].$d[2].$d[3].$href;

},$data);




//link href,script src,img src
$data=preg_replace_callback('/<(link|script|img|iframe|data)([^>]*)(href="|href=\'|href=|src="|src=\'|src=)([^"\'> ]*)/i',function($d){
global $url;


$d[4]=trim($d[4]);
$d[4]=preg_replace('/&amp;/i',"&",$d[4]);

$hash="";
if(stripos("@".$d[4],"#")>0){

$hj=explode("#",$d[4],2);
$d[4]=$hj[0];
if(!empty($hj[1])){ $hash="#".$hj[1]; }

}

$href=rel2abs($d[4],$url).$hash;

if(empty($d[4])){ $href="".$hash; }

return '<'.$d[1].$d[2].$d[3].$href;

},$data);




//form method post and get

//for sort
$data=str_replace(array("<form>","<FORM>","<Form>"),"<form action='".$url."'>",$data);

//for long
$data=preg_replace_callback('/<(form|FORM)([^>]*)(action="|action=\'|action=)([^"\'>]*)([^>]*)>/i',function($d){
global $url;


$d[4]=trim($d[4]);
$d[4]=preg_replace('/&amp;/i',"&",$d[4]);

if(stripos("@".$d[4],"#")>0){

$hj=explode("#",$d[4],2);
$d[4]=$hj[0];
if(!empty($hj[1])){ $hash="#".$hj[1]; }

}


$href="index.php".$d[5].">

<input type='hidden' name='get_post_url' value='".rel2abs($d[4],$url)."'>";

if(empty($d[4])){  $href="index.php".$d[5].">

<input type='hidden' name='get_post_url' value='".$url."'>"; }


return '<'.$d[1].$d[2].$d[3].$href;
},$data);

/*not working now
//remove javascript
//if(setting["js"]=="yes"){
$data=preg_replace('/<script\b[^>]*>(.*?)</script>/is',"",$data);
//}
*/




//body head
$data=preg_replace_callback('/<bod([^>]*)>/i',function($d){

$rt= '<bod'.$d[1].'> <includedtheform> '.browse_form;
return $rt; 
},$data);
//remove end body to bypass 000webhost ads
$data=str_replace(array("</body>","</BODY>","</Body>"),"</endofdoc>",$data);

//head head
$data=preg_replace_callback('/<hea([^>]*)>/i',function($d){
return '<hea'.$d[1].'><headisgiven>'.head_data;
},$data);



//if form is not given
if(strpos($data,"<includedtheform>")==false){
$data=browse_form.$data;
}
//if head is not given
if(strpos($data,"<headisgiven>")==false){
$data="<html><head>".head_data."</head><body>".$data;
}








return $data;
}