var LHCCoBrowserOperator = (function () {
		
	function LHCCoBrowserOperator(w,d,params){	
			this.$awesomebar = d.getElementById('awesomebar');	     
			this.$lastmessage = d.getElementById('last-message');	     
			this.$finishedmessage = d.getElementById('finished-message');	     
		    this.iFrameDocument, this.mirror;
		    var _this = this;
	      	   		    
		    this.initialize = params['initialize'];
		    this.base = params['base'];
		    this.chat_id = params['chat_id'];
		    this.refreshTimeout = null;
		    
		  this.treeMirrorParams = {
		    createElement: function(tagName) {
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
		    setAttribute: function(node, attr, val) {
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
		    };
			
			_this.startChangesMonitoring();
	    
	    /*if (!socket) {
	      socket = new WebSocket(socketURL)

	      socket.onmessage = function(event) {
	        var msg = JSON.parse(event.data)
	        console.log('MSG: ', msg)
	        if (msg instanceof Array) {
	          msg.forEach(function(subMessage) {
	            handleMessage(JSON.parse(subMessage))
	          })
	        } else {
	          handleMessage(msg)
	        }
	      }

	      socket.onclose = function() {
	        socket = new WebSocket(socketURL)
	      }
	    }*/
	 }
	};
	
	LHCCoBrowserOperator.prototype.startChangesMonitoring = function() {
		var _this = this;
		$.getJSON(WWW_DIR_JAVASCRIPT + 'cobrowse/checkmirrorchanges/' + _this.chat_id,function(data){
			
			if (typeof data.empty == "undefined") {				
				_this.handleMessage(data);
			};
			
			_this.refreshTimeout = setTimeout(function(){
				_this.startChangesMonitoring();
			},1400);
			
    	}).fail(function(){    	
    		_this.refreshTimeout = setTimeout(function(){
				_this.startChangesMonitoring();
			},1400);
    	});
	};
	
	LHCCoBrowserOperator.prototype.clearPage = function() {
	    if (this.iFrameDocument) {
	      while (this.iFrameDocument.firstChild) {
	    	  this.iFrameDocument.removeChild(this.iFrameDocument.firstChild)
	      }
	    }
	};
	
	LHCCoBrowserOperator.prototype.visitorCursor = function(pos) {
		var element = this.iFrameDocument.getElementById('user-cursor');
		if (element === null) {			
			var fragment = this.appendHTML('<div id="user-cursor" style="box-shadow: rgba(0, 0, 0, 0.74902) 0px 0px 4px 4px;border-radius:50%;top:'+pos.y+'px;left:'+pos.x+'px;position:absolute;width:15px;height:15px;background-color:rgb(68, 186, 247);"></div>');
			this.iFrameDocument.body.insertBefore(fragment, this.iFrameDocument.body.childNodes[0]);
		} else {
			element.style.top = pos.y+'px';
			element.style.left = pos.x+'px';
		}
	};
	
	LHCCoBrowserOperator.prototype.appendHTML = function (htmlStr) {
        var frag = this.iFrameDocument.createDocumentFragment(),
            temp = this.iFrameDocument.createElement('div');
        temp.innerHTML = htmlStr;
        while (temp.firstChild) {
            frag.appendChild(temp.firstChild);
        };
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
	    	$('#status-icon-sharing').attr('title',msg.finished.text);
	    	if (msg.finished.status == 1) {
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
	    	this.mirror = new TreeMirror(this.iFrameDocument, this.treeMirrorParams);
	    	this.mirror.initialize.apply(this.mirror, msg.args);
	    } else if (msg.f && msg.f == 'cursor') {	      
	    	this.visitorCursor(msg.pos);
	    } else if (msg.f) {  
	    	if (typeof this.mirror != "undefined"){
	    		this.mirror[msg.f].apply(this.mirror, msg.args);
	    	}
	    } else if (msg instanceof Array) {
	    	var _this = this;
	    	msg.forEach(function(subMessage) {
		    	  try {
		    		_this.handleMessage(subMessage);		    		
		    	  } catch (err) {
	  	    			console.log(err);
				  };
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