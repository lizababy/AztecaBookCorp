<?php
//This Page is welcome page for admin
require ('../includes/config.inc.php');
include (HEADER);
?>
    <div class="jumbotron">
        
        <?php 
            
  			redirect_ifNotLoggedIn();
            echo '<h2><b>Welcome To Administrators Hub';
            if (isset($_SESSION['first_name'])) {
                echo ", {$_SESSION['first_name']}";
            }
            echo '!</b></h2>';
        ?>        
    </div>
<?php
echo '<h3>Publisher Benefits:</h3><p class="text-info"> You are the super User. You can get all user details registered to 
the site. You have the privillege to edit their roles and unsubscribing them. 
You can view all books and edit privilleges. You can add categories of books</br>
You can also view all orders of books</p>';


include(FOOTER);