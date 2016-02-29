<?php 
// This page is for changing user level of a member.
// This page is accessed through view_users.php or my_account.php
// This page can be accessed only by authors,publishers aand admin.

require ('../includes/config.inc.php');
$page_title = 'Change Role';
include (HEADER);
// Need the functions
require_once (FUN_DEFS);
echo '<h1>Change role</h1>';

// If no user_id session variable exists, redirect the user:
redirect_ifNotLoggedIn();

// Check for a valid user ID, through GET :
if ( (isset($_GET['id'])) && (is_numeric($_GET['id'])) ) { // From view_users.or my_account.php
    $id = $_GET['id'];
} elseif ( (isset($_POST['id'])) && (is_numeric($_POST['id'])) ) { // Form submission.
    $id = $_POST['id'];
}else { // No valid ID, kill the script.
    echo '<p class="error">This page has been accessed in error.</p>';
    include (FOOTER); 
    exit();
}

require (MYSQL);

// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
     // Assume invalid value for user_level:
        $ul = FALSE;
        
	// Check for valid submitted user level:
	if (empty($_POST['role'])) {
		echo '<p class="error">You forgot to choose role!</p>';
	} else {
		$ul = mysqli_real_escape_string($dbc, $_POST['role']);
	}
    if($ul){
        // Make the query:
        $q = "UPDATE azteca_users SET user_level=? WHERE user_id=? LIMIT 1";
        // Prepare the statement:
        mysqli_stmt_prepare($dbc, $q);

        // Bind the variables:
        mysqli_stmt_bind_param($stmt, 'ii',$ul,$id);

        // Execute the query:
        mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));

        if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
            // Print a message:
            echo '<p class="text-success">The role has been changed to '.  role_toString($ul).' </p>';	

        } else { // If it did not run OK.
            echo '<p class="error">The role could not be edited due to a system error. We apologize for any inconvenience.</p>'; // Public message
        }	
    }else { // If role test failed.

		echo '<p class="error">Please try again.</p>';	
	
    } // End of IF.

} // End of submit conditional.

// Retrieve the user's current role:
$q = "SELECT user_level,first_name FROM azteca_users WHERE user_id=?";		
$stmt = mysqli_stmt_init($dbc);
// Prepare the statement:
mysqli_stmt_prepare($stmt, $q);

// Bind the variables:
mysqli_stmt_bind_param($stmt, 'i',$id);

// Execute the query:
mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
$r = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($r) == 1 && $_SESSION['user_level']==100) { // Valid user ID, show the form.	

    // Get the user's role:
    $row = mysqli_fetch_array ($r, MYSQLI_ASSOC);
    $role = $row['user_level'];
        
    echo '</br><p><b>First Name: '.$row['first_name'].'</b></p>';
    echo '<p><b>Current Role: '.role_toString($role).'</b></p>';
        // Create the form:
    echo '<form action="change_role.php" method="post">
            <p><b><label for ="role">
                New Role:
                    <input type = "radio" name ="role" value =10';
                        if ($role == 10){echo ' checked="checked"';} echo '>Member';  
                echo '<input type = "radio" name ="role" value =30';
                        if ($role == 30){echo ' checked="checked"';}echo '>Author'; 
                echo '<input type ="radio" name = "role" value=50';
                        if ($role == 50){echo ' checked="checked"';}echo '>Publisher';
                echo '<input type ="radio" name = "role" value=100';
                        if ($role == 100){echo ' checked="checked"';}echo '>Admin';
    echo  '</label></b>    </p>
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
