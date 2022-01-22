<?php
function is_blocked($url){

$txt=file_get_contents("pn.txt")."\n".file_get_contents("pn_extra.txt");

$blocked=explode("\n",$txt);

extract(parse_url($url));

$ho=explode(".",$host);

$mhost=strtolower($ho[count($ho)-2].".".$ho[count($ho)-1]);


if(in_array($mhost,$blocked))return true;

return false;
}