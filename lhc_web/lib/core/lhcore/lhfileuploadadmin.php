<?php

class erLhcoreClassFileUploadAdmin extends erLhcoreClassFileUpload
{

    public $uploadedFile = false;

    // https://stackoverflow.com/questions/3614925/remove-exif-data-from-jpg-using-php
    public static function removeExif($in, $out)
    {
        $buffer_len = 4096;
        $fd_in = fopen($in, 'rb');
        $fd_out = fopen($out, 'wb');
        while (($buffer = fread($fd_in, $buffer_len))) {
            //  \xFF\xE1\xHH\xLLExif\x00\x00 - Exif
            //  \xFF\xE1\xHH\xLLhttp://      - XMP
            //  \xFF\xE2\xHH\xLLICC_PROFILE  - ICC
            //  \xFF\xED\xHH\xLLPhotoshop    - PH
            while (preg_match('/\xFF[\xE1\xE2\xED\xEE](.)(.)(exif|photoshop|http:|icc_profile|adobe)/si', $buffer, $match, PREG_OFFSET_CAPTURE)) {
                $len = ord($match[1][0]) * 256 + ord($match[2][0]);
                fwrite($fd_out, substr($buffer, 0, $match[0][1]));
                $filepos = $match[0][1] + 2 + $len - strlen($buffer);
                fseek($fd_in, $filepos, SEEK_CUR);
                $buffer = fread($fd_in, $buffer_len);
            }
            fwrite($fd_out, $buffer, strlen($buffer));
        }
        fclose($fd_out);
        fclose($fd_in);
    }

    protected function handle_file_upload($uploaded_file, $name, $size, $type, $error, $index = null, $content_range = null)
    {

        $matches = array();
        if (strpos($name, '.') === false && preg_match('/^image\/(gif|jpe?g|png)/', $type, $matches)) {
            $name = $uploadFileName = 'clipboard.' . $matches[1];
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
            $fileUpload->upload_name = (isset($this->options['file_name_manual']) && $this->options['file_name_manual'] != '') ? $this->options['file_name_manual'] . ' - ' . $name : $name;;
            $fileUpload->file_path = $this->options['upload_dir'];
            $fileUpload->chat_id = 0;
            $fileUpload->persistent = (isset($this->options['persistent']) && $this->options['persistent'] == true) ? 1 : 0;

            $matches = array();
            if (strpos($name, '.') === false && preg_match('/^image\/(gif|jpe?g|png)/', $fileUpload->type, $matches)) {
                $fileUpload->extension = strtolower($matches[1]);
            } else {
                $partsFile = explode('.', $name);
                $fileUpload->extension = strtolower(end($partsFile));
            }

            if (isset($this->options['remove_meta']) && $this->options['remove_meta'] == true && in_array($fileUpload->extension, array('jpg', 'jpeg', 'png', 'gif'))) {
                self::removeExif($fileUpload->file_path_server, $fileUpload->file_path_server . '_exif');
                unlink($fileUpload->file_path_server);
                rename($fileUpload->file_path_server . '_exif', $fileUpload->file_path_server);
                $fileUpload->size = filesize($fileUpload->file_path_server);
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