<?php 
// This page is for editing a user record.
// This page is accessed through view_users.php.

require ('../includes/config.inc.php');
$page_title = 'Edit a User';
include (HEADER);
echo '<h1>Edit My Profile</h1>';


// Check for a valid user ID, through GET or POST:
if ( (isset($_GET['id'])) && (is_numeric($_GET['id'])) ) { // From view_users.php
	$id = $_GET['id'];
} elseif ( (isset($_POST['id'])) && (is_numeric($_POST['id'])) ) { // Form submission.
	$id = $_POST['id'];
} else { // No valid ID, kill the script.
	echo '<p class="error">This page has been accessed in error.</p>';
	include (FOOTER); 
	exit();
}

require (MYSQL);

// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
			 // Trim all the incoming data:
				$trimmed = array_map('trim', $_POST);
				$fn = mysqli_real_escape_string($dbc, $trimmed['first_name']);
				$ln = mysqli_real_escape_string($dbc, $trimmed['last_name']);
				$e = mysqli_real_escape_string($dbc, $trimmed['email']);

			
			
				//  Test for unique email address:
				$q = "SELECT user_id FROM azteca_users WHERE email=? AND user_id != ?";
				$stmt = mysqli_stmt_init($dbc);
                // Prepare the statement:
                mysqli_stmt_prepare($stmt, $q);

                // Bind the variables:
                mysqli_stmt_bind_param($stmt, 'si',$e,$id);

                // Execute the query:
                mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
                $r = mysqli_stmt_get_result($stmt);
				if (mysqli_num_rows($r) == 0) {

					// Make the query:
					$q = "UPDATE azteca_users SET first_name= ?, last_name=?, email=? WHERE user_id=? LIMIT 1";
					
								// Prepare the statement:
								$stmt = mysqli_prepare($dbc, $q);

								// Bind the variables:
								mysqli_stmt_bind_param($stmt, 'sssi',$fn,$ln,$e,$id);

								// Execute the query:
								mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
								

					if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.

						// Print a message:
						echo '<p class="text-success">The user has been edited.</p>';	
						
					} else { // If it did not run OK.
						echo '<p class="error">The user could not be edited due to a system error. We apologize for any inconvenience.</p>'; // Public message
					}
						
				} else { // Already registered.
					echo '<p class="error">The email address has already been registered.</p>';
				}
		
	

} // End of submit conditional.

// Always show the form...

// Retrieve the user's information:
$q = "SELECT first_name, last_name, email FROM azteca_users WHERE user_id=?";	
$stmt = mysqli_stmt_init($dbc);
// Prepare the statement:
mysqli_stmt_prepare($stmt, $q);

// Bind the variables:
mysqli_stmt_bind_param($stmt, 'i',$id);

// Execute the query:
mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
$r = mysqli_stmt_get_result($stmt);
 

if (mysqli_num_rows($r) == 1) { // Valid user ID, show the form.

	// Get the user's information:
	$row = mysqli_fetch_array ($r, MYSQLI_NUM);
	
	// Create the form:
	echo '<form action="edit_profile.php" method="post">
				
                <p><b>First Name:</b> <input type="text" data-validation="custom" data-validation-regexp ="^[A-Z a-z]{2,20}$" name="first_name" size="30" value="' . $row[0] . '" />
				<small>Use only letters. Must be between 2 and 20 characters long.</small></p></p>
                <p><b>Last Name:</b> <input type="text" data-validation="custom" data-validation-regexp ="^[A-Z a-z]{2,40}$" name="last_name" size="30" value="' . $row[1] . '" />
				<small>Use only letters. Must be between 2 and 40 characters long.</small></p></p>
                <p><b>Email Address:</b> <input type="text" data-validation="email" name="email" size="40" maxlength="60" value="' . $row[2] . '"  /> </p>
                <p><input type="submit" name="submit" value="Submit" /></p>
                   <input type="hidden" name="id" value="' . $id . '" />
              </form>';

} else { // Not a valid user ID.
	echo '<p class="error">This page has been accessed in error.</p>';
}
/* free result */
mysqli_stmt_free_result($stmt);

/* close statement */
mysqli_stmt_close($stmt);
mysqli_close($dbc);
		
include (FOOTER);