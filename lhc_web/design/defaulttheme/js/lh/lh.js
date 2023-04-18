//var chatWindow = require('./lh-modules/chat-window');

__webpack_public_path__ = window.WWW_DIR_LHC_WEBPACK;

(function() {

    var prevChatId = 0;
    var nextChatId = 0;

     function showPreviewClickListener(evt) {

        if (evt.altKey && (evt.which == 38 || evt.which == 40)) {
            if (evt.which == 40) {
                if (nextChatId > 0) {
                    $('#preview-item-'+nextChatId).click();
                }
            } else if (prevChatId > 0) {
                $('#preview-item-'+prevChatId).click();
            }
            return;
        }
     }

	 global.lhc = {
			previewChat : function(chat_id, event) {

                var keyword = '',navigatorList = '';

                prevChatId = 0, nextChatId = 0;

                if (event) {
                    keyword = typeof event.getAttribute('data-keyword') !== 'undefined' ? event.getAttribute('data-keyword') : '';

                    if (event.classList.contains('preview-list')) {
                        $('.preview-list').removeClass('bg-current');
                        $(event).addClass('bg-current');
                    }

                    navigatorList = this.attachNavigator(chat_id, event);

                }

				this.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/previewchat/'+chat_id + '?keyword=' + (keyword || '') + navigatorList,
                    'showcallback' : function() {
                        document.addEventListener("keyup", showPreviewClickListener);
                    },
                    'hidecallback' : function() {
                        document.removeEventListener("keyup", showPreviewClickListener);
                }});
			},

            attachNavigator : function(chat_id, event) {
                var navigatorLink = '';

                if (typeof event.getAttribute('data-list-navigate') !== 'undefined') {

                    $('.chat-row-tr.bg-light').removeClass('bg-light');
                    $('#chat-row-tr-'+chat_id).addClass('bg-light');

                    prevChatId = $(event).parent().parent().prevAll('tr:not(.ignore-row)').first().attr('data-chat-id');
                    nextChatId = $(event).parent().parent().nextAll('tr:not(.ignore-row)').first().attr('data-chat-id');

                    if (prevChatId) {
                        navigatorLink = '&prevId=' + prevChatId;
                        document.addEventListener("keyup", showPreviewClickListener);
                    }

                    if (nextChatId) {
                        navigatorLink = navigatorLink + '&nextId=' + nextChatId;
                        document.addEventListener("keyup", showPreviewClickListener);
                    }
                }

                return navigatorLink;
            },

          	previewChatArchive : function(archive_id, chat_id, event) {
                var keyword = '',navigatorList = '';
                prevChatId = 0, nextChatId = 0;

                if (event) {
                    keyword = typeof event.getAttribute('data-keyword') !== 'undefined' ? event.getAttribute('data-keyword') : '';
                    if (event.classList.contains('preview-list')){
                        $('.preview-list').removeClass('bg-current');
                        $(event).addClass('bg-current');
                    }

                    navigatorList = this.attachNavigator(chat_id, event);
                }

				this.revealModal({'url':WWW_DIR_JAVASCRIPT+'chatarchive/previewchat/'+archive_id+'/'+chat_id + '?keyword=' + (keyword || '') + navigatorList,
                    'showcallback' : function() {
                        document.addEventListener("keyup", showPreviewClickListener);
                    },
                    'hidecallback' : function() {
                        document.removeEventListener("keyup", showPreviewClickListener);
                    }
                });
			},

			revealModal : function(params) {				
				require.ensure([], function () {
					var revealModalName = require('./lh-modules/reveal-modal');				
					revealModalName.initializeModal();
					revealModalName.revealModal(params);	
				});	
			},
			/**
			 * This can be used on any singleton class
			 * */
			methodCall : function(module,functionName,params) {
				require([], function() {
					require("./lh-modules/lazy/speak/" + module + ".js")[functionName](params);
				});
			} 
	  }
}());
