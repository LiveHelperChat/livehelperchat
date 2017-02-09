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
        		var settingsJSON = [];

        		panelList.each(function(indexColumn, panelListColumn) {
        			savingSettings = true;

        			var items = [];        			        			
           	                    
                	$(panelListColumn).find('.panel-dashboard').each(function(index, elem) { 
                		items.push($(elem).attr('data-panel-id'));
                    });
            	
                	settingsJSON.push(items);                	
                });

        		var toJson = Object.toJSON || window.JSON && (window.JSON.stringify || window.JSON.encode) || $.toJSON;
        		        		
        		$.post(WWW_DIR_JAVASCRIPT + 'user/setsettingajaxraw/dwo',{'value':toJson(settingsJSON)}, function() {
            		savingSettings = false;
        		});
    		}    		    		 
       }
    });
});