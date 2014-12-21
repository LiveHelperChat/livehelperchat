var LHCCoBrowser = (function () {
	
	function LHCCoBrowser(params){
		var _this = this;
		this.sendCommands = [];
		this.updateTimeout = null;
		this.mirrorClient = null;
		this.trans = {};
		
		if (params['url']) {
			this.url = params['url'];
		};
		
		if (params['trans']) {
			this.trans = params['trans'];
		};
		
		this.mouse = {x: 0, y: 0};

		this.mouseTimeout = null;
		document.addEventListener('mousemove', function(e){ 
			_this.mouse.x = e.clientX || e.pageX; 
			_this.mouse.y = (e.clientY || e.pageY) + document.body.scrollTop;
			
			if (_this.mouseTimeout === null){
				_this.mouseTimeout = setTimeout(function(){
					_this.sendData({'f':'cursor','pos':_this.mouse});
					_this.mouseTimeout = null;					
				},1000);
			};			
		}, false);
	};
	
	
	LHCCoBrowser.prototype.sendData = function(msg){	
		var _this = this;
				
		this.sendCommands.push(msg);				
		if (this.updateTimeout === null){				
			this.updateTimeout = setTimeout(function(){				
				var xhr = new XMLHttpRequest();
		        xhr.open( "POST", _this.url, true);
			    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			    xhr.send( "data=" + encodeURIComponent( lh_inst.JSON.stringify(_this.sendCommands) ) );				    
			    _this.sendCommands = [];
			    _this.updateTimeout = null;
			},500); // Send grouped changes every 1.5 seconds
		}
	};

	LHCCoBrowser.prototype.stopMirroring = function(){
		try {
			this.mirrorClient.disconnect();
			this.mirrorClient = null;
			clearTimeout(this.updateTimeout);				
			lh_inst.finishScreenSharing(); // Inform main chat handler about finished session	        
		} catch (e) {
			console.log(e);
		}
	};

	LHCCoBrowser.prototype.startMirroring = function(){
		var _this = this;
		this.mirrorClient = new TreeMirrorClient(document, {
		      initialize: function(rootId, children) {
		    	 _this.sendData({
			          f: 'initialize',
			          args: [rootId, children]
			     });		       
		      },
		      applyChanged: function(removed, addedOrMoved, attributes, text) {
		    	  _this.sendData({
		    		  f: 'applyChanged',
			          args: [removed, addedOrMoved, attributes, text]
			     });
		      }
		});
		
		var htmlStatus = '<div id="lhc_status_mirror" style="border-radius:5px;cursor:pointer;position:fixed;top:5px;right:5px;padding:5px;font-weight:bold;z-index:9999;text-align:center;background-color:rgba(140, 227, 253, 0.53);font-family:arial;font-size:14px;">'+this.trans.operator_watching+'</div>';
        var fragment = lh_inst.appendHTML(htmlStatus);        
        document.body.insertBefore(fragment, document.body.childNodes[0]);  
        
        document.getElementById('lhc_status_mirror').onclick = function() { 
        	_this.stopMirroring();
        };
	};
	
	return LHCCoBrowser;	
})();