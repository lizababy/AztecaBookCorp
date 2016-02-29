
<?php
// This script allows publishers to process image files.
require ('../includes/config.inc.php');
include (HEADER);
require_once (FUN_DEFS);
require (MYSQL);

redirect_ifNotLoggedIn();
function show_images($orginal_name){
    $original_path = "../../tmpmedia/original/".$orginal_name.".jpg";
    $thumbnail_path =  "../../tmpmedia/thumbnail/".$orginal_name.".thumbnail.jpg";
    $teaser_path =  "../../tmpmedia/teaser/".$orginal_name.".teaser.jpg";
    $cover_path =  "../../tmpmedia/cover/".$orginal_name.".cover.jpg";
    
    if(file_exists($original_path)){
        echo('<div><h3>original Image :</h3><img src='.$original_path.'></div>');
    }
    if(file_exists($thumbnail_path)){
        echo('<div><h3>Thumbnail Image :</h3><img src='.$thumbnail_path.'></div>');
    }
    if(file_exists($teaser_path)){
        echo('<div><h3>Teaser Image :</h3><img src='.$teaser_path.'></div>');
    }
    if(file_exists($cover_path)){
        echo('<div><h3>Cover Image :</h3><img src='.$cover_path.'></div>');
    }
}

if(isset($_GET['b_id'])){
    $orginal_name = $_GET['b_id'];
}else{
    $orginal_name = "";
}

echo '<h1>Process Images of book</h1>
<div class ="btn-group">
    <a href="process_image.php?mode=show&b_id='.$orginal_name.'"><button type="button" class ="btn btn-primary">Show Images</button></a>
    <a href="process_image.php?mode=upload&b_id='.$orginal_name.'"><button type="button" class ="btn btn-primary">Upload new</button></a>
    <a href="process_image.php?mode=thumb&b_id='.$orginal_name.'"><button type="button" class ="btn btn-primary">Create Thumbnail</button></a>
    <a href="process_image.php?mode=teaser&b_id='.$orginal_name.'"><button type="button" class ="btn btn-primary">Create teaser</button></a>
    <a href="process_image.php?mode=cover&b_id='.$orginal_name.'"><button type="button" class ="btn btn-primary">Create book Cover</button></a>
</div>';
    
if(isset($_GET['mode'])&& $orginal_name){
    $b_title = get_b_detail($dbc,"b_title",$orginal_name);
    $b_author = get_b_detail($dbc,"author_name",$orginal_name);
    $b_pub = get_b_detail($dbc,"pub_name",$orginal_name);
    $original_path = "../../tmpmedia/original/".$orginal_name.".jpg";

    if(file_exists($original_path)){
         $file_name = $orginal_name;
    }else{
        $file_name = "";
    }
    echo("<hr><div>");
    switch ($_GET['mode']) {
        case 'show':
            show_images($orginal_name);
                
            break;
        case 'upload':
            echo('<form action="../AztecaPython/upload_handler.py" method="post" enctype="multipart/form-data">
                <div class="checkbox">
                  <label><input type="checkbox" name="optcheck" value="1">Read GPS EXIF Data if any</label>
                </div>

                <div class="radio">
                  <label><input type="radio" name="optradio" value="1">Apply Embossing</label>
                </div>
                <div class="radio">
                  <label><input type="radio" name="optradio" value="2">Apply Edge Detection</label>
                </div>
                <div class="radio">
                  <label><input type="radio" name="optradio" value="3">Apply Blur</label>
                </div>
                <div class="radio">
                  <label><input type="radio" name="optradio" value="4">None</label>
                </div>
                <div class="btn-group inline pull-left">

                        <div class="btn btn-sm"><input type="file" name="fileToUpload" id="fileToUpload"></div>
                        <div class="btn btn-md"><input type="submit" class="btn btn-primary" value="Upload Image" name="submit"></div>'.
                    '<input type="hidden" name="file_name" value="'. $file_name .'"/></form></div>');

            break;
        case 'thumb':
            echo('<form action="../AztecaPython/thumb_handler.py" method="post">' .
            '<b>Create Thumbnail of uploaded image:</b></br><input type="submit" value="create/show Thumbnail"><hr>' .
            '<input type="hidden" name="fileToThumb" value="'. $file_name .'"/></form>');

            break;
        case 'teaser':
            echo('<form action="../AztecaPython/teaser_handler.py" method="post">' .
            '<b>Enter line 1(eg: a quote on the top): </b><div><TextArea name="teaser_text1" cols = "40" rows = "2">Azteca Book Corp Presents...</TextArea></div>'.
            '<b>Enter line 2 (eg: Title of the book): </b><div><TextArea name="teaser_text2" cols = "40" rows = "2">'.$b_title.'</TextArea></div>'.
            '<b>Enter line 3 (eg: Author of the book): </b><div><TextArea name="teaser_text3" cols = "40" rows = "2">'.$b_author.'</TextArea></div>'.
            '<b>Add Watermarking text(eg: copyright): </b><div><TextArea name="teaser_text_c" cols = "40" rows = "2">ABC Corp</TextArea></div>'.
            '</br><input type="submit" value = "create/show Teaser"><hr>' .
            '<input type="hidden" name="fileToTeaser" value="'. $file_name .'"/></form>');

            break;
        case 'cover':
             echo('<form action="../AztecaPython/cover_handler.py" method="post">' .
            '<b>Enter text on front line1(eg: Title of the book): </b><div><TextArea name="cover_text_f1" cols = "40" rows = "2">'.$b_title.'</TextArea></div>'.
            '<b>Enter text on front line2(eg: Author of the book): </b><div><TextArea name="cover_text_f2" cols = "40" rows = "2">'.$b_author.'</TextArea></div>'.
            '<b>Enter line on spine (eg: Title of the book): </b><div><TextArea name="cover_text_s" cols = "40" rows = "2">'.$b_title.'</TextArea></div>'.
            '<b>Enter line on back (eg: publisher name): </b><div><TextArea name="cover_text_b" cols = "40" rows = "2">'.$b_pub.'</TextArea></div>'.
            '</br><input type="submit" value = "create/show Book cover"><hr>' .
            '<input type="hidden" name="fileToCover" value="'. $file_name .'"/></form>');

            break;

        default:
             
            break;
    }
    echo("</div>");
}


include (FOOTER);