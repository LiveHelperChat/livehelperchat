$( document ).ready(function() {
	$('#map').popover({
		  trigger:'hover',
		  html : true, 
		  selector: '[data-toggle="popover"]',
		  template : '<div class="popover" role="tooltip"><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
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
				 return $('#popover-title-'+$(this).attr('data-chat-id')).html();
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
        handle: ".card-header",
        items: '> div',
        update: function() {
        	if (savingSettings == false)
    		{
        		var settingsJSON = [];

        		panelList.each(function(indexColumn, panelListColumn) {
        			savingSettings = true;

        			var items = [];        			        			
           	                    
                	$(panelListColumn).find('.card-dashboard').each(function(index, elem) {
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