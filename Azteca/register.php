<?php
// This is the registration page for the site.
require ('../includes/config.inc.php');

$page_title = 'Registration';

include (HEADER);

// Check for form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			// Need the database connection:
			require (MYSQL);
				
			// Trim all the incoming data:
			$trimmed = array_map('trim', $_POST);
			$fn = mysqli_real_escape_string ($dbc, $trimmed['first_name']);
			$ln = mysqli_real_escape_string ($dbc, $trimmed['last_name']);
			$e = mysqli_real_escape_string ($dbc, $trimmed['email']);
			$p = mysqli_real_escape_string ($dbc, $trimmed['password1']);
			   
			
			// Make sure the email address is available:
			$q = "SELECT user_id FROM azteca_users WHERE email=?";
				// Prepare the statement:
			$stmt = mysqli_prepare($dbc, $q);
				// Bind the variable:
			mysqli_stmt_bind_param($stmt,'s', $e);
			
			// Execute the query:
			mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
			mysqli_stmt_store_result( $stmt );
			
			if (mysqli_stmt_num_rows( $stmt ) == 0) { // Available.
				// add the user in the database...

					// Make the query:
					$q = 'INSERT INTO azteca_users (first_name, last_name, email, pass, registration_date) VALUES (?, ?, ?, ?, NOW())';

					// Prepare the statement:
					$stmt = mysqli_prepare($dbc, $q);

					// Bind the variables:
					mysqli_stmt_bind_param($stmt, 'ssss', $fn, $ln, $e, $p);
				
					$p = sha1($p);
							
					// Execute the query:
					mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));


					if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
							  
						// Print a message:                    
						echo '<h2 class="text-success">Thank you for registering at Azteca Book Corp!</h2>                          
							 <p>You are now registered!</p><p><br /></p>';
								
						// Include the footer and quit the script:
						include (FOOTER); 
						exit();
									   
					} else { // If it did not run OK.
							// Public message:
						echo '<h1>System Error</h1>
							  <p class="error">You could not be registered due to a system error. We apologize for any inconvenience.</p>'; 

					} // End of if ($r) IF.
			} else { // The email address is not available.
						
				echo '<p class="error">That email address has already been registered.</p>';
			}
        
        
mysqli_close($dbc); // Close the database connection.    	

} // End of the main Submit conditional.
?>
<h1>Register</h1>
<form action="register.php" method="post"> 
    
    <p><b>First Name:</b> <input type="text" data-validation="custom" data-validation-regexp ="^[A-Z a-z]{2,20}$" name="first_name" size="30" value="<?php        if (isset($trimmed['first_name'])) {
            echo $trimmed['first_name'];
        }
        ?>" /><small>Use only letters. Must be between 2 and 20 characters long.</small></p></p>
    
    <p><b>Last Name:</b> <input type="text" data-validation="custom" data-validation-regexp ="^[A-Z a-z]{2,40}$" name="last_name" size="30" value="<?php        if (isset($trimmed['last_name'])) {
            echo $trimmed['last_name'];
        }
        ?>" /><small>Use only letters. Must be between 2 and 40 characters long.</small></p></p>
    
    <p><b>Email Address:</b> <input type="text" data-validation="email" name="email" size="40" maxlength="60" value="<?php        if (isset($trimmed['email'])) {
            echo $trimmed['email'];
        }
        ?>"  /> </p>
    
    <p><b>Password: </b><input type="password" name="password1" data-validation="custom" data-validation-regexp="^\w{4,20}$" size="30" maxlength="40" value="<?php        if (isset($trimmed['password1'])) {
            echo $trimmed['password1'];
        }
        ?>" /> <small>Use only letters, numbers, and the underscore. Must be between 4 and 20 characters long.</small></p>
    
    <p><b>Confirm Password:</b> <input type="password" name="password2"  data-validation="confirmation"  data-validation-confirm="password1" size="30" maxlength="30" value="<?php        if (isset($trimmed['password2'])) {
            echo $trimmed['password2'];
        }
        ?>"  /></p>
  
<div><input type="submit" name="submit" value="Register" /></div>
</form>

<?php include (FOOTER); 