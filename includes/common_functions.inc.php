<?php
//$b_categ_array = array("Educational","Thrillers","Spirituality","Romance",
                        //"Fiction","Biography","Short Stories","Poem","Literature","Drama");
$b_status_array = array(5,10,15,20);

function get_b_categ($dbc){ 

    // Define the query:
    $q = "SELECT b_categ FROM azteca_b_categ";		
    $r = mysqli_query($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc)); // Run the query.
   

    // Fetch and print category list for current book.
    $b_categ_array = array();
    while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)){
        
        $b_categ_array[] = $row['b_categ'];
    }
    mysqli_free_result ($r);
    return $b_categ_array;
}

function display_b_categ($dbc){
    $b_categ_array = get_b_categ($dbc);
    $i = 0;
    
    echo '<h3>Book Categories:</h3>';
    echo '<h4>There are currently '.count($b_categ_array).' numbers of categories </h4>';
    foreach ($b_categ_array as $b_categ) {
        
        echo '<p align="left"><b>'.++$i.'. '. $b_categ . '</p>';
    }
}

function status_toString($status){
    switch($status){
        case '5':
            return 'Incomplete! Editing in progress!';
            break;
        case '10':
            return 'Complete! Request/Wish to publish!';
            break;
        case '15':
            return 'Publish in progress! Accepted and wait to get published!';
            break;
        case '20':
            return 'Published! Ready to buy!';
            break;
        default:
            return 'status unknown';
            break;
    }
}
function get_user_profile($dbc,$id){
    // Define the query:
    $q = "SELECT first_name,last_name, email,user_level, DATE_FORMAT(registration_date, '%M %d, %Y') AS dr FROM azteca_users WHERE user_id =?";		
    
    $stmt = mysqli_stmt_init($dbc);
    // Prepare the statement:
    mysqli_stmt_prepare($stmt, $q);

    // Bind the variables:
    mysqli_stmt_bind_param($stmt, 'i',$id);
    // Execute the query:
    mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
    
    $r = mysqli_stmt_get_result($stmt);
    
    $row = mysqli_fetch_array($r, MYSQLI_ASSOC);

    // Fetch and print all the colums for current user.
    if ($row) {    
        echo '<h3>Account Details:</h3>';
        // Table header.
        echo '<table align="left" cellspacing="3" cellpadding="3" width="50%">
              <tr><td align="left"></td><td align="left"></td></tr>
            ';
            $keys = ['First Name:','Last Name:','Email:','Role:','Date Registered:']; 
            $i = 0;
            // Fetch and print all the records:
            foreach ($row as $key=>$value) {           
                if($key == 'user_level'){                
                    echo '<tr><td align="left">' . $keys[$i]. '</td><td align="left">' . role_toString($value) . '</td></tr>';
                }else{
                    echo '<tr><td align="left">' . $keys[$i]. '</td><td align="left">' .$value . '</td></tr>';
                }
                $i++;           

            }
        echo '</table>'; // Close the table.
        
    } // End of if.

    mysqli_free_result ($r);

}
function get_user_full_name($dbc,$id){
	$q = "SELECT CONCAT(first_name,' ',last_name) AS full_name FROM azteca_users WHERE user_id =?";		
    
    $stmt = mysqli_stmt_init($dbc);
    // Prepare the statement:
    mysqli_stmt_prepare($stmt, $q);

    // Bind the variables:
    mysqli_stmt_bind_param($stmt, 'i',$id);
    // Execute the query:
    mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
    
    $r = mysqli_stmt_get_result($stmt);
    
    $row = mysqli_fetch_array($r, MYSQLI_ASSOC); 	
    // Fetch
    if ($row) { 
        $value = $row['full_name'];
    }else{
        echo '<p class="error">System error!We Appologize!</p>';
        include (FOOTER);
        exit();
    }
    /* free result */
    mysqli_stmt_free_result($stmt);

    /* close statement */
    mysqli_stmt_close($stmt);
    return $value;	
  
}
function get_b_orders($dbc,$key,$b_id){
    // Define the query:
	$q = "SELECT "."$key"." FROM azteca_orders WHERE b_id IN (?) ";
	
    
	$stmt = mysqli_stmt_init($dbc);
    // Prepare the statement:
    mysqli_stmt_prepare($stmt, $q);

    // Bind the variables:
    mysqli_stmt_bind_param($stmt, 'i',$b_id);

    // Execute the query:
    mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
    $r = mysqli_stmt_get_result($stmt);
	$qty = 0;
    if($row = mysqli_fetch_array($r)){
	
		$qty += $row[$key];
	}	
     /* free result */
    mysqli_stmt_free_result($stmt);

    /* close statement */
    mysqli_stmt_close($stmt);
   
    return $qty;
}
function get_b_detail($dbc,$key,$b_id){
    // Define the query:
    $q = "SELECT "."$key"." FROM azteca_books WHERE b_id = ? ";	
    $stmt = mysqli_stmt_init($dbc);
    // Prepare the statement:
    mysqli_stmt_prepare($stmt, $q);

    // Bind the variables:
    mysqli_stmt_bind_param($stmt, 'i',$b_id);

    // Execute the query:
    mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
    $r = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($r);

    // Fetch
    if ($row) { 
        $value = $row[0];
    }else{
        echo '<p class="error">System error!We Appologize!</p>';
        include (FOOTER);
        exit();
    }
    /* free result */
    mysqli_stmt_free_result($stmt);

    /* close statement */
    mysqli_stmt_close($stmt);
    return $value;
}

function get_book_details($dbc, $b_id){ 

    // Define the query:
    $q = "SELECT b_title, author_name, b_categ, b_desc, b_status,"
            . "DATE_FORMAT(b_created_on, '%M %d, %Y') AS b_created_date,"
            . " pub_name,"         
            . "DATE_FORMAT(b_pub_on, '%M %d, %Y') AS b_pub_date,b_price,b_ratings"
            . " FROM azteca_books where b_id =? ";		
    $stmt = mysqli_stmt_init($dbc);
    // Prepare the statement:
    $stmt = mysqli_prepare($dbc, $q);

    // Bind the variables:
    mysqli_stmt_bind_param($stmt, 'i',$b_id);

    // Execute the query:
    mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
    $r = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_array($r, MYSQLI_ASSOC);

    // Fetch and print all the colums for current book.
    if ($row) {    
        echo '<h3>Book Details:</h3><pre>';
            $keys = ['Title :','Author :','Category :','Description :','Book Status :',
                'Created on :','Publisher :','Published Date :','Price:','Rating :']; 
            $i = 0;
            // Fetch and print all the records:
            foreach ($row as $key=>$value) {           
                if($key == 'b_status'){                
                    echo '<p align="left"><b>' . $keys[$i]. '</b>   '. status_toString($value) . '</p>';
                }else if($key == 'b_ratings'){                
                     echo '<p align="left"><b>' . $keys[$i]. '</b>    ';     
                    for($i = 0 ; $i < $row['b_ratings']; $i++)
                        echo ' <span class="glyphicon glyphicon-star"></span>';
                    echo  '</p>';   
                }else{
                    echo '<p align="left"><b>' . $keys[$i]. '</b>    ' .$value . '</p>';
                }
                $i++;  
            }
            echo '</pre>';
    }else{ // End of if.{
        echo 'System error!We Appologize!';
        include (FOOTER);
        exit();
    }
    /* free result */
    mysqli_stmt_free_result($stmt);

    /* close statement */
    mysqli_stmt_close($stmt);
    if(basename($_SERVER['PHP_SELF'])=='manage_all_books.php'){
        return $row['b_status'];
    }
        
    
}



function b_sort_function($sort){
    // Determine the sorting order:
    switch ($sort) {
            case 'user_level':
                    $order_by = 'user_level DESC';
                    break;
            case 'full_name':
                    $order_by = 'full_name ASC';
                    break;
            case 'registration_date':
                    $order_by = 'registration_date ASC';
                    break;
                
            case 'b_title':
                    $order_by = 'b_title ASC';
                    break;
            case 'b_categ':
                    $order_by = 'b_categ ASC';
                    break;
            case 'author_name':
                    $order_by = 'author_name ASC';
                    break;
            case 'b_status':
                    $order_by = 'b_status ASC';
                    break;
            case 'b_created_on':
                    $order_by = 'b_created_date ASC';
                    break;
            case 'b_price':
                    $order_by = 'b_price ASC';
                    break;
            case 'pub_name':
                    $order_by = 'pub_name ASC';
                    break;   
            case 'b_pub_on':
                    $order_by = 'b_pub_on ASC';
                    break;
            case 'b_ratings':
                    $order_by = 'b_ratings DESC';
                    break;
            default:'b_categ ASC';                
                    $order_by = 'b_categ ASC';
                    break;
    }
    return $order_by;
}

function get_page_no($dbc,$q,$display){
    
    // Count the number of records:
	$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
	$row = mysqli_fetch_array ($r, MYSQLI_NUM);
	$records = $row[0];
	// Calculate the number of pages...
	if ($records > $display) { // More than 1 page.
		$pages = ceil ($records/$display);
	} else {
		$pages = 1;
	}
    return $pages;    
}

function link_section($pages,$start,$display, $sort,$base_url){
    // Make the links to other pages, if necessary.
    if ($pages > 1) {

            echo '<br/><p>';
            $current_page = ($start/$display) + 1;

            // If it's not the first page, make a Previous button:
            if ($current_page != 1) {
                    echo '<a href=' . $base_url . '?s=' . ($start - $display) . '&p=' . $pages . '&sort=' . $sort . '>Previous</a> ';
            }

            // Make all the numbered pages:
            for ($i = 1; $i <= $pages; $i++) {
                    if ($i != $current_page) {
                            echo '<a href=' . $base_url . '?s=' . (($display * ($i - 1))) . '&p=' . $pages . '&sort=' . $sort . '> '. $i .' </a>';
                    } else {
                            echo $i . ' ';
                    }
            } // End of FOR loop.

            // If it's not the last page, make a Next button:
            if ($current_page != $pages) {
                    echo '<a href=' . $base_url . '?s=' . ($start + $display) . '&p=' . $pages . '&sort=' . $sort . '>Next</a>';
            }

            echo '</p>'; // Close the paragraph.

    } // End of links section.
    
}



function role_toString($role){
    switch($role){
        case '10':
            return 'Member';
            break;
        case '30':
            return 'Author';
            break;
        case '50':
            return 'Publisher';
            break;
        case '100':
            return 'Admin';
            break;
        default:
            return 'status pending';
            break;
    }
}


    
    



