<?php

// --------------------------------------------------------------------------------------------
// Store input parameters 
// --------------------------------------------------------------------------------------------

$name = $_GET['name'];


// --------------------------------------------------------------------------------------------
// Connect to Database
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
	echo "select UIN preparation failed with error number " . $conn->errno . " (" . $conn->error . ")";

if (!$stmt->bind_param("ss", $first_name, $last_name))
	echo "Select UIN parameter binding failed with error number " . $stmt->errno . " (" . $stmt->error . ")";

if (!$stmt->execute())
	echo "Select UIN execute failed with error number " . $stmt->errno . " (" . $stmt->error . ")";

if (!$stmt->bind_result($result))
	echo "Select UIN result binding failed with error number " . $stmt->errno . " (" . $stmt->error . ")";
	
// Storing result into $uin
while ($stmt->fetch())
	$uin = $result;


// --------------------------------------------------------------------------------------------
// Retrieve shifts the user has signed up for 
// --------------------------------------------------------------------------------------------

if (!($stmt = $conn->prepare("SELECT shiftid FROM sign_ups WHERE uin = ?")))
	echo "Select shifts preparation failed with error number " . $conn->errno . " (" . $conn->error . ")";

if (!$stmt->bind_param("s", $uin))
	echo "Select shifts parameter binding failed with error number " . $stmt->errno . " (" . $stmt->error . ")";

if (!$stmt->execute())
	echo "Select shifts execute failed with error number " . $stmt->errno . " (" . $stmt->error . ")";

if (!$stmt->bind_result($shiftid))
	echo "Select shifts result binding failed with error number " . $stmt->errno . " (" . $stmt->error . ")";

// Storing shifts in array
$i = 0;
while ($stmt->fetch()) {
	$array[$i] = $shiftid;
	$i++;
}


// --------------------------------------------------------------------------------------------
// Retreieve event information 
// --------------------------------------------------------------------------------------------

if (!($stmt = $conn->prepare("SELECT * FROM events, shifts WHERE shifts.shiftid = ? AND shifts.eventid = events.id")))
	echo "Select event info preparation to get events failed with error number " . $conn->errno . " (" . $conn->error . ")";

foreach ($array as $value) {
	if (!$stmt->bind_param("s", $value))
		echo "Select event info parameter binding failed with error number " . $stmt->errno . " (" . $stmt->error . ")";

	if (!$stmt->execute())
		echo "Select event info execute failed with error number " . $stmt->errno . " (" . $stmt->error . ")";

	$result = $stmt->get_result();
	
	// Additional query to get the name of the officer for each event
	if (!($name_stmt = $conn->prepare("SELECT first_name, last_name FROM members WHERE uin = ?")))
		echo "Select officer name preparation failed with error number " . $conn->errno . " (" . $conn->error . ")";

	while ($row = $result->fetch_assoc()) {

		if (!$name_stmt->bind_param("s", $row['uin']))
			echo "Select officer name parameter binding failed with error number " . $stmt->errno . " (" . $stmt->error . ")";

		if (!$name_stmt->execute())
			echo "Select officer name execute failed with error number " . $stmt->errno . " (" . $stmt->error . ")";

		if (!$name_stmt->bind_result($first_name, $last_name))
			echo "Select officer name result binding failed with error number " . $stmt->errno . " (" . $stmt->error . ")";

		// Stitching the officer's name together
		while ($name_stmt->fetch())
			$officer_name = $first_name . " " . $last_name;

		// Constructing 2 dimensional array to store all the events and their info
		$events[$row['name']] = array(
				"id" => $row['id'],
				"officer_name" => $officer_name,
				"uin" => $uin,		
				"type" => $row['type'],
				"points" => $row['points'],			
				"date" => $row['date'],
				"freeze" => $row['freeze'],
			);
	}
}


// --------------------------------------------------------------------------------------------
// JSON encode array, return it and close all connections
// --------------------------------------------------------------------------------------------

$json = json_encode($events);
echo $json;

$stmt->close();
$name_stmt->close();
$conn->close();
?>