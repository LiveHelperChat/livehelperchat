var LHCCannedMessageAutoSuggest = (function() {
	
	function LHCCannedMessageAutoSuggest(params) {
		
		this.chat_id = params['chat_id'];
		this.suggesting = false; // Are we in suggesting mode		
		this.cannedMode = false; // false - tag mode menu, true - canned mode
		
		this.currentText = null;
		this.currentKeword = null;

		// Uppercase related
		this.nextUppercase = false;
		this.nextUppercasePos = 0;
		this.nextUppercaseCallback = null;
		this.nextUppercaseEnabled = typeof params['uppercase_enabled'] === 'undefined' || params['uppercase_enabled'] == true;

        // General one
		var _that = this;
		
		this.textarea = jQuery('#CSChatMessage-'+this.chat_id);

		this.textarea.bind('keyup', function (evt) {

			if (_that.nextUppercaseEnabled == true)
			{
                if (_that.nextUppercase == true) {
                    clearTimeout(_that.nextUppercaseCallback);
                    _that.nextUppercaseCallback = setTimeout(function(){
                        _that.capitalizeSentences(evt);
                    },50);
                } else {
                    _that.capitalizeSentences(evt);
                }
			}


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

    /*
    * Capitalizes sentences.
    * */
    LHCCannedMessageAutoSuggest.prototype.capitalizeSentences = function(evt) {

        var originalText = this.textarea.val();
        var capText = originalText;
        var caretPos = this.textarea[0].selectionStart;

        if (evt.keyCode == 8 || evt.keyCode == 46) {
            this.nextUppercase = false;
            return;
        }

        // Replace very first character
    	if (originalText.length <= 3) {
            capText = capText.replace(capText.charAt(0),capText.charAt(0).toUpperCase());
		}

        if (this.nextUppercase == true) {
             capText = capText.substr(0, this.nextUppercasePos) + capText.charAt(this.nextUppercasePos).toUpperCase() + capText.substr(this.nextUppercasePos+1);
		}

        if (originalText.charAt(caretPos-1) == ' ' && (originalText.charAt(caretPos-2) == '.' || originalText.charAt(caretPos-2) == '?' || originalText.charAt(caretPos-2) == '!') && originalText.length == caretPos) {
            this.nextUppercase = true;
            this.nextUppercasePos = caretPos;
        } else if (this.nextUppercase == true) {
            this.nextUppercase = false;
        }

        if (confLH.content_language == 'en') {
            capText = capText.replace(/\si\s/g,' I ');
		}

		if (capText != originalText) {

            this.textarea.val(capText);

            if ('selectionStart' in this.textarea[0]) {
                this.textarea[0].selectionStart = caretPos;
                this.textarea[0].selectionEnd = caretPos;
            } else if (this.textarea[0].setSelectionRange) {
                this.textarea[0].setSelectionRange(caretPos, caretPos);
            } else if (this.textarea[0].createTextRange) {
                var range = this.textarea[0].createTextRange();
                range.collapse(true);
                range.moveEnd('character', caretPos);
                range.moveStart('character', caretPos);
                range.select();
            }
		}
	}

	LHCCannedMessageAutoSuggest.prototype.moveAction = function(action)
	{
		if (this.cannedMode === false) {
			var current = $('#canned-hash-'+this.chat_id+' > li.current-item');
		} else {
			var current = $('#canned-hash-current-'+this.chat_id+' > ul > li.current-item');
		}

		if (current.length == 0) {
			return;
		}

		if (action == 'up') {			
			var prev = current.prev();			
			if (prev.is('li')){
				current.removeClass('current-item');
				prev.addClass('current-item');
			} else {
                current.removeClass('current-item').parent().find(' > li').last().addClass('current-item');
			}
		} else if(action == 'down') {
			var next = current.next();			
			if (next.is('li')){
				current.removeClass('current-item');
				next.addClass('current-item');
			} else {
                current.removeClass('current-item').parent().find(' > li').first().addClass('current-item');
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

	this.timeoutRequest = null;

	LHCCannedMessageAutoSuggest.prototype.showSuggester = function()
	{
		var _that = this;
		
		this.extractKeyword();	
		this.cannedMode = false;
        clearTimeout(this.timeoutRequest);

		if (this.currentKeword !== null) {	

			this.suggesting = true;

			this.timeoutRequest = setTimeout(function () {

				$.getJSON(WWW_DIR_JAVASCRIPT + 'cannedmsg/showsuggester/' + _that.chat_id,{keyword : _that.currentKeword}, function(data) {
					_that.textarea.parent().find('.canned-suggester').remove();
					_that.textarea.before(data.result);
					_that.initSuggester();
				});

			}, 200);

		} else {

			this.stopSuggesting();
		}
	}

	LHCCannedMessageAutoSuggest.prototype.initSuggester = function()
	{
		var _that = this;
		$('#canned-hash-'+this.chat_id+' > li:last-child').addClass('current-item');
		
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
                var textAppend = $(this).attr('data-msg');

                var textBeforeCursor = currentValue.substring(0, caretPos);

                // Strip keyword
                var index = textBeforeCursor.lastIndexOf('#');
                textBeforeCursor =  textBeforeCursor.substring(0, index) + textAppend;

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