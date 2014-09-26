<?php

class erLhcoreClassImageConverter {

   public $converter;
   private static $instance = null;

   function __construct()
   {
       $conversionSettings = array();

       /* if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'imagemagic_enabled' ) == true)
       {
           $conversionSettings[] = new ezcImageHandlerSettings( 'imagemagick', 'erLhcoreClassGalleryImagemagickHandler' );
       } */

       $conversionSettings[] =  new ezcImageHandlerSettings( 'gd','erLhcoreClassGalleryGDHandler' );

       $this->converter = new ezcImageConverter(
                new ezcImageConverterSettings(
                    $conversionSettings
                )
            );

            $filterNormal = array();
            $filterWatermarkAll = array();

            $this->converter->createTransformation(
                'photow_150',
                array(
                    new ezcImageFilter(
                        'croppedThumbnail',
                        array(
                            'width'     => 150,
                            'height'    => 150,
                            'direction' => ezcImageGeometryFilters::SCALE_DOWN,
                        )
                    ),
                ),
                array(
                    'image/jpeg',
                    'image/png',
                ),
                new ezcImageSaveOptions(array('quality' => (int)95))
            );

            $this->converter->createTransformation( 'jpeg', $filterWatermarkAll,
                array(
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                ),
                new ezcImageSaveOptions(array('quality' => 95)));
    }

    public static function getInstance()
    {
        if ( is_null( self::$instance ) )
        {
            self::$instance = new erLhcoreClassImageConverter();
        }
        return self::$instance;
    }

    public static function isPhoto($file)
    {
       if ($_FILES[$file]['error'] == 0)
       {
           try {
               $image = new ezcImageAnalyzer( $_FILES[$file]['tmp_name'] );
               if ($image->data->size < ((int)2000*1024) && $image->data->width > 10 && $image->data->height > 10)
               {
                   return true;

               } else

               return false;
           } catch (Exception $e) {
               return false;
           }

       } else {
           return false;
       }
    }

    public static function isPhotoLocal($filePAth)
    {
           try {
               $image = new ezcImageAnalyzer( $filePAth );
               if ($image->data->size < ((int)2000*1024) && $image->data->width > 10 && $image->data->height > 10)
               {
                   return true;

               } else
               return false;
           } catch (Exception $e) {
               return false;
           }
    }

    public static function getExtension($fileName) {
        return current(end(explode('.',$fileName)));
    }
}


/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize()){
            return false;
        }

        $target = fopen($path, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }
    function getName() {
        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Exception('Getting content length is not supported.');
        }
    }
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

class qqFileUploader {
    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;

    private $filePath = null;
    private $fileName = null;
    private $fileSize = null;
    private $fileExtension = null;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit = $sizeLimit;

        $this->checkServerSettings();

        if (isset($_GET['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false;
        }
    }

    private function checkServerSettings(){
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
        }
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getMimeType()
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        return finfo_file($finfo, $this->filePath);
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getUserFileName()
    {
        return $this->file->getName();
    }

    public function getFileSize()
    {
        return $this->fileSize;
    }

    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
        if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Upload directory isn't writable.");
        }

        if (!$this->file){
            return array('error' => 'No files were uploaded.');
        }

        $this->fileSize = $size = $this->file->getSize();

        if ($size == 0) {
            return array('error' => 'File is empty');
        }

        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }

        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
        //$filename = md5(uniqid());
        $ext = $pathinfo['extension'];

        $this->fileExtension = $ext;

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }

        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }

        $this->filePath = $uploadDirectory . $filename . '.' . $ext;
        $this->fileName =  $filename . '.' . $ext;

        if ( $this->file->save($this->filePath) ) {
            return array('success'=>true);
        } else {
            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
    }

    public static function upload( $file, $upload_name = 'SlideFile', $save_path = 'var/video/' ) {

   		$errors = array();

   		// Settings
		$max_file_size_in_bytes = 2147483647; // 2GB in bytes
		$extension_whitelist = array('jpg','jpeg','png'); // Allowed file extensions
		$valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-'; // Characters allowed in the file name (in a Regular Expression format)

		// Other variables
		$MAX_FILENAME_LENGTH = 260;
		$file_name = "";
		$file_extension = "";
		$uploadErrors = array(
        	0=>"There is no error, the file uploaded with success",
        	1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini",
        	2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
        	3=>"The uploaded file was only partially uploaded",
        	4=>"No file was uploaded",
        	6=>"Missing a temporary folder");

        // Validate the upload
		if (!isset($file[$upload_name])) {
			$errors[] = "No upload found in \$_FILES for " . $upload_name;
		} else if (isset($file[$upload_name]["error"]) && $file[$upload_name]["error"] != 0) {
			$errors[] = $uploadErrors[$file[$upload_name]["error"]];
		} else if (!isset($file[$upload_name]["tmp_name"]) || !@is_uploaded_file($file[$upload_name]["tmp_name"])) {
			$errors[] = "Upload failed is_uploaded_file test.";
			return $return = array( 'errors' => $errors );
		} else if (!isset($file[$upload_name]['name'])) {
			$errors[] = "File has no name.";
		}

		// If errors canel
		if (count($errors)) {
			return $return = array( 'errors' => $errors );
		}

		// Validate the file size (Warning: the largest files supported by this code is 2GB)
		$file_size = @filesize($file[$upload_name]["tmp_name"]);

		if (!$file_size || $file_size > $max_file_size_in_bytes) {
			$errors[] = "File exceeds the maximum allowed size";
		}

		if ($file_size <= 0) {
			$errors[] = "File size outside allowed lower bound";
		}

		// If errors canel
		if (count($errors)) {
			return $return = array( 'errors' => $errors );
		}

		// Validate file name (for our purposes we'll just remove invalid characters)
		$file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($file[$upload_name]['name']));

		if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH) {
			$errors[] = "Invalid file name";
		}

		// If errors canel
		if (count($errors)) {
			return $return = array( 'errors' => $errors );
		}

		// Validate that we won't over-write an existing file
		if (file_exists($save_path . $file_name)) {
			$errors[] = "File with this name already exists";
		}

		// If errors canel
		if (count($errors)) {
			return $return = array( 'errors' => $errors );
		}

		// Validate file extension
		$path_info = pathinfo($file[$upload_name]['name']);

		$file_extension = $path_info["extension"];

		$is_valid_extension = false;

		foreach ($extension_whitelist as $extension) {
			if (strcasecmp($file_extension, $extension) == 0) {
				$is_valid_extension = true;
				break;
			}
		}

		if (!$is_valid_extension) {
			$errors[] = "Invalid file extension";
		}

		// If errors canel
		if (count($errors)) {
			return $return = array( 'errors' => $errors );
		}

		// Creat new file name
		$parts = explode( '.', strtolower($file_name));
		$fileSuffix =  array_pop( $parts );

		// Get MineType
		//$mimeType = $this->getMineType($fileSuffix);

		$mimeType = 'application/octet-stream';
		$minetypes = array( 'jpg' => 'image/jpeg',
							'gif' => 'image/gif',
							'swf' => 'application/x-shockwave-flash',
							'png' => 'image/png');

		if (key_exists($fileSuffix,$minetypes)) {
			$mimeType = $minetypes[$fileSuffix];
		}

		if( $fileSuffix ) {
		   	$fileSuffix = '.' . $fileSuffix;
		}

		// Filename without suffix
    	$fileBaseName = basename( $file_name, $fileSuffix );

   		$fileNameNew = md5( $fileBaseName . microtime() . mt_rand() ).$fileSuffix;

    	$time = time();

    	// Sub dir

		$saveDir = $save_path;

    	if (!@move_uploaded_file($file[$upload_name]["tmp_name"], $saveDir.$fileNameNew)) {
			$errors[] = "File could not be saved.";
			return $return = array( 'errors' => $errors );
		}

		$data = array( 'filename'  		   => $fileNameNew,
					   'mime_type' 		   => $mimeType,
					   'original_filename' => $file_name,
					   'created' 		   => $time,
					   'dir' 			   => $saveDir);

		return array( 'errors' => $errors, 'data' => $data );
   }
}

?>