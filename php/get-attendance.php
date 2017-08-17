<?php

// --------------------------------------------------------------------------------------------
// Store input parameters 
// --------------------------------------------------------------------------------------------

$name = $_GET['name'];


// --------------------------------------------------------------------------------------------
// Connect to database 
// --------------------------------------------------------------------------------------------

// Set up connection variables
$hostname="localhost";
$username="acesan7_max";
$password="dbpw2669";
$dbname="acesan7_db";

// Connecting to database
$conn = new mysqli($hostname, $username, $password, $dbname);
if ($conn->connect_errno)
	echo "Failed to connect to database with error number " . $conn->connect_errno . " (" . $conn->connect_error . ")";


// --------------------------------------------------------------------------------------------
// Get UIN of user
// --------------------------------------------------------------------------------------------

// Split name into first and last names
$names = explode(" ", $name);
$first_name = $names[0];
$last_name = $names[1];

if (!($stmt = $conn->prepare("SELECT uin FROM members WHERE first_name = ? AND last_name = ?")))
	echo "UIN statement preparation failed with error number " . $conn->errno . " (" . $conn->error . ")";

if (!$stmt->bind_param("ss", $first_name, $last_name))
	echo "UIN parameter binding failed with error number " . $stmt->errno . " (" . $stmt->error . ")";

if (!$stmt->execute())
	echo "UIN execute failed with error number " . $stmt->errno . " (" . $stmt->error . ")";

if (!$stmt->bind_result($result))
	echo "UIN result binding failed with error number " . $stmt->errno . " (" . $stmt->error . ")";
	
// Storing result into $uin
while ($stmt->fetch())
	$uin = $result;


// --------------------------------------------------------------------------------------------
// Get eventids and points of the user's completed events
// --------------------------------------------------------------------------------------------

if (!($stmt = $conn->prepare("SELECT attendance FROM attendance WHERE uin = ?")))
	echo "Select attendance preparation failed with error number " . $conn->errno . " (" . $conn->error . ")";

if (!$stmt->bind_param("s", $uin))
	echo "Select attendance parameter binding failed with error number " . $stmt->errno . " (" . $stmt->error . ")";

if (!$stmt->execute())
	echo "Select attendance execute failed with error number " . $stmt->errno . " (" . $stmt->error . ")";

if (!$stmt->bind_result($result))
	echo "Select attendance result binding failed with error number " . $stmt->errno . " (" . $stmt->error . ")";

$attendance = 0;
while ($stmt->fetch()) {
	$attendance = $result;
}


// --------------------------------------------------------------------------------------------
// Return attendance and close connections
// --------------------------------------------------------------------------------------------

echo $attendance;

$stmt->close();
$conn->close();
?>