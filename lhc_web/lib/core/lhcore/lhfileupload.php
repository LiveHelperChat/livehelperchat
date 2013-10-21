<?php

class erLhcoreClassFileUpload extends UploadHandler {

	protected function get_file_name($name, $type = null, $index = null, $content_range = null) {
		$name = sha1($name . erLhcoreClassModelForgotPassword::randomPassword(40).time());
		return md5($this->get_unique_filename(
				$this->trim_file_name($name, $type, $index, $content_range),
				$type,
				$index,
				$content_range
		));
	}

    protected function handle_file_upload($uploaded_file, $name, $size, $type, $error, $index = null, $content_range = null) {

        $file = parent::handle_file_upload(
        	$uploaded_file, $name, $size, $type, $error, $index, $content_range
        );

        if (empty($file->error)) {

        	$fileUpload = new erLhcoreClassModelChatFile();
        	$fileUpload->size = $file->size;
        	$fileUpload->type = $file->type;
        	$fileUpload->name = $file->name;
        	$fileUpload->upload_name = $name;
        	$fileUpload->file_path = $this->options['upload_dir'];
        	$fileUpload->chat_id = $this->options['chat']->id;

        	$matches = array();
        	if (strpos($name, '.') === false && preg_match('/^image\/(gif|jpe?g|png)/', $fileUpload->type, $matches)) {
        		$fileUpload->extension = $matches[1];
        	} else {
        		$fileUpload->extension = end(explode('.', $fileUpload->upload_name));
        	}

        	$fileUpload->saveThis();

	        $file->id = $fileUpload->id;

	        // Chat assign
	        $chat = $this->options['chat'];

	        // Format message
	        $msg = new erLhcoreClassModelmsg();
	        $msg->msg = '[file='.$file->id.'_'.md5($fileUpload->name.'_'.$fileUpload->chat_id).']';
	        $msg->chat_id = $chat->id;
	        $msg->user_id = 0;
	        $chat->last_user_msg_time = $msg->time = time();

	        erLhcoreClassChat::getSession()->save($msg);

	        // Set last message ID
	        if ($chat->last_msg_id < $msg->id) {
	        	$chat->last_msg_id = $msg->id;
	        }

	        $chat->has_unread_messages = 1;
	        $chat->updateThis();
        }

        return $file;
    }

    public function delete($print_response = true) {
        return false;
    }
}

?>