<?php

require ('../includes/config.inc.php');
include (HEADER);;
require (MYSQL);
require_once (FUN_DEFS);

$page_title = 'View my cart';

redirect_ifNotLoggedIn();

$user_id = $_SESSION['user_id'];

if ( isset( $_GET['b_id'] ) ){
    
    $b_id= $_GET['b_id'] ;


    $total_price = get_b_detail($dbc, 'b_price', $b_id);
   // Does the cart already contain one of that book id                   
    $q = "SELECT order_id, b_qty, total_price FROM azteca_orders WHERE (b_id IN (?))  AND (user_id IN (?))" ;
    $stmt = mysqli_stmt_init($dbc);
    // Prepare the statement:
    mysqli_stmt_prepare($stmt, $q);

    // Bind the variables:
    mysqli_stmt_bind_param($stmt, 'ii',$b_id,$user_id);

    // Execute the query:
    mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
    $r = mysqli_stmt_get_result($stmt);
    
   if (mysqli_affected_rows($dbc) == 1  )
   {
       $row = mysqli_fetch_array( $r,MYSQLI_ASSOC);
       $b_qty = ++$row['b_qty'];
       $order_id = $row['order_id'];
       $total_price *=$b_qty;

        // Add another one of those books
        // Make the query:
       $q = "UPDATE azteca_orders SET b_qty=?,total_price=? WHERE order_id=? LIMIT 1";
       // Prepare the statement:
        $stmt = mysqli_prepare($dbc, $q);

        // Bind the variables:
        mysqli_stmt_bind_param($stmt, 'idi',$b_qty,$total_price,$order_id);

        // Execute the query:
        mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
        if (mysqli_stmt_affected_rows($stmt) == 1) { // If it ran OK.
                // Print a message:
		
                echo '<h3 class="text-success">Another one of those books has been added to your cart</h3>';
   	
        } else { // If it did not run OK.
                echo '<p class="error">The book could not be added due to a system error. We apologize for any inconvenience.</p>'; // Public message
        }		
    }else{
            
        $b_qty =1;
            // Add a different book
        $q = "INSERT INTO azteca_orders (b_qty, b_id, user_id,total_price, order_date) VALUES "
                    . "('$b_qty','$b_id', '$user_id','$total_price',NOW() )";
            // Execute the query:
        $r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));

        if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
            
            echo '<h3 class="text-success">A book has been added to your cart</h3>' ;
            
        } else { // If it did not run OK.
                    // Public message:
                echo '<h1 class="error">System Error</h1>
                      <p class="error" >You could not add new book due to a system error. We apologize for any inconvenience.</p>'; 

        } // End of if ($r) IF.
        
        
    }
}
                   
   
   // Table header:
    echo '</br><table class="table table-bordered">
        <thead>
            <tr bgcolor = "#E0CCE0">
                  <th><b>#</b></th>
                  <th><b>Order Id</b></th>
                  <th><b>Book Title</b></th>
                  <th><b>Order Date</b></th>
                  <th><b>Quantity</b></th>
                  <th><b>Book Price</b></th>
                  <th><b>Sub Total</b></th>
            </tr>    
        </thead>';
            
   
    // Fetch and print all the records....
    $bg = '#FFFAFA'; 
    $si_no = 0;
    
    $sum = 0;
    $q = "SELECT * FROM azteca_orders WHERE user_id = ?" ;
    $stmt = mysqli_stmt_init($dbc);
    // Prepare the statement:
    mysqli_stmt_prepare($stmt, $q);

    // Bind the variables:
    mysqli_stmt_bind_param($stmt, 'i',$user_id);

    // Execute the query:
    mysqli_stmt_execute($stmt) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
    $r = mysqli_stmt_get_result($stmt);
    // create the order table
   while ($row = mysqli_fetch_array ($r, MYSQLI_ASSOC))
   {    
       $bg = ($bg=='#FFFAFA' ? '#FFFFFF' : '#FFFAFA'); 
        $sum += $row['total_price'];
        $b_title = get_b_detail($dbc, 'b_title', $row['b_id']);
        $b_price = get_b_detail($dbc, 'b_price', $row['b_id']);
        
        // Display the table$bg = ($bg=='#FFFAFA' ? '#FFFFFF' : '#FFFAFA'); 
            echo '<tbody>
                <tr bgcolor="' . $bg . '">
            <td>' . ++$si_no . '</td>
            <td>'. $row['order_id'] . '</td>
            <td>'. $b_title . '</td>
            <td>' . $row['order_date'] . '</td>
            <td>' . $row['b_qty'] . '</td>
            <td>at ' . $b_price . ' each </td> 
            <td>'.number_format($row['total_price'], 2). '</td></tr>';
  
   }
   // Display the total
   echo ' <tr><td colspan="7" style="text-align:right">

   Total = '.number_format($sum,2).'</td> </tr>
            </tbody>
</table>';
        
     /* free result */
    mysqli_stmt_free_result($stmt);

    /* close statement */
    mysqli_stmt_close($stmt);
   
mysqli_close($dbc);

// Insert a link to continue
echo '<p><a href="order_book.php">Continue Shopping</a>';


include (FOOTER);

