

module.exports = (function() {

	function LHCTranslationText() {	
				
	};

    LHCTranslationText.prototype.startAutoTranslation = function(params)
    {
        lhc.revealModal({
            'url':WWW_DIR_JAVASCRIPT+'chat/singleaction/' + params['chat_id'] + '/translation',
            'showcallback' : function() {
                 jQuery('#live_translations_'+params['chat_id']).attr('checked','checked');
                 if (params['old']) {
                    jQuery('#chat_auto_translate_'+params['chat_id']).attr('checked','checked');
                 }
                 lhc.methodCall('lhc.translation','startTranslation',{
                    'btn': params['btn'], 
                    'chat_id': params['chat_id'],
                    'auto_hide': true
                })
        }});
    };

	LHCTranslationText.prototype.startTranslation = function(params)
	{
		// Disable buttons
		params.btn.prop('disabled','disabled');		
		params.btn.button('loading');
        jQuery('.translate-button-'+params['chat_id']).prop('disabled','disabled').button('loading');	

        jQuery.postJSON(WWW_DIR_JAVASCRIPT + 'translation/starttranslation/' + params['chat_id'] + '/' +jQuery('#id_chat_locale_'+params['chat_id']).val()+'/'+jQuery('#id_chat_locale_to_'+params['chat_id']).val(), {
            live_translations: jQuery('#live_translations_'+params['chat_id']).is(':checked'),
            translate_old: jQuery('#chat_auto_translate_'+params['chat_id']).is(':checked')
        }, function(data) {
			
			// Handle errors
			jQuery('#main-user-info-translation-'+params['chat_id']+' > div.alert').remove();
			jQuery('#main-user-info-translation-'+params['chat_id']).prepend(data.result);	
						
			// Don't do anything like it's some error just
			if (data.error === false) {				
				if (data.translation_status === true) // User started translation process
				{				
					// Clear current chat messages
					jQuery('#messagesBlock-'+params['chat_id']).html('');
					
					// Just let the core to do the hard job
					lhinst.updateChatLastMessageID(params['chat_id'],0);
					lhinst.syncadmincall();	
					
					// Restore button
					jQuery('.translate-button-'+params['chat_id']).addClass('btn-outline-success');		
				} else { // User stopped translation process
					// Restore button
					jQuery('.translate-button-'+params['chat_id']).removeClass('btn-outline-success');
				}

                if (params['auto_hide']) {
                    $('#myModal').modal('hide');
                    lhinst.setFocus(params['chat_id']);
                }

			} else {
				// There was an error so show tab for user
				jQuery('#chat-tab-items-'+params['chat_id']+' a[href="#main-user-info-translation-'+params['chat_id']+'"]').tab('show');
			}
			
			params.btn.button('reset');
			params.btn.prop('disabled','');
            jQuery('.translate-button-'+params['chat_id']).prop('disabled','').button('reset');
			
		});
	};

    LHCTranslationText.prototype.translateMessage = function(params) {
        params.btn.prop('disabled', 'disabled');
        params.btn.button('loading');

        let editor = jQuery('#CSChatMessage-' + params['chat_id']);
        let value = '';

        if (editor.prop('nodeName') == 'LHC-EDITOR') {
            value = editor[0].getContent();
        } else {
            value = editor.val();
        }

        jQuery.postJSON(WWW_DIR_JAVASCRIPT + 'translation/translateoperatormessage/' + params['chat_id'], {msg: value}, function (data) {

            if (data.error === false) {

                if (editor.prop('nodeName') == 'LHC-EDITOR') {
                    editor[0].setContent(data.msg);
                } else {
                    editor.val(data.msg)
                }

            } else {
                alert(data.msg);
            }

            params.btn.button('reset');
            params.btn.prop('disabled','');
        });
    };

    LHCTranslationText.prototype.translateMessageVisitor = function(params) {
        jQuery.postJSON(WWW_DIR_JAVASCRIPT + 'translation/translatevisitormessage/' + params['chat_id'] + '/' + params['msg_id'], function (data) {
            if (data.error == false) {
                lhinst.updateMessageRowAdmin(params['chat_id'], params['msg_id']);
            } else {
                alert(data.msg);
            }
        });
    }

	return new LHCTranslationText();
})();