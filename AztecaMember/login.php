<?php 

//// This is the login page for the site.

require ('../includes/config.inc.php'); 

// Include the header:
$page_title = 'Login';
include (HEADER);


// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			   require (MYSQL);
			   $e = mysqli_real_escape_string ($dbc, $_POST['email']);
			   $p = mysqli_real_escape_string ($dbc, $_POST['pass']);
				
				// Query the database:
				$q = "SELECT user_id, first_name, user_level FROM azteca_users WHERE (email=? AND pass= ?)";	
                $stmt = mysqli_stmt_init($dbc);
                // Prepare the statement:
				mysqli_stmt_prepare($stmt, $q);

                // Bind the variables:
                mysqli_stmt_bind_param($stmt, 'ss',$e, $p);

                $p = sha1($p);

                // Execute the query:
                mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
                $r = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($r) == 1) { // A match was made.

                    // Set the session data:
                    $_SESSION = mysqli_fetch_array ($r, MYSQLI_ASSOC); 
                    $_SESSION['last_timestamp'] = time(); //set new timestamp

                    setcookie('last_loggedin', date("F j, Y")." -- ".date("g:i a"),time()+60*60*24,"/");
                    setcookie('last_ip', getRealIpAddr(),time()+60*60*24,"/");

                    
                     /* free result */
                    mysqli_stmt_free_result($stmt);

                    /* close statement */
                    mysqli_stmt_close($stmt);

                    ob_end_clean(); // Delete the buffer.				
                    // Redirect the user to home
                            
                    redirect_user();
                }		
		else { // No match was made.
			echo '<p class="error">Either the email address or password entered do not match those on file</p>';
		}
		
	
	mysqli_close($dbc);		
	

} // End of the main submit conditional.

// Display the form:
?>
<h1>Login</h1>
<p>Your browser must allow cookies in order to log in.</p>
<form action="login.php" method="post">
    <fieldset>
	<p><b>Email Address:</b> <input type="text" data-validation="email" name="email" size="40" maxlength="60" /> </p>
	<p><b>Password: </b><input type="password" name="pass" data-validation="custom" data-validation-regexp="^\w{4,20}$" size="30" maxlength="40" /> 
    <button type="submit" class="btn btn-success">Sign in</button>
    </fieldset>
</form>

<?php include (FOOTER);
?>