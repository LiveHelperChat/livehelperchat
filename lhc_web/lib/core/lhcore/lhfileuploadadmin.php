<?php

class erLhcoreClassFileUploadAdmin extends erLhcoreClassFileUpload {
	
	public $uploadedFile = false;
	
    protected function handle_file_upload($uploaded_file, $name, $size, $type, $error, $index = null, $content_range = null) {
    	    	
    	$matches = array();
    	if (strpos($name, '.') === false && preg_match('/^image\/(gif|jpe?g|png)/', $type, $matches)) {
    		$name = $uploadFileName = 'clipboard.'.$matches[1];
    	} else {
    		$uploadFileName = $name;
    	}

    	if (!preg_match($this->options['accept_file_types_lhc'], $uploadFileName)) {    		
    		throw new Exception($this->get_error_message('accept_file_types'));
    		return false;
    	}
    	
        $file = parent::handle_file_upload_parent(
        	$uploaded_file, $name, $size, $type, $error, $index, $content_range
        );
 
        if (empty($file->error)) {

        	$fileUpload = new erLhcoreClassModelChatFile();
        	$fileUpload->size = $file->size;
        	$fileUpload->type = $file->type;
        	$fileUpload->name = $file->name;
        	$fileUpload->date = time();
        	$fileUpload->user_id = isset($this->options['user_id']) ? $this->options['user_id'] : 0;
        	$fileUpload->upload_name = (isset($this->options['file_name_manual']) && $this->options['file_name_manual'] != '') ? $this->options['file_name_manual'].' - '.$name : $name;;
        	$fileUpload->file_path = $this->options['upload_dir'];
        	$fileUpload->chat_id = 0;

        	$matches = array();
        	if (strpos($name, '.') === false && preg_match('/^image\/(gif|jpe?g|png)/', $fileUpload->type, $matches)) {
        		$fileUpload->extension = $matches[1];
        	} else {
        		$partsFile = explode('.', $name);
        		$fileUpload->extension = end($partsFile);
        	}

        	$fileUpload->saveThis();
        	
        	$this->uploadedFile = $fileUpload;
        } else {
        	throw new Exception($file->error);
        }

        return $file;
    }
}

?>