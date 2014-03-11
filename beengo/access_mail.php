<?php

require_once('classes/SendMail.php');

$sendMail = new SendMail;

$sendMail->setTo(MAIL_TO);
$sendMail->setSubject("http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']} にアクセスがありました");
$sendMail->setFrom(MAIL_FROM);
$sendMail->setFromName('sp');

date_default_timezone_set('Asia/Tokyo'); // これがないと、時間系関数を使った時におかしくなる。
$datetime = date('Y/m/d H:i:s');

$body = "{$datetime}\n\n";

$body .= "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}\n\n";
//現在ページをみているユーザーの IP アドレス。
$body .= "IP address :\n{$_SERVER['REMOTE_ADDR']}\n\n";
//現在のページにアクセスしているホスト名。
$body .= "Remote host :\n{$_SERVER['REMOTE_HOST']}\n\n";
//現在のリクエストに User-Agent: ヘッダが もしあればその内容。
$body .= "User-Agent :\n{$_SERVER['HTTP_USER_AGENT']}\n\n";
//現在のページに遷移する前にユーザーエージェントが参照していた ページのアドレス（もしあれば）。
$body .= "HTTP_REFERER :\n{$_SERVER['HTTP_REFERER']}";

$sendMail->setBody($body);

$res = $sendMail->send();
