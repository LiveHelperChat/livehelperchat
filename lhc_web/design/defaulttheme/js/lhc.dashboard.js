$( document ).ready(function() {
	$('#dashboard-body, #onlineusers, #map').popover({
		  trigger:'hover',
		  html : true, 
		  container: 'body',
		  selector: '[data-toggle="popover"]',
		  content: function () {
			 if ($(this).is('[data-popover-content]')) {
				 return $('#'+$(this).attr('data-popover-content')+'-'+$(this).attr('data-chat-id')).html();
		     } else {
		    	 return $('#popover-content-'+$(this).attr('data-chat-id')).html();
			 }
		  },
		  title: function () {
			 if ($(this).is('[data-popover-title]')) {
				 return $('#'+$(this).attr('data-popover-title')+'-'+$(this).attr('data-chat-id')).html();
			 } else {
				 return  $('#popover-title-'+$(this).attr('data-chat-id')).html();
			 }
		  }
	});
	
    $(".btn-block-department").on("click", "[data-stopPropagation]", function(e) {
        e.stopPropagation();
    });

    var panelList = $( ".sortable-column-dashboard" );

    var savingSettings = false;
    
    panelList.sortable({
        connectWith:".sortable-column-dashboard",
        opacity: 0.7,       
        handle: ".panel-heading",         
        items: '> div',
        update: function() {
        	if (savingSettings == false)
    		{
                var settings = '';            
            	panelList.each(function(indexColumn, panelListColumn) {
        			savingSettings = true;
                	if (indexColumn > 0) {
                		settings = settings + '|';            		
                    }
                    
                	$(panelListColumn).find('.panel-dashboard').each(function(index, elem) { 
                    	if (index > 0) {
                    		settings = settings + ',';
                        }                  
                        settings = settings + $(elem).attr('data-panel-id');
                    });
                });
            	
            	$.post(WWW_DIR_JAVASCRIPT + 'user/setsettingajaxraw/dwo',{'value':settings}, function() {
            		savingSettings = false;
        		});
    		}    		    		 
       }
    });
});