<?php
// This page allows a logged-in user to change their password.
require ('../includes/config.inc.php'); 
$page_title = 'Change Your Password';
include (HEADER);

redirect_ifNotLoggedIn();

// Check for form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				require (MYSQL);// Connect to the db.
						
				
				$p = mysqli_real_escape_string ($dbc, $_POST['password1']);

				// Make the query:
				$q = "UPDATE azteca_users SET pass=? WHERE user_id= ? LIMIT 1";	
				// Prepare the statement:
                $stmt = mysqli_prepare($dbc, $q);

                // Bind the variables:
                mysqli_stmt_bind_param($stmt, 'si',$p,$id);
                $p = sha1($p);
                $id = (int)$_SESSION['user_id'];
                // Execute the query:
                mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
                if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.

					// Send an email, if desired.
					echo '<h3 class="text-success">Your password has been changed.</h3>';
					mysqli_close($dbc); // Close the database connection.
					include (FOOTER); // Include the HTML footer.
					exit();
			
				} else { // If it did not run OK.
				
					echo '<p class="error">Your password was not changed. Make sure your new password is different than the current password. Contact the system administrator if you think an error occurred.</p>'; 

				}
				mysqli_close($dbc); // Close the database connection.

} // End of the main Submit conditional.
?>

<h1>Change Your Password</h1>
<form action="change_password.php" method="post">
     <p><b>Password: </b><input type="password" name="password1" data-validation="custom" data-validation-regexp="^\w{4,20}$" size="30" maxlength="40" value="<?php        if (isset($trimmed['password1'])) {
            echo $trimmed['password1'];
        }
        ?>" /> <small>Use only letters, numbers, and the underscore. Must be between 4 and 20 characters long.</small></p>
    
    <p><b>Confirm Password:</b> <input type="password" name="password2"  data-validation="confirmation"  data-validation-confirm="password1" size="30" maxlength="30" value="<?php        if (isset($trimmed['password2'])) {
            echo $trimmed['password2'];
        }
        ?>"  /></p>    <div><input type="submit" name="submit" value="Change My Password" /></div>
</form>

<?php include (FOOTER); ?>
