<?php
// This is the page for adding new book categories.
//Page has access only to admin
require ('../includes/config.inc.php');
$page_title = 'add new category';

include (HEADER);

// Need the database connection:
require_once (MYSQL);
    
require_once(FUN_DEFS);

  redirect_ifNotLoggedIn();
// If no user_id session variable exists or user level lesser than author, redirect the user:
if($_SESSION['user_level']!=100){
    ob_end_clean(); // Delete the buffer.
    
    redirect_user();
}

// Check for form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $b_categ = mysqli_real_escape_string ($dbc, trim($_POST['b_categ']));
    if ($b_categ ) { // If everything's OK...
                    		
        // add the book in the database...
        // Make the query:
        $q = "INSERT INTO azteca_b_categ (b_categ) VALUES (?)";
        
        $stmt = mysqli_stmt_init($dbc);
        // Prepare the statement:
        mysqli_stmt_prepare($stmt, $q);

        // Bind the variables:
        mysqli_stmt_bind_param($stmt, 's',$b_categ);

        // Execute the query:
        mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
        
        if (mysqli_stmt_affected_rows($stmt) == 1) { // If it ran OK.
                      
               // Print a message:                    
                echo '<h2 class="text-success">Book Category added successfully!</p>';
		                       
            } else { // If it did not run OK.
                    // Public message:
                echo '<h1 class="error">System Error</h1>
                      <p class="error">You could not add new category due to a system error.The category may be already existing! please check.</p>'; 

            } // End of if ($r) IF.
        
        } else { // If one of the data tests failed.

            echo '<p class="error">Please try again.</p>';	
        }  	

} // End of the main Submit conditional.
?>
<h1>Add a new Book category</h1>
<form action="add_book_category.php" method="POST">
    <p><label>Book Category*:</label><br/>
            <input data-validation="length" data-validation-length ="4-30" type="text" name="b_categ" size="40" maxlength="30"/>
    <button type="submit" class="btn btn-success"> <span class="glyphicon glyphicon-plus"></span> </button></p>
                                        
</form>
<?php 
display_b_categ($dbc);
mysqli_close($dbc); // Close the database connection.   
include (FOOTER); 