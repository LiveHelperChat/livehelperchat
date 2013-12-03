domContainer.onmousedown = function(e) {domContainer.setAttribute('draggable','true');};
domContainer.onmouseup = function(e) {domContainer.setAttribute('draggable','false');};

if (this.cookieData.pos) {
  		var posContainer = this.cookieData.pos.split(',');
  		<?php if ($currentPosition['pos'] == 'r') : ?>
  		domContainer.style.right = posContainer[0];
  		<?php else : ?>
  		domContainer.style.left = posContainer[0];
  		<?php endif; ?>
  		
  		<?php if ($currentPosition['posv'] == 't') : ?>
        domContainer.style.top = posContainer[1];
        <?php else : ?>
        domContainer.style.bottom = posContainer[1];
        <?php endif;?>
};

this.addEvent(domContainer, 'dragstart', function (event) {	
		  	  
		var style = window.getComputedStyle(event.target, null);
		lhc_obj.offset_data = (parseInt(style.getPropertyValue("<?php echo $currentPosition['pos'] == 'r' ? 'right' : 'left'?>"),10) + (<?php echo $currentPosition['pos'] == 'r' ? '' : '-'?>event.clientX)) + ',' + (parseInt(style.getPropertyValue("<?php echo $currentPosition['posv'] == 't' ? 'top' : 'bottom' ?>"),10)<?php echo $currentPosition['posv'] == 't' ? '-' : '+' ?>event.clientY);
	    try {
	    	event.dataTransfer.setData("text/plain",lhc_obj.offset_data); 
	    } catch (e){};
	    lhc_obj.is_dragging = true;
	    domContainer.style.zIndex=9995;	
	    setTimeout(function(){
	    if (lhc_obj.is_dragging == true){
	    	document.getElementById(domIframe).style.marginTop = '-5000px';
	    }},5);	    
});
  	 
this.addEvent(domContainer, 'dragenter', function (e) {
		lhc_obj.is_dragging = true;		
		setTimeout(function(){
	    if (lhc_obj.is_dragging == true){
	    	document.getElementById(domIframe).style.marginTop = '-5000px';
	    }},5);    
    	return false;
});

if (!this.dragAttatched) {
	this.dragAttatched = true;
	this.addEvent(document.body, 'drop', function (event) {	
			if (lhc_obj.is_dragging == true) {
				
				domContainer = document.getElementById(domContainerId);
				
				domContainer.style.zIndex=9990;
				
				lhc_obj.is_dragging = false;
				document.getElementById(domIframe).style.marginTop = '0px';
							
			    var offset = lhc_obj.offset_data.split(',');
			   
			    dm = domContainer;
				var cookiePos = '';
				<?php if ($currentPosition['pos'] == 'r') : ?>		  				    	
			    	dm.style.right = (parseInt(offset[0],10)-event.clientX) + 'px';		
			    	cookiePos += dm.style.right;			    	   	
			    <?php else : ?>
			    	dm.style.left = (event.clientX + parseInt(offset[0],10)) + 'px';
			    	cookiePos += dm.style.left;	
			    <?php endif;?>
		    
			    <?php if ($currentPosition['posv'] == 't') : ?>
			    dm.style.top = (event.clientY + parseInt(offset[1],10)) + 'px';
			    cookiePos += ","+dm.style.top;
			    <?php else : ?>
			    dm.style.bottom = (-event.clientY + parseInt(offset[1],10)) + 'px';
			    cookiePos += ","+dm.style.bottom;		
			    <?php endif;?>
			    		    		    
			    lhc_obj.addCookieAttribute('pos',cookiePos);
			    event.preventDefault();
			    
			    domContainer.draggable = false;
			    
			    return false;    
		    };
	  });
	 	  
	this.addEvent(document.body, 'dragover', function (event) {	    	  
	    	if (lhc_obj.is_dragging == true) {    
	    		  
		  		domContainer = document.getElementById(domContainerId);
		  		
		  		domContainer.setAttribute('draggable','false');
		  			  		
		 		var offset = lhc_obj.offset_data.split(',');			    			    
			    var dm = domContainer;	
			    			
			    <?php if ($currentPosition['pos'] == 'r') : ?>	
			    	dm.style.right = (parseInt(offset[0],10)-event.clientX) + 'px';	
			    <?php else : ?>
			    	dm.style.left = (event.clientX + parseInt(offset[0],10)) + 'px';
			    <?php endif; ?>	
		   		 
			    <?php if ($currentPosition['posv'] == 't') : ?>			   
			    	dm.style.top = (event.clientY + parseInt(offset[1],10)) + 'px';
			    <?php else : ?>			    
			    	dm.style.bottom = (-event.clientY + parseInt(offset[1],10)) + 'px';
			    <?php endif; ?>
			    			  			   				   				   				    
			    event.preventDefault();
			    return false;	
		    }		    		
	});
};
