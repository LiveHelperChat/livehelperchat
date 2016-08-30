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
		this.NodeJsSupportEnabled = false;
		this.initializeData = null;
		this.selectorInitialized = false;
		this.initialiseBlock = true;
		this.shareStyleStatus = "border-radius:3px;cursor:pointer;position:fixed;top:5px;right:5px;padding:5px;z-index:9999;text-align:center;font-weight:bold;background-color:rgba(140, 227, 253, 0.53);font-family:arial;font-size:12px;";
		this.formsenabled = typeof params['formsenabled'] != 'undefined' ? params['formsenabled'] : true;

		this.trans = {};
		
		if (params['url']) {
			this.url = params['url'];
		}
		;

		if (params['style_share']) {
			this.shareStyleStatus = params['style_share'];
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

		this.inputChangeKeyUpListener = function(e) {
			_this.changeEventListener(e);
		};
		
		var inputs = document.getElementsByTagName("INPUT");		
		for (var i = 0; i < inputs.length; i++) {			
			inputs[i].addEventListener('keyup', this.inputChangeKeyUpListener,false);
			inputs[i].addEventListener('change', this.inputChangeKeyUpListener,false);
		};		
		
		var inputs = document.getElementsByTagName("TEXTAREA");		
		for (var i = 0; i < inputs.length; i++) {			
			inputs[i].addEventListener('keyup', this.inputChangeKeyUpListener,false);
		};		
		
		var inputs = document.getElementsByTagName("SELECT");		
		for (var i = 0; i < inputs.length; i++) {
			inputs[i].addEventListener('change', this.inputChangeKeyUpListener,false);
		};		
		
		this.hashchangeEventListenerCallback = function(e) {			
			_this.hashchangeEventListener(e);
		};
		
		window.addEventListener('hashchange',this.hashchangeEventListenerCallback,false); 
		
		
		this.scrollTimeout = null;
		this.scrollTop = this.scrollTopGS();
		this.scrollLeft = this.scrollLeftGS();
		
		this.scrollEventListenerCallback = function(e) {
			_this.scrollEventListener(e);
		};
		
		document.addEventListener("scroll",this.scrollEventListenerCallback, false);
		
		/**
		 * Setup NodeJs support if required
		 * */
		if (params['nodejsenabled']) {
			this.NodeJsSupportEnabled = true;			
			this.setupNodeJs();
		}
	};
	
	LHCCoBrowser.prototype.hashchangeEventListener = function(e)
	{
		this.sendData({
			'f' : 'uchash',
			'pos' : {'hsh':window.location.hash,'t':this.scrollTopGS(),'l':this.scrollLeftGS()}
		});		
	}
	
	LHCCoBrowser.prototype.sendInputsData = function()
	{
		if (typeof jQuery !== 'undefined' ) {
			this.initializeSelector();			
			var inputs = document.getElementsByTagName("INPUT");		
			for (var i = 0; i < inputs.length; i++) {
				var selectorData = jQuery(inputs[i]).getSelector();	
				if (selectorData.length >= 1) {						
					if (inputs[i].type == 'checkbox' || inputs[i].type == 'radio') {
						this.sendData({
							'f' : 'chkval',
							'selector' : selectorData[0],
							'value' : jQuery(inputs[i]).is(':checked')
						});
					} else {
						this.sendData({
							'f' : 'textdata',
							'selector' : selectorData[0],
							'value' : inputs[i].value
						});
					}					
				}
			};		
			
			var inputs = document.getElementsByTagName("TEXTAREA");		
			for (var i = 0; i < inputs.length; i++) {			
				var selectorData = jQuery(inputs[i]).getSelector();	
				if (selectorData.length >= 1) {	
					this.sendData({
						'f' : 'textdata',
						'selector' : selectorData[0],
						'value' : inputs[i].value
					});
				}
			};		
			
			var inputs = document.getElementsByTagName("SELECT");		
			for (var i = 0; i < inputs.length; i++) {
				var selectorData = jQuery(inputs[i]).getSelector();	
				if (selectorData.length >= 1) {	
					this.sendData({
						'f' : 'selectval',
						'selector' : selectorData[0],
						'value' : inputs[i].selectedIndex
					});
				}
			};
		}
	}
	
	LHCCoBrowser.prototype.scrollEventListener = function() 
	{
		this.scrollTop = this.scrollTopGS();
		this.scrollLeft = this.scrollLeftGS();
		
		var _this = this;
		
		if (this.scrollTimeout === null) {
			this.scrollTimeout = setTimeout(function() {
				_this.sendData({
					'f' : 'uscroll',
					'pos' : {'t':_this.scrollTop,'l':_this.scrollLeft}
				});
				_this.scrollTimeout = null;				
			}, (this.isNodeConnected === true) ? 20 : 1000);
		};
	};
	
	LHCCoBrowser.prototype.initializeSelector = function()
	{		
		if (typeof jQuery !== 'undefined' && this.selectorInitialized == false) {		
			this.selectorInitialized = true;
			/* https://github.com/ngs/jquery-selectorator */
			(function() {	
				  (function($) {
			    var Selectorator, clean, contains, escapeSelector, extend, inArray, map, unique;
			    map = $.map;
			    extend = $.extend;
			    inArray = $.inArray;
			    contains = function(item, array) {
			      return inArray(item, array) !== -1;
			    };
			    escapeSelector = function(selector) {
			      return selector.replace(/([\!\"\#\$\%\&'\(\)\*\+\,\.\/\:\;<\=>\?\@\[\\\]\^\`\{\|\}\~])/g, "\\$1");
			    };
			    clean = function(arr, reject) {
			      return map(arr, function(item) {
			        if (item === reject) {
			          return null;
			        } else {
			          return item;
			        }
			      });
			    };
			    unique = function(arr) {
			      return map(arr, function(item, index) {
			        if (parseInt(index, 10) === parseInt(arr.indexOf(item), 10)) {
			          return item;
			        } else {
			          return null;
			        }
			      });
			    };
			    Selectorator = (function() {
			
			      function Selectorator(element, options) {
			        this.element = element;
			        this.options = extend(extend({}, $.selectorator.options), options);
			        this.cachedResults = {};
			      }
			
			      Selectorator.prototype.query = function(selector) {
			        var _base;
			        return (_base = this.cachedResults)[selector] || (_base[selector] = $(selector.replace(/#([^\s]+)/g, "[id='$1']")));
			      };
			
			      Selectorator.prototype.getProperTagName = function() {
			        if (this.element[0]) {
			          return this.element[0].tagName.toLowerCase();
			        } else {
			          return null;
			        }
			      };
			
			      Selectorator.prototype.hasParent = function() {
			        return this.element && 0 < this.element.parent().size();
			      };
			
			      Selectorator.prototype.isElement = function() {
			        var node;
			        node = this.element[0];
			        return node && node.nodeType === node.ELEMENT_NODE;
			      };
			
			      Selectorator.prototype.validate = function(selector, parentSelector, single, isFirst) {
			        var delimiter, element;
			        if (single == null) {
			          single = true;
			        }
			        if (isFirst == null) {
			          isFirst = false;
			        }
			        element = this.query(selector);
			        if (single && 1 < element.size() || !single && 0 === element.size()) {
			          if (parentSelector && selector.indexOf(':') === -1) {
			            delimiter = isFirst ? ' > ' : ' ';
			            selector = parentSelector + delimiter + selector;
			            element = this.query(selector);
			            if (single && 1 < element.size() || !single && 0 === element.size()) {
			              return null;
			            }
			          } else {
			            return null;
			          }
			        }
			        if (contains(this.element[0], element.get())) {
			          return selector;
			        } else {
			          return null;
			        }
			      };
			
			      Selectorator.prototype.generate = function() {
			        var fn, res, _i, _len, _ref;
			        if (!(this.element && this.hasParent() && this.isElement())) {
			          return [''];
			        }
			        res = [];
			        _ref = [this.generateSimple, this.generateAncestor, this.generateRecursive];
			        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
			          fn = _ref[_i];
			          res = unique(clean(fn.call(this)));
			          if (res && res.length > 0) {
			            return res;
			          }
			        }
			        return unique(res);
			      };
			
			      Selectorator.prototype.generateAncestor = function() {
			        var isFirst, parent, parentSelector, parentSelectors, results, selector, selectors, _i, _j, _k, _len, _len1, _len2, _ref;
			        results = [];
			        _ref = this.element.parents();
			        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
			          parent = _ref[_i];
			          isFirst = true;
			          selectors = this.generateSimple(null, false);
			          for (_j = 0, _len1 = selectors.length; _j < _len1; _j++) {
			            selector = selectors[_j];
			            parentSelectors = new Selectorator($(parent), this.options).generateSimple(null, false);
			            for (_k = 0, _len2 = parentSelectors.length; _k < _len2; _k++) {
			              parentSelector = parentSelectors[_k];
			              $.merge(results, this.generateSimple(parentSelector, true, isFirst));
			            }
			          }
			          isFirst = false;
			        }
			        return results;
			      };
			
			      Selectorator.prototype.generateSimple = function(parentSelector, single, isFirst) {
			        var fn, res, self, tagName, validate, _i, _len, _ref;
			        self = this;
			        tagName = self.getProperTagName();
			        validate = function(selector) {
			          return self.validate(selector, parentSelector, single, isFirst);
			        };
			        _ref = [
			          [self.getIdSelector], [self.getClassSelector], [self.getIdSelector, true], [self.getClassSelector, true], [self.getNameSelector], [
			            function() {
			              return [self.getProperTagName()];
			            }
			          ]
			        ];
			        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
			          fn = _ref[_i];
			          res = fn[0].call(self, fn[1]) || [];
			          res = clean(map(res, validate));
			          if (res.length > 0) {
			            return res;
			          }
			        }
			        return [];
			      };
			
			      Selectorator.prototype.generateRecursive = function() {
			        var index, parent, parentSelector, selector;
			        selector = this.getProperTagName();
			        if (selector.indexOf(':') !== -1) {
			          selector = '*';
			        }
			        parent = this.element.parent();
			        parentSelector = new Selectorator(parent).generate()[0];
			        index = parent.children(selector).index(this.element);
			        selector = "" + selector + ":eq(" + index + ")";
			        if (parentSelector !== '') {
			          selector = parentSelector + " > " + selector;
			        }
			        return [selector];
			      };
			
			      Selectorator.prototype.getIdSelector = function(tagName) {
			        var id;
			        if (tagName == null) {
			          tagName = false;
			        }
			        tagName = tagName ? this.getProperTagName() : '';
			        id = this.element.attr('id');
			        if (typeof id === "string" && !contains(id, this.getIgnore('id'))) {
			          return ["" + tagName + "#" + (escapeSelector(id))];
			        } else {
			          return null;
			        }
			      };
			
			      Selectorator.prototype.getClassSelector = function(tagName) {
			        var classes, invalidClasses, tn;
			        if (tagName == null) {
			          tagName = false;
			        }
			        tn = this.getProperTagName();
			        if (/^(body|html)$/.test(tn)) {
			          return null;
			        }
			        tagName = tagName ? tn : '';
			        invalidClasses = this.getIgnore('class');
			        classes = (this.element.attr('class') || '').replace(/\{.*\}/, "").split(/\s/);
			        return map(classes, function(klazz) {
			          if (klazz && !contains(klazz, invalidClasses)) {
			            return "" + tagName + "." + (escapeSelector(klazz));
			          } else {
			            return null;
			          }
			        });
			      };
			
			      Selectorator.prototype.getNameSelector = function() {
			        var name, tagName;
			        tagName = this.getProperTagName();
			        name = this.element.attr('name');
			        if (name && !contains(name, this.getIgnore('name'))) {
			          return ["" + tagName + "[name='" + name + "']"];
			        } else {
			          return null;
			        }
			      };
			
			      Selectorator.prototype.getIgnore = function(key) {
			        var mulkey, opts, vals;
			        opts = this.options.ignore || {};
			        mulkey = key === 'class' ? 'classes' : "" + key + "s";
			        vals = opts[key] || opts[mulkey];
			        if (typeof vals === 'string') {
			          return [vals];
			        } else {
			          return vals;
			        }
			      };
			
			      return Selectorator;
			
			    })();
			    $.selectorator = {
			      options: {},
			      unique: unique,
			      clean: clean,
			      escapeSelector: escapeSelector
			    };
			    $.fn.selectorator = function(options) {
			      return new Selectorator($(this), options);
			    };
			    $.fn.getSelector = function(options) {
			      return this.selectorator(options).generate();
			    };
			    return this;
			  })(jQuery);}).call(this);
		}		
	};
	
	LHCCoBrowser.prototype.changeEventListener = function(e) {
		if (typeof jQuery !== 'undefined' ) {
			this.initializeSelector();
			var selectorData = jQuery(e.target).getSelector();			
			if (selectorData.length >= 1) {	
				if (e.target.tagName == 'SELECT') {	
					this.sendData({
						'f' : 'selectval',
						'selector' : selectorData[0],
						'value' : e.target.selectedIndex
					});					
				} else {					
					if (e.target.type == 'checkbox' || e.target.type == 'radio') {
						this.sendData({
							'f' : 'chkval',
							'selector' : selectorData[0],
							'value' : jQuery(e.target).is(':checked')
						});
					} else {
						this.sendData({
							'f' : 'textdata',
							'selector' : selectorData[0],
							'value' : jQuery(e.target).val()
						});
					}
				}
			}
		}
	};
	
	LHCCoBrowser.prototype.handleMessage = function(msg) {				
		if (msg[1] == 'hightlight') {
			var parts = msg[2].split('__SPLIT__');
			var selectorData = parts[1].replace(new RegExp('_SEL_','g'),':');
			var element = null;
			if (selectorData != '' && typeof jQuery !== 'undefined' ) {
				var objects = jQuery(selectorData);				
				if (objects !== null && objects.length == 1) {
					element = objects[0];
				}
			};
						
			var pos = parts[0].split(',');
			
			var origScroll = {scrollLeft: this.scrollLeftGS(),scrollTop:this.scrollTopGS()};						
			this.scrollLeftGS(pos[2]);
			this.scrollTopGS(pos[3]);
						
			
			// Avoid highlight on our own cursor
			var operatorCursor = document.getElementById('lhc-user-cursor');
			var origDisplay = "";
			if (operatorCursor !== null) {
				origDisplay = operatorCursor.style.display;
				operatorCursor.style.display = "none";
			};
			
			if (element === null) {
				// Get original page element
				element = document.elementFromPoint(pos[0], pos[1]);
			};
			
			// Now we can restore operator cursor
			if (operatorCursor !== null) {
				operatorCursor.style.display = origDisplay;
			};
			
			
			
			// Restore user scrollbar position where we found it
			if (this.windowForceScroll == false) {
				this.scrollLeftGS(origScroll['scrollLeft']);
				this.scrollTopGS(origScroll['scrollTop']);
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
					lh_inst.addCss('.lhc-higlighted{-webkit-box-shadow: 0px 0px 20px 5px rgba(88,140,204,1)!important;-moz-box-shadow: 0px 0px 20px 5px rgba(88,140,204,1)!important;box-shadow: 0px 0px 20px 5px rgba(88,140,204,1)!important;}');
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
						document.body.appendChild(fragment);
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
			
			var parts = msg[2].split('__SPLIT__');
			var selectorData = parts[1].replace(new RegExp('_SEL_','g'),':');
			var element = null;
			if (selectorData != '' && typeof jQuery !== 'undefined' ) {
				var objects = jQuery(selectorData);				
				if (objects !== null && objects.length == 1) {
					element = objects[0];
				}
			}
						
			var pos = parts[0].split(',');
			
			var origScroll = {scrollLeft: this.scrollLeftGS(),scrollTop: this.scrollTopGS()};
			this.scrollLeftGS(pos[2]);
			this.scrollTopGS(pos[3]);
			
			// Avoid highlight on our own cursor
			var operatorCursor = document.getElementById('lhc-user-cursor');
			var origDisplay = "";
			if (operatorCursor !== null) {
				origDisplay = operatorCursor.style.display;
				operatorCursor.style.display = "none";
			};
			
			// Get original page element only if smarter way failed
			if (element === null){
				element = document.elementFromPoint(pos[0], pos[1]);
			}
			
			// Now we can restore operator cursor
			if (operatorCursor !== null) {
				operatorCursor.style.display = origDisplay;
			};
			
			
			// Restore user scrollbar position where we found it
			if (this.windowForceScroll == false) {
				this.scrollLeftGS(origScroll['scrollLeft']);
				this.scrollTopGS(origScroll['scrollTop']);
			};
			
			if (element !== null) {
				element.focus();
				element.click();
			} else {
				console.log('not found');
			}			
		} else if (msg[1] == 'fillform' && this.formsenabled) {
			var data = msg[2].split('__SPLIT__');
			var value = data[0].replace(new RegExp('_SEL_','g'),':');
			var selectorData = data[1].replace(new RegExp('_SEL_','g'),':');
			
			var elements = [];
			if (selectorData != '' && typeof jQuery !== 'undefined' ) {
				var objects = jQuery(selectorData);
				if (objects !== null && objects.length == 1) {
					elements.push(objects[0]);					
				}
			}
			
			if (elements.length == 0) {
				elements = document.getElementsByClassName('lhc-higlighted');
			}
			
			for (var i = 0; i < elements.length; i++) {	

				// Remove our event listeners while we change 
				elements[i].removeEventListener('keyup',this.inputChangeKeyUpListener, false);
				elements[i].removeEventListener('change',this.inputChangeKeyUpListener, false);
				
				elements[i].value = value;
								
				try {
					elements[i].dispatchEvent(new Event('change', { 'bubbles': true }));
					elements[i].dispatchEvent(new Event('keyup', { 'bubbles': true }));
				} catch (err) {	}
				
				elements[i].addEventListener('keyup', this.inputChangeKeyUpListener,false);
				elements[i].addEventListener('change', this.inputChangeKeyUpListener,false);				
			};
			
			
		} else if (msg[1] == 'changeselect' && this.formsenabled) {
			var data = msg[2].split('__SPLIT__');
			var selectorData = data[1].replace(new RegExp('_SEL_','g'),':');			
			var value = data[0];
						
			var elements = [];
			if (selectorData != '' && typeof jQuery !== 'undefined' ) {
				var objects = jQuery(selectorData);
				if (objects !== null && objects.length == 1) {
					elements.push(objects[0]);					
				}
			}; 
			
			if (elements.length == 0) {
				elements = document.getElementsByClassName('lhc-higlighted');
			}	
					
			for (var i = 0; i < elements.length; i++) {	
				if (elements[i].tagName == 'SELECT'){
					elements[i].selectedIndex = value;					
					try {
						elements[i].dispatchEvent(new Event('change', { 'bubbles': true }));
					} catch (err) {	}					
				}
			};
		}
	};
	
	LHCCoBrowser.prototype.mouseEventListener = function(e) {
		var _this = this;

		var mouseX = (e.clientX || e.pageX) + this.scrollLeftGS();
		var mouseY = (e.clientY || e.pageY) + this.scrollTopGS();
		
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
					path : this.node_js_settings.path,
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

	LHCCoBrowser.prototype.scrollTopGS = function(stop)
	{
		if (typeof stop === 'undefined') {
			return document.documentElement.scrollTop || document.body.scrollTop;
		} else {
			if (document.documentElement){
				try {
					document.documentElement.scrollTop = stop;
				} catch (e) {}	
			};	
			
			if (document.body) {
				try {
					document.body.scrollTop = stop;
				} catch (e) {}	
			}			
		}
	};
	
	LHCCoBrowser.prototype.scrollLeftGS = function(sleft)
	{
		if (typeof sleft === 'undefined') {
			return document.documentElement.scrollLeft || document.body.scrollLeft;
		} else {
			if (document.documentElement){
				try {
					document.documentElement.scrollLeft = sleft;
				} catch (e) {}	
			};		
			if (document.body) {
				try {
					document.body.scrollLeft = sleft;
				} catch (e) {}	
			}
		}
	};
	
	LHCCoBrowser.prototype.onConnected = function() {
		this.isNodeConnected = true;
		this.socket.emit('join', {
			chat_id : this.chat_hash
		});
		
		if (this.initializeData !== null) {			
			this.sendData({base:location.href.match(/^(.*\/)[^\/]*$/)[1]});
			this.sendData(this.initializeData);
			this.initializeData = null;
		};
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

		if (this.NodeJsSupportEnabled == true && msg.f && msg.f == 'initialize' && this.initializeData === null)
		{
			this.initializeData = msg;
		};
	
		var _this = this;
		this.sendCommands.push(msg);
		if (this.updateTimeout === null) {
			this.updateTimeout = setTimeout(function() {

				if (window.XDomainRequest) xhr = new XDomainRequest();
				else if (window.XMLHttpRequest) xhr = new XMLHttpRequest();
				else xhr = new ActiveXObject("Microsoft.XMLHTTP");
				xhr.open("POST", _this.url, true);
				if (typeof xhr.setRequestHeader !== 'undefined'){
					xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				};
				xhr.send("data=" + encodeURIComponent(lh_inst.JSON.stringify(_this.sendCommands)));

				xhr.onload = function() {
					var response = lh_inst.JSON.parse(xhr.responseText);
					// stop mirroring if initialize request return an error
					if (response !== "undefined" && response.disableShare == "true") {
						alert(response.error_msg);
						_this.stopMirroring();
					}
				};

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
			document.removeEventListener('mousemove',this.mouseEventListenerCallback, false);
			document.removeEventListener('scroll',this.scrollEventListenerCallback, false);
						
			var inputs = document.getElementsByTagName("INPUT");		
			for (var i = 0; i < inputs.length; i++) {			
				inputs[i].removeEventListener('keyup', this.inputChangeKeyUpListener,false);
				inputs[i].removeEventListener('change', this.inputChangeKeyUpListener,false);
			}		
			
			var inputs = document.getElementsByTagName("TEXTAREA");		
			for (var i = 0; i < inputs.length; i++) {			
				inputs[i].removeEventListener('keyup', this.inputChangeKeyUpListener,false);
			}	
			
			var inputs = document.getElementsByTagName("SELECT");		
			for (var i = 0; i < inputs.length; i++) {
				inputs[i].removeEventListener('change', this.inputChangeKeyUpListener,false);
			}	
			
		} catch (e) {
			console.log(e);
		}
	};

	LHCCoBrowser.prototype.startMirroring = function() {
		var _this = this;
		this.mirrorClient = new TreeMirrorClient(document, {
			initialize : function(rootId, children) {
				setTimeout(function(){
					_this.initialiseBlock = false;
					_this.scrollEventListener();
					_this.sendInputsData();
				},3000);
				
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
					formsEnabled: _this.formsenabled,
					args : [ rootId, children ]
				});
				
			},
			applyChanged : function(removed, addedOrMoved, attributes, text){
				if (_this.initialiseBlock == false){
					_this.sendData({
						f : 'applyChanged',
						args : [ removed, addedOrMoved, attributes, text ]
					});
				} else {
					setTimeout(function(){
						_this.sendData({
							f : 'applyChanged',
							args : [ removed, addedOrMoved, attributes, text ]
						});
					},3100);
				}
			}
		});

		var htmlStatus = '<div id="lhc_status_mirror" style="'+this.shareStyleStatus+'">'
				+ this.trans.operator_watching + '</div>';
		var fragment = lh_inst.appendHTML(htmlStatus);
		
		document.body.appendChild(fragment);
	
		document.getElementById('lhc_status_mirror').onclick = function() {
			_this.stopMirroring();
		};
		
	};

	return LHCCoBrowser;
})();