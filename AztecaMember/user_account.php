<?php
// This page is for viewing membership profile.
// This page is accessed only after login.

require ('../includes/config.inc.php');
$page_title = 'View Profile';
include (HEADER);
require_once (FUN_DEFS);

require (MYSQL);
// If no user_id session variable exists, redirect the user:
// 
redirect_ifNotLoggedIn();

// Check for a valid user ID, through GET :
if ( (isset($_GET['id'])) && (is_numeric($_GET['id'])) ) { // From manage_users.php 
	$id = $_GET['id'];

} else { // No valid ID, kill the script.
	echo '<p class="error">This page has been accessed in error.</p>';
	include (FOOTER); 
	exit();
}

get_user_profile($dbc, $id);
mysqli_close($dbc);


include (FOOTER); 