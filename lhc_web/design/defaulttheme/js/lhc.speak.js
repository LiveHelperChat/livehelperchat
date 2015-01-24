var LHCSpeechToTextCallbackListener = (function() {
	
	function LHCSpeechToTextCallbackListener(params) {
		this.recognizing = false;
		this.startOnEnd = false;
		this.final_transcript = ''; 	
		this.chat_id = params['chat_id'];	
		this.recognition = params['recognition'];
		this.originText = $('#CSChatMessage-'+this.chat_id).val() != '' ? $('#CSChatMessage-'+this.chat_id).val()+' ' : '';	
	}
	
	LHCSpeechToTextCallbackListener.prototype.onstart = function(params)
	{
		$('#CSChatMessage-'+this.chat_id).addClass('admin-chat-mic');
		$('#user-chat-status-'+this.chat_id).removeClass('icon-user').addClass('icon-mic');
		$('#mic-chat-'+this.chat_id).addClass('icon-mic-recording').html(this.recognition.lang);
		$('#user-is-typing-'+this.chat_id).html('Speak now.').css("visibility","visible");
	}
	
	LHCSpeechToTextCallbackListener.prototype.onend = function(params)
	{
		$('#user-chat-status-'+this.chat_id).addClass('icon-user').removeClass('icon-mic');
		$('#CSChatMessage-'+this.chat_id).removeClass('admin-chat-mic');
		$('#mic-chat-'+this.chat_id).removeClass('icon-mic-recording').html('');
		$('#user-is-typing-'+this.chat_id).html('');
		
		if (this.startOnEnd == true) {			
			this.originText = '';
			this.final_transcript = '';
			this.startOnEnd = false;
			this.recognition.start();
		}		
	}
	
	LHCSpeechToTextCallbackListener.prototype.onerror = function(event){
		if (event.error == 'no-speech') {
			$('#user-is-typing-'+this.chat_id).html('No speech was detected.').css("visibility","visible");
	    }
	    if (event.error == 'audio-capture') {
	    	 $('#user-is-typing-'+this.chat_id).html('No microphone was found.').css("visibility","visible");
	    }
	    if (event.error == 'not-allowed') {			 
	        $('#user-is-typing-'+this.chat_id).html('Permission to use microphone was denied.').css("visibility","visible");
	    }
	}
	
	LHCSpeechToTextCallbackListener.prototype.onresult = function(event)
	{
		if (this.startOnEnd == false) { // Do not replace last text like user already clicked send message
			var interim_transcript = '';
		    for (var i = event.resultIndex; i < event.results.length; ++i) {
		      if (event.results[i].isFinal) {
		        this.final_transcript += event.results[i][0].transcript;
		      } else {
		        interim_transcript += event.results[i][0].transcript;
		      }
		    }
		    if (interim_transcript != ''){
		    	$('#user-is-typing-'+this.chat_id).html(interim_transcript).css("visibility","visible");
		    } else {
		    	$('#user-is-typing-'+this.chat_id).html('').css("visibility","hidden");
		    }
		   
		    $('#CSChatMessage-'+this.chat_id).val(this.originText + this.final_transcript + interim_transcript).focus();
		    
		    // Pretend that operator is typing
		    lhinst.operatorTypingCallback(this.chat_id);
	    }	    
	}
	
	return LHCSpeechToTextCallbackListener;
})();

var LHCSpeechToText = (function() {

	function LHCSpeechToText() {	
		if (!('webkitSpeechRecognition' in window)) {
			  alert("Sorry but only chrome is supported");		
			  this.browserSupported = false;
		} else {
			this.recognizing = false;
			this.browserSupported = true; 	
			this.final_transcript = ''; 	
			this.chat_id = false;
			
			this.chatDialect = [];
		}		
	};
	
	LHCSpeechToText.prototype.stopSpeech = function()
	{
		if (this.browserSupported == true)
		{
			if (this.recognizing == true) {
				this.recognizing = false;			
				this.recognition.stop();			
			}
		}
	}
	
	LHCSpeechToText.prototype.messageSend = function() {	
		if (this.browserSupported == true)
		{
			this.recognition.callbackHandler.startOnEnd = true;
			this.recognition.stop();
		}
	}
	
	LHCSpeechToText.prototype.setChatDialect = function(chat_id, dialect) {
		this.chatDialect[chat_id] = dialect;
	};
	
	LHCSpeechToText.prototype.getChatDialectAndStart = function() {
		var _this = this;		
		$.getJSON(WWW_DIR_JAVASCRIPT + 'speech/getchatdialect/' + this.chat_id, function(data){
			if (data.error == false){
				_this.chatDialect[_this.chat_id] = data.dialect;
				_this.recognition.lang = _this.chatDialect[_this.chat_id];
				_this.recognition.start();
			} else {
				alert(data.result);
			}			
		});
	};
	
	LHCSpeechToText.prototype.listen = function(params)
	{
		if (this.browserSupported == true)
		{
			// Stop any previous chat listening
			if (this.chat_id !== false && this.chat_id != params['chat_id']) {
				this.stopSpeech();
			}
					
			// Set new chat id
			this.chat_id = params['chat_id'];
							
			if (this.recognizing == false) {
													
				// Start new object
				this.recognition = new webkitSpeechRecognition();
				this.recognition.continuous = true;
				this.recognition.interimResults = true;	
					
				var callbackListener = new LHCSpeechToTextCallbackListener({'chat_id' : this.chat_id,'recognition':this.recognition});
				
				this.recognition.onresult = function(event){
					callbackListener.onresult(event);
				};	
				
				this.recognition.onstart = function(event){
					callbackListener.onstart();
				};	
							
				this.recognition.onend = function(event){
					callbackListener.onend();
				};	
	
				this.recognition.onerror = function(event) {
					callbackListener.onerror(event);							   
				};
				
				this.recognition.callbackHandler = callbackListener;
				
				this.recognizing = true;
				
				
				if (this.chatDialect[this.chat_id] != undefined) {
					this.recognition.lang = this.chatDialect[this.chat_id];
					this.recognition.start();
				} else {
					this.getChatDialectAndStart();
				}
				
			} else {
				this.stopSpeech();				
			}
		}
	};
		
	return LHCSpeechToText;
})();