<?php
// This page lets the user logout.
require ('../includes/config.inc.php'); 

// Set the page title and include the HTML header:
$page_title = 'Logout';
include ('../includes/header.html');

// If no first_name session variable exists, redirect the user:
redirect_ifNotLoggedIn();

 // Cancel the session: Log out the user.

$_SESSION = array(); // Clear the variables.
session_destroy(); // Destroy the session itself.
setcookie (session_name(), '', time()-3600); // Destroy the cookie.


// Print a customized message:
echo '<h2 class="text-success">Logged Out!</h2>';
header( "refresh:1;url=../Azteca/index.php" );
include (FOOTER);
