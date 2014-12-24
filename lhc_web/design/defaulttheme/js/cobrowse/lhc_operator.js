var LHCCoBrowserOperator = (function() {

	function LHCCoBrowserOperator(w, d, params) {
		this.$awesomebar = d.getElementById('awesomebar');
		this.$lastmessage = d.getElementById('last-message');
		this.$finishedmessage = d.getElementById('finished-message');
		this.iFrameDocument, this.mirror;
		var _this = this;

		this.initialize = params['initialize'];
		this.base = params['base'];
		this.chat_id = params['chat_id'];
		this.chat_hash = params['chat_hash'];
		this.node_js_settings = params['nodejssettings'];
		this.refreshTimeout = null;
		this.isNodeConnected = false;
		this.isInitialized = false;
		this.cursor = params['cursor'];
		this.mouseShow = false;
		this.windowScroll = false;
		this.fillTimeout = null;
		this.textSend = null;
		this.highlightedCSS = false;
		
		if (params['nodejsenabled']) {
			this.setupNodeJs();
		}
		;
		
		if (parseInt(params['cpos'].w) > 0 && parseInt(params['cpos'].wh) > 0){
			document.getElementById('center-layout').style.width = params['cpos'].w+'px';
			document.getElementById('center-layout').style.height = params['cpos'].wh+'px';
		}
		
		if (params['options'].opmouse) {
			params['options'].opmouse.click(function(){
				if ($(this).is(':checked')){
					_this.sendData('lhc_cobrowse_cmd:mouse:show');
					_this.mouseShow = true;					
				} else {
					_this.sendData('lhc_cobrowse_cmd:mouse:hide');
					_this.mouseShow = false;
				}
			});
			if (params['options'].opmouse.is(':checked')){
				this.mouseShow = true;
			};
		};
		
		if (params['options'].scroll) {
			params['options'].scroll.click(function(){
				if ($(this).is(':checked')){
					_this.sendData('lhc_cobrowse_cmd:scroll:true');
					_this.windowScroll = true;
				} else {
					_this.sendData('lhc_cobrowse_cmd:scroll:false');
					_this.windowScroll = false;
				}
			});
			if (params['options'].scroll.is(':checked')){
				this.windowScroll = true;
			};			
		};
		
		/**
		 * Setup mouse tracking
		 * */
		this.mouse = {
				x : 0,
				y : 0				
		};
		
		this.mouseTimeout = null;
		this.mouseEventListenerCallback = function(e) {
			_this.mouseEventListener(e);
		};

		this.treeMirrorParams = {
			createElement : function(tagName) {
				if (tagName == 'SCRIPT') {
					var node = document.createElement('NO-SCRIPT');
					node.style.display = 'none';
					return node;
				}
				
				if (tagName == 'HEAD') {
					var node = document.createElement('HEAD');
					node.appendChild(document.createElement('BASE'));
					node.firstChild.href = _this.base;
					return node;
				}
			},
			setAttribute : function(node, attr, val) {
				
				// don't mess with our helper iframe, strange things starts to happen without this :) 
				if (node.nodeName == 'IFRAME' && attr == 'id' && val == 'lhc_iframe') {
					// Remove iframe after tree mirror has done it's job
					setTimeout(function(){
						$(_this.iFrameDocument).find('#lhc_container').remove();	
					},2000);					
					return true;
				}
				
				// remove anchors's onclick dom0-style handlers so they
				// don't mess with our click handler and don't produce errors	
				if (node.nodeName == 'A' && attr == 'onclick') {
					return true
				}
			}
		};

		w.onIframeLoaded = function() {
			_this.iFrameDocument = w.frames['content'].document
			
			_this.iFrameDocument.addEventListener('mousemove', _this.mouseEventListenerCallback, false);
			
			// Override behavior for links: instead of reloading an iframe,
			// use them via our "browser" so that mirrors stay in sync.
			_this.iFrameDocument.onclick = function(e) {							
				if (e.ctrlKey) {					
					if (!(e.target && e.target.tagName == 'INPUT' && (e.target.type == 'radio' || e.target.type == 'checkbox')) ){
						e.preventDefault();
					};
					
					_this.sendClickCommand(e.x,e.y,_this.iFrameDocument.body.scrollLeft,_this.iFrameDocument.body.scrollTop);
				} else {
					e.preventDefault();
					// Highlight element on click
					_this.highlightElement(e.x,e.y,_this.iFrameDocument.body.scrollLeft,_this.iFrameDocument.body.scrollTop);	
				}
			};

			_this.iFrameDocument.onkeyup = function(evt) {
			    evt = evt || window.event;
			    var charCode = evt.keyCode || evt.which;
			    var charStr = String.fromCharCode(charCode);
			    _this.fillForm(charStr);	
			};
			
			if (_this.initialize !== null && _this.initialize != '') {
				_this.handleMessage(_this.initialize);
			}
			;
			
			_this.startChangesMonitoring();
		}
	};
	
	LHCCoBrowserOperator.prototype.fillForm = function()
	{
		var elements = this.iFrameDocument.getElementsByClassName('lhc-higlighted');
		for (var i = 0; i < elements.length; i++) {
			if (elements[i].tagName == 'INPUT' || elements[i].tagName == 'TEXTAREA') {
				this.textSend = elements[i].value.replace(new RegExp(':','g'),'__SPLIT__');
				if (this.isNodeConnected === true) {
					this.sendData('lhc_cobrowse_cmd:fillform:'+this.textSend); // Split is required to avoid mixing argumetns	
				} else {
					if (this.fillTimeout === null) {
						var _that = this;
						this.fillTimeout = setTimeout(function() {
							_that.sendData('lhc_cobrowse_cmd:fillform:'+_that.textSend);
							_that.fillTimeout = null;
						}, 300);
					};
				}
			}
		};
	};
	
	LHCCoBrowserOperator.prototype.highlightElement = function(x,y,l,t)
	{
		this.sendData('lhc_cobrowse_cmd:hightlight:'+x+','+y+','+l+','+t);		
		
		if (this.highlightedCSS == false)
		{
			this.highlightedCSS = true;
			var fragment = this.appendHTML('<style>.lhc-higlighted{-webkit-box-shadow: 0px 0px 20px 5px rgba(88,140,204,1);-moz-box-shadow: 0px 0px 20px 5px rgba(88,140,204,1);box-shadow: 0px 0px 20px 5px rgba(88,140,204,1);}</style>');
			if (this.iFrameDocument.body !== null) {
				this.iFrameDocument.body.insertBefore(fragment,
						this.iFrameDocument.body.childNodes[0]);
			}			
		}
	};
	
	LHCCoBrowserOperator.prototype.sendClickCommand = function(x,y,l,t)
	{
		this.sendData('lhc_cobrowse_cmd:click:'+x+','+y+','+l+','+t);	
	};
	
	LHCCoBrowserOperator.prototype.sendData = function(command)
	{
		if (this.isNodeConnected === false) {
			lhinst.addRemoteCommand(this.chat_id,command);	
		} else {
			this.socket.emit('remotecommand', {
				chat_id : this.chat_id + '_' + this.chat_hash,
				cmd : command
			});
		}
	};
	
	LHCCoBrowserOperator.prototype.mouseEventListener = function(e) {			
		var _this = this;
		if (this.iFrameDocument.body != null) {		
			var mouseX = (e.clientX || e.pageX)
					+ this.iFrameDocument.body.scrollLeft;
			var mouseY = (e.clientY || e.pageY)
					+ this.iFrameDocument.body.scrollTop;
	
			if (_this.mouse.x != mouseX || _this.mouse.y != mouseY) {
				_this.mouse.x = mouseX;
				_this.mouse.y = mouseY;
				if (this.mouseShow == true) {
					if (_this.mouseTimeout === null) {
						_this.mouseTimeout = setTimeout(function() {
							_this.sendData('lhc_cobrowse_cmd:operatorcursor:'
									+ _this.mouse.x + ',' + _this.mouse.y);
							_this.mouseTimeout = null;
						}, (_this.isNodeConnected === true) ? 20 : 1000);
					}				
				}
			}
		}
	};
	
	LHCCoBrowserOperator.prototype.setupNodeJs = function() {
		var _this = this;
		if (typeof io == "undefined") {
			var th = document.getElementsByTagName('head')[0];
			var s = document.createElement('script');
			s.setAttribute('type', 'text/javascript');
			s.setAttribute('src', this.node_js_settings.nodejssocket);
			th.appendChild(s);
			s.onreadystatechange = s.onload = function() {
				_this.setupNodeJs();
			};
		} else {
			try {
				this.socket = io.connect(this.node_js_settings.nodejshost, {
					secure : this.node_js_settings.secure
				});
				this.socket.on('connect', function() {
					_this.onConnected();
				});

				this.socket.on('reconnect_error', function() {
					_this.isNodeConnected = false;
				});

				this.socket.on('connect_timeout', function() {
					_this.isNodeConnected = false;
				});

				this.socket.on('usermessage', function(msg) {
					_this.handleMessage(msg);
				});

				this.socket.on('userleft', function(chat_id) {
					_this.userLeft(chat_id);
				});

				this.socket.on('userjoined', function(chat_id) {
					_this.userJoined(chat_id);
				});

			} catch (err) {
				console.log(err);
			}
		}
		;
	};

	LHCCoBrowserOperator.prototype.userLeft = function(chat_id) {
		$('#status-icon-sharing').addClass('eye-not-sharing');
		this.clearPage();
		
	};

	LHCCoBrowserOperator.prototype.userJoined = function(chat_id) {
		$('#status-icon-sharing').removeClass('eye-not-sharing');
		var _this = this;

		// User realoaded page and most likely stored new initializer point
		// Also this way operators can join to the same screen sharing session without any issues.
		setTimeout(function() {
			$.getJSON(WWW_DIR_JAVASCRIPT + 'cobrowse/checkinitializer/'
					+ _this.chat_id, function(data) {
				if (typeof data.empty == "undefined") {
					_this.handleMessage(data);
				}
			});
		}, 3000);

	};

	LHCCoBrowserOperator.prototype.onConnected = function() {
		this.isNodeConnected = true;
		this.socket.emit('joinadmin', {
			chat_id : this.chat_id + '_' + this.chat_hash
		});
	};

	LHCCoBrowserOperator.prototype.startChangesMonitoring = function() {
		if (this.isNodeConnected === false) {
			var _this = this;
			this.isInitialized = true;
			$.getJSON(
					WWW_DIR_JAVASCRIPT + 'cobrowse/checkmirrorchanges/'
							+ _this.chat_id, function(data) {

						if (typeof data.empty == "undefined") {
							_this.handleMessage(data);
						}
						;

						_this.refreshTimeout = setTimeout(function() {
							_this.startChangesMonitoring();
						}, 1400);

					}).fail(function() {
				_this.refreshTimeout = setTimeout(function() {
					_this.startChangesMonitoring();
				}, 1400);
			});
		}
	};

	LHCCoBrowserOperator.prototype.clearPage = function() {
		this.highlightedCSS = false;
		if (this.iFrameDocument) {
			while (this.iFrameDocument.firstChild) {
				this.iFrameDocument.removeChild(this.iFrameDocument.firstChild)
			}
		}
	};

	LHCCoBrowserOperator.prototype.visitorCursor = function(pos) {

		document.getElementById('center-layout').style.width = pos.w+'px';
		document.getElementById('center-layout').style.height = pos.wh+'px';
		
		var element = this.iFrameDocument.getElementById('user-cursor');

		if (element === null) {
			var fragment = this
					.appendHTML('<div id="user-cursor" style="z-index:99999;top:'
							+ pos.y
							+ 'px;left:'
							+ parseInt(pos.x-12)
							+ 'px;position:absolute;"><img src="'+this.cursor+'" /></div>');
			if (this.iFrameDocument.body !== null) {
				this.iFrameDocument.body.insertBefore(fragment,
						this.iFrameDocument.body.childNodes[0]);
			}
			
		} else {
			element.style.top = pos.y + 'px';		
			element.style.left = parseInt(pos.x-12) + 'px';
		}
	};

	LHCCoBrowserOperator.prototype.appendHTML = function(htmlStr) {
		var frag = this.iFrameDocument.createDocumentFragment(), temp = this.iFrameDocument
				.createElement('div');
		temp.innerHTML = htmlStr;
		while (temp.firstChild) {
			frag.appendChild(temp.firstChild);
		}
		;
		return frag;
	};

	LHCCoBrowserOperator.prototype.handleMessage = function(msg) {
		if (msg.base) {
			this.base = msg.base;			
		} else if (msg.err) {
			this.iFrameDocument.getElementById('loading').style.display = 'none';
			this.iFrameDocument.getElementById('error').innerHTML = msg.err;
			this.iFrameDocument.getElementById('error').style.display = 'block';
		} else if (msg.lmsg) {
			this.$lastmessage.innerHTML = msg.lmsg;
		} else if (msg.finished) {
			$('#status-icon-sharing').attr('title', msg.finished.text);
			if (msg.finished.status == false) {
				$('#status-icon-sharing').removeClass('eye-not-sharing');
			} else {
				$('#status-icon-sharing').addClass('eye-not-sharing');
			}
		} else if (msg.url) {
			this.$awesomebar.value = msg.url;

			// trigger treemirror's method; in our example only 'initialize' can be triggered,
			// so it's reasonable to clearPage() and (re-)instantiate the mirror here
		} else if (msg.f && msg.f == 'initialize') {
			this.clearPage();
			this.mirror = new TreeMirror(this.iFrameDocument,
					this.treeMirrorParams);
			this.mirror.initialize.apply(this.mirror, msg.args);
			
			var _this = this;
			setTimeout(function(){
				var fragment = _this.appendHTML("<style>#lhc-user-cursor{display:none!important;}.lhc-higlighted{-webkit-box-shadow: 0px 0px 20px 5px rgba(88,140,204,1);-moz-box-shadow: 0px 0px 20px 5px rgba(88,140,204,1);box-shadow: 0px 0px 20px 5px rgba(88,140,204,1);}</style>");
				if (_this.iFrameDocument.body !== null) {
					_this.iFrameDocument.body.insertBefore(fragment,
							_this.iFrameDocument.body.childNodes[0]);
				}
			},2000);			
			
			if (this.mouseShow == true) {
				this.sendData('lhc_cobrowse_cmd:mouse:show');
			};
			
			if (this.windowScroll == true) {
				this.sendData('lhc_cobrowse_cmd:scroll:true');
			};						
			
		} else if (msg.f && msg.f == 'cursor') {
			this.visitorCursor(msg.pos);
		} else if (msg.f) {
			if (typeof this.mirror != "undefined") {
				this.mirror[msg.f].apply(this.mirror, msg.args);
			}
		} else if (msg instanceof Array) {
			var _this = this;
			msg.forEach(function(subMessage) {
				try {
					_this.handleMessage(subMessage);
				} catch (err) {
					console.log(err);
				}
				;
			});
			// called when remote socket is closed
		} else if (msg.clear) {
			this.clearPage();
		} else {
			//console.log('just message: ', msg);
		}
	};

	return LHCCoBrowserOperator;
})();