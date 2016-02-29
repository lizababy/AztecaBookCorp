<?php
require ('../includes/config.inc.php');
include (HEADER);
?>
    <div class="jumbotron">
        
        <?php 
            redirect_ifNotLoggedIn();
            echo '<h2><b>Welcome To Shopping Hub';
            if (isset($_SESSION['first_name'])) {
                echo ", {$_SESSION['first_name']}";
            }
            echo '!</b></h2>';
        ?>        
    </div>
<?php
echo '<h3>Member Benefits:</h3><p class="text-info"> You Can search, Order and manage books.</br>
            You can become author or publisher only if approved by administrators.</p>';



include(FOOTER);