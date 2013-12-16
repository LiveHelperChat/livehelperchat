<?php

class erLhcoreClassGalleryImagemagickHandler extends ezcImageImagemagickHandler {
	
    private $binary_composite;
    private $filterOptionsComposite;
    
    /**
     * Map of MIME types to convert tags.
     * 
     * @var array(string=>string)
     */
    private $tagMap = array(
            'application/pcl' => 'PCL',
            'application/pdf' => 'PDF',
            'application/postscript' => 'PS',
            'application/vnd.palm' => 'PDB',
            'application/x-icb' => 'ICB',
            'application/x-mif' => 'MIFF',
            'image/bmp' => 'BMP3',
            'image/x-ms-bmp' => 'BMP',
            'image/dcx' => 'DCX',
            'image/g3fax' => 'G3',
            'image/gif' => 'GIF',
            'image/jng' => 'JNG',
            'image/jpeg' => 'JPG',
            'image/pbm' => 'PBM',
            'image/pcd' => 'PCD',
            'image/pict' => 'PCT',
            'image/pjpeg' => 'PJPEG',
            'image/png' => 'PNG',
            'image/ras' => 'RAS',
            'image/sgi' => 'SGI',
            'image/svg+xml' => 'SVG',
            // Left over for BC reasons
            'image/svg' => 'SVG',
            'image/tga' => 'TGA',
            'image/tiff' => 'TIF',
            'image/vda' => 'VDA',
            'image/vnd.wap.wbmp' => 'WBMP',
            'image/vst' => 'VST',
            'image/x-fits' => 'FITS',
            'image/x-otb' => 'OTB',
            'image/x-palm' => 'PALM',
            'image/x-pcx' => 'PCX',
            'image/x-pgm' => 'PGM',
            'image/psd' => 'PSD',
            'image/x-ppm' => 'PPM',
            'image/x-ptiff' => 'PTIF',
            'image/x-viff' => 'VIFF',
            'image/x-xbitmap' => 'XPM',
            'image/x-xv' => 'P7',
            'image/xpm' => 'PICON',
            'image/xwd' => 'XWD',
            'text/plain' => 'TXT',
            'video/mng' => 'MNG',
            'video/mpeg' => 'MPEG',
            'video/mpeg2' => 'M2V',
     );
    
    
    protected function addFilterOptionComposite( $reference, $name, $parameter = null )
    {
        $this->filterOptionsComposite[$reference][] = $name . ( $parameter !== null ? ' ' . escapeshellarg( $parameter ) : '' );
    }   
    
    public function __construct(ezcImageHandlerSettings $settings)
    {
        $this->checkImageMagickComposite( $settings );
        parent::__construct($settings);
    }
         
    private function checkImageMagickComposite()
    {
        if ( !isset( $settings->options['binary_composite'] ) )
        {
            $this->binary_composite = ezcBaseFeatures::findExecutableInPath('composite');
        }
        else if ( file_exists( $settings->options['binary_composite'] ) )
        {
            $this->binary_composite = $settings->options['binary_composite'];
        }

        if ( $this->binary_composite === null )
        {
            throw new ezcImageHandlerNotAvailableException(
                'ezcImageImagemagickHandler',
                'ImageMagick not installed or not available in PATH variable.'
            );
        }
        
        // Prepare to run ImageMagick command
        $descriptors = array( 
            array( 'pipe', 'r' ),
            array( 'pipe', 'w' ),
            array( 'pipe', 'w' ),
        );

        // Open ImageMagick process
        $imageProcess = proc_open( $this->binary_composite, $descriptors, $pipes );

        // Close STDIN pipe
        fclose( $pipes[0] );

        $outputString = '';
        // Read STDOUT 
        do 
        {
            $outputString .= rtrim( fgets( $pipes[1], 1024 ), "\n" );
        } while ( !feof( $pipes[1] ) );

        $errorString = '';
        // Read STDERR 
        do 
        {
            $errorString .= rtrim( fgets( $pipes[2], 1024 ), "\n" );
        } while ( !feof( $pipes[2] ) );
        
        // Wait for process to terminate and store return value
        $return = proc_close( $imageProcess );

        // Process potential errors
        if ( strlen( $errorString ) > 0 || strpos( $outputString, 'ImageMagick' ) === false )
        {
            throw new ezcImageHandlerNotAvailableException( 'ezcImageImagemagickHandler', 'ImageMagick not installed or not available in PATH variable.' );
        }
    }
    
    
	public function watermarkCenterAbsolute( $image, $posX, $posY, $width = false, $height = false )
    {
        if ( !is_string( $image ) || !file_exists( $image ) || !is_readable( $image ) )
        {
            throw new ezcBaseValueException( 'image', $image, 'string, path to an image file' );
        }
        if ( !is_int( $posX ) )
        {
            throw new ezcBaseValueException( 'posX', $posX, 'int' );
        }
        if ( !is_int( $posY ) )
        {
            throw new ezcBaseValueException( 'posY', $posY, 'int' );
        }
        if ( !is_int( $width ) && !is_bool( $width ) )
        {
            throw new ezcBaseValueException( 'width', $width, 'int/bool' );
        }
        if ( !is_int( $height ) && !is_bool( $height ) )
        {
            throw new ezcBaseValueException( 'height', $height, 'int/bool' );
        }

        $dataWatermark = getimagesize($image);
        $data = getimagesize( $this->getActiveResource() );

        $paddingX = $posX;
        $paddingY = $posY;
        
        $posX = $data[0]/2 - ($dataWatermark[0]/2);
        $posY = $data[1]/2 - ($dataWatermark[1]/2);
                 
        if ($posX < 0){        	
        	if ($width == false) {
        		$width = round($data[0] - $paddingX*2);
        	}        	 
        	$posX = $data[0]/2 - $width/2;
        	
        	//We have to adjust vertical position now
        	$posY = $data[1]/2 - ($dataWatermark[1] * ($width/$dataWatermark[0]))/2;
        }
                
        if ($posY < 0){        	
        	if ($height == false) {
        		$height = round($data[1] - $paddingY*2);
        	}        	
        	$posY = $data[1]/2 - $height/2;
        	
        	//We have to adjust horisontal position now
        	$posX = $data[0]/2 - ($dataWatermark[0] * ($height/$dataWatermark[1]))/2;
        }     
        
        // If new size set, both sizes have to be set
        if ($height !== false && $width == false)
        {
        	$width = $dataWatermark[0];
        }  
        
        if ($height == false && $width !== false)
        {
        	$height = $dataWatermark[1];        	
        }
        
        $this->addFilterOption(
            $this->getActiveReference(),
            '-composite',
            '' 
        );

        $this->addFilterOption(
            $this->getActiveReference(),
            '-geometry',
            ( $width !== false ? $width : "" ) . ( $height !== false ? "x$height" : "" ) . "+$posX+$posY"
        );

        $this->addCompositeImage( $this->getActiveReference(), $image );
    }
    
    /**
     * @param $side 
     * 0 - left side cut
     * 1 - right side cut
     * */
    public function anaglyphImageSide($side = 0)
    {
        $data = getimagesize( $this->getActiveResource() );
        $imageHalfWidth = (int)($data[0]/2);
        $imageHeight = $data[1];
      
        if ($side == 0)
        {
            $this->addFilterOption(
                $this->getActiveReference(),
                '-crop',
                $imageHalfWidth.'x'.$imageHeight.'+0+0'
            );   
        } else {
           $this->addFilterOption(
                $this->getActiveReference(),
                '-crop',
                $imageHalfWidth.'x'.$imageHeight.'+'.$imageHalfWidth.'+0'
            ); 
        }            
    }
    
    public function saveComposite($image, $newFile = null, $mime = null, ezcImageSaveOptions $options = null)
    {
        if ( $options === null )
        {
            $options = new ezcImageSaveOptions();
        }

        if ( $newFile !== null )
        {
            $this->checkFileName( $newFile );
        }
                
        $this->saveCommon( $image, $newFile, $mime );
                                
        // Prepare ImageMagick command
        // Here we need a work around, because older ImageMagick versions do not
        // support this option order        
        $command = $this->binary_composite . ' ' .
                escapeshellarg( $this->getReferenceData( $image, 'file' ) ) . ' ' .
                ( isset( $this->filterOptionsComposite[$image] ) ? implode( ' ', $this->filterOptionsComposite[$image] ) : '' ) . ' ' .
                escapeshellarg( $this->tagMap[$this->getReferenceData( $image, 'mime' )] . ':' . $this->getReferenceData( $image, 'file' ) );
             
        // Prepare to run ImageMagick command
        $descriptors = array( 
            array( 'pipe', 'r' ),
            array( 'pipe', 'w' ),
            array( 'pipe', 'w' ),
        );
        
      
        // Open ImageMagick process
        $imageProcess = proc_open( $command, $descriptors, $pipes );
        // Close STDIN pipe
        fclose( $pipes[0] );
        
        $errorString  = '';
        $outputString = '';
        // Read STDERR 
        do 
        {
            $outputString .= rtrim( fgets( $pipes[1], 1024 ), "\n" );
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
            throw new ezcImageFileNotProcessableException(
                $this->getReferenceData( $image, 'resource' ),
                "The command '{$command}' resulted in an error ({$status['exitcode']}): '{$errorString}'. Output: '{$outputString}'"
            );
        }
        // Finish atomic file operation
        copy( $this->getReferenceData( $image, 'file' ), $this->getReferenceData( $image, 'resource' ) );                        
    }
    
    public function croppedThumbnailTop( $width, $height )
    {
        if ( !is_int( $width )  || $width < 1 )
        {
            throw new ezcBaseValueException( 'width', $width, 'int > 0' );
        }
        if ( !is_int( $height )  || $height < 1 )
        {
            throw new ezcBaseValueException( 'height', $height, 'int > 0' );
        }
        $data = getimagesize( $this->getReferenceData( $this->getActiveReference(), "resource" ) );
        
        $scaleRatio  = max( $width / $data[0], $height / $data[1] );
        $scaleWidth  = round( $data[0]  * $scaleRatio );
        $scaleHeight = round( $data[1] * $scaleRatio );
        
        $cropOffsetX = ( $scaleWidth > $width )   ? "+" . round( ( $scaleWidth - $width ) / 2 )   : "+0";
        $cropOffsetY = "+0"; // Crop from top

        $this->addFilterOption(
            $this->getActiveReference(),
            '-resize',
            $scaleWidth . "x" . $scaleHeight
        );
        $this->addFilterOption(
            $this->getActiveReference(),
            '-crop',
            $width . "x" . $height . $cropOffsetX . $cropOffsetY . "!"
        );
    }
    
    /**
     * @param $side 
     * 0 - left side cut
     * 1 - right side cut
     * */
    public function anaglyphImage($imageRight)
    {           
       $this->addFilterOptionComposite(
            $this->getActiveReference(),
            '-stereo',
            $imageRight 
       ); 
           
       $this->saveComposite( $this->getActiveReference() );        
    }
        
    public function rotateImage(){
        $this->addFilterOption(
            $this->getActiveReference(),
            '-rotate',
            90
        );
    }    
    
    public function switchImage(){
        $this->addFilterOption(
            $this->getActiveReference(),
            '-flop'
        );
    }
     
    public function switchvImage(){
        $this->addFilterOption(
            $this->getActiveReference(),
            '-flip'
        );
    }
    
    public function extractAnimatedGifFrame($frame = 0) {
         
    }
	
}