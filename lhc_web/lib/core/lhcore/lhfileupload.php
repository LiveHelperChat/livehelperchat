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
			/* $sql = 'INSERT INTO `'.$this->options['db_table']
				.'` (`name`, `size`, `type`, `title`, `description`)'
				.' VALUES (?, ?, ?, ?, ?)';
	        $query = $this->db->prepare($sql);
	        $query->bind_param(
	        	'sisss',
	        	$file->name,
	        	$file->size,
	        	$file->type,
	        	$file->title,
	        	$file->description
	        );
	        $query->execute();
	        $file->id = $this->db->insert_id; */
        }

        return $file;
    }

    public function delete($print_response = true) {
        return false;
    }
}

?>