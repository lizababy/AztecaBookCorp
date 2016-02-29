<?php
//Main Page for Authors
require ('../includes/config.inc.php');
include (HEADER);
?>
    <div class="jumbotron">
        
        <?php 
            redirect_ifNotLoggedIn();
            echo '<h2><b>Welcome To Authors Hub';
            if (isset($_SESSION['first_name'])) {
                echo ", {$_SESSION['first_name']}";
            }
            echo "!</b></h2>";
        ?>        
    </div>
    <?php
    echo '<h3>Author Benefits:</h3><p class="text-info"> You Can Add New Books in Add new book page.</br>
            You can view the books you are authored off in Manage Authors Books</br>
            You can also view all books both published and unpublished. But cant edit any of them.</p>';


include(FOOTER);