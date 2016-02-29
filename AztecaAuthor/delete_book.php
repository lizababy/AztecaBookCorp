<?php
// This page is for deleting a record of book
// This page is accessed through manage_my_books.php 


require ('../includes/config.inc.php');
$page_title = 'Delete book';
include (HEADER);
redirect_ifNotLoggedIn();

// Check for a valid book ID, through GET or POST:
if ( (isset($_GET['b_id'])) && (is_numeric($_GET['b_id'])) ) { // From manage_my_books.php 
	$b_id = $_GET['b_id'];
} elseif ( (isset($_POST['b_id'])) && (is_numeric($_POST['b_id'])) ) { // Form submission.
	$b_id = $_POST['b_id'];
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
		$q = "DELETE FROM azteca_books WHERE b_id=? LIMIT 1";		
		// Prepare the statement:
                $stmt = mysqli_prepare($dbc, $q);

                // Bind the variables:
               mysqli_stmt_bind_param($stmt, 'i',$b_id);

               // Execute the query:
               mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
               if (mysqli_stmt_affected_rows($stmt) == 1) { // If it ran OK.

			// Print a message:
			echo '<h3 class="text-success">Removed!</h3>';	

		} else { // If the query did not run OK.
			echo "<p class='error'>Can't remove due to a system error.</p>"; // Public message.
			
		}
	
	} else { // No confirmation of deletion.
		echo '<p class="text-success">NOT deleted!</p>';	
	}

} else { // Show the form.
        

	// Retrieve the book's title:
	$q = "SELECT b_title FROM azteca_books WHERE b_id = ?";
	$stmt = mysqli_stmt_init($dbc);
        // Prepare the statement:
        $stmt = mysqli_prepare($dbc, $q);

         // Bind the variables:
        mysqli_stmt_bind_param($stmt, 'i',$b_id);

        // Execute the query:
        mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
        $r = mysqli_stmt_get_result($stmt);
	if (mysqli_num_rows($r) == 1) { // Valid book ID, show the form.

		// Get the book's information:
		$row = mysqli_fetch_array ($r, MYSQLI_NUM);
                
                echo '<h2 class="text-info">Delete a book?</h2>';
		
		// Display the record being deleted:
		echo '<h3 class="text-primary">Title: $row[0]</h3> This will remove complete record of this book.</br>
		Are you sure you want to remove this book?';
		
		// Create the form:
		echo '<form action="delete_book.php" method="post">
	<input type="radio" name="sure" value="Yes" /> Yes 
	<input type="radio" name="sure" value="No" checked="checked" /> No
	<input type="submit" name="submit" value="Submit" />
	<input type="hidden" name="b_id" value="' . $b_id . '" />
	</form>';
	
	} else { // Not a valid book ID.
		echo '<p class="error">This page has been accessed in error.</p>';
	}

} // End of the main submission conditional.

mysqli_close($dbc);
		
include (FOOTER);
