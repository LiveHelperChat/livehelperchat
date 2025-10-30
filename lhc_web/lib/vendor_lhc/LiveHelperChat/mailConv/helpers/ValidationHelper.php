<?php

namespace LiveHelperChat\mailConv\helpers;

class ValidationHelper
{
    public static function isValidPDF($file_path)
    {
        // Check if file exists and is readable
        if (!is_file($file_path) || !is_readable($file_path)) {
            return false;
        }
 
        // Check magic bytes - PDF files must start with '%PDF-'
        $fh = fopen($file_path, 'rb');
        if ($fh === false) {
            return false;
        }
        $magic = fread($fh, 5);
        fclose($fh);
        
        if ($magic !== '%PDF-') {
            return false;
        }

        // Check MIME type using finfo
        if (class_exists('\finfo')) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file_path);
            
            // Accept both standard PDF MIME types
            if ($mime !== 'application/pdf' && $mime !== 'application/x-pdf') {
                return false;
            }
        }
   
        // lightweight token scan for suspicious PDF objects
        $contents = file_get_contents($file_path, false, null, 0, 2000000); // read up to first 2MB
        $dangerTokens = ['/JavaScript', '/JS', '/OpenAction', '/AA', '/AcroForm', '/Launch', '/EmbeddedFile', '/RichMedia'];
        foreach ($dangerTokens as $t) {
            if (stripos($contents, $t) !== false) {
                return false;
                break;
            }
        }

        return true;
    }
}