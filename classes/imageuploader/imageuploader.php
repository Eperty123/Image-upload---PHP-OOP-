<?php

/**
 * @category   PHP
 * @copyright  Copyright @ 2017 Carlo De Leon (http://carlodeleon.net)
 * @license    GPLv2 License
 * @version    1.1-1
 * @link       https://github.com/Eperty123/Image-upload---PHP-OOP-.
 * @since      Version 1.0
 */


/* WideImage */
require_once "wideimage/WideImage.php";


/* Initialize the class. */

class imageuploader {
    /* PUBLIC VARIABLES. */

    public $inputFile;
    public $ouputDirectory;
    public $OutputDimension;


    /* PRIVATE VARIABLES. */
    private $originalName;
    private $tempName;
    private $inputDimension;
    private $extensionName;
    private $all_files;
    private $post_name;
    private $all_together;
    private $should_crop;
    private $uploaded;

    /**
     * Initialize the class with some kickstart arguments.
     * @param type $width The width of the output image should be. (Only used if cropping is true)
     * @param type $height The height of the output image should be. (Only used if cropping is true)
     * @param type $outputdirectory The output directory to put uploaded files in.
     * @param type $crop Should images be cropped? (Default: false)
     */
    public function __construct($width = "", $height = "", $outputdirectory = "", $crop = false) {

        $this->inputFile;
        $this->OutputDimension;
        $this->originalName = array();
        $this->tempName = array();
        $this->inputDimension = array();
        $this->extensionName = array();
        $this->all_files = array();
        $this->post_name;
        $this->should_crop = $crop;
        $this->OutputDimension = array($width, $height);
        $this->ouputDirectory = $outputdirectory;

        $this->checkdirectory();
    }

    /* ========================================================================================================
     *  ALL FUNDEMENTAL FUNCTIONS.
      /* ========================================================================================================
     */

    /**
     * Upload the current file and get its information.
     */
    public function upload($post_name, $post_picture) {
        /* Only do stuff if the POST is not empty. */
        if (!empty($post_picture)) {
            //$this->inputFile = $_FILES[$post_picture];
            /* Put all the files together in an array
             * if more than 1 file is uploaded.
             */

            $this->checkdirectory();
            $this->post_name = $post_name;
            $this->all_files = $this->remaparray($post_picture);

            /* Remove empty array entries if any. */
            $this->remove_empty_array_entry();


            /* Check if the file array is empty.
             * If not, proceed.
             */
            if (!empty($this->all_files)) {
                if ($this->all_files[0]["size"] > 0) {

                    /* Call other useful functions. */
                    $this->getimageresolution();
                    $this->getoriginalimagename();
                    $this->gettempname();
                    $this->getextensionname();

                    $this->put_all_together();
                }
            }
        } else {
            echo "<p>Failed to upload.</p>";
        }
    }

    /**
     * Debug. Nothing for a consumer user. Please do not call unless you know
     * what you're doing.
     */
    public function dodebug() {
        if (count($this->all_files) == 1) {
            echo "The file uploaded has the following information:";
        } else {
            echo "The files uploaded have the following information:";
        }
        echo "<pre>";
        var_dump($this->all_together);
        echo "</pre>";
    }

    /**
     * Set an output width and height for cropping.
     * @param type $width The width.
     * @param type $height The height.
     */
    public function set_resolution($width, $height) {
        $this->OutputDimension = array($width, $height);
    }

    /**
     * Set an output directory for the uploaded files to be placed in.
     * @param type $directory The folder to place all the uploaded files in.
     */
    public function set_directory($directory) {
        $this->ouputDirectory = $directory;
    }

    /**
     * Should images be scaled?
     * @param type $value true or false.
     * @param type $width The output width of the image.
     * @param type $height The output height of the image.
     */
    public function should_scale($value, $width, $height) {
        if (is_bool($value)) {
            $this->should_crop = $value;
            $this->OutputDimension = array($width, $height);
        } else {
            echo "Wrong value. It needs to be a boolean. (true/false)";
        }
    }

    /**
     * Returns all the uploaded images and all their information.
     * @return type array
     */
    public function get_all_images() {
        return $this->all_together;
    }

    /**
     * Find an image.
     * @param type $image_number The image number.
     * @return type array
     */
    public function get_image($image_number = 0) {
        if (count($this->all_together) == 1) {
            $image_number = 0;
        }
        return $this->all_together[$image_number];
    }

    /**
     * Gets the current state of the upload process.
     * @return type bool
     */
    public function has_uploaded() {
        return $this->uploaded;
    }

    /* ========================================================================================================
     *  ALL THE ESSENTIAL FUNCTIONS.
      /* ========================================================================================================
     */

    /**
     * Check the uploaded image's resolution.
     * @return type array
     */
    private function getimageresolution() {
        if (count($this->all_files) == 1) {
            $this->inputDimension = getimagesize($this->all_files[0]["tmp_name"]);
            return $this->inputDimension;
        } else {
            foreach ($this->all_files as $file) {
                if ($file["tmp_name"] != "") {
                    $this->inputDimension[] = getimagesize($file["tmp_name"]);
                }
            }
        }
    }

    /**
     * Get the uploaded image's name.
     * $return type string
     */
    private function getoriginalimagename() {
        if (count($this->all_files) == 1) {
            $this->originalName = basename($this->all_files[0]["name"]);
            return $this->originalName;
        } else {
            foreach ($this->all_files as $file) {
                $this->originalName[] = $file["name"];
            }
        }
    }

    /**
     * Get the uploaded image's temp name.
     * $return type string
     */
    private function gettempname() {
        if (count($this->all_files) == 1) {
            $this->tempName = $this->all_files[0]["tmp_name"];
            return $this->tempName;
        } else {
            foreach ($this->all_files as $file) {
                $this->tempName[] = $file["tmp_name"];
            }
        }
    }

    /**
     * Get the uploaded image's extension.
     * @return type string
     */
    private function getextensionname() {
        if (count($this->all_files) == 1) {
            $this->extensionName = pathinfo($this->all_files[0]["name"], PATHINFO_EXTENSION);
            return $this->extensionName;
        } else {
            foreach ($this->all_files as $file) {
                if ($file["name"] != "") {
                    $this->extensionName[] = $file["name"];
                }
            }
        }
    }

    /**
     * Remap the $_FILES array into a single dimensional array.
     * Function taken from: http://php.net/manual/en/features.file-upload.multiple.php.
     * @param type $array The $_FILES array.
     * @return type array
     */
    private function remaparray($array) {

        $file_array = array();
        $file_count = count($array['name']);
        $file_keys = array_keys($array);

        if (is_array($file_keys) && is_array($file_count)) {
            for ($i = 0; $i < $file_count; $i++) {
                foreach ($file_keys as $key) {
                    $file_array[$i][$key] = $array[$key][$i];
                }
            }


            return $file_array;
        } else {
            die("<b>Warning:</b> The input type name is not an array. Please make sure it's an array!");
        }
    }

    /**
     * Put everything together.
     */
    private function put_all_together() {
        $res = $this->inputDimension;
        $idx = -1;
        if (count($this->all_files != 0)) {
            if (count($this->all_files) > 1) {
                foreach ($this->all_files as $file) {
                    $idx++;
                    $this->all_together[] = array("name" => $file["name"], "tmp_name" => $file["tmp_name"], "size" => $file["size"], "width" => $res[$idx][0], "height" => $res[$idx][1]);
                    if ($this->should_crop) {
                        $this->WI_crop($file["tmp_name"], $file["name"]);
                    } else {
                        $this->put_to_output_folder($file["tmp_name"], $file["name"]);
                    }
                }
            } else {
                $file = $this->all_files;
                $this->all_together[] = array("name" => $file[0]["name"], "tmp_name" => $file[0]["tmp_name"], "size" => $file[0]["size"], "width" => $res[0], "height" => $res[1]);

                if ($this->should_crop) {
                    $this->WI_crop($file[0]["tmp_name"], $file[0]["name"]);
                } else {
                    $this->put_to_output_folder($file[0]["tmp_name"], $file[0]["name"]);
                }
            }
        } else {
            die("Something went wrong! Please refresh the page and start over.");
        }
    }

    /**
     * Remove empty array entries from the list.
     */
    private function remove_empty_array_entry() {
        $idx = -1;
        $array = array();
        if (is_array($this->all_files)) {
            foreach ($this->all_files as $entry) {
                $idx++;
                if ($entry["name"] == "") {
                    unset($this->all_files[$idx]);
                } else {
                    $array[] = $entry;
                }
            }
            $this->all_files = $array;
        }
    }

    /**
     * Use WideImage to crop and then save the result.
     * @param type $file_in The input file.
     * $param type $file_out The output file.
     */
    private function WI_crop($file_in, $file_out) {
        $input = WideImage::load($file_in);
        $done = $input->resize($this->OutputDimension[0], $this->OutputDimension[1], "inside");
        $done->saveToFile($this->ouputDirectory . $file_out);

        if (count($this->all_together) == count($this->all_files)) {
            $this->uploaded = true;
        }
    }

    /**
     * Puts all the uploaded file to output directory.
     * @param type $file_in The input file.
     * @param type $file_out The output file.
     */
    private function put_to_output_folder($file_in, $file_out) {
        if (!$this->should_crop) {
            if (move_uploaded_file($file_in, $this->ouputDirectory . $file_out)) {
                if (count($this->all_together) == count($this->all_files)) {
                    $this->uploaded = true;
                }
            } else {
                die("The uploaded file could not be moved!");
            }
        }
    }

    /**
     * Search after a specific value inside an array.
     * @param type $value The value to search for.
     * @param type $field A field to search $value in.
     * @param type $array The array.
     * @return type string
     */
    private function array_search_r($value, $field, $array) {
        foreach ($array as $key => $d) {
            if ($d[$field] == $value) {
                return $key;
            }
        }
        //return false;
    }

    /**
     * Check if the directory of the output directory exists.
     * If not, create it.
     */
    private function checkdirectory() {
        if ($this->ouputDirectory != "") {
            if (!file_exists($this->ouputDirectory)) {
                mkdir($this->ouputDirectory, 0777, true);
            }
        }
    }

}
