<?php
/**
 * 
 * @author Remigijus Kiminas
 * 
 * @desc Main chat product to departament assignment object
 *
 */
class erLhAbstractModelProductDepartament {
    
    use erLhcoreClassDBTrait;
    
    public static $dbTable = 'lh_abstract_product_departament';
    
    public static $dbTableId = 'id';
    
    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';
    
    public static $dbSortOrder = 'DESC';
    
	public function getState()
	{
		$stateArray = array (
			'id'         	 => $this->id,
			'departament_id' => $this->departament_id,
			'product_id'	 => $this->product_id,
		);

		return $stateArray;
	}

	public function __toString()
	{
		return $this->product_id;
	}
	
	public function __get($var)
	{
	   switch ($var) {

	   	case 'left_menu':
	   	       $this->left_menu = '';
	   		   return $this->left_menu;
	   		break;

	   	case 'departament':
	   	       $this->departament = erLhcoreClassModelDepartament::fetch($this->departament_id);
	   		   return $this->departament;
	   		break;

	   	case 'name_department':
	   	       $this->name_department = $this->name.' ('.$this->departament.')';
	   		   return $this->name_department;
	   		break;

	   	case 'product':
	   	       $this->product = erLhAbstractModelProduct::fetch($this->product_id);
	   	       return $this->product;
	   	    break;

	   	case 'product_name':
	   	       $this->product_name = $this->product->name;
	   		   return $this->product_name;
	   		break;

	   	default:
	   		break;
	   }
	}
	
   	public $id = null;
	public $product_id = 0;
	public $departament_id = 0;
}

?>