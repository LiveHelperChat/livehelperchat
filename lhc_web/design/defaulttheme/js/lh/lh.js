//var chatWindow = require('./lh-modules/chat-window');

__webpack_public_path__ = window.WWW_DIR_LHC_WEBPACK;

(function() { 
	  global.lhc = {
			previewChat : function(chat_id){
				this.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/previewchat/'+chat_id});						
			},

            previewMail : function(chat_id){
				this.revealModal({'url':WWW_DIR_JAVASCRIPT+'mailconv/previewmail/'+chat_id,hidecallback : function(){
				    ee.emitEvent('unloadMailChat', ['mc'+chat_id,'preview']);
                }});
			},

          	previewChatArchive : function(archive_id, chat_id){
				this.revealModal({'url':WWW_DIR_JAVASCRIPT+'chatarchive/previewchat/'+archive_id+'/'+chat_id});
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
