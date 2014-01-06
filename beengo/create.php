<?php

require_once('../config/config.php');
require_once('classes/ManageDB.php');
require_once('classes/RandUnqStr.php');
require_once('classes/CreateEvent.php');

session_start();
// var_dump($address);
$address = new RandUnqStr();
$address = $address->getStr();
// var_dump($address);


$title = $_POST['title'];
$master_name = $_POST['master_name'];
$description = $_POST['description'];
$required_time = $_POST['required_time'];
$pass = $_POST['pass'];
$datetime = $_POST['datetime'];

$createEvent = new CreateEvent();
// var_dump($createEvent);
$_SESSION['event_id'] = $createEvent->insertEvent($title, $master_name, $description, $required_time, $address, $pass, $datetime);
// var_dump($_SESSION['eventId']);

// DB接続解除
$createEvent->close();

header('Location: ' . SITE_URL . 'created.php');