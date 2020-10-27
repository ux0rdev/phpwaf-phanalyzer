<?php
ob_start();
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
function reel_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
        $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //Proxy den bağlanıyorsa gerçek IP yi alır.

    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
   elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) //Proxy den bağlanıyorsa gerçek IP yi alır.

    {
        $ip = $_SERVER['HTTP_X_FORWARDED'];
    }

   elseif (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) //Proxy den bağlanıyorsa gerçek IP yi alır.

    {
        $ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    }
	   elseif (!empty($_SERVER['HTTP_FORWARDED'])) //Proxy den bağlanıyorsa gerçek IP yi alır.

    {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    else
    {
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function ErrorMessage($type, $method) {
echo '    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <link rel="stylesheet" media="screen" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600" />
    <link rel="stylesheet" media="screen" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
<link rel="stylesheet" media="screen" href="style.css" />
  <body class="background error-page-wrapper background-color background-image">
    <center>
  <div class="content-container shadow">
  <br>
    <div class="head-line secondary-text-color">
		Illegal Girişim Algılandı
    </div>
	<div class="hr"></div>
    <div class="context primary-text-color">
      Deneme Türü : '.strip_tags($type).' ('.strip_tags($method).')
    </div>
    <div class="hr"></div>
    <div class="context secondary-text-color">
	<p> Method Türü : '.strip_tags($method).'</p>
      <p>URL : <br>'.strip_tags($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']).'</p>
	  <p>User-Agent : <br>'.strip_tags($_SERVER['HTTP_USER_AGENT']).'</p>
	  <p>Tarih : '.date('d.m.Y H:i:s').'</p>
    </div>
    <div class="buttons-container">
      <a class="button" onclick="history.back();" target="_blank"><span class="fa fa-home"></span> Geri Dön</a>
      <a class="button" href="mailto:alicangonullu@yahoo.com" target="_blank"><span class="fa fa-warning"></span> Problem Bildir</a>
    </div>';
}
function IPError($ad) {
if(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2) == "tr") {
echo '<br><div class="context secondary-text-color">
<p>IP Adresiniz '.strip_tags($ad).' Saat Banlandı.</p>
</div>';
} else {
echo '<br><div class="context secondary-text-color">
<p>IP Adress : '.strip_tags($ad).' Banned from Web Server for One Hour.</p>
</div>';
}
}

function LogIslem($ad) {
foreach ($ad as $key => $value) {
$json = "".strip_tags($key)."¿".strip_tags($value)."";
$yasakla = explode('¿',$json);
$json2 = var_dump(json_encode($yasakla));
	}
}

function HeaderIslem() {
$header = apache_request_headers();
foreach ($header as $headers => $value) {
$jsons = ''.strip_tags($headers).'¿?'.strip_tags($value).'';
$yasakla = explode('¿?',$jsons);
$jsonk = var_dump(json_encode($yasakla));
	}
}

function Debug() {
echo '<pre class="container">';
if($_SERVER['REQUEST_METHOD'] == "GET") {
$getlog = LogIslem($_GET);
echo '<br>';
$gethead = HeaderIslem();
} elseif($_SERVER['REQUEST_METHOD'] == "POST")
{
$getpost = LogIslem($_POST);
echo '<br>';
$gethead = HeaderIslem();
} else {
$getpost = LogIslem($_SERVER['REQUEST_METHOD']);
echo '<br>';
$gethead = HeaderIslem();
}
echo '</pre>';
}

function kisalt($metin, $uzunluk){
$metin = substr($metin, 0, $uzunluk)."...";
$metin_son = strrchr($metin, " ");
$metin = str_replace($metin_son," ...", $metin);
return $metin;
}
// IP Engelleme
try {
$ip = "localhost"; //host
$user = "root";  // host id
$password = "19742008";  // password local olduğu için varsayılan şifre
$dbad = "ali_waf"; // db adı

     $db = new PDO("mysql:host=$ip;dbname=$dbad", "$user", "$password");
     $db->query("SET CHARACTER SET 'utf8'");
     $db->query("SET NAMES 'utf8'");

} catch ( PDOException $e ){
     echo '
	 <table>
<center><img src="veri/sql.png" alt="Örnek Resim"/></center>
<center>No MySQL Connection</center>
<center>Bunun Sebebi Bir DDoS Saldırısı Olabilir</center>
<center>Sistem Yöneticinizle Irtibata Geçin</center>
	 </table>';
	 die();
}

try {

$stmt = $db->query("SELECT * FROM waf_ayar ORDER BY ayar_id");
if($stmt->rowCount()) {
while($row = $stmt->fetch()){
session_start();
$ayaraktif = md5(sha1($row["ayar_aktif"]));
$otoban = md5(sha1($row["oto_ban"]));
$ipadres = reel_ip();
$wafdurum = md5(sha1($row["waf_aktif"]));
$debug = md5(sha1($row["debug"]));
if (md5(sha1($row["ayar_aktif"])) == md5(sha1(1))){
header('X-AliWAF: ACTIVE');
} else {
header('X-AliWAF: DEACTIVE');
}
   }
		}
		    } catch(PDOException $e) {
}




if ($wafdurum == md5(sha1(1))){
if ($debug == md5(sha1(1))){
Debug();
} else {
}
if ($ayaraktif == md5(sha1(1))){
$ip = reel_ip();
$stmt = $db->query("SELECT * FROM ip_ban WHERE ip_adresi = ".$db->quote($ip)."");
if($stmt->rowCount()) {
while($row = $stmt->fetch()){
session_start();
$_SESSION['suresi'] = strip_tags($row["ip_suresi"]);
		if ($_SESSION['suresi'] - date('H:i:s') >= 30){

		} else {
ErrorMessage("IP Ban", $ip);
					echo '
		<p align="center">IP Ban Listesindesiniz</p><br>
		<p align="center"> IP Adresin <b>'.strip_tags($ip).'</b>';
		die();
		}
   }
}


$stmt = $db->query('SELECT * FROM guard_watch ORDER BY kural_id');
while($row = $stmt->fetch()){
$parametreler = strtolower($_SERVER['QUERY_STRING']);
$yasaklar=($row['kural_icerik']);
$yasakla=explode('¿¿',$yasaklar);
$sayiver=substr_count($yasaklar,'¿¿');
$i=0;
while ($i<=$sayiver) {
if (strstr($parametreler,$yasakla[$i])) {
ErrorMessage("Rule Injection", $row['kural_adi']);

if ($otoban == md5(sha1(1))){
$bandurum = md5(sha1(1));
$update = $db->prepare("INSERT INTO ip_ban(ip_adresi, ip_suresi, ip_usragent) VALUES (:ipadresi, :ipsuresi, :ipusragent) ");
$update->bindValue(':ipadresi', strip_tags($ip));
$update->bindValue(':ipusragent', strip_tags($_SERVER['HTTP_USER_AGENT']));
$update->bindValue(':ipsuresi', date('H:i:s'));
$update->execute();
if($update){
IPError("1");
die();
}
} else {
die();
}
}
$i++;
}
if (strlen($parametreler)>=90) {
exit;
}
	}

	//Guard Bitti
$method = strip_tags($_SERVER['REQUEST_METHOD']);
$stmt = $db->query("SELECT * FROM method_blok WHERE method_turu = ".$db->quote($method)."");
if($stmt->rowCount()) {
while($row = $stmt->fetch()){
   }
} else {
header($_SERVER["SERVER_PROTOCOL"]." 405 Method Not Allowed", true, 405);
ErrorMessage("Method Injection", $method);
if ($otoban == md5(sha1(1))){
$bandurum = md5(sha1(1));
$update = $db->prepare("INSERT INTO ip_ban(ip_adresi, ip_suresi, ip_usragent) VALUES (:ipadresi, :ipsuresi, :ipusragent) ");
$update->bindValue(':ipadresi', strip_tags($ip));
$update->bindValue(':ipusragent', strip_tags($_SERVER['HTTP_USER_AGENT']));
$update->bindValue(':ipsuresi', date('H:i:s'));
$update->execute();
if($update){
IPError("1");
die();
}
} else {

}

}
} else {
header('X-AliWAF: DEACTIVE');
}
} else {
header('X-AliWAF: DEACTIVE');
}
	//Istek Engellendi

?>