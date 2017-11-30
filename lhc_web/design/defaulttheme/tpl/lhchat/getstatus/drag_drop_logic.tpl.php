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

        if (typeof posContainer[2] !== 'undefined' && posContainer[2] > 0){
            this.pendingHeight = posContainer[2];
        }
};

this.addEvent(domContainer, 'dragstart', function (event) {	
		lhc_obj.removeById('<?php echo $chatCSSPrefix?>_overlapse');
		var style = window.getComputedStyle(event.target, null);
		lhc_obj.offset_data = (parseInt(style.getPropertyValue("<?php echo $currentPosition['pos'] == 'r' ? 'right' : 'left'?>"),10) + (<?php echo $currentPosition['pos'] == 'r' ? '' : '-'?>event.clientX)) + ',' + (parseInt(style.getPropertyValue("<?php echo $currentPosition['posv'] == 't' ? 'top' : 'bottom' ?>"),10)<?php echo $currentPosition['posv'] == 't' ? '-' : '+' ?>event.clientY);
	    try {
	    	event.dataTransfer.setData("text/plain",lhc_obj.offset_data); 
	    } catch (e){};
	    lhc_obj.is_dragging = true;
	    domContainer.style.zIndex=2147483647;
	    theKid = document.createElement("div");
		theKid.innerHTML = '';
		theKid.setAttribute('id','<?php echo $chatCSSPrefix?>_overlapse');
		theKid.style.cssText = "position:absolute;height:" + domContainer.style.height + ";width:100%;";			
	    domContainer.insertBefore(theKid, domContainer.firstChild);
});
  	 
this.addEvent(domContainer, 'dragenter', function (e) {
		lhc_obj.is_dragging = true;	
    	return false;
});

if (!this.dragAttatched) {
	this.dragAttatched = true;
	this.addEvent(document.body, 'drop', function (event) {	
			if (lhc_obj.is_dragging == true) {
				
				domContainer = document.getElementById(domContainerId);				
				domContainer.style.zIndex=2147483646;
				lhc_obj.is_dragging = false;
				lhc_obj.removeById('<?php echo $chatCSSPrefix?>_overlapse');
			    var offset = lhc_obj.offset_data.split(',');
			   
			    dm = domContainer;
			    
			    var w = window,
				    d = document,
				    e = d.documentElement,
				    g = d.getElementsByTagName('body')[0],
				    x = w.innerWidth || e.clientWidth || g.clientWidth,
				    y = w.innerHeight|| e.clientHeight|| g.clientHeight;
				    
				var cookiePos = '';
				<?php if ($currentPosition['pos'] == 'r') : ?>		  				
					var rightpos = (parseInt(offset[0],10)-event.clientX);
			    	rightpos = rightpos < 0 ? 0 : rightpos;				    				    	
			    	if (x < (rightpos + parseInt(dm.offsetWidth)+20)){
			    		rightpos = x - parseInt(dm.offsetWidth)-10;
			    	};			    				    		
			    	dm.style.right = rightpos + 'px';
			    	cookiePos += dm.style.right;			    	   	
			    <?php else : ?>
			    	var leftpos = (event.clientX + parseInt(offset[0],10));
			    	leftpos = leftpos < 0 ? 0 : leftpos;			    	
			    	if (x < (leftpos + parseInt(dm.offsetWidth)+20)){
			    		leftpos = x - parseInt(dm.offsetWidth)-10;
			    	};			    	
			    	dm.style.left = leftpos + 'px';
			    	cookiePos += dm.style.left;	
			    <?php endif;?>
		    		    
			    <?php if ($currentPosition['posv'] == 't') : ?>
			    var toppos = (event.clientY + parseInt(offset[1],10));
			    toppos = toppos < 0 ? 0 :  toppos;
			    if (y < (toppos + parseInt(dm.offsetHeight))){
			    		toppos = y - parseInt(dm.offsetHeight);
			    };	
			    dm.style.top = toppos + 'px';
			    cookiePos += ","+dm.style.top;			    
			    <?php else : ?>
			    var botpos = (-event.clientY + parseInt(offset[1],10));
		    	botpos = botpos < 0 ? 0 :  botpos;			    	
		    	if (y < (botpos + parseInt(dm.offsetHeight))){
		    		botpos = y - parseInt(dm.offsetHeight);
		    	};				    	
		    	dm.style.bottom = botpos + 'px';
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
			    
			    var w = window,
				    d = document,
				    e = d.documentElement,
				    g = d.getElementsByTagName('body')[0],
				    x = w.innerWidth || e.clientWidth || g.clientWidth,
				    y = w.innerHeight|| e.clientHeight|| g.clientHeight;
				    				    			
			    <?php if ($currentPosition['pos'] == 'r') : ?>	
			    	var rightpos = (parseInt(offset[0],10)-event.clientX);
			    	rightpos = rightpos < 0 ? 0 : rightpos;				    				    	
			    	if (x < (rightpos + parseInt(dm.offsetWidth)+20)){
			    		rightpos = x - parseInt(dm.offsetWidth)-10;
			    	};			    				    		
			    	dm.style.right = rightpos + 'px';	
			    <?php else : ?>
			    	var leftpos = (event.clientX + parseInt(offset[0],10));
			    	leftpos = leftpos < 0 ? 0 : leftpos;			    	
			    	if (x < (leftpos + parseInt(dm.offsetWidth)+20)){
			    		leftpos = x - parseInt(dm.offsetWidth)-10;
			    	};				    	
			    	dm.style.left = leftpos + 'px';
			    <?php endif; ?>	
		   		 
			    <?php if ($currentPosition['posv'] == 't') : ?>			   
			    	var toppos = (event.clientY + parseInt(offset[1],10));
			    	toppos = toppos < 0 ? 0 :  toppos;			    	
			    	if (y < (toppos + parseInt(dm.offsetHeight))){
			    		toppos = y - parseInt(dm.offsetHeight);
			    	};			    	
			    	dm.style.top = toppos + 'px';
			    <?php else : ?>
			        var botpos = (-event.clientY + parseInt(offset[1],10));
			    	botpos = botpos < 0 ? 0 :  botpos;			    	
			    	if (y < (botpos + parseInt(dm.offsetHeight))){
			    		botpos = y - parseInt(dm.offsetHeight);
			    	};				    	
			    	dm.style.bottom = botpos + 'px';					    		    	
			    <?php endif; ?>
			    			  			   				   				   				    
			    event.preventDefault();
			    return false;	
		    }		    		
	});
};
