//var chatWindow = require('./lh-modules/chat-window');

__webpack_public_path__ = window.WWW_DIR_LHC_WEBPACK;

(function() { 
	  global.lhc = {
			previewChat : function(chat_id, event) {

                var keyword = '';
                if (event) {
                    keyword = typeof event.getAttribute('data-keyword') !== 'undefined' ? event.getAttribute('data-keyword') : '';
                }

				this.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/previewchat/'+chat_id + '?keyword=' + (keyword || '') });
			},

          	previewChatArchive : function(archive_id, chat_id, event) {
                var keyword = '';
                if (event) {
                    keyword = typeof event.getAttribute('data-keyword') !== 'undefined' ? event.getAttribute('data-keyword') : '';
                }
				this.revealModal({'url':WWW_DIR_JAVASCRIPT+'chatarchive/previewchat/'+archive_id+'/'+chat_id + '?keyword=' + (keyword || '')});
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
