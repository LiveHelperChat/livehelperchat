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
		
		if (params['nodejsenabled']) {
			this.setupNodeJs();
		}
		;

		this.treeMirrorParams = {
			createElement : function(tagName) {
				if (tagName == 'SCRIPT') {
					var node = document.createElement('NO-SCRIPT');
					node.style.display = 'none';
					return node;
				}

				if (tagName == 'IFRAME') {
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
				// remove anchors's onclick dom0-style handlers so they
				// don't mess with our click handler and don't produce errors		    			    	
				if (node.nodeName == 'A' && attr == 'onclick') {
					return true
				}
			}
		};

		w.onIframeLoaded = function() {
			_this.iFrameDocument = w.frames['content'].document
			
			// Override behavior for links: instead of reloading an iframe,
			// use them via our "browser" so that mirrors stay in sync.
			_this.iFrameDocument.onclick = function(e) {
				// "slave" mirrors can't navigate
				e.preventDefault();
			};

			if (_this.initialize !== null && _this.initialize != '') {
				_this.handleMessage(_this.initialize);
			}
			;

			_this.startChangesMonitoring();
		}
	}
	;

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
				;
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
		if (this.iFrameDocument) {
			while (this.iFrameDocument.firstChild) {
				this.iFrameDocument.removeChild(this.iFrameDocument.firstChild)
			}
		}
	};

	LHCCoBrowserOperator.prototype.visitorCursor = function(pos) {

		document.getElementById('center-layout').style.width = pos.w+'px';
		
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
			console.log('just message: ', msg);
		}
	};

	return LHCCoBrowserOperator;
})();