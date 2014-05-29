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
	   			),
	   			'Active' => new ezcInputFormDefinitionElement(
	   					ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
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
	   	
	   	if ( $form->hasValidData( 'Active' ) && $form->Active == true )	{
	   		$docshare->active = 1;
	   	} else {
	   		$docshare->active = 0;
	   	}
	   	
	   	if (empty($Errors)) {
	   		
	   		if ( isset($_FILES["qqfile"]) && is_uploaded_file($_FILES["qqfile"]["tmp_name"]) && $_FILES["qqfile"]["error"] == 0 ) {
	   			   			
	   			$objectData = erLhcoreClassModelChatConfig::fetch('doc_sharer');
	   			$dataDocSharer = (array)$objectData->data;
	   				   			
		   		$allowedExtensions = explode(',',$dataDocSharer['supported_extension']);
		   		
		   		// max file size in bytes
		   		$sizeLimit = $dataDocSharer['max_file_size'] * 1024 * 1024;
		   		
		   		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		   		$result = $uploader->handleUpload('var/tmpfiles/');
		   		
		   		if ( isset($result['success']) && $result['success'] == 'true' ) {
		   			$result['filepath'] = $uploader->getFilePath();
		   			$result['filename'] = $uploader->getFileName();
		   			$result['filename_user'] = $uploader->getUserFileName();
		   			$docshare->type = $uploader->getMimeType();
		   			
		   			if ($docshare->id == null) {
		   				$docshare->saveThis();
		   			}
		   			
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
		   			$docshare->pdf_to_img_converted = 0; 
		   			$docshare->pages_pdf_count = 0; 
		   			$docshare->ext = $uploader->getFileExtension(); 
		   			$docshare->saveThis();
		   		} elseif ($docshare->id == null) {
		   			$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('faq/view',$result['error']);
		   		}
	   		} elseif ($docshare->id == null) {
		   			$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('faq/view','Please choose a file');
		   	}   		
	   	}
	   	
	   	return $Errors;
   }

   public static function makeConversion($docShare, $conjobInterface = false) {
   		
   		$docSharer = erLhcoreClassModelChatConfig::fetch('doc_sharer');
   		$data = (array)$docSharer->data;
   	
   		if ($conjobInterface == true || $data['background_process'] == 0) {
   		
	   		if ($docShare->converted == 0) {
	   			erLhcoreClassDocShare::covertToPDF($docShare);   			
	   		}
	   		
	   		if ($docShare->pdf_to_img_converted == 0) {
	   			erLhcoreClassDocShare::convertPDFToPNG($docShare);
	   		}
   		}
   }
   
   public static function covertToPDF($docShare) {
   		
	   	try {	   	
	   		$config = erConfigClassLhConfig::getInstance();
	   
	   		// If pdf we do not need to convert anything
	   		if ($docShare->ext == 'pdf') {
	   			$docShare->pdf_file = $docShare->file_name;
	   			$docShare->converted = 1;
	   			$docShare->saveThis();
	   			return ;
	   		}
	   		
	   		$pdfFileCopy = $docShare->file_path_server.'.'.$docShare->ext;
	   		
	   		if (!file_exists("/tmp/ooohomedir"))
	   		{
	   			@mkdir("/tmp/ooohomedir");
	   			@chmod("/tmp/ooohomedir", 0777);
	   		}
	   		
	   		@putenv("HOME=/tmp/ooohomedir");
	  
	   		$objectData = erLhcoreClassModelChatConfig::fetch('doc_sharer');
	   		$dataDocSharer = (array)$objectData->data;
	   			   		
	   		if (copy($docShare->file_path_server, $pdfFileCopy)) {
		   		$command = $dataDocSharer['libre_office_path'].' --nologo --invisible --headless -convert-to pdf:writer_pdf_Export '.escapeshellarg( $pdfFileCopy ) . ' --outdir '.$docShare->file_path_dir;
		   				   		
		   		self::processCommand($command);
		   	
		   		$pdfFile = $docShare->file_path_dir.$docShare->file_name.'.pdf';
		   			
		   		if ( file_exists($pdfFile) ) {	
		   					   			
		   			$docShare->pdf_file = sha1(sha1($docShare->file_name.'doctopdf').time()).'pdf';
		   			rename($pdfFile, $docShare->file_path_dir.$docShare->pdf_file);

		   			chown($docShare->file_path_dir.$docShare->pdf_file,$dataDocSharer['http_user_name']);
		   			chgrp($docShare->file_path_dir.$docShare->pdf_file,$dataDocSharer['http_user_group_name']);
		   			chmod($docShare->file_path_dir.$docShare->pdf_file,0664);
		   			
		   			$docShare->converted = 1;		   			
		   			$docShare->saveThis();		   	
		   		} else {
		   			$docShare->converted = 1;
		   			$docShare->saveThis();
		   		}
		   		
		   		unlink($pdfFileCopy);
	   		} else {
	   			$docShare->converted = 1;
	   			$docShare->saveThis();
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
	   			
	   			$objectData = erLhcoreClassModelChatConfig::fetch('doc_sharer');
	   			$dataDocSharer = (array)$objectData->data;
	   				   			
	   			erLhcoreClassFileUpload::mkdirRecursive($fileObject->pdftoimg_path,true,$dataDocSharer['http_user_name'],$dataDocSharer['http_user_group_name']);
	   
	   			$ocrParsed = true;
	   
	   			// Prepare to run ImageMagick command
	   			$descriptors = array(
	   					array( 'pipe', 'r' ),
	   					array( 'pipe', 'w' ),
	   					array( 'pipe', 'w' ),
	   			);
	   
	   			$appendCommand = '';
	   			
	   			if ($dataDocSharer['pdftoppm_limit'] > 0) {
	   				$appendCommand .= ' -l ' . $dataDocSharer['pdftoppm_limit'];
	   			}
	   				   				   			
	   			$command = $dataDocSharer['pdftoppm_path'].$appendCommand.' -png -r 200 ' . escapeshellarg( $pdfFile ) . ' '.$fileObject->pdftoimg_path.$fileObject->id;
	   

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
	   	   		   			
	   			foreach ($data as $key => $file) {
	   					   				
	   				$pagesCount++;
	   				
	   				$parts = explode('-', $file);
	   				array_pop($parts);
	   				$parts[] = '-'.$pagesCount.'.png';
	   				$newName = implode('', $parts);
	   				rename($file, $newName);
	   				$file = $newName;
		   			
		   			chown($file,$dataDocSharer['http_user_name']);
		   			chgrp($file,$dataDocSharer['http_user_group_name']);
		   			chmod($file,0664);
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