<?php
require ('../includes/config.inc.php');
include (HEADER);
?>
    <div class="jumbotron">
        
        <?php 
            redirect_ifNotLoggedIn();
            echo '<h2><b>Welcome To Publishers Hub';
            if (isset($_SESSION['first_name'])) {
                echo ", {$_SESSION['first_name']}";
            }
            echo '!</b></h2>';
        ?>        
    </div>
<?php
echo '<h3>Publisher Benefits:</h3><p class="text-info"> You Can Publish completed Books
by first moving books to publishers list in books to publish page. Once added to publisher list you have all
the rights to publish,set price,ratings, edit contents.</br>
You can also view all books both published and unpublished. But cant edit basic details of other publishers books.</p>';



include(FOOTER);