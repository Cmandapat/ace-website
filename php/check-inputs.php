<?php
// Summary: Checks an array of any size for null values. Used to check for null inputs passed from client-side forms
// Input: Array
// Output: Will terminate program and identify null values if there are any
// Creator: Max Mckinley
function checkNull ($array) {
	$nullKeys = array_keys($array, NULL);
	$missing = "";

	$i = 0;
	if ($nullKeys) {
		foreach($nullKeys as $key) {
			if ($i == 0)
				$missing .= $key;
			else
				$missing .= ", " . $key;

			$i++;
	 	}
		die("Missing " . $missing . ".  Please resubmit");
	}
}
