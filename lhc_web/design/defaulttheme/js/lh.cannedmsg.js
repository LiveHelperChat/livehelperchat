var LHCCannedMessageAutoSuggest = (function() {
	
	function LHCCannedMessageAutoSuggest(params) {
		
		this.chat_id = params['chat_id'];
		this.suggesting = false; // Are we in suggesting mode		
		this.cannedMode = false; // false - tag mode menu, true - canned mode
		
		this.currentText = null;
		this.currentKeword = null;
		
		this.initialising = false;
		this.timeoutDelay = null;
				
		var _that = this;
		
		this.textarea = jQuery('#CSChatMessage-'+this.chat_id);
		
		this.textarea.bind('keyup', function (evt) {
			
			if (evt.key == '#' || evt.keyCode == 51 || evt.keyCode == 222) {	
				_that.currentText = _that.textarea.val();				
				_that.showSuggester();	
			} else if (evt.keyCode == 32 && _that.suggesting == true) {
				_that.stopSuggesting();
			} else if (_that.suggesting == true && evt.keyCode != 38 && evt.keyCode != 40 && evt.keyCode != 39 && evt.keyCode != 37 && evt.keyCode != 13) {
				
				if (_that.currentText !== _that.textarea.val()) { // Show suggester only if text is different
					_that.showSuggester();
					_that.currentText = _that.textarea.val();					
				}
				
			} else if (_that.suggesting == true && (evt.keyCode == 37 || evt.keyCode == 39) && _that.cannedMode === false) {
				
				var oldKeyword = _that.currentKeword;
				
				if (oldKeyword !== _that.extractKeyword()) { // Only if keyword is different, happens if we migrate from canned messages to normal list.
					_that.showSuggester();					
				}
				
			} else if (_that.suggesting == false && (evt.keyCode == 39 || evt.keyCode == 37 || evt.keyCode == 8)) { // Perhaps user moved cursor to the right				
				if (_that.extractKeyword() !== null) {					
					_that.showSuggester();
				}				
			}
		});
		
		this.textarea.bind('keydown', function (evt) {
			if (_that.suggesting == true) {	
				if (evt.keyCode == 38) {
					_that.moveAction('up');
					evt.preventDefault();
					evt.stopImmediatePropagation();
				} else if (evt.keyCode == 40) {					
					_that.moveAction('down');					
					evt.preventDefault();
				} else if (evt.keyCode == 39 || evt.keyCode == 13) { // right arrow OR enter
					if (_that.cannedMode === false) {
						$('#canned-hash-'+_that.chat_id+' > li.current-item a').trigger( "click" );								
					} else {
						$('#canned-hash-current-'+_that.chat_id+' > ul > li.current-item > span.canned-msg').trigger( "click" );
					}					
					evt.preventDefault();
					evt.stopImmediatePropagation();
				} else if (evt.keyCode == 37) { // left arrow
					if (_that.cannedMode === true) {
						$('#canned-hash-current-'+_that.chat_id+' > ul > li.current-item > span.left-return').trigger( "click" );
						evt.preventDefault();
						evt.stopImmediatePropagation();
					}
				}
			} 
		});
	}
	
	LHCCannedMessageAutoSuggest.prototype.moveAction = function(action)
	{
		if (this.cannedMode === false) {
			var current = $('#canned-hash-'+this.chat_id+' > li.current-item');
		} else {
			var current = $('#canned-hash-current-'+this.chat_id+' > ul > li.current-item');
		}
		
		if (action == 'up') {			
			var prev = current.prev();			
			if (prev.is('li')){
				current.removeClass('current-item');
				prev.addClass('current-item');
			}			
		} else if(action == 'down') {
			var next = current.next();			
			if (next.is('li')){
				current.removeClass('current-item');
				next.addClass('current-item');
			}
		}
	}
	
	LHCCannedMessageAutoSuggest.prototype.stopSuggesting = function()
	{
		this.textarea.parent().find('.canned-suggester').remove();
		this.suggesting = false;
		this.cannedMode = false;
		this.currentText = null;
		this.currentKeword = null;
	}
	
	LHCCannedMessageAutoSuggest.prototype.extractKeyword = function()
	{
		var caretPos = this.textarea[0].selectionStart;
		currentValue = this.textarea.val();
		var keyword = '';
		
		for (i = caretPos; i > 0; i--) {
			char = currentValue.substring(i-1, i);
			if (char == ' ') {	
				this.currentKeword = null;
				return null;
			} else if (char == '#') {
				this.currentKeword = keyword;
				return keyword;				
			} else {							
				keyword = char + keyword;
			}		
		}		
		
		this.currentKeword = null;
		return null;
	}
	
	LHCCannedMessageAutoSuggest.prototype.showSuggester = function()
	{
		var _that = this;
		
		this.extractKeyword();	
		this.cannedMode = false;
		
		if (this.currentKeword !== null) {	
						
			if (this.initialising === false) {
				
				this.initialising = true;
				
				$.getJSON(WWW_DIR_JAVASCRIPT + 'cannedmsg/showsuggester/' + this.chat_id,{keyword : this.currentKeword}, function(data) {			
					_that.textarea.parent().find('.canned-suggester').remove();
		    		_that.textarea.before(data.result);
		    		_that.initSuggester();
		    		_that.initialising = false;
		    		_that.suggesting = true;
		    	});
				
			} else {								
				clearTimeout(this.timeoutDelay);
				this.timeoutDelay = setTimeout(function(){
					_that.showSuggester();
				},500);				
			}
			
		} else {
			this.stopSuggesting();
		}
	}
	
	LHCCannedMessageAutoSuggest.prototype.initSuggester = function()
	{
		var _that = this;
		$('#canned-hash-'+this.chat_id+' > li:first-child').addClass('current-item');
		
		$('#canned-hash-'+this.chat_id+' > li > a').click(function() {
			
			_that.cannedMode = true;
			
			var content = $('#canned-hash-current-'+_that.chat_id);
			content.html('').show();
			$(this).parent().find('ul').clone().appendTo(content);
			content.find('ul > li:first-child').addClass('current-item');
			
			var container = $(this).parent().parent();
			container.hide();
			
			content.find('span.canned-msg').click(function(){
								
				// Insert selected text
				var caretPos = _that.textarea[0].selectionStart,
		        currentValue = _that.textarea.val();
				
				var textBeforeCursor = currentValue.substring(0, caretPos - 1 - _that.currentKeword.length) + $(this).attr('data-msg');
				_that.textarea.val(textBeforeCursor + currentValue.substring(caretPos));	
				
				// Set cursor position
				if ('selectionStart' in _that.textarea[0]) {
					_that.textarea[0].selectionStart = textBeforeCursor.length;
					_that.textarea[0].selectionEnd = textBeforeCursor.length;
		        } else if (_that.textarea[0].setSelectionRange) {
		        	_that.textarea[0].setSelectionRange(textBeforeCursor.length, textBeforeCursor.length);
		        } else if (_that.textarea[0].createTextRange) {
		            var range = _that.textarea[0].createTextRange();
		            range.collapse(true);
		            range.moveEnd('character', textBeforeCursor.length);
		            range.moveStart('character', textBeforeCursor.length);
		            range.select();
		        }
				
				_that.stopSuggesting();
			});
			
			content.find('span.left-return').click(function(){
				container.show();
				content.html('').hide();
				_that.cannedMode = false;
			});
		});
		
		// Show first canned message list if there is only one tag matched
		if ($('#canned-hash-'+this.chat_id+' > li').size() == 1) {
			$('#canned-hash-'+this.chat_id+' > li > a').trigger( "click" );
		}		
	}
	
	return LHCCannedMessageAutoSuggest;
})();