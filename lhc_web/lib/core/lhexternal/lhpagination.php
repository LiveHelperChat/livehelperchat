<?php

class lhPaginator {
    
	var $items_per_page;
	var $items_total;
	var $current_page;
	var $num_pages;
	var $mid_range;
	var $low;
	var $high;
	var $limit;
	var $return;
	var $default_ipp = 20;
	var $querystring;
    var $serverURL;
    var $lastArrayNumber;
    var $prev_page;
    var $next_page;
	
	function __construct()
	{		
		$this->mid_range = 10;		
		$url = erLhcoreClassURL::getInstance();				
		$this->current_page = ($url->getParam('page') !== null && (int)$url->getParam('page') > 0) ? (int)$url->getParam('page') : 1; // must be numeric > 0
		$this->items_per_page = $this->default_ipp;		
		$this->low = ($this->current_page-1) * $this->items_per_page;
	}

	function setItemsPerPage($itemsPerPage)
	{
	    $this->items_per_page = $itemsPerPage;		
		$this->low = ($this->current_page-1) * $this->items_per_page;
	}
	
	function paginate()
	{				
		$this->num_pages = ceil($this->items_total/$this->items_per_page);		
		if($this->current_page > $this->num_pages) $this->current_page = $this->num_pages;
		$prev_page = $this->current_page-1;
		$this->next_page = $this->current_page+1;
	
		$this->prev_page = $prev_page > 1 ? "/(page)/$prev_page" : '';
			
		if($this->num_pages > 10)
		{	
			$this->start_range = $this->current_page - floor($this->mid_range/2);
			$this->end_range = $this->current_page + floor($this->mid_range/2);

			if($this->start_range <= 0)
			{
				$this->end_range += abs($this->start_range)+1;
				$this->start_range = 1;
			}
			if($this->end_range > $this->num_pages)
			{
				$this->start_range -= $this->end_range-$this->num_pages;
				$this->end_range = $this->num_pages;
			}
			
			$this->range = range($this->start_range,$this->end_range);
						
			end($this->range);
			$this->lastArrayNumber = current($this->range);
		}
	}
}