<?php

if(_POST)
{
	//Setting up form variables
	$uin = $_POST['uin'];
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$major = $_POST['major'];
	$family = $_POST['family'];
	$position = $_POST['position'];
	$joined = $_POST['joined'];
	$graduation = $_POST['graduation'];
	$email = $_POST['email'];
	$phone_number = $_POST['phone_number'];

	// Data validation
	if(!$uin) { die("UIN was not entered. You can just click back and add it in, then resubmit."); }
	if(!$first_name) { die("First name was not entered. You can just click back and add it in, then resubmit."); }
	if(!$last_name) { die("Last name was not entered. You can just click back and add it in, then resubmit."); }
	if(!$joined) { die("Date joined was not entered. You can just click back and add it in, then resubmit."); }
	if(!$graduation) { die("Graduation date was not entered. You can just click back and add it in, then resubmit."); }
	if(!$email) { die("Email was not entered. You can just click back and add it in, then resubmit."); }
	if(!$phone_number) { die("Phone number was not entered. You can just click back and add it in, then resubmit."); }
	$joined = $joined . '-01';
	$graduation = $graduation . '-01';

	//Setting connection variables
	$hostname="localhost";
	$username="acesan7_max";
	$password="dbpw2669";
	$dbname="acesan7_db";
	$usertable="members";

	//Connect to the database
	$connection = mysql_connect($hostname, $username, $password);
	if (!$connection) { die('Could not connect: ' . mysql_error()); }
	mysql_select_db($dbname, $connection);

	// Check if UIN already exists
	$query = "SELECT * FROM `$usertable`";
	$result = mysql_query($query);

	$exists = FALSE;
	if ($result)
	{
		while ($row = mysql_fetch_array($result))
		{
			if($uin == $row['uin']) { $exists = TRUE; }
		}
	}

	// Enter new information into database
	if($exists)	// Update existing member's data
	{
		$query = "UPDATE `$usertable` SET `first_name`='$first_name', `last_name`='$last_name', `major`='$major', `family`='$family', `position`='$position', `joined`='$joined', `graduation`='$graduation', `email`='$email', `phone_number`='$phone_number' WHERE `uin`='$uin'";
		$result = mysql_query($query);
	}
	else {	// Create entry for new member
		$query = "INSERT INTO `$usertable` (`uin`, `first_name`, `last_name`, `major`, `family`, `position`, `joined`, `graduation`, `email`, `phone_number`) VALUES ('$uin', '$first_name', '$last_name', '$major', '$family', '$position', '$joined', '$graduation', '$email', '$phone_number')";
		$result = mysql_query($query);

		$query = "INSERT INTO `points` (`uin`) VALUES ('$uin')";
		$result = mysql_query($query);
	}

	// Display feedback
	echo "<h2>Member info updated, thank you " . $first_name . ".<h2>";

	mysql_close($connection);	// Close database connection
}
?>
