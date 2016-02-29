<?php
// This page is for deleting a user record.
// This page is accessed through view_users.php and edit_profile.php to unsubscribe


require ('../includes/config.inc.php');
$page_title = 'Unsubscribe';
include (HEADER);
echo '<h1>Unsubscribe</h1>';
redirect_ifNotLoggedIn();
// Check for a valid user ID, through GET or POST:
if ( (isset($_GET['id'])) && (is_numeric($_GET['id'])) ) { // From view_users.php or edit_profile.php
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

	if ($_POST['sure'] == 'Yes') { // Delete the record.

		// Make the query:
		$q = "DELETE FROM azteca_users WHERE user_id= ? LIMIT 1";		
		// Prepare the statement:
                $stmt = mysqli_prepare($dbc, $q);

                // Bind the variables:
                mysqli_stmt_bind_param($stmt, 'i',$id);

                // Execute the query:
                mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
                if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.

			// Print a message:
			echo '<p class="text-success">Unsubscribed!</p>';	

		} else { // If the query did not run OK.
			echo "<p class='text-danger'>Can't unsubscribe due to a system error.</p>"; // Public message.
			
		}
	
	} else { // No confirmation of deletion.
		echo '<p class="text-success">NOT unsubscribed!</p>';	
	}

} else { // Show the form.

	// Retrieve the user's information:
	$q = "SELECT CONCAT(last_name, ', ', first_name) FROM azteca_users WHERE user_id=?";
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
		
		// Display the record being deleted:
		echo "<h3>Name: $row[0]</h3>
		Are you sure you want to unsubscribe?";
		
		// Create the form:
		echo '<form action="delete_user.php" method="post">
	<input type="radio" name="sure" value="Yes" /> Yes 
	<input type="radio" name="sure" value="No" checked="checked" /> No
	<input type="submit" name="submit" value="Submit" />
	<input type="hidden" name="id" value="' . $id . '" />
	</form>';
	
	} else { // Not a valid user ID.
		echo '<p class="error">This page has been accessed in error.</p>';
	}
        /* free result */
        mysqli_stmt_free_result($stmt);

        /* close statement */
        mysqli_stmt_close($stmt);

} // End of the main submission conditional.
 
mysqli_close($dbc);
		
include (FOOTER);
