<?php

class erLhcoreClassModelDocShare {

	public function getState()
	{
		return array(
				'id'         			=> $this->id,
				'name'   	 			=> $this->name,
				'desc'     	 			=> $this->desc,				
				'user_id'    			=> $this->user_id,
				'active'     			=> $this->active,				
				'converted'  			=> $this->converted,				
				'file_name'  			=> $this->file_name,
				'file_path'  			=> $this->file_path,
				'file_size'  			=> $this->file_size,
				'file_name_upload'  	=> $this->file_name_upload,
				'type'  				=> $this->type,				
				'ext'  					=> $this->ext,				
				'pdf_file'  			=> $this->pdf_file,				
				'pdf_to_img_converted'  => $this->pdf_to_img_converted,				
				'pages_pdf_count'  		=> $this->pages_pdf_count				
		);
	}

	public function setState( array $properties )
	{
		foreach ( $properties as $key => $val )
		{
			$this->$key = $val;
		}
	}

	public function __toString(){
		return $this->name;
	}
	
	public static function getCount($params = array())
	{
		$session = erLhcoreClassDocShare::getSession();
		$q = $session->database->createSelectQuery();
		$q->select( "COUNT(id)" )->from( "lh_doc_share" );

		if (isset($params['filter']) && count($params['filter']) > 0)
		{
			$conditions = array();

			foreach ($params['filter'] as $field => $fieldValue)
			{
				$conditions[] = $q->expr->eq( $field, $q->bindValue($fieldValue) );
			}

			$q->where(
					$conditions
			);
		}

		$stmt = $q->prepare();
		$stmt->execute();
		$result = $stmt->fetchColumn();

		return $result;
	}

	public function __get($var) {
		switch ($var) {
			case 'has_file':
				return $this->has_file = $this->file_name != '';
			;
			break;
			
			case 'file_path_server':
				return $this->file_path_server = 'var/' . $this->file_path . $this->file_name;
			;
			break;
			
			case 'file_path_dir':
				return $this->file_path_server = 'var/' . $this->file_path;
			;
			break;
			
			case 'pdftoimg_path':
				return $this->pdftoimg_path = 'var/' . $this->file_path.'img/';
			;
			break;
			
			case 'pdf_file_path_server':
				return $this->pdf_file_path_server = 'var/' . $this->file_path . $this->pdf_file;
			;
			break;
			
			case 'file_name_upload_pdf':
				$nameParts = explode('.', $this->file_name_upload);
				array_pop($nameParts);
				return $this->file_name_upload_pdf = implode('.', $nameParts).'.pdf';
			;
			break;
			
			case 'pages_pdf':
				$this->pages_pdf = array();
				if ($this->pages_pdf_count) {
					$links = array();
					$i = 1;
					while ($i <= $this->pages_pdf_count) {
						$links[] = $this->pdftoimg_path.$this->id.'-'.$i.'.png';
						$i++;
					};
					$this->pages_pdf = $links;
				}
				return $this->pages_pdf;
				break;
				
			case 'pdftoimg_url_path':
					return $this->pdftoimg_url_path = erLhcoreClassSystem::instance()->wwwDir() . '/var/' . $this->file_path.'img/';
					;
					break;
					
			case 'pages_pdf_url':
				$this->pages_pdf_url = array();
				if ($this->pages_pdf_count) {
					$baseDir = erLhcoreClassSystem::instance()->wwwDir();
					foreach ($this->pages_pdf as $img) {
						$this->pages_pdf_url[] =  $baseDir . '/' . $img;
					}				
				};
				return $this->pages_pdf_url;
				break;
				
			case 'book_data':
					return array('baseImagesDesgin' => erLhcoreClassDesign::design('images/BookReader/images').'/','baseImg' => $this->id.'-','numLeafs' => $this->pages_pdf_count,'bookTitle' => $this->name,'urlPath' => $this->pdftoimg_url_path); 
				break;
				
			default:
				;
			break;
		}
	}
	
	public function removeFile() {
		if ( $this->has_file && file_exists($this->file_path_server) ) {
			unlink($this->file_path_server);
			erLhcoreClassFileUpload::removeRecursiveIfEmpty('var/', $this->file_path);
		}
		
		$this->removePDF();
			
	}
	
	public function removePDF(){
		if ($this->pdf_file != '' &&  file_exists($this->pdf_file_path_server)) {
			unlink($this->pdf_file_path_server);
		}
		
		if ($this->pdf_to_img_converted && $this->pages_pdf_count > 0) {
			foreach ($this->pages_pdf as $pdfFilePath) {
				if (file_exists($pdfFilePath)) {
					unlink($pdfFilePath);
				}
			}
		}
		
	}
	
	public static function getList($paramsSearch = array())
	{
		$paramsDefault = array('limit' => 32, 'offset' => 0);

		$params = array_merge($paramsDefault,$paramsSearch);

		$session = erLhcoreClassDocShare::getSession();
		$q = $session->createFindQuery( 'erLhcoreClassModelDocShare' );

		$conditions = array();

		if (isset($params['filter']) && count($params['filter']) > 0)
		{
			foreach ($params['filter'] as $field => $fieldValue)
			{
				$conditions[] = $q->expr->eq( $field, $q->bindValue($fieldValue) );
			}
		}

		if (isset($params['filterin']) && count($params['filterin']) > 0)
		{
			foreach ($params['filterin'] as $field => $fieldValue)
			{
				$conditions[] = $q->expr->in( $field, $fieldValue );
			}
		}

		if (isset($params['filterlt']) && count($params['filterlt']) > 0)
		{
			foreach ($params['filterlt'] as $field => $fieldValue)
			{
				$conditions[] = $q->expr->lt( $field, $q->bindValue($fieldValue) );
			}
		}

		if (isset($params['filtergt']) && count($params['filtergt']) > 0)
		{
			foreach ($params['filtergt'] as $field => $fieldValue)
			{
				$conditions[] = $q->expr->gt( $field,$q->bindValue( $fieldValue ));
			}
		}

		if (count($conditions) > 0)
		{
			$q->where(
					$conditions
			);
		}

		$q->limit($params['limit'],$params['offset']);

		$q->orderBy(isset($params['sort']) ? $params['sort'] : 'id DESC' );

		$objects = $session->find( $q );

		return $objects;
	}

	public static function fetch($id) {
		$Faq = erLhcoreClassDocShare::getSession()->load( 'erLhcoreClassModelDocShare', (int)$id );
		return $Faq;
	}

	public function saveThis()
	{
		erLhcoreClassDocShare::getSession()->saveOrUpdate($this);
	}

	public function removeThis() {
		erLhcoreClassDocShare::getSession()->delete( $this );
	}

	public $id = null;
	public $name = '';
	public $desc = '';
	public $user_id = 0;
	public $active = 1;
	public $converted = 0;
	public $file_name = '';		
	public $file_path = '';		
	public $file_name_upload = '';		
	public $file_size = 0;		
	public $type = '';		
	public $ext = '';		
	public $pdf_file = '';		
	public $pages_pdf_count = 0;		
	public $pdf_to_img_converted = 0;		
}

?>