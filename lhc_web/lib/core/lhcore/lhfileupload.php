<?php

class erLhcoreClassFileUpload extends UploadHandler {
	
	public $uploadedFile = false;
	
	protected function get_file_name($name, $type = null, $index = null, $content_range = null) {
		$name = sha1($name . erLhcoreClassModelForgotPassword::randomPassword(40).time());
		return md5($this->get_unique_filename(
				$this->trim_file_name($name, $type, $index, $content_range),
				$type,
				$index,
				$content_range
		));
	}

	protected function generate_response($content, $print_response = true) {
		parent::generate_response($content,false);
	}
	
	protected function handle_file_upload_parent($uploaded_file, $name, $size, $type, $error, $index, $content_range) {
		return parent::handle_file_upload(
				$uploaded_file, $name, $size, $type, $error, $index, $content_range
		);
	}
	
    protected function handle_file_upload($uploaded_file, $name, $size, $type, $error, $index = null, $content_range = null) {

    	$matches = array();
    	if (strpos($name, '.') === false && preg_match('/^image\/(gif|jpe?g|png)/', $type, $matches)) {
    		$name = $uploadFileName = 'clipboard.'.$matches[1];
    	} else {
    		$uploadFileName = $name;
    	}

    	if (!preg_match($this->options['accept_file_types_lhc'], $uploadFileName)) {
    		$file->error = $this->get_error_message('accept_file_types');
    		return false;
    	}

        $file = parent::handle_file_upload(
        	$uploaded_file, $name, $size, $type, $error, $index, $content_range
        );

        if (empty($file->error)) {

        	$fileUpload = new erLhcoreClassModelChatFile();
        	$fileUpload->size = $file->size;
        	$fileUpload->type = $file->type;
        	$fileUpload->name = $file->name;
        	$fileUpload->date = time();
        	$fileUpload->user_id = isset($this->options['user_id']) ? $this->options['user_id'] : 0;
        	$fileUpload->upload_name = $name;
        	$fileUpload->file_path = $this->options['upload_dir'];
        	$fileUpload->chat_id = $this->options['chat']->id;

        	$matches = array();
        	if (strpos($name, '.') === false && preg_match('/^image\/(gif|jpe?g|png)/', $fileUpload->type, $matches)) {
        		$fileUpload->extension = $matches[1];
        	} else {
        		$partsFile = explode('.', $fileUpload->upload_name);
        		$fileUpload->extension = end($partsFile);
        	}

        	$fileUpload->saveThis();

	        $file->id = $fileUpload->id;

	        // Chat assign
	        $chat = $this->options['chat'];

	        // Format message
	        $msg = new erLhcoreClassModelmsg();
	        $msg->msg = '[file='.$file->id.'_'.md5($fileUpload->name.'_'.$fileUpload->chat_id).']';
	        $msg->chat_id = $chat->id;
	        $msg->user_id = isset($this->options['user_id']) ? $this->options['user_id'] : 0;
	        if ($msg->user_id > 0 && isset($this->options['name_support'])){
	        	$msg->name_support = (string)$this->options['name_support'];
	        }
	        $chat->last_user_msg_time = $msg->time = time();

	        erLhcoreClassChat::getSession()->save($msg);

	        // Set last message ID
	        if ($chat->last_msg_id < $msg->id) {
	        	$chat->last_msg_id = $msg->id;
	        }

	        $chat->has_unread_messages = 1;
	        $chat->updateThis();
	        
	        $this->uploadedFile = $fileUpload;
        }

        return $file;
    }

    public function delete($print_response = true) {
        return false;
    }

    public static function mkdirRecursive($path, $chown = false, $wwwUser = 'apache', $wwwUserGroup = 'apache') {
    	$partsPath = explode('/',$path);
    	$pathCurrent = '';
    
    	foreach ($partsPath as $key => $path)
    	{
    		$pathCurrent .= $path . '/';
    		if ( !is_dir($pathCurrent) ) {
    			mkdir($pathCurrent,0755);
    			if ($chown == true){
    				chown($pathCurrent,$wwwUser);
    				chgrp($pathCurrent,$wwwUserGroup);
    			}
    		}
    	}
    }

    public static function hasFiles($sourceDir)
    {
    	if ( !is_dir( $sourceDir ) )
    	{
    		return true;
    	}

    	$elements = array();
    	$d = @dir( $sourceDir );
    	if ( !$d )
    	{
    		return true;
    	}

    	while ( ( $entry = $d->read() ) !== false )
    	{
    		if ( $entry == '.' || $entry == '..' )
    		{
    			continue;
    		}

    		return true;
    	}

    	return false;
    }

    public static function removeRecursiveIfEmpty($basePath,$removePath)
    {
    	$removePath = trim($removePath,'/');
    	$partsRemove = explode('/',$removePath);

    	$pathElementsCount = count($partsRemove);
    	foreach ($partsRemove as $part) {
    		// We found some files/folders, so we have to exit
    		if (self::hasFiles( $basePath . implode('/',$partsRemove) ) === true) {
    			return ;
    		} else {
    			//Folder is empty, delete this folder
    			@rmdir($basePath . implode('/',$partsRemove));
    		}
    		array_pop($partsRemove);
    	}
    }
}

?>