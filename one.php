<?php 
header("content-type:text/html;charset=utf-8");
for ($i=1; $i<=1; $i++) { 
$title='#<a class="tit" href="(.*)" target="_blank">(.*)<span class="jian">荐</span></a>#';
$disge='/<div class="con">(.*?)<\/div>/s';
$pl='#<span>评论&nbsp;(.*)</span>#';
$autor='#<a href="(.*)" class="name fl" target="_blank">(.*)</a>#';
$url="http://blog.51cto.com/artcommend/60/p$i";
$curl=curlR($url);
$pdo=pdo();
preg_match_all($title,$curl,$title);
preg_match_all($disge,$curl,$disge);
preg_match_all($pl,$curl,$pl);
preg_match_all($autor,$curl, $autor);
echo "<pre/>";
$arr=array();
foreach ($title[2] as $key => $value) {
	$arr[$key]['title']=$value;
}
foreach ($disge[1] as $key => $value) {
	$arr[$key]['content']=$value;
}
foreach ($pl[1] as $key => $value) {
	$arr[$key]['pi']=$value;
} 
foreach ($autor[2] as $key => $value) {
	$arr[$key]['autor']=$value;
} 
// var_dump($arr);die;
foreach ($arr as $key => $val) {
	$title=$val['title'];
	$pi=$val['pi'];
	$autor=$val['autor'];
	$disge=$val['content'];
	var_dump($disge);
	// $sql="insert into curlt values('null','$title','$disge','$autor','$pi')";
	// $pdo->exec($sql);
}
}

function curlR($url){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$html=curl_exec($ch);
curl_close($ch);
return $html;
}
function pdo(){
$dsn = 'mysql:dbname=week4;host=127.0.0.1';
$user = 'root';
$password = 'root';
try {
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
return $dbh;
}
 ?>
