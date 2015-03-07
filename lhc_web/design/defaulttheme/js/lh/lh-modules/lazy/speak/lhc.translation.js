

module.exports = (function() {

	function LHCTranslationText() {	
				
	};
		
	LHCTranslationText.prototype.startTranslation = function(params)
	{
		// Disable buttons
		params.btn.prop('disabled','disabled');		
		params.btn.button('loading');
	    
		jQuery.getJSON(WWW_DIR_JAVASCRIPT + 'translation/starttranslation/' + params['chat_id'] + '/' +jQuery('#id_chat_locale_'+params['chat_id']).val()+'/'+jQuery('#id_chat_locale_to_'+params['chat_id']).val(), function(data){
			
			// Handle errors
			jQuery('#main-user-info-translation-'+params['chat_id']+' > div.alert').remove();
			jQuery('#main-user-info-translation-'+params['chat_id']).prepend(data.result);	
						
			// Don't do anything like it's some error just
			if (data.error === false) {				
				if (data.translation_status === true) // User started translation process
				{				
					jQuery('#id_chat_locale_'+params['chat_id']+' option[value="' + data.chat_locale + '"]').prop('selected',true);
					jQuery('#id_chat_locale_to_'+params['chat_id']+' option[value="' + data.chat_locale_to + '"]').prop('selected',true);
					
					// Clear current chat messages
					jQuery('#messagesBlock-'+params['chat_id']).html('');
					
					// Just let the core to do the hard job
					lhinst.updateChatLastMessageID(params['chat_id'],0);
					lhinst.syncadmincall();	
					
					// Restore button
					jQuery('.translate-button-'+params['chat_id']).addClass('btn-success');		
					
				} else { // User stopped translation process
					
					// Restore button
					jQuery('.translate-button-'+params['chat_id']).removeClass('btn-success');
					
					// Restore to default options
					jQuery('#id_chat_locale_'+params['chat_id']+' option[value="0"]').prop('selected',true);
					jQuery('#id_chat_locale_to_'+params['chat_id']+' option[value="0"]').prop('selected',true);						
				}
			} else {
				// There was an error so show tab for user
				jQuery('#chat-tab-items-'+params['chat_id']+' a[href="#main-user-info-translation-'+params['chat_id']+'"]').tab('show');
			}
			
			params.btn.button('reset');
			params.btn.prop('disabled','');
			
		});
	};
		
	return new LHCTranslationText();
})();