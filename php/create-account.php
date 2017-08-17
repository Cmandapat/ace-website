<?php
session_start();

require("connect.php");
require("check-inputs.php");

checkNull($_POST);

// Setting up form variables
$uin = $_POST['uin'];
$name = $_POST['name'];
$major = $_POST['major'];
$family = $_POST['family'];
$position = $_POST['position'];
$phone = $_POST['phone'];
$email = $_POST['email'];

//password stuff

if (!($stmt = $conn->prepare("INSERT INTO nmembers (uin, name, major, family, position, phone, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")));
	echo "Insert statement preparation failed with error number " . $conn->errno . " (" . $conn->error . ")";

if (!$stmt->bind_param("ssssssss", $first_name, $last_name))
	echo "Insert parameter binding failed with error number " . $stmt->errno . " (" . $stmt->error . ")";

if (!$stmt->execute())
	echo "Insert execute failed with error number " . $stmt->errno . " (" . $stmt->error . ")";

// log user in

// return values

$stmt->close();
$conn->close();
