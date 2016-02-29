<?php
/* This script:
 * - define constants and settings
 * - dictates how errors are handled
 * - defines useful functions
 */
 
// Created by Liza Baby as a fulfillment of CS547 assignment 1,
// creating azteca book publishing company's website application  


// ********************************** //
// ************ SETTINGS ************ //

// Flag variable for site status:
define('LIVE', FALSE);

// Admin contact address:
define('EMAIL', 'lizababy88@gmail.com');

// Site URL (base for all redirections):
define ('BASE_URL', 'http://www.ABCorp.com/');

// Location of the MySQL connection script:
define ('MYSQL', '../../mysqli_connect.php');

// Adjust the time zone for PHP 5.1 and greater:
date_default_timezone_set ('US/Pacific');

// ************ SETTINGS ************ //
// ********************************** //
// 
// ************ USEFUL FUNCTIONS/ INCLUDES FILES************ //
// ************************************************** //


//Location of header file
define ('HEADER', '../includes/header.html');

//Location of main footer file
define ('FOOTER', '../includes/footer.html');


//Location of common useful function definition script file
define ('FUN_DEFS', '../includes/common_functions.inc.php');


// If no session variable exists, redirect the user:
function redirect_ifNotLoggedIn(){
	if (!isset($_SESSION['user_id'])&& !isset($_SESSION['user_level'])) {
		//ob_end_clean(); // Delete the buffer.
	 	redirect_user();   
	
	}  
}
/* This function determines an absolute URL and redirects the user there.
 * The function takes one argument: the page to be redirected to.
 * The argument defaults to index.php.
 */
function redirect_user ($page = '../Azteca/index.php') {

	// Start defining the URL...
	// URL is http:// plus the host name plus the current directory:
	$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname(htmlspecialchars($_SERVER["PHP_SELF"]));
	
	// Remove any trailing slashes:
	$url = rtrim($url, '/\\');
	
	// Add the page:
	$url .= '/' . $page;
	
	// Redirect the user:
	header("Location: $url");
	exit(); // Quit the script.

} // End of redirect_user() function.


// ****************************************** //
// ************ ERROR MANAGEMENT ************ //

// Create the error handler:
function my_error_handler ($e_number, $e_message, $e_file, $e_line, $e_vars) {

	// Build the error message:
	$message = "An error occurred in script '$e_file' on line $e_line: $e_message\n";
	
	// Add the date and time:
	$message .= "Date/Time: " . date('n-j-Y H:i:s') . "\n";
	
	if (!LIVE) { // Development (print the error).

		// Show the error message:
		echo '<div class="error">' . nl2br($message);
	
		// Add the variables and a backtrace:
		echo '<pre>' . print_r ($e_vars, 1) . "\n";
		debug_print_backtrace();
		echo '</pre></div>';
		
	} else { // Don't show the error:

		// Send an email to the admin:
		$body = $message . "\n" . print_r ($e_vars, 1);
		mail(EMAIL, 'Site Error!', $body, 'From: email@example.com');
	
		// Only print an error message if the error isn't a notice:
		if ($e_number != E_NOTICE) {
			echo '<div class="error">A system error occurred. We apologize for the inconvenience.</div><br />';
		}
	} // End of !LIVE IF.

} // End of my_error_handler() definition.

// Use my error handler:
set_error_handler ('my_error_handler');

// ************ ERROR MANAGEMENT ************ //
// ****************************************** //