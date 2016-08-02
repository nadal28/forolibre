<?php

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://www.forocoches.com/foro/login.php?do=login');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'vb_login_username=USERNAMEjdt&cookieuser=1&vb_login_password=PASSWORD&s=&securitytoken=guest&do=login&vb_login_md5password=&vb_login_md5password_utf=');
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');

curl_exec($ch);
curl_close($ch);

?>
