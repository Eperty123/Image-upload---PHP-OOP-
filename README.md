# Image Upload (OOP) - PHP
ImageUploader is an object oriented class library for uploading files in PHP. It integrates WideImage for seamless scaling and cropping solutions.

# Usage
Make sure to create a new instance of the class. (You can put the object inside a session if you want.)
#### The first parameter is the width for an image (Only needed if you need cropping)
#### The second is the height (same as above)
#### The third is the directory of the files to be put in. Remember to put a slash at the end!
#### The forth is whether to crop the uploaded files or not.

## NOTE:
The name of the input type file must be an array (must have "[]" wtihout the quotes). It may not report it!

ImageUploader <b><i>does not</i></b> support multi dimensional arrays just yet. It may do in the near future but for now it doesn't.

See the example below.

## Functions
##### setdirectory("example/") - Sets the output directory of where the uploaded files should be placed in.
##### upload("name", $_FILES["name"]) - Uploads all the files. Check the example below for the syntax.
##### set_resolution(640, 480) - Sets the output resolution of each uploaded files.
##### should_scale(true, width, height) - Should images be scaled?
##### get_all_images() - Return all uploaded images as an array.
##### get_image(3) - Find and return an image based on the number.
##### has_uploaded - Checks if the upload processs is done.

# Requirememts
* PHP 5.6 and up.


# Example
##### Initialize the class in a session.
$_SESSION["imageuploader"] = new imageuploader(600, 480, "upload/", true);


***

<?php
session_start();
require_once "classes/imageuploader/imageuploader.php";
$_SESSION["imageuploader"] = new imageuploader();
$picuploader = $_SESSION["imageuploader"];
$picuploader->set_directory("upload/");
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

                    // Check if all files have been uploaded.
                    if ($picuploader->has_uploaded()) {
                        $array = $picuploader->get_all_images();
                        foreach ($array as $image) {
                            echo sprintf("<div class=\"center\"><img src=\"upload/%s\"></div>", $image["name"]);
                        }
                    } else {
                        echo "Nothing uploaded!";
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


***


# Additionals
This class includes WideImage which is an image manipulation system for PHP. The developer's site can be found her: http://wideimage.sourceforge.net/.

This class library is dedicated to my course and fellow classmates.
