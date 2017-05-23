# Image Upload (OOP) - PHP
The first image upload class I've ever made to make your file uploads easier.

# Usage
Make sure to create a new instance of the class. (You can put the object inside a session if you want.)
#### The first parameter is the width for an image (Only needed if you need cropping)
#### The second is the height (same as above)
#### The third is the directory of the files to be put in. Remember to put a slash at the end!
#### The forth is whether to crop the uploaded files or not.

## Functions
##### setdirectory("example/") - Sets the output directory of where the uploaded files should be placed in.
##### upload("name", $_FILES["name"]) - Uploads all the files. Check the example below for the syntax.
##### set_resolution(width<, height) - Sets the output resolution of each uploaded files.
##### should_scale(true, width, height) - Should images be scaled?

# Example
##### Initialize the class in a session.
$_SESSION["imageuploader"] = new imageuploader(600, 480, "upload/", true);


<?php
session_start();
require_once "classes/imageuploader/imageuploader.php";
$_SESSION["imageuploader"] = new imageuploader(600, 480, "upload/");
$picuploader = $_SESSION["imageuploader"];
?>
<!doctype html>
<html>
    <head>
        <title>Upload test</title>
        <link rel="stylesheet" href="css/style.css">

    </head>
    <body>
        <div class="col6 center clearfix">
            <?php
            /* POST HANDLER */
            if (isset($_POST)) {
                if (isset($_FILES["picture"])) {
                    $image = $_FILES["picture"];
                    $picuploader->upload("picture", $_FILES["picture"]);
                    $picuploader->dodebug();
                    $array = $picuploader->get_all_images();
                    foreach ($array as $image) {
                        echo sprintf("<div class=\"center\"><img src=\"upload/%s\"></div>", $image["name"]);
                    }
                }
            }
            ?>

            <form method="post" enctype="multipart/form-data">
                <label>Upload</label>
                <input type="file" name="picture[]">
                <input type="file" name="picture[]">
                <input type="submit" value="Upload">
            </form>
        </div>
    </body>
</html>
