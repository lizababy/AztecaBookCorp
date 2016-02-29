<?php #index.php
// This is the home page for the site.

// Include the configuration file:
require ('../includes/config.inc.php'); 

// Set the page title and include the HTML header:
$page_title = 'Welcome to AztecA Book Corp Web Site!';
include (HEADER);
?>
    <div class="jumbotron">         
        <?php  // Welcome the user (by name if they are logged in):
            echo '<h2><b>Welcome';
            if (isset($_SESSION['first_name'])) {
                echo ", {$_SESSION['first_name']} !</b></h2>";
            }else{
                echo '!</b></h2><br><h3>To order a book signup and login!</h3>';
            }
            
        ?>
        <div id="myCarousel" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
    <li data-target="#myCarousel" data-slide-to="1"></li>
    <li data-target="#myCarousel" data-slide-to="2"></li>
    <li data-target="#myCarousel" data-slide-to="3"></li>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
    <div class="item active">
      <img src="../includes/images/logo_l.png" width = "512" height="512" alt="ABC Corp">
    </div>
    <?php
        $dir = "../../tmpmedia/teaser/";
        $teaser_im_array = scandir($dir);
        foreach($teaser_im_array as $image_file){
            if(strlen($image_file)>10){
                $image = $dir.$image_file;
                echo('<div class="item"><img src='.$image.' width = "512" height="512" alt="Book"></div>');
            }
        }

    ?>
  </div>

  <!-- Left and right controls -->
  <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
    </div>

<?php  
    include (FOOTER);
    