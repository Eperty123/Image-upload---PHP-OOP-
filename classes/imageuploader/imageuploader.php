<?php

/**
 * Created by: Carlo De Leon. @ 2017.
 * Github page: https://github.com/Eperty123/Image-upload---PHP-OOP-.
 * Version: 1.0-b
 */
/* WideImage */
require_once "wideimage/WideImage.php";

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

    /* Initialize the class. */

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
            $this->post_name = $post_name;
            $this->all_files = $this->remaparray($post_picture);

            /* Remove empty array entries if any. */
            $this->remove_empty_array_entry();

            if ($this->all_files[0]["size"] > 0) {

                /* Call other useful functions. */
                $this->getimageresolution();
                $this->getoriginalimagename();
                $this->gettempname();
                $this->getextensionname();

                $this->put_all_together();
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
    public function find_and_get($image_number = 0) {
        if (count($this->all_together) == 1) {
            $image_number = 0;
        }
        return $this->all_together[$image_number];
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

        for ($i = 0; $i < $file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_array[$i][$key] = $array[$key][$i];
            }
        }

        return $file_array;
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
        foreach ($this->all_files as $entry) {
            $idx++;
            if ($entry["name"] == "") {
                unset($this->all_files[$idx]);
            }
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
    }

    /**
     * Puts all the uploaded file to output directory.
     * @param type $file_in The input file.
     * @param type $file_out The output file.
     */
    private function put_to_output_folder($file_in, $file_out) {
        if (!$this->should_crop) {
            if (move_uploaded_file($file_in, $this->ouputDirectory . $file_out)) {
                
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
        } else {
            die("Please set a directory for uploaded files!");
        }
    }

}
