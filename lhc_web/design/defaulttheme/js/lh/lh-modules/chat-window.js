module.exports = {
		processCollapse : function(chat_id) {
			if (!$('#chat-main-column-'+chat_id+' .collapse-right').hasClass('icon-left-circled')){
		    	$('#chat-right-column-'+chat_id).hide();
		    	$('#chat-main-column-'+chat_id).addClass('large-12');
		    	$('#chat-main-column-'+chat_id+' .collapse-right').addClass('icon-left-circled').removeClass('icon-right-circled');
	    	} else {
	    		$('#chat-right-column-'+chat_id).show();
		    	$('#chat-main-column-'+chat_id).removeClass('large-12');
		    	$('#chat-main-column-'+chat_id+' .collapse-right').removeClass('icon-left-circled').addClass('icon-right-circled');
		    	$(document).foundation('section', 'reflow');
	    	};
		}
};