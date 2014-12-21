var LHCCoBrowser = (function() {

	function LHCCoBrowser(params) {
		var _this = this;
		this.sendCommands = [];
		this.updateTimeout = null;
		this.mirrorClient = null;
		this.socket = null;

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
			h : 0
		};

		this.mouseTimeout = null;
		this.mouseEventListenerCallback = function(e) {
			_this.mouseEventListener(e);
		};

		document.addEventListener('mousemove', this.mouseEventListenerCallback,
				false);

		if (params['nodejsenabled']) {
			this.setupNodeJs();
		}
		;
	}
	;

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

				console.log("connect-again");

			} catch (err) {
				console.log(err);
			}
		}
		;
	};

	LHCCoBrowser.prototype.onConnected = function() {
		this.isNodeConnected = true;
		this.socket.emit('join', {
			chat_id : lh_inst.cookieData.hash
		});
	};

	LHCCoBrowser.prototype.sendData = function(msg) {

		if (this.isNodeConnected === true && this.socket !== null) {
			this.socket.emit('usermessage', {
				chat_id : lh_inst.cookieData.hash,
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
			}, 500); // Send grouped changes every 0.5 seconds
		}
	};

	LHCCoBrowser.prototype.stopMirroring = function() {
		try {
			this.mirrorClient.disconnect();
			this.mirrorClient = null;
			clearTimeout(this.updateTimeout);
			clearTimeout(this.mouseTimeout);
			lh_inst.finishScreenSharing(); // Inform main chat handler about finished session	      

			if (this.isNodeConnected == true) {
				this.isNodeConnected = false;
				this.socket.emit('userleft', {
					chat_id : lh_inst.cookieData.hash
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