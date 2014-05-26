<?php

class erLhcoreClassDocShare {

   public static function getSession()
   {
        if ( !isset( self::$persistentSession ) )
        {
            self::$persistentSession = new ezcPersistentSession(
                ezcDbInstance::get(),
                new ezcPersistentCodeManager( './pos/lhdocshare' )
            );
        }
        return self::$persistentSession;
   }

   public static function validateDocShare(& $docshare) {
	   	$definition = array(
	   			'name' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
	   			),
	   			'desc' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
	   			)
	   			
	   	);
	   	$form = new ezcInputForm( INPUT_POST, $definition );
	   	$Errors = array();

	   	if ( !$form->hasValidData( 'name' ) || $form->name == '')
	   	{
	   		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('faq/view','Please enter name!');
	   	} else {
	   		$docshare->name = $form->name;
	   	}
	   	
	   	if ( $form->hasValidData( 'desc' ) && $form->desc != '' )
	   	{
	   		$docshare->desc = $form->desc;	   		
	   	} else {
	   		$docshare->desc = '';
	   	}
	   	
	   	if ( isset($_FILES["qqfile"]) && is_uploaded_file($_FILES["qqfile"]["tmp_name"]) && $_FILES["qqfile"]["error"] == 0 ) {
	   		if (empty($Errors)) {
		   		$allowedExtensions = array('ppt','pptx','doc','odp','epub','mobi','docx','xlsx','txt','xls','xlsx','png','pdf','rtf','odt');
		   		
		   		// max file size in bytes
		   		$sizeLimit = 20 * 1024 * 1024;
		   		
		   		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		   		$result = $uploader->handleUpload('var/tmpfiles/');
		   		
		   		if ( isset($result['success']) && $result['success'] == 'true' ) {
		   			$result['filepath'] = $uploader->getFilePath();
		   			$result['filename'] = $uploader->getFileName();
		   			$result['filename_user'] = $uploader->getUserFileName();
		   			$docshare->type = $uploader->getMimeType();
		   			
		   			$docshare->removeFile();
		   			
		   			$photoDir = 'var/storagedocshare/'.date('Y').'y/'.date('m').'/'.date('d').'/'.$docshare->id;
		   			$photoDirPhoto = 'storagedocshare/'.date('Y').'y/'.date('m').'/'.date('d').'/'.$docshare->id.'/';
		   			erLhcoreClassFileUpload::mkdirRecursive($photoDir);
		   			
		   			$fileName = sha1(sha1($result['filepath']).time()) . $uploader->getFileExtension();
		   			$photoDir .= '/' . $fileName;
		   			rename($result['filepath'],$photoDir);
		   			
		   			$docshare->file_name = $fileName;
		   			$docshare->file_path = $photoDirPhoto;
		   			$docshare->file_name_upload = $result['filename_user'];	   			
		   			$docshare->file_size = $uploader->getFileSize();	   
		   			$docshare->converted = 0; 
		   			$docshare->ext = $uploader->getFileExtension(); 
		   			$docshare->saveThis();
		   		}
	   		}	   		
	   	}
	   	
	   	return $Errors;
   }

   public static function covertToPDF($docShare){
   		
	   	try {	   	
	   		$config = erConfigClassLhConfig::getInstance();
	   
	   		$pdfFileCopy = $docShare->file_path_server.'.'.$docShare->ext;
	   		
	   		if (!file_exists("/tmp/ooohomedir"))
	   		{
	   			mkdir("/tmp/ooohomedir");
	   			chmod("/tmp/ooohomedir", 0777);
	   		}
	   		
	   		putenv("HOME=/tmp/ooohomedir");
	  
	   		
	   		if (copy($docShare->file_path_server, $pdfFileCopy)) {
		   		$command = erLhcoreClassModelChatConfig::fetch('soffice_path')->current_value.' --nologo --invisible --headless -convert-to pdf:writer_pdf_Export '.escapeshellarg( $pdfFileCopy ) . ' --outdir '.$docShare->file_path_dir;
		   		
		   		self::processCommand($command);
		   	
		   		$pdfFile = $docShare->file_path_dir.$docShare->file_name.'.pdf';
		   			
		   		if ( file_exists($pdfFile) ) {	
		   					   			
		   			$docShare->pdf_file = sha1(sha1($docShare->file_name.'doctopdf').time()).'pdf';
		   			rename($pdfFile, $docShare->file_path_dir.$docShare->pdf_file);
		   					   			
		   			$docShare->converted = 1;		   			
		   			$docShare->saveThis();		   	
		   		}
		   		
		   		unlink($pdfFileCopy);
	   		}
	   		
	   	} catch (Exception $e) {
	   		throw $e;
	   	}
   }
   
   public static function processCommand($command) {
   
	   	$descriptors = array(
	   			array( 'pipe', 'r' ),
	   			array( 'pipe', 'w' ),
	   			array( 'pipe', 'w' ),
	   	);
	   
	   	$ocrParsed = true;
	   
	   	// Open color_indexer process
	   	$imageProcess = proc_open( $command, $descriptors, $pipes );
	   
	   	// Close STDIN pipe
	   	fclose( $pipes[0] );
	   
	   	$errorString  = '';
	   	$outputString = '';
	   	// Read STDERR
	   	do
	   	{
	   		$errorString  .= rtrim( fgets( $pipes[2], 1024 ), "\n" );
	   	} while ( !feof( $pipes[2] ) );
	   
	   	// Wait for process to terminate and store return value
	   	$status = proc_get_status( $imageProcess );
	   	while ( $status['running'] !== false )
	   	{
	   		// Sleep 1/100 second to wait for convert to exit
	   		usleep( 10000 );
	   		$status = proc_get_status( $imageProcess );
	   	}
	   
	   	$return = proc_close( $imageProcess );
	  	   	
	   	// Process potential errors
	   	// Exit code may be messed up with -1, especially on Windoze
	   	if ( ( $status['exitcode'] != 0 && $status['exitcode'] != -1 ) || strlen( $errorString ) > 0 )
	   	{
	   		$ocrParsed = false;
	   	}
	   
	   	return $ocrParsed;   
   }
   
   public static function convertPDFToPNG(erLhcoreClassModelDocShare & $fileObject) {
   
	   	$pdfFile = $fileObject->pdf_file_path_server;
	   
	   	if ( $fileObject->pdf_file != '' && file_exists($pdfFile) ) {
	   
	   		try {
	   
	   			$config = erConfigClassLhConfig::getInstance();
	   
	   			erLhcoreClassFileUpload::mkdirRecursive($fileObject->pdftoimg_path,true);
	   
	   			$ocrParsed = true;
	   
	   			// Prepare to run ImageMagick command
	   			$descriptors = array(
	   					array( 'pipe', 'r' ),
	   					array( 'pipe', 'w' ),
	   					array( 'pipe', 'w' ),
	   			);
	   
	   			$appendCommand = '';
	   
	   			/* if ($fileObject->spages > 0) {
	   				$appendCommand .= ' -f ' . ($fileObject->spages+1);
	   			}
	   
	   			if ($fileObject->lpages > 0) {
	   				$appendCommand .= ' -l ' . ($fileObject->lpages+$fileObject->spages);
	   			} */
	   				   				   		
	   			// Process OCR
	   			//$command = $config->conf->getSetting( 'lingjob', 'pdftoppm' ).$appendCommand.' -png -r 200 ' . escapeshellarg( $pdfFile ) . ' '.$fileObject->pdftoimg_path.$fileObject->id;
	   			$command = '/usr/bin/pdftoppm -png -r 200 ' . escapeshellarg( $pdfFile ) . ' '.$fileObject->pdftoimg_path.$fileObject->id;
	   

	   			// Open color_indexer process
	   			$imageProcess = proc_open( $command, $descriptors, $pipes );
	   
	   			// Close STDIN pipe
	   			fclose( $pipes[0] );
	   
	   			$errorString  = '';
	   			$outputString = '';
	   			// Read STDERR
	   			do
	   			{
	   				$errorString  .= rtrim( fgets( $pipes[2], 1024 ), "\n" );	   			
	   			} while ( !feof( $pipes[2] ) );
	   
	   			// Wait for process to terminate and store return value
	   			$status = proc_get_status( $imageProcess );
	   			while ( $status['running'] !== false )
	   			{
	   				// Sleep 1/100 second to wait for convert to exit
	   				usleep( 10000 );
	   				$status = proc_get_status( $imageProcess );	   				
	   			}
	   			$return = proc_close( $imageProcess );

	   			
	   			$data = ezcBaseFile::findRecursive(
	   					$fileObject->pdftoimg_path,
	   					array( "@{$fileObject->id}-.*\.png@" ),
	   					array( )
	   			);
	   	   				   
	   			$contentPages = array();
	   			$pagesCount = 0;
	   
	   			/* $wwwUser = $config->conf->getSetting( 'site', 'default_www_user' );
	   			$wwwUserGroup = $config->conf->getSetting( 'site', 'default_www_group' ); */
	   	  	   				
	   			foreach ($data as $key => $file) {
	   					   				
	   				$pagesCount++;
	   				
	   				$parts = explode('-', $file);
	   				array_pop($parts);
	   				$parts[] = '-'.$pagesCount.'.png';
	   				$newName = implode('', $parts);
	   				rename($file, $newName);
	   				$file = $newName;
		   			
		   			/* chown($file,$wwwUser);
		   			chgrp($file,$wwwUserGroup);
		   			chmod($file,$config->conf->getSetting( 'site', 'StorageFilePermissions' )); */
		   		}
	   		   		
	  
	   
	   		$fileObject->pdf_to_img_converted = 1;
	   		$fileObject->pages_pdf_count = $pagesCount;
	   			
	   		$fileObject->saveThis();
	   
	  
	   
	   	} catch (Exception $e) {
	   		throw $e;
	   	}
	   		
	   }   
   }
   
   private static $persistentSession;
}

?>