var LHCCoBrowserOperator = (function() {

	function LHCCoBrowserOperator(w, d, params) {
		this.$awesomebar = d.getElementById('awesomebar');
		this.$lastmessage = d.getElementById('last-message');
		this.$finishedmessage = d.getElementById('finished-message');
		this.iFrameDocument, this.mirror;
		var _this = this;

		this.initialize = params['initialize'];
		this.base = params['base'];
		this.chat_id = params['chat_id'] ? params['chat_id'] : params['online_user_id'];
		this.chat_hash = params['chat_hash'] ? params['chat_hash'] : params['online_user_hash'];
		this.mode_co_browse =  params['mode'] ? '/(cobrowsemode)/'+params['mode'] : '/(cobrowsemode)/chat';
		this.mode_co_browse_internal =  params['mode'] ? params['mode'] : 'chat';		
		this.node_js_settings = params['nodejssettings'];
		this.disablejs = params['disablejs'];
		this.disableiframe = typeof params['disableiframe'] != 'undefined' ? params['disableiframe'] : true;
		this.formsenabled = typeof params['formsenabled'] != 'undefined' ? params['formsenabled'] : true;
		this.refreshTimeout = null;
		this.isNodeConnected = false;
		this.isInitialized = false;
		this.cursor = params['cursor'];
		this.mouseShow = false;
		this.windowScroll = false;
		this.fillTimeout = null;
		this.textSend = null;
		this.highlightedCSS = false;
		this.httpsmode = params['httpsmode'];
		this.lhcbase = params['lhcbase'];
		this.sitehttps = false;
				
		if (this.base != '' && this.base.indexOf('https') > -1) {
			this.sitehttps = true;
		}
		
		if (params['nodejsenabled']) {
			this.setupNodeJs();
		}
		
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
		
		this.opcontrol = false;
		
		if (params['options'].opcontrol) {
			params['options'].opcontrol.click(function(){
				if ($(this).is(':checked')){			
					_this.opcontrol = true;					
				} else {					
					_this.opcontrol = false;
				}
			});
			if (params['options'].opcontrol.is(':checked')){
				this.opcontrol = true;
			};
		};
		
		this.userScrollSync = false;
		this.userScrollData = {'t':0,'l':0};
		
		if (params['options'].opscroll) {
			params['options'].opscroll.click(function(){
				if ($(this).is(':checked')){
					_this.userScrollSync = true;	
					_this.visitorScroll(_this.userScrollData);
				} else {
					_this.userScrollSync = false;
				}
			});
			if (params['options'].opscroll.is(':checked')) {			
				this.userScrollSync = true;
				this.visitorScroll(this.userScrollData);
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
				if (_this.disablejs == true && tagName == 'SCRIPT') {
					var node = document.createElement('NO-SCRIPT');
					node.style.display = 'none';
					return node;
				}
				
				if (tagName == 'SELECT') {
					var node = document.createElement('SELECT');

					if (_this.formsenabled == false) {
						node.setAttribute('disabled','disabled');
					};

					node.addEventListener('change', function(){
						_this.changeSelectValue($(node)[0].selectedIndex, _this.getSelectorQuery(node));
					}, false);
					
					return node;
				}
				
				if (tagName == 'INPUT' || tagName == 'TEXTAREA') {
					var node = document.createElement(tagName);
					
					if (_this.formsenabled == false) {
						node.setAttribute('readonly','readonly');	
					};	
					
					node.addEventListener('focus', function(){					
						_this.highlightElement(-1,-1,_this.scrollLeftGS(),_this.scrollTopGS(),_this.getSelectorQuery(node),node);
					}, false);
					
					return node;
				}				
				
				if (tagName == 'HEAD') {
					var node = document.createElement('HEAD');
					node.appendChild(document.createElement('BASE'));
					node.firstChild.href = _this.base;
					node.firstChild.id = "lhc-co-browse-base-id";
					return node;
				}
			},
			setAttribute : function(node, attr, val) {
				
				if (_this.disableiframe == true && node.nodeName == 'IFRAME' && attr == 'src') {
					node.setAttribute(attr,"javascript:void(0)"); // By settings this we will know we can't use href link
					return true;
				}
				
				// don't mess with our helper iframe, strange things starts to happen without this :) 
				if (node.nodeName == 'IFRAME' && attr == 'id' && val == 'lhc_iframe') {
					// Remove iframe after tree mirror has done it's job
					setTimeout(function(){
						$(_this.iFrameDocument).find('#lhc_container').hide().attr('id','lhc_container_dummy').find('#lhc_iframe_container').remove();	
					},2000);					
					return true;
				}
				
				// There exists original base so remove our own detected.
				if (node.nodeName == 'BASE') {
					$(_this.iFrameDocument).find('#lhc-co-browse-base-id').remove();
					if (node.attr == 'href') {
						_this.base = val;
					}
				}
				
				if (node.nodeName == 'SCRIPT' && attr == 'src') {
					if (val.indexOf('google') > -1){ // Skip all google scripts
						node.setAttribute('src',"");						
						return true;
					}
				}
				
				if (node.nodeName == 'LINK' && ( (attr == 'type' && val == 'text/css') || (attr == 'rel' && val == 'stylesheet' ))) {
					if (node.getAttribute('lhc-css') === null)
					{
						node.setAttribute('lhc-css','true');	
						// Perhaps href was already set for particular node
						if (_this.httpsmode == true && _this.sitehttps == false && node.getAttribute('href') != "") {
							node.setAttribute('href',_this.lhcbase+'/'+_this.chat_id+_this.mode_co_browse+'/?base='+encodeURIComponent(_this.base)+'&css='+encodeURIComponent(node.getAttribute('href')));						
						}
					}
				}
								
				if (node.nodeName == 'LINK' && attr == 'href') {					
					// We have to proxy CSS request because LHC is in HTTPS and user site in HTTP
					if (_this.httpsmode == true && _this.sitehttps == false && node.getAttribute('lhc-css') !== null) {
						node.setAttribute('href',_this.lhcbase +'/'+_this.chat_id+_this.mode_co_browse+'/?base='+encodeURIComponent(_this.base)+'&css='+encodeURIComponent(val));
						return true;
					}
				}
				
				// We don't need href links
				// This way it's also more secure like operator would have to type link directly to test
				if (node.nodeName == 'A' && attr == 'href') {
					node.setAttribute(attr,"javascript:void(0)");
					node.setAttribute('title',val);
					return true;
				}
				
				// remove anchors's onclick dom0-style handlers so they
				// don't mess with our click handler and don't produce errors	
				if (node.nodeName == 'A' && attr == 'onclick') {
					if (val != ''){
						node.setAttribute(attr,"javascript:void(0)"); // By settings this we will know we can't use href link
						return true;
					} else {
						return true;
					}					
				}
			}
		};
		
		w.onIframeLoaded = function() {
			_this.iFrameDocument = w.frames['content'].document
			
			_this.iFrameDocument.addEventListener('mousemove', _this.mouseEventListenerCallback, false);
			
			// Override behavior for links: instead of reloading an iframe,
			// use them via our "browser" so that mirrors stay in sync.
			_this.iFrameDocument.onclick = function(e) {							
				if (e.ctrlKey || _this.opcontrol == true) {					
					if (!(e.target && e.target.tagName == 'INPUT' && (e.target.type == 'radio' || e.target.type == 'checkbox')) ){
						e.preventDefault();
					};
										
					_this.sendClickCommand(e.x,e.y,_this.scrollLeftGS(),_this.scrollTopGS(),_this.getSelectorQuery(e.target));
										
				} else {
					e.preventDefault();
														
					// Highlight element on click
					_this.highlightElement(e.x,e.y,_this.scrollLeftGS(),_this.scrollTopGS(),_this.getSelectorQuery(e.target),e.target);	
				}
			};

			_this.iFrameDocument.onkeyup = function(evt) {
			    evt = evt || window.event;			  				
			    _this.fillForm(evt.target,_this.getSelectorQuery(evt.target));	
			};
			
			if (_this.initialize !== null && _this.initialize != '') {
				_this.handleMessage(_this.initialize);
			}
						
			_this.startChangesMonitoring();
		}
	};
	
	
	LHCCoBrowserOperator.prototype.scrollTopGS = function(stop)
	{
		if (typeof stop === 'undefined') {
			return this.iFrameDocument.documentElement.scrollTop || this.iFrameDocument.body.scrollTop;
		} else {		
			if (this.iFrameDocument.documentElement){
				try {
					this.iFrameDocument.documentElement.scrollTop = stop;
				} catch (e) {}	
			};	
			
			if (this.iFrameDocument.body) {
				try {
					this.iFrameDocument.body.scrollTop = stop;
				} catch (e) {}	
			}
		}
	};
	
	LHCCoBrowserOperator.prototype.scrollLeftGS = function(sleft)
	{
		if (typeof sleft === 'undefined') {
			return this.iFrameDocument.documentElement.scrollLeft || this.iFrameDocument.body.scrollLeft;
		} else {
			if (this.iFrameDocument.documentElement){
				try {
					this.iFrameDocument.documentElement.scrollLeft = sleft;
				} catch (e) {}	
			};		
			if (this.iFrameDocument.body) {
				try {
					this.iFrameDocument.body.scrollLeft = sleft;
				} catch (e) {}	
			}			
		}
	};
	
	
	LHCCoBrowserOperator.prototype.getSelectorQuery = function(node)
	{
		var selectorData = $(node).getSelector();				
		if (selectorData.length >= 1) {				
			return selectorData[0];
		}		
		return '';	
	};
		
	LHCCoBrowserOperator.prototype.fillForm = function(target,selector)
	{
		if (this.formsenabled == true) {
			var elements = [target];
			for (var i = 0; i < elements.length; i++) {
				if (elements[i].tagName == 'INPUT' || elements[i].tagName == 'TEXTAREA') {
					this.textSend = elements[i].value.replace(new RegExp(':','g'),'_SEL_');
					if (this.isNodeConnected === true) {					
						this.sendData('lhc_cobrowse_cmd:fillform:'+this.textSend+'__SPLIT__'+selector.replace(new RegExp(':','g'),'_SEL_')); // Split is required to avoid mixing argumetns	
					} else {
						if (this.fillTimeout === null) {
							var _that = this;
							this.fillTimeout = setTimeout(function() {
								_that.sendData('lhc_cobrowse_cmd:fillform:'+_that.textSend+'__SPLIT__'+selector.replace(new RegExp(':','g'),'_SEL_'));
								_that.fillTimeout = null;
							}, 300);
						};
					}
				}
			};
		}
	};
	
	LHCCoBrowserOperator.prototype.changeSelectValue = function(val,selector)
	{		
		if (this.formsenabled == true) {
			this.sendData('lhc_cobrowse_cmd:changeselect:'+val+'__SPLIT__'+selector.replace(new RegExp(':','g'),'_SEL_'));
		}
	};
	
	LHCCoBrowserOperator.prototype.highlightElement = function(x,y,l,t,selector,node)
	{
		this.sendData('lhc_cobrowse_cmd:hightlight:'+x+','+y+','+l+','+t+'__SPLIT__'+selector.replace(new RegExp(':','g'),'_SEL_'));		
		
		var hightlight = true;
		
		if (jQuery(node).hasClass('lhc-higlighted') == true && (node.tagName != 'INPUT' && node.tagName != 'TEXTAREA' && node.tagName != 'SELECT')) {
			hightlight = false;
		};
		
		jQuery(this.iFrameDocument).find('.lhc-higlighted').removeClass('lhc-higlighted');
		
		if (hightlight == true) {
			jQuery(node).addClass('lhc-higlighted');
		};
			
		if (this.highlightedCSS == false)
		{
			this.highlightedCSS = true;
			var fragment = this.appendHTML('<style>.lhc-higlighted{-webkit-box-shadow: 0px 0px 20px 5px rgba(88,140,204,1)!important;-moz-box-shadow: 0px 0px 20px 5px rgba(88,140,204,1)!important;box-shadow: 0px 0px 20px 5px rgba(88,140,204,1)!important;}</style>');
			if (this.iFrameDocument.body !== null) {
				this.iFrameDocument.body.insertBefore(fragment,
						this.iFrameDocument.body.childNodes[0]);
			}			
		}
	};
	
	LHCCoBrowserOperator.prototype.sendClickCommand = function(x,y,l,t,selector)
	{
		this.sendData('lhc_cobrowse_cmd:click:'+x+','+y+','+l+','+t+'__SPLIT__'+selector.replace(new RegExp(':','g'),'_SEL_'));	
	};
	
	LHCCoBrowserOperator.prototype.sendData = function(command)
	{
		if (this.isNodeConnected === false) {
			
			if (this.mode_co_browse_internal == 'onlineuser'){
				return lhinst.addExecutionCommand(this.chat_id,'lhc_cobrowse_multi_command__'+command);
			} else {		
				lhinst.addRemoteCommand(this.chat_id,command);
			}	
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
			var mouseX = (e.clientX || e.pageX) + this.scrollLeftGS();
			var mouseY = (e.clientY || e.pageY) + this.scrollTopGS();
	
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
					secure : this.node_js_settings.secure,
					path : this.node_js_settings.path
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
		$('#status-icon-sharing').text('visibility_off');
		this.clearPage();
		
	};

	LHCCoBrowserOperator.prototype.userJoined = function(chat_id) {
		$('#status-icon-sharing').text('visibility');
		var _this = this;
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
							+ _this.chat_id + _this.mode_co_browse, function(data) {

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
		
		if (typeof this.iFrameDocument !== 'undefined') {				
			var element = this.iFrameDocument.getElementById('user-cursor');
			
			if (element === null) {
				var fragment = this
				.appendHTML('<div id="user-cursor" style="z-index:99999;top:'
						+ pos.y
						+ 'px;left:'
						+ parseInt(pos.x-12)
						+ 'px;position:absolute;"><img src="'+this.cursor+'" /></div>');
				if (this.iFrameDocument.body !== null) {
					this.iFrameDocument.body.appendChild(fragment);				
				}
				
			} else {
				element.style.top = pos.y + 'px';		
				element.style.left = parseInt(pos.x-12) + 'px';
			}
		}
	};
	
	LHCCoBrowserOperator.prototype.visitorScroll = function(pos) {
		this.userScrollData = pos;
		if (typeof this.iFrameDocument !== 'undefined' && this.userScrollSync == true) {			
			this.scrollTopGS(pos.t);
			this.scrollLeftGS(pos.l);
		}
	};

	LHCCoBrowserOperator.prototype.visitorHash = function(pos) {	
		if (typeof this.iFrameDocument !== 'undefined') {	
			
			// Try to find element by id first	
			if (jQuery(this.iFrameDocument).find(pos.hsh).size() > 0) {	
				this.scrollTopGS(jQuery(this.iFrameDocument).find(pos.hsh).offset().top);
			} else {			
				this.scrollTopGS(pos.t);
				this.scrollLeftGS(pos.l);
			}
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
	
	LHCCoBrowserOperator.prototype.changeTextValue = function(msg) {
		if (msg.selector != '') {
			$(this.iFrameDocument).find(msg.selector).val(msg.value);
		}
	};
	
	LHCCoBrowserOperator.prototype.changeSelectValueFromUser = function(msg) {
		if (msg.selector != '') {
			if ($(this.iFrameDocument).find(msg.selector).size() > 0) {
				$(this.iFrameDocument).find(msg.selector)[0].selectedIndex = msg.value;
			}
		}
	};
	
	LHCCoBrowserOperator.prototype.changeCheckboxValueFromUser = function(msg) {
		if (msg.selector != '') {
			if ($(this.iFrameDocument).find(msg.selector).size() > 0) {
				$(this.iFrameDocument).find(msg.selector)[0].checked = msg.value;
			}
		}
	};	
	
	
	LHCCoBrowserOperator.prototype.handleMessage = function(msg) {
		if (msg.base) {
			this.base = msg.base;		
			if (this.base.indexOf('https') > -1) {
				this.sitehttps = true;
			}
		} else if (msg.err) {
			this.iFrameDocument.getElementById('loading').style.display = 'none';
			this.iFrameDocument.getElementById('error').innerHTML = msg.err;
			this.iFrameDocument.getElementById('error').style.display = 'block';
		} else if (msg.lmsg) {
			this.$lastmessage.innerHTML = msg.lmsg;
		} else if (msg.finished) {
			$('#status-icon-sharing').attr('title', msg.finished.text);
			if (msg.finished.status == false) {
				$('#status-icon-sharing').text('visibility');
			} else {
				$('#status-icon-sharing').text('visibility_off');
			}
		} else if (msg.url) {
			this.$awesomebar.value = msg.url;

			// trigger treemirror's method; in our example only 'initialize' can be triggered,
			// so it's reasonable to clearPage() and (re-)instantiate the mirror here
		} else if (msg.f && msg.f == 'initialize') {			
			this.clearPage();

			if(typeof msg.formsEnabled != "undefined") {
				this.formsenabled = msg.formsEnabled;
			}

			this.mirror = new TreeMirror(this.iFrameDocument,
					this.treeMirrorParams);
			this.mirror.initialize.apply(this.mirror, msg.args);			
			var _this = this;
			setTimeout(function(){
				var fragment = _this.appendHTML("<style>body{visibility:visible!important;}#lhc_status_container{display:none!important;}#lhc-user-cursor{display:none!important;}.lhc-higlighted{-webkit-box-shadow: 0px 0px 20px 5px rgba(88,140,204,1);-moz-box-shadow: 0px 0px 20px 5px rgba(88,140,204,1);box-shadow: 0px 0px 20px 5px rgba(88,140,204,1);}</style>");
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
		} else if (msg.f && msg.f == 'uscroll') {
			this.visitorScroll(msg.pos);
		} else if (msg.f && msg.f == 'uchash') {
			this.visitorHash(msg.pos);
		} else if (msg.f && msg.f == 'textdata') {
			this.changeTextValue(msg);
		} else if (msg.f && msg.f == 'selectval') {
			this.changeSelectValueFromUser(msg);
		} else if (msg.f && msg.f == 'chkval') {
			this.changeCheckboxValueFromUser(msg);
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
		} else if (msg.error_msg) {
			alert(msg.error_msg);
		} else {
			//console.log('just message: ', msg);
		}
	};

	return LHCCoBrowserOperator;
})();