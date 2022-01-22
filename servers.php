<?php
error_reporting(0);

//servers
$servers=array(
"freenetbd.cf"=>"A",
"tipsliker.ml"=>"B",
);

$servers_f=array(
"freenetbd.cf"=>225705534873956,
"tipsliker.ml"=>1063730513700875,
);


$servers["localhost:8080"]="xyz";
$servers_f["localhost:8080"]=10000000000;

$server_by_code=array();
foreach($servers as $sck=>$scv){
$server_by_code[$scv]=$sck;
}


$thost=str_replace("www.", "", strtolower($_SERVER['HTTP_HOST']));
$server_id=$servers[$thost];
$server_fid=$servers_f[$thost];

if(empty($server_id)||empty($server_fid)){
echo "Server id undefined...";
exit;
}
/*
usages
if(user server==$server_id)
it can be continue
*/

//thats all
?>