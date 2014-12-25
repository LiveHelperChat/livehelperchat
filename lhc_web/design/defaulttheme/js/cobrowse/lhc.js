var LHCCoBrowser = (function() {

	function LHCCoBrowser(params) {
		var _this = this;
		this.sendCommands = [];
		this.updateTimeout = null;
		this.mirrorClient = null;
		this.socket = null;
		this.chat_hash = params['chat_hash'];
		this.cssAdded = false;
		this.operatorMouseVisible = false;
		this.windowForceScroll = false;
		
		this.trans = {};

		if (params['url']) {
			this.url = params['url'];
		}
		;

		if (params['trans']) {
			this.trans = params['trans'];
		}
		;
		this.node_js_settings = params['nodejssettings'];

		this.mouse = {
			x : 0,
			y : 0,
			w : 0,
			h : 0,
			wh : 0
		};

		/**
		 * Setup mouse tracking
		 * */
		this.mouseTimeout = null;
		this.mouseEventListenerCallback = function(e) {
			_this.mouseEventListener(e);
		};

		document.addEventListener('mousemove', this.mouseEventListenerCallback,false);

		/**
		 * Setup NodeJs support if required
		 * */
		if (params['nodejsenabled']) {
			this.setupNodeJs();
		}
		;
	}
	;
	
	LHCCoBrowser.prototype.handleMessage = function(msg) {
				
		if (msg[1] == 'hightlight') {
			var pos = msg[2].split(',');
			
			var origScroll = {scrollLeft: document.body.scrollLeft,scrollTop:document.body.scrollTop};
			document.body.scrollLeft = pos[2];
			document.body.scrollTop = pos[3];
			
			// Avoid highlight on our own cursor
			var operatorCursor = document.getElementById('lhc-user-cursor');
			var origDisplay = "";
			if (operatorCursor !== null) {
				origDisplay = operatorCursor.style.display;
				operatorCursor.style.display = "none";
			};
			
			// Get original page element
			var element = document.elementFromPoint(pos[0], pos[1]);
			
			// Now we can restore operator cursor
			if (operatorCursor !== null) {
				operatorCursor.style.display = origDisplay;
			};
			
			
			// Restore user scrollbar position where we found it
			if (this.windowForceScroll == false) {
				document.body.scrollLeft = origScroll['scrollLeft'];
				document.body.scrollTop = origScroll['scrollTop'];
			};
			
			var hightlight = true;
			if (element !== null && lh_inst.hasClass(element,'lhc-higlighted') && (element.tagName != 'INPUT' && element.tagName != 'TEXTAREA' && element.tagName != 'SELECT') ){
				hightlight = false;
			};
			
			// Remove previously highlighted element
			var elements = document.getElementsByClassName('lhc-higlighted');
			for (var i = 0; i < elements.length; i++) {			
				lh_inst.removeClass(elements[i],'lhc-higlighted');
				if (elements[i] == element){
					hightlight = false;
				}
			};
			
			// Highlight only if required
			if (hightlight == true && element !== null){
				lh_inst.addClass(element,'lhc-higlighted');				
				if (this.cssAdded == false) {	
					this.cssAdded = true;
					lh_inst.addCss('.lhc-higlighted{-webkit-box-shadow: 0px 0px 20px 5px rgba(88,140,204,1);-moz-box-shadow: 0px 0px 20px 5px rgba(88,140,204,1);box-shadow: 0px 0px 20px 5px rgba(88,140,204,1);}');
				}
			}
		} else if (msg[1] == 'operatorcursor'){
			
			if (this.operatorMouseVisible == true) {
				var pos = msg[2].split(',');
				var element = document.getElementById('lhc-user-cursor');
	
				if (element === null) {
					var fragment = lh_inst.appendHTML('<div id="lhc-user-cursor" style="z-index:99999;top:'
									+ pos[1]
									+ 'px;left:'
									+ parseInt(pos[0]-12)
									+ 'px;position:absolute;"><img src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABUAAAAcCAQAAAAkETzVAAAAAmJLR0QA/4ePzL8AAAAJcEhZcwAACxMAAAsTAQCanBgAAAAHdElNRQfeDBYQLhknKDAhAAAC4ElEQVQ4y32US2xVRRjHf/M457774KpNxRaBGjRq0igJxmriI9EaF2BoNCHgRiI748odxARXLNmpKzbEjTFqlI0sDLLSBcE0GNTW0sIl5bba9txzzzkzZ8bFubdeI/hNMqvf983/ew0AMCSa4dtjSOSjgv+zIYHeu3vxk2P7RAkd3BsuCzSVqaeiztUL78xWRwi1fPYuuAIr0VSaO2dPXNo993Q5WlzatMvg7oIiCak2Jw4f/Vre2fHK9GS8dH3NgvL+P6gmpN6cPPzWZW7LW/UDT+73N35vpd5XvB1A5RGBQBFS9hgyrsvPx+vvffT+zMOi3FXVAc0SEAgkgSMjIxML4tx9y8fPfPjytK7GOpR9VAMgkKgCNaTiFp/WX3rj1FjjzMUfNhNMkeK2D8JjeifmD87qjw+8e/L4qzsbBFoCqI74UxFSa04cPHgRgyGhTZuMX9TGA3OPPxH9utjOKs4iF7bD9rSSsEYOGPFtcPaxRz547YVyqav6AkSBGgwZMVnPOReStDUfpSWUEnIXgC+uImpn+51xf2j9wveXWj5A5ULODAgwvbi97vjZLLj6zbyRxav/VABHhsGQ9seI1zc/+2l5ixyH79fV473TfhhDzA4iOgI8SV5NSUixuCmvCxCXdNXC6dAplFDXGqfDdWG4Up9+8MuEFEP+m9fnPZ4cs3Lz+VOMMUrt/uGTz705dU4l4krlyEM12zHk+EKrIydli3VWaXF7beWLH59Z3e8kgZis7ykXSoshFEgEAkdGQtebdjyh5sZfVDN3vvvq8ny8QTJiE68h9Jmli8CyRZkaw1F0Pru5bPy1lRs/RzEOX/F/FRWriY5CowkIKTHEKKOq7oWLaLPKOtF41vJiYHIlEkVAlQYNqigSNtlgiy52pKhrSAYOh8CSk2NJKCGxxHTJtLNUCjSjVnTe4zF4HBm6l6jF6n/t2IA1BQpNSIkQHcp6T+Q9f5JdAmDJ9/fK8jfQNz5Ki5DVMQAAAABJRU5ErkJggg==\"/></div>');
					if (document.body !== null) {
						document.body.insertBefore(fragment,
								document.body.childNodes[0]);
					}				
				} else {
					element.style.top = pos[1] + 'px';		
					element.style.left = parseInt(pos[0]-12) + 'px';
				}
			}
		} else if (msg[1] == 'mouse') {			
			if (msg[2] == 'show') {
				this.operatorMouseVisible = true;
			} else {
				this.operatorMouseVisible = false;
				lh_inst.removeById('lhc-user-cursor');				
			}
		} else if (msg[1] == 'scroll') {			
			if (msg[2] == 'true') {
				this.windowForceScroll = true;
			} else {
				this.windowForceScroll = false;
			}
		} else if (msg[1] == 'navigate') {			
			document.location =msg[2].replace(new RegExp('__SPLIT__','g'),':');
		} else if (msg[1] == 'click') {	
			var pos = msg[2].split(',');
			
			var origScroll = {scrollLeft: document.body.scrollLeft,scrollTop:document.body.scrollTop};
			document.body.scrollLeft = pos[2];
			document.body.scrollTop = pos[3];
			
			// Avoid highlight on our own cursor
			var operatorCursor = document.getElementById('lhc-user-cursor');
			var origDisplay = "";
			if (operatorCursor !== null) {
				origDisplay = operatorCursor.style.display;
				operatorCursor.style.display = "none";
			};
			
			// Get original page element
			var element = document.elementFromPoint(pos[0], pos[1]);
			
			// Now we can restore operator cursor
			if (operatorCursor !== null) {
				operatorCursor.style.display = origDisplay;
			};
			
			
			// Restore user scrollbar position where we found it
			if (this.windowForceScroll == false) {
				document.body.scrollLeft = origScroll['scrollLeft'];
				document.body.scrollTop = origScroll['scrollTop'];
			};
			
			if (element !== null) {
				element.focus();								
				element.click();
			} else {
				console.log('not found');
			}			
		} else if (msg[1] == 'fillform') {				
			var value = msg[2].replace(new RegExp('__SPLIT__','g'),':');			
			var elements = document.getElementsByClassName('lhc-higlighted');
			for (var i = 0; i < elements.length; i++) {			
				elements[i].value = value;
			};
		}
	};
	
	LHCCoBrowser.prototype.mouseEventListener = function(e) {
		var _this = this;

		var mouseX = (e.clientX || e.pageX) + document.body.scrollLeft;
		var mouseY = (e.clientY || e.pageY) + document.body.scrollTop;

		if (_this.mouse.x != mouseX || _this.mouse.y != mouseY) {
			_this.mouse.x = mouseX;
			_this.mouse.y = mouseY;
			_this.mouse.w = Math.max(document.documentElement["clientWidth"],
					document.body["scrollWidth"],
					document.documentElement["scrollWidth"],
					document.body["offsetWidth"],
					document.documentElement["offsetWidth"]);
			_this.mouse.h = Math.max(document.documentElement["clientHeight"],
					document.body["scrollHeight"],
					document.documentElement["scrollHeight"],
					document.body["offsetHeight"],
					document.documentElement["offsetHeight"]);
			_this.mouse.wh = window.innerHeight;
			
			if (_this.mouseTimeout === null) {
				_this.mouseTimeout = setTimeout(function() {
					_this.sendData({
						'f' : 'cursor',
						'pos' : _this.mouse
					});
					_this.mouseTimeout = null;
				}, (_this.isNodeConnected === true) ? 20 : 1000);
			}
			;
		}
		;
	};

	LHCCoBrowser.prototype.setupNodeJs = function() {
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
					'forceNew' : true
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

				this.socket.on('remotecommand', function(cmd) {
					_this.handleMessage(cmd.split(':'));
				});

			} catch (err) {
				console.log(err);
			}
		}
		;
	};

	LHCCoBrowser.prototype.onConnected = function() {
		this.isNodeConnected = true;
		this.socket.emit('join', {
			chat_id : this.chat_hash
		});
	};

	LHCCoBrowser.prototype.sendData = function(msg) {

		if (this.isNodeConnected === true && this.socket !== null) {
			this.socket.emit('usermessage', {
				chat_id : this.chat_hash,
				msg : msg
			});
			if (!msg.f || msg.f != 'initialize') { // always store initialize command data to database, so rest of operators can join
				return;
			}
		}
		;

		var _this = this;
		this.sendCommands.push(msg);
		if (this.updateTimeout === null) {
			this.updateTimeout = setTimeout(function() {
				var xhr = new XMLHttpRequest();
				xhr.open("POST", _this.url, true);
				xhr.setRequestHeader("Content-type",
						"application/x-www-form-urlencoded");
				xhr.send("data="
						+ encodeURIComponent(lh_inst.JSON
								.stringify(_this.sendCommands)));
				_this.sendCommands = [];
				_this.updateTimeout = null;
			}, this.isNodeConnected === true ? 0 : 500); // Send grouped changes every 0.5 seconds
		}
	};

	LHCCoBrowser.prototype.stopMirroring = function() {
		try {
			this.mirrorClient.disconnect();
			this.mirrorClient = null;
			clearTimeout(this.updateTimeout);
			clearTimeout(this.mouseTimeout);
			
			// Remove previously highlighted element
			var elements = document.getElementsByClassName('lhc-higlighted');
			for (var i = 0; i < elements.length; i++) {			
				lh_inst.removeClass(elements[i],'lhc-higlighted');				
			};
			
			// Hide operator cursor
			lh_inst.removeById('lhc-user-cursor');	
			lh_inst.finishScreenSharing(); // Inform main chat handler about finished session	      

			if (this.isNodeConnected == true) {
				this.isNodeConnected = false;
				this.socket.emit('userleft', {
					chat_id : this.chat_hash
				});
				this.socket.disconnect();
				this.socket = null;
			}
			;

			document.removeEventListener('mousemove',
					this.mouseEventListenerCallback, false);

		} catch (e) {
			console.log(e);
		}
	};

	LHCCoBrowser.prototype.startMirroring = function() {
		var _this = this;
		this.mirrorClient = new TreeMirrorClient(document, {
			initialize : function(rootId, children) {
				_this.sendData({
					'f' : 'cursor',
					'pos' : {w:Math.max(document.documentElement["clientWidth"],
							document.body["scrollWidth"],
							document.documentElement["scrollWidth"],
							document.body["offsetWidth"],
							document.documentElement["offsetWidth"]),h:Math.max(document.documentElement["clientHeight"],
									document.body["scrollHeight"],
									document.documentElement["scrollHeight"],
									document.body["offsetHeight"],
									document.documentElement["offsetHeight"]),wh:window.innerHeight}
				});
				
				_this.sendData({
					f : 'initialize',
					args : [ rootId, children ]
				});
			},
			applyChanged : function(removed, addedOrMoved, attributes, text) {
				_this.sendData({
					f : 'applyChanged',
					args : [ removed, addedOrMoved, attributes, text ]
				});
			}
		});

		var htmlStatus = '<div id="lhc_status_mirror" style="border-radius:3px;cursor:pointer;position:fixed;top:5px;right:5px;padding:5px;z-index:9999;text-align:center;font-weight:bold;background-color:rgba(140, 227, 253, 0.53);font-family:arial;font-size:12px;">'
				+ this.trans.operator_watching + '</div>';
		var fragment = lh_inst.appendHTML(htmlStatus);
		document.body.insertBefore(fragment, document.body.childNodes[0]);

		document.getElementById('lhc_status_mirror').onclick = function() {
			_this.stopMirroring();
		};
	};

	return LHCCoBrowser;
})();