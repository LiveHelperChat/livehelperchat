/**
 * SCEditor
 * http://www.sceditor.com/
 *
 * Copyright (C) 2011-2013, Sam Clarke (samclarke.com)
 *
 * SCEditor is licensed under the MIT license:
 *	http://www.opensource.org/licenses/mit-license.php
 *
 * @fileoverview SCEditor - A lightweight WYSIWYG BBCode and HTML editor
 * @author Sam Clarke
 * @requires jQuery
 * Modyfi : Hiền Trung, support for livehelperchat
 */

// ==ClosureCompiler==
// @output_file_name jquery.sceditor.min.js
// @compilation_level SIMPLE_OPTIMIZATIONS
// ==/ClosureCompiler==

/*jshint smarttabs: true, scripturl: true, jquery: true, devel:true, eqnull:true, curly: false */
/*global Range: true, browser*/

;(function ($, window, document) {
	'use strict';

	/**
	 * HTML templates used by the editor and default commands
	 * @type {Object}
	 * @private
	 */
	var _templates = {
		html:		'<!DOCTYPE html>' +
				'<html>' +
					'<head>' +
						'<style>.ie * {min-height: auto !important}</style>' +
						'<meta http-equiv="Content-Type" content="text/html;charset={charset}" />' +
						'<link rel="stylesheet" type="text/css" href="{style}" />' +
					'</head>' +
					'<body contenteditable="true" {spellcheck}></body>' +
				'</html>',

		toolbarButton:	'<a class="sceditor-button sceditor-button-{name}" data-sceditor-command="{name}" unselectable="on"><div unselectable="on">{dispName}</div></a>',

		emoticon:	'<img src="{url}" data-sceditor-emoticon="{key}" alt="{key}" title="{tooltip}" />',

		fontOpt:	'<a class="sceditor-font-option" href="#" data-font="{font}"><font face="{font}">{font}</font></a>',

		sizeOpt:	'<a class="sceditor-fontsize-option" data-size="{size}" href="#"><font size="{size}">{size}</font></a>',

		pastetext:	'<div><label for="txt">{label}</label> ' +
				'<textarea cols="20" rows="7" id="txt"></textarea></div>' +
				'<div><input type="button" class="button" value="{insert}" /></div>',

		table:		'<div><label for="rows">{rows}</label><input type="text" id="rows" value="2" /></div>' +
				'<div><label for="cols">{cols}</label><input type="text" id="cols" value="2" /></div>' +
				'<div><input type="button" class="button" value="{insert}" /></div>',

		image:		'<div><label for="link">{url}</label> <input type="text" id="image" value="http://" /></div>' +
				'<div><label for="width">{width}</label> <input type="text" id="width" size="2" /></div>' +
				'<div><label for="height">{height}</label> <input type="text" id="height" size="2" /></div>' +
				'<div><input type="button" class="button" value="{insert}" /></div>',

		email:		'<div><label for="email">{label}</label> <input type="text" id="email" /></div>' +
				'<div><input type="button" class="button" value="{insert}" /></div>',

		link:		'<div><label for="link">{url}</label> <input type="text" id="link" value="http://" /></div>' +
				'<div><label for="des">{desc}</label> <input type="text" id="des" /></div>' +
				'<div><input type="button" class="button" value="{ins}" /></div>',

		youtubeMenu:	'<div><label for="link">{label}</label> <input type="text" id="link" value="http://" /></div><div><input type="button" class="button" value="{insert}" /></div>',

		youtube:	'<iframe width="560" height="315" src="http://www.youtube.com/embed/{id}?wmode=opaque" data-youtube-id="{id}" frameborder="0" allowfullscreen></iframe>'
	};

	/**
	 * <p>Replaces any params in a template with the passed params.</p>
	 *
	 * <p>If createHTML is passed it will use jQuery to create the HTML. The
	 * same as doing: $(editor.tmpl("html", {params...}));</p>
	 *
	 * @param {string} templateName
	 * @param {Object} params
	 * @param {Boolean} createHTML
	 * @private
	 */
	var _tmpl = function(name, params, createHTML) {
		var template = _templates[name];

		$.each(params, function(name, val) {
			template = template.replace(new RegExp('\\{' + name + '\\}', 'g'), val);
		});

		if(createHTML)
			template = $(template);

		return template;
	};

	/**
	 * SCEditor - A lightweight WYSIWYG editor
	 *
	 * @param {Element} el The textarea to be converted
	 * @return {Object} options
	 * @class sceditor
	 * @name jQuery.sceditor
	 */
	$.sceditor = function (el, options) {
		/**
		 * Alias of this
		 * @private
		 */
		var base = this;

		/**
		 * The textarea element being replaced
		 * @private
		 */
		var original  = el.get ? el.get(0) : el;
		var $original = $(original);

		/**
		 * The div which contains the editor and toolbar
		 * @private
		 */
		var $editorContainer;

		/**
		 * The editors toolbar
		 * @private
		 */
		var $toolbar;

		/**
		 * The editors iframe which should be in design mode
		 * @private
		 */
		var $wysiwygEditor;
		var wysiwygEditor;

		/**
		 * The WYSIWYG editors body element
		 * @private
		 */
		var $wysiwygBody;

		/**
		 * The WYSIWYG editors document
		 * @private
		 */
		var $wysiwygDoc;

		/**
		 * The editors textarea for viewing source
		 * @private
		 */
		var $sourceEditor;
		var sourceEditor;

		/**
		 * The current dropdown
		 * @private
		 */
		var $dropdown;

		/**
		 * Array of all the commands key press functions
		 * @private
		 * @type {Array}
		 */
		var keyPressFuncs = [];

		/**
		 * Store the last cursor position. Needed for IE because it forgets
		 * @private
		 */
		var lastRange;

		/**
		 * The editors locale
		 * @private
		 */
		var locale;

		/**
		 * Stores a cache of preloaded images
		 * @private
		 * @type {Array}
		 */
		var preLoadCache = [];

		/**
		 * The editors rangeHelper instance
		 * @type {jQuery.sceditor.rangeHelper}
		 * @private
		 */
		var rangeHelper;

		/**
		 * Tags which require the new line fix
		 * @type {Array}
		 * @private
		 */
		var requireNewLineFix = [];

		/**
		 * An array of button state handlers
		 * @type {Array}
		 * @private
		 */
		var btnStateHandlers = [];

		/**
		 * Element which gets focused to blur the editor.
		 *
		 * This will be null until blur() is called.
		 * @type {HTMLElement}
		 * @private
		 */
		var $blurElm;

		/**
		 * Plugin manager instance
		 * @type {jQuery.sceditor.PluginManager}
		 * @private
		 */
		var pluginManager;

		/**
		 * The current node containing the selection/caret
		 * @type {Node}
		 * @private
		 */
		var currentNode;

		/**
		 * The first block level parent of the current node
		 * @type {node}
		 * @private
		 */
		var currentBlockNode;

		/**
		 * The current node selection/caret
		 * @type {Object}
		 * @private
		 */
		var currentSelection;

		/**
		 * Used to make sure only 1 selection changed check is called every 100ms.
		 * Helps improve performance as it is checked a lot.
		 * @type {Boolean}
		 * @private
		 */
		var isSelectionCheckPending;

		/**
		 * If content is required (equivalent to the HTML5 required attribute)
		 * @type {Boolean}
		 * @private
		 */
		var isRequired;

		/**
		 * The inline CSS style element. Will be undefined until css() is called
		 * for the first time.
		 * @type {HTMLElement}
		 * @private
		 */
		var inlineCss;

		/**
		 * Object containing a list of shortcut handlers
		 * @type {Object}
		 * @private
		 */
		var shortcutHandlers = {};

		/**
		 * An array of all the current emoticons.
		 *
		 * Only used or populated when emoticonsCompat is enabled.
		 * @type {Array}
		 * @private
		 */
		var currentEmoticons = [];

		/**
		 * Private functions
		 * @private
		 */
		var	init,
			replaceEmoticons,
			handleCommand,
			saveRange,
			initEditor,
			initPlugins,
			initLocale,
			initToolBar,
			initOptions,
			initEvents,
			initCommands,
			initResize,
			initEmoticons,
			getWysiwygDoc,
			handlePasteEvt,
			handlePasteData,
			handleKeyDown,
			handleBackSpace,
			handleKeyPress,
			handleFormReset,
			handleMouseDown,
			handleEvent,
			handleDocumentClick,
			handleWindowResize,
			updateToolBar,
			updateActiveButtons,
			sourceEditorSelectedText,
			appendNewLine,
			checkSelectionChanged,
			checkNodeChanged,
			autofocus,
			emoticonsKeyPress,
			emoticonsCheckWhitespace,
			currentStyledBlockNode;

		/**
		 * All the commands supported by the editor
		 * @name commands
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.commands = $.extend(true, {}, (options.commands || $.sceditor.commands));

		/**
		 * Options for this editor instance
		 * @name opts
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.opts = options = $.extend({}, $.sceditor.defaultOptions, options);


		/**
		 * Creates the editor iframe and textarea
		 * @private
		 */
		init = function () {
			$original.data("sceditor", base);

			// Clone any objects in options
			$.each(options, function(key, val) {
				if($.isPlainObject(val))
					options[key] = $.extend(true, {}, val);
			});

			// Load locale
			if(options.locale && options.locale !== 'en')
				initLocale();

			$editorContainer = $('<div class="sceditor-container" />')
				.insertAfter($original)
				.css('z-index', options.zIndex);

			// Add IE version to the container to allow IE specific CSS
			// fixes without using CSS hacks or conditional comments
			if($.sceditor.ie)
				$editorContainer.addClass('ie ie' + $.sceditor.ie);

			isRequired = !!$original.attr('required');
			$original.removeAttr('required');

			// create the editor
			initPlugins();
			initEmoticons();

			initToolBar();
			initEditor();
			initCommands();
			initOptions();
			initEvents();

			// force into source mode if is a browser that can't handle
			// full editing
			if(!$.sceditor.isWysiwygSupported)
				base.toggleSourceMode();

			var loaded = function() {
				$(window).unbind('load', loaded);

				if(options.autofocus)
					autofocus();

				if(options.autoExpand)
					base.expandToContent();

				// Page width might have changed after CSS is loaded so
				// call handleWindowResize to update any % based dimensions
				handleWindowResize();
			};
			$(window).load(loaded);
			if(document.readyState && document.readyState === 'complete')
				loaded();

			updateActiveButtons();
			pluginManager.call('ready');
		};

		initPlugins = function() {
			var plugins   = options.plugins;
			plugins       = plugins ? plugins.toString().split(',') : [];
			pluginManager = new $.sceditor.PluginManager(base);

			$.each(plugins, function(idx, plugin) {
				pluginManager.register($.trim(plugin));
			});
		};

		/**
		 * Init the locale variable with the specified locale if possible
		 * @private
		 * @return void
		 */
		initLocale = function() {
			var lang;

			if($.sceditor.locale[options.locale])
				locale = $.sceditor.locale[options.locale];
			else
			{
				lang = options.locale.split('-');

				if($.sceditor.locale[lang[0]])
					locale = $.sceditor.locale[lang[0]];
			}

			if(locale && locale.dateFormat)
				options.dateFormat = locale.dateFormat;
		};

		/**
		 * Creates the editor iframe and textarea
		 * @private
		 */
		initEditor = function () {
			var doc, tabIndex;

			$sourceEditor  = $('<textarea></textarea>').hide();
			$wysiwygEditor = $('<iframe frameborder="0"></iframe>');

			if(!options.spellcheck)
				$sourceEditor.attr('spellcheck', 'false');

			if(window.location.protocol === 'https:')
				$wysiwygEditor.attr('src', 'javascript:false');

			// add the editor to the HTML and store the editors element
			$editorContainer.append($wysiwygEditor).append($sourceEditor);
			wysiwygEditor = $wysiwygEditor[0];
			sourceEditor  = $sourceEditor[0];

			base.width(options.width || $original.outerWidth());
			base.height(options.height || $original.height());

			doc = getWysiwygDoc();
			doc.open();
			doc.write(_tmpl('html', { spellcheck: options.spellcheck ? '' : 'spellcheck="false"', charset: options.charset, style: options.style }));
			doc.close();

			$wysiwygDoc  = $(doc);
			$wysiwygBody = $(doc.body);

			base.readOnly(!!options.readOnly);

			// Add IE version class to the HTML element so can apply
			// conditional styling without CSS hacks
			if($.sceditor.ie)
				$wysiwygDoc.find('html').addClass('ie ie' + $.sceditor.ie);

			// iframe overflow fix for iOS, also fixes an IE issue with the
			// editor not getting focus when clicking inside
			if($.sceditor.ios || $.sceditor.ie)
			{
				$wysiwygBody.height('100%');

				if(!$.sceditor.ie)
					$wysiwygBody.bind('touchend', base.focus);
			}

			rangeHelper = new $.sceditor.rangeHelper(wysiwygEditor.contentWindow);

			// load any textarea value into the editor
			base.val($original.hide().val());

			tabIndex = $original.attr('tabindex');
			$sourceEditor.attr('tabindex', tabIndex);
			$wysiwygEditor.attr('tabindex', tabIndex);
		};

		/**
		 * Initialises options
		 * @private
		 */
		initOptions = function() {
			// auto-update original textbox on blur if option set to true
			if(options.autoUpdate)
			{
				$wysiwygBody.bind('blur', base.updateOriginal);
				$sourceEditor.bind('blur', base.updateOriginal);
			}

			if(options.rtl === null)
				options.rtl = $sourceEditor.css('direction') === 'rtl';

			base.rtl(!!options.rtl);

			if(options.autoExpand)
				$wysiwygDoc.bind('keyup', base.expandToContent);

			if(options.resizeEnabled)
				initResize();

			$editorContainer.attr('id', options.id);
			base.emoticons(options.emoticonsEnabled);
		};

		/**
		 * Initialises events
		 * @private
		 */
		initEvents = function() {
			$(document).click(handleDocumentClick);

			$(original.form)
				.bind('reset', handleFormReset)
				.submit(base.updateOriginal);

			$(window).bind('resize orientationChanged', handleWindowResize);

			$wysiwygBody
				.keypress(handleKeyPress)
				.keydown(handleKeyDown)
				.keydown(handleBackSpace)
				.keyup(appendNewLine)
				.bind('paste', handlePasteEvt)
				.bind($.sceditor.ie ? 'selectionchange' : 'keyup focus blur contextmenu mouseup touchend click', checkSelectionChanged)
				.bind('keydown keyup keypress focus blur contextmenu', handleEvent);

			if(options.emoticonsCompat && window.getSelection)
				$wysiwygBody.keyup(emoticonsCheckWhitespace);

			$sourceEditor.bind('keydown keyup keypress focus blur contextmenu', handleEvent).keydown(handleKeyDown);

			$wysiwygDoc
				.keypress(handleKeyPress)
				.mousedown(handleMouseDown)
				.bind($.sceditor.ie ? 'selectionchange' : 'focus blur contextmenu mouseup click', checkSelectionChanged)
				.bind('beforedeactivate keyup', saveRange)
				.keyup(appendNewLine)
				.focus(function() {
					lastRange = null;
				});

			$editorContainer
				.bind('selectionchanged', checkNodeChanged)
				.bind('selectionchanged', updateActiveButtons)
				.bind('selectionchanged', handleEvent)
				.bind('nodechanged', handleEvent);
		};

		/**
		 * Creates the toolbar and appends it to the container
		 * @private
		 */
		initToolBar = function () {
			var	$group, $button,
				exclude = (options.toolbarExclude || '').split(','),
				groups  = options.toolbar.split('|');

			$toolbar = $('<div class="sceditor-toolbar" unselectable="on" />');
			$.each(groups, function(idx, group) {
				$group  = $('<div class="sceditor-group" />');

				$.each(group.split(','), function(idx, button) {
					// The button must be a valid command and not excluded
					if(!base.commands[button] || $.inArray(button, exclude) > -1)
						return;

					$button = _tmpl('toolbarButton', {
							name: button,
							dispName: base._(base.commands[button].tooltip || button)
						}, true);

					$button.data('sceditor-txtmode', !!base.commands[button].txtExec);
					$button.data('sceditor-wysiwygmode', !!base.commands[button].exec);
					$button.click(function() {
						var $this = $(this);
						if(!$this.hasClass('disabled'))
							handleCommand($this, base.commands[button]);

						updateActiveButtons();
						return false;
					});

					//if(base.commands[button].tooltip)
					//	$button.attr('title', base._(base.commands[button].tooltip));

					if(!base.commands[button].exec)
						$button.addClass('disabled');

					if(base.commands[button].shortcut)
						base.addShortcut(base.commands[button].shortcut, button);

					$group.append($button);
				});

				// Exclude empty groups
				if($group[0].firstChild)
					$toolbar.append($group);
			});

			if (options.customizeToolbar)
				$toolbar.append(options.customizeToolbar);
				
			// append the toolbar to the toolbarContainer option if given
			$(options.toolbarContainer || $editorContainer).append($toolbar);
		};

		/**
		 * Creates an array of all the key press functions
		 * like emoticons, ect.
		 * @private
		 */
		initCommands = function () {
			$.each(base.commands, function (name, cmd) {
				if(cmd.keyPress)
					keyPressFuncs.push(cmd.keyPress);

				if(cmd.forceNewLineAfter && $.isArray(cmd.forceNewLineAfter))
					requireNewLineFix = $.merge(requireNewLineFix, cmd.forceNewLineAfter);

				if(cmd.state)
					btnStateHandlers.push({ name: name, state: cmd.state });
				// exec string commands can be passed to queryCommandState
				else if(typeof cmd.exec === 'string')
					btnStateHandlers.push({ name: name, state: cmd.exec });
			});

			appendNewLine();
		};

		/**
		 * Creates the resizer.
		 * @private
		 */
		initResize = function () {
			var	minHeight, maxHeight, minWidth, maxWidth, mouseMoveFunc, mouseUpFunc,
				$grip       = $('<div class="sceditor-grip" />'),
				// cover is used to cover the editor iframe so document still gets mouse move events
				$cover      = $('<div class="sceditor-resize-cover" />'),
				startX      = 0,
				startY      = 0,
				startWidth  = 0,
				startHeight = 0,
				origWidth   = $editorContainer.width(),
				origHeight  = $editorContainer.height(),
				dragging    = false,
				rtl         = base.rtl();

			minHeight = options.resizeMinHeight || origHeight / 1.5;
			maxHeight = options.resizeMaxHeight || origHeight * 2.5;
			minWidth  = options.resizeMinWidth  || origWidth  / 1.25;
			maxWidth  = options.resizeMaxWidth  || origWidth  * 1.25;

			mouseMoveFunc = function (e) {
				// iOS must use window.event
				if(e.type === 'touchmove')
					e = window.event;

				var	newHeight = startHeight + (e.pageY - startY),
					newWidth  = rtl ? startWidth - (e.pageX - startX) : startWidth + (e.pageX - startX);

				if(maxWidth > 0 && newWidth > maxWidth)
					newWidth = maxWidth;

				if(maxHeight > 0 && newHeight > maxHeight)
					newHeight = maxHeight;

				if(!options.resizeWidth || newWidth < minWidth || (maxWidth > 0 && newWidth > maxWidth))
					newWidth = false;

				if(!options.resizeHeight || newHeight < minHeight || (maxHeight > 0 && newHeight > maxHeight))
					newHeight = false;

				if(newWidth || newHeight)
				{
					base.dimensions(newWidth, newHeight);

					// The resize cover will not fill the container in IE6 unless a height is specified.
					if($.sceditor.ie < 7)
						$editorContainer.height(newHeight);
				}

				e.preventDefault();
			};

			mouseUpFunc = function (e) {
				if(!dragging)
					return;

				dragging = false;

				$cover.hide();
				$editorContainer.removeClass('resizing').height('auto');
				$(document).unbind('touchmove mousemove', mouseMoveFunc);
				$(document).unbind('touchend mouseup', mouseUpFunc);

				e.preventDefault();
			};

			$editorContainer.append($grip);
			$editorContainer.append($cover.hide());

			$grip.bind('touchstart mousedown', function (e) {
				// iOS must use window.event
				if(e.type === 'touchstart')
					e = window.event;

				startX      = e.pageX;
				startY      = e.pageY;
				startWidth  = $editorContainer.width();
				startHeight = $editorContainer.height();
				dragging    = true;

				$editorContainer.addClass('resizing');
				$cover.show();
				$(document).bind('touchmove mousemove', mouseMoveFunc);
				$(document).bind('touchend mouseup', mouseUpFunc);

				// The resize cover will not fill the container in IE6 unless a height is specified.
				if($.sceditor.ie < 7)
					$editorContainer.height(startHeight);

				e.preventDefault();
			});
		};

		/**
		 * Prefixes and preloads the emoticon images
		 * @private
		 */
		initEmoticons = function () {
			var	emoticon,
				emoticons = options.emoticons,
				root      = options.emoticonsRoot;

			if(!$.isPlainObject(emoticons) || !options.emoticonsEnabled)
				return;

			$.each(emoticons, function (idx, val) {
				$.each(val, function (key, url) {
					// Prefix emoticon root to emoticon urls
					if(root)
					{
						url = {
							url: root + (url.url || url),
							tooltip: url.tooltip || key
						};

						emoticons[idx][key] = url;
					}

					// Preload the emoticon
					// Idea from: http://engineeredweb.com/blog/09/12/preloading-images-jquery-and-javascript
					emoticon     = document.createElement('img');
					emoticon.src = url.url || url;
					preLoadCache.push(emoticon);
				});
			});
		};

		/**
		 * Autofocus the editor
		 * @private
		 */
		autofocus = function() {
			var	rng, elm, txtPos,
				doc      = $wysiwygDoc[0],
				body     = $wysiwygBody[0],
				focusEnd = !!options.autofocusEnd;

			// Can't focus invisible elements
			if(!$editorContainer.is(':visible'))
				return;

			if(base.sourceMode())
			{
				txtPos = sourceEditor.value.length;

				if(sourceEditor.setSelectionRange)
					sourceEditor.setSelectionRange(txtPos, txtPos);
				else if (sourceEditor.createTextRange)
				{
					rng = sourceEditor.createTextRange();
					rng.moveEnd('character', txtPos);
					rng.moveStart('character', txtPos);
					rangeHelper.selectRange(rng);
				}
			}
			else // WYSIWYG mode
			{
				$.sceditor.dom.removeWhiteSpace(body);

				if(focusEnd)
				{
					if(!(elm = body.lastChild))
						$wysiwygBody.append((elm = doc.createElement('div')));

					while(elm.lastChild)
					{
						elm = elm.lastChild;

						if(/br/i.test(elm.nodeName) && elm.previousSibling)
							elm = elm.previousSibling;
					}
				}
				else
					elm = body.firstChild;

				if(doc.createRange)
				{
					rng = doc.createRange();

					if(/br/i.test(elm.nodeName))
						rng.setStartBefore(elm);
					else
						rng.selectNodeContents(elm);

					rng.collapse(false);
				}
				else
				{
					rng = body.createTextRange();
					rng.moveToElementText(elm.nodeType !== 3 ? elm : elm.parentNode);
					rng.collapse(false);
				}
				rangeHelper.selectRange(rng);

				if(focusEnd)
				{
					$wysiwygDoc.scrollTop(body.scrollHeight);
					$wysiwygBody.scrollTop(body.scrollHeight);
				}
			}

			base.focus();
		};

		/**
		 * Gets if the editor is read only
		 *
		 * @since 1.3.5
		 * @function
		 * @memberOf jQuery.sceditor.prototype
		 * @name readOnly
		 * @return {Boolean}
		 */
		/**
		 * Sets if the editor is read only
		 *
		 * @param {boolean} readOnly
		 * @since 1.3.5
		 * @function
		 * @memberOf jQuery.sceditor.prototype
		 * @name readOnly^2
		 * @return {this}
		 */
		base.readOnly = function(readOnly) {
			if(typeof readOnly !== 'boolean')
				return $sourceEditor.attr('readonly') === 'readonly';

			$wysiwygBody[0].contentEditable = !readOnly;

			if(!readOnly)
				$sourceEditor.removeAttr('readonly');
			else
				$sourceEditor.attr('readonly', 'readonly');

			updateToolBar(readOnly);

			return this;
		};

		/**
		 * Gets if the editor is in RTL mode
		 *
		 * @since 1.4.1
		 * @function
		 * @memberOf jQuery.sceditor.prototype
		 * @name rtl
		 * @return {Boolean}
		 */
		/**
		 * Sets if the editor is in RTL mode
		 *
		 * @param {boolean} rtl
		 * @since 1.4.1
		 * @function
		 * @memberOf jQuery.sceditor.prototype
		 * @name rtl^2
		 * @return {this}
		 */
		base.rtl = function(rtl) {
			var dir = rtl ? 'rtl' : 'ltr';

			if(typeof rtl !== 'boolean')
				return $sourceEditor.attr('dir') === 'rtl';

			$wysiwygBody.attr('dir', dir);
			$sourceEditor.attr('dir', dir);

			$editorContainer
				.removeClass('rtl')
				.removeClass('ltr')
				.addClass(dir);

			return this;
		};

		/**
		 * Updates the toolbar to disable/enable the appropriate buttons
		 * @private
		 */
		updateToolBar = function(disable) {
			var inSourceMode = base.inSourceMode();

			$toolbar.find('.sceditor-button').removeClass('disabled').each(function () {
				var button = $(this);

				if(disable === true || (inSourceMode && !button.data('sceditor-txtmode')))
					button.addClass('disabled');
				else if (!inSourceMode && !button.data('sceditor-wysiwygmode'))
					button.addClass('disabled');
			});
		};

		/**
		 * Gets the width of the editor in pixels
		 *
		 * @since 1.3.5
		 * @function
		 * @memberOf jQuery.sceditor.prototype
		 * @name width
		 * @return {int}
		 */
		/**
		 * Sets the width of the editor
		 *
		 * @param {int} width Width in pixels
		 * @since 1.3.5
		 * @function
		 * @memberOf jQuery.sceditor.prototype
		 * @name width^2
		 * @return {this}
		 */
		/**
		 * Sets the width of the editor
		 *
		 * The saveWidth specifies if to save the width. The stored width can be
		 * used for things like restoring from maximized state.
		 *
		 * @param {int}		height			Width in pixels
		 * @param {boolean}	[saveWidth=true]	If to store the width
		 * @since 1.4.1
		 * @function
		 * @memberOf jQuery.sceditor.prototype
		 * @name width^3
		 * @return {this}
		 */
		base.width = function (width, saveWidth) {
			if(!width && width !== 0)
				return $editorContainer.width();

			base.dimensions(width, null, saveWidth);

			return this;
		};

		/**
		 * Returns an object with the properties width and height
		 * which are the width and height of the editor in px.
		 *
		 * @since 1.4.1
		 * @function
		 * @memberOf jQuery.sceditor.prototype
		 * @name dimensions
		 * @return {object}
		 */
		/**
		 * <p>Sets the width and/or height of the editor.</p>
		 *
		 * <p>If width or height is not numeric it is ignored.</p>
		 *
		 * @param {int}	width	Width in px
		 * @param {int}	height	Height in px
		 * @since 1.4.1
		 * @function
		 * @memberOf jQuery.sceditor.prototype
		 * @name dimensions^2
		 * @return {this}
		 */
		/**
		 * <p>Sets the width and/or height of the editor.</p>
		 *
		 * <p>If width or height is not numeric it is ignored.</p>
		 *
		 * <p>The save argument specifies if to save the new sizes.
		 * The saved sizes can be used for things like restoring from
		 * maximized state. This should normally be left as true.</p>
		 *
		 * @param {int}		width		Width in px
		 * @param {int}		height		Height in px
		 * @param {boolean}	[save=true]	If to store the new sizes
		 * @since 1.4.1
		 * @function
		 * @memberOf jQuery.sceditor.prototype
		 * @name dimensions^3
		 * @return {this}
		 */
		base.dimensions = function(width, height, save) {
			// IE6 & IE7 add 2 pixels to the source mode textarea height which must be ignored.
			// Doesn't seem to be any way to fix it with only CSS
			var ieBorderBox = $.sceditor.ie < 8 || document.documentMode < 8 ? 2 : 0;

			// set undefined width/height to boolean false
			width  = (!width && width !== 0) ? false : width;
			height = (!height && height !== 0) ? false : height;

			if(width === false && height === false)
				return { width: base.width(), height: base.height() };

			if(typeof $wysiwygEditor.data('outerWidthOffset') === 'undefined')
				base.updateStyleCache();

			if(width !== false)
			{
				if(save !== false)
					options.width = width;

				if(height === false)
				{
					height = $editorContainer.height();
					save   = false;
				}

				$editorContainer.width(width);
				if(width && width.toString().indexOf('%') > -1)
					width = $editorContainer.width();

				$wysiwygEditor.width(width - $wysiwygEditor.data('outerWidthOffset'));
				$sourceEditor.width(width - $sourceEditor.data('outerWidthOffset'));

				// Fix overflow issue with iOS not breaking words unless a width is set
				if($.sceditor.ios && $wysiwygBody)
					$wysiwygBody.width(width - $wysiwygEditor.data('outerWidthOffset') - ($wysiwygBody.outerWidth(true) - $wysiwygBody.width()));
			}

			if(height !== false)
			{
				if(save !== false)
					options.height = height;

				// Convert % based heights to px
				if(height && height.toString().indexOf('%') > -1)
				{
					height = $editorContainer.height(height).height();
					$editorContainer.height('auto');
				}

				height -= !options.toolbarContainer ? $toolbar.outerHeight(true) : 0;
				$wysiwygEditor.height(height - $wysiwygEditor.data('outerHeightOffset'));
				$sourceEditor.height(height - ieBorderBox - $sourceEditor.data('outerHeightOffset'));
			}

			return this;
		};

		/**
		 * Updates the CSS styles cache. Shouldn't be needed unless changing the editors theme.
		 *
		 * @since 1.4.1
		 * @function
		 * @memberOf jQuery.sceditor.prototype
		 * @name updateStyleCache
		 * @return {int}
		 */
		base.updateStyleCache = function() {
			// caching these improves FF resize performance
			$wysiwygEditor.data('outerWidthOffset', $wysiwygEditor.outerWidth(true) - $wysiwygEditor.width());
			$sourceEditor.data('outerWidthOffset', $sourceEditor.outerWidth(true) - $sourceEditor.width());

			$wysiwygEditor.data('outerHeightOffset', $wysiwygEditor.outerHeight(true) - $wysiwygEditor.height());
			$sourceEditor.data('outerHeightOffset', $sourceEditor.outerHeight(true) - $sourceEditor.height());
		};

		/**
		 * Gets the height of the editor in px
		 *
		 * @since 1.3.5
		 * @function
		 * @memberOf jQuery.sceditor.prototype
		 * @name height
		 * @return {int}
		 */
		/**
		 * Sets the height of the editor
		 *
		 * @param {int} height Height in px
		 * @since 1.3.5
		 * @function
		 * @memberOf jQuery.sceditor.prototype
		 * @name height^2
		 * @return {this}
		 */
		/**
		 * Sets the height of the editor
		 *
		 * The saveHeight specifies if to save the height. The stored height can be
		 * used for things like restoring from maximized state.
		 *
		 * @param {int} height Height in px
		 * @param {boolean} [saveHeight=true] If to store the height
		 * @since 1.4.1
		 * @function
		 * @memberOf jQuery.sceditor.prototype
		 * @name height^3
		 * @return {this}
		 */
		base.height = function (height, saveHeight) {
			if(!height && height !== 0)
				return $editorContainer.height();

			base.dimensions(null, height, saveHeight);

			return this;
		};

		/**
		 * Gets if the editor is maximised or not
		 *
		 * @since 1.4.1
		 * @function
		 * @memberOf jQuery.sceditor.prototype
		 * @name maximize
		 * @return {boolean}
		 */
		/**
		 * Sets if the editor is maximised or not
		 *
		 * @param {boolean} maximize If to maximise the editor
		 * @since 1.4.1
		 * @function
		 * @memberOf jQuery.sceditor.prototype
		 * @name maximize^2
		 * @return {this}
		 */
		base.maximize = function(maximize) {
			if(typeof maximize === 'undefined')
				return $editorContainer.is('.sceditor-maximize');

			maximize = !!maximize;

			// IE 6 fix
			if($.sceditor.ie < 7)
				$('html, body').toggleClass('sceditor-maximize', maximize);

			$editorContainer.toggleClass('sceditor-maximize', maximize);
			base.width(maximize ? '100%' : options.width, false);
			base.height(maximize ? '100%' : options.height, false);

			return this;
		};

		/**
		 * Expands the editors height to the height of it's content
		 *
		 * Unless ignoreMaxHeight is set to true it will not expand
		 * higher than the maxHeight option.
		 *
		 * @since 1.3.5
		 * @param {Boolean} [ignoreMaxHeight=false]
		 * @function
		 * @name expandToContent
		 * @memberOf jQuery.sceditor.prototype
		 * @see #resizeToContent
		 */
		base.expandToContent = function(ignoreMaxHeight) {
			var	currentHeight = $editorContainer.height(),
				height        = $wysiwygBody[0].scrollHeight || $wysiwygDoc[0].documentElement.scrollHeight,
				padding       = (currentHeight - $wysiwygEditor.height()),
				maxHeight     = options.resizeMaxHeight || ((options.height || $original.height()) * 2);

			height += padding;

			if(ignoreMaxHeight !== true && height > maxHeight)
				height = maxHeight;

			if(height > currentHeight)
				base.height(height);
		};

		/**
		 * Destroys the editor, removing all elements and
		 * event handlers.
		 *
		 * Leaves only the original textarea.
		 *
		 * @function
		 * @name destroy
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.destroy = function () {
			pluginManager.destroy();

			rangeHelper   = null;
			lastRange     = null;
			pluginManager = null;

			$(document).unbind('click', handleDocumentClick);
			$(window).unbind('resize orientationChanged', handleWindowResize);

			$(original.form)
				.unbind('reset', handleFormReset)
				.unbind('submit', base.updateOriginal);

			$wysiwygBody.unbind();
			$wysiwygDoc.unbind().find('*').remove();

			$sourceEditor.unbind().remove();
			$toolbar.remove();
			$editorContainer.unbind().find('*').unbind().remove();
			$editorContainer.remove();

			$original
				.removeData('sceditor')
				.removeData('sceditorbbcode')
				.show();

			if(isRequired)
				$original.attr('required', 'required');
		};

		/**
		 * Creates a menu item drop down
		 *
		 * @param {HTMLElement}	menuItem		The button to align the drop down with
		 * @param {string}	dropDownName		Used for styling the dropown, will be a class sceditor-dropDownName
		 * @param {HTMLElement}	content			The HTML content of the dropdown
		 * @param {bool}	[ieUnselectable=true]	If to add the unselectable attribute to all the contents elements. Stops IE from deselecting the text in the editor
		 * @function
		 * @name createDropDown
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.createDropDown = function (menuItem, dropDownName, content, ieUnselectable) {
			// first click for create second click for close
			var	css,
				onlyclose = $dropdown && $dropdown.is('.sceditor-' + dropDownName);

			base.closeDropDown();

			if (onlyclose) return;

			// IE needs unselectable attr to stop it from unselecting the text in the editor.
			// The editor can cope if IE does unselect the text it's just not nice.
			if (ieUnselectable !== false)
			{
				$(content)
					.find(':not(input,textarea)')
					.filter(function() {
						return this.nodeType===1;
					})
					.attr('unselectable', 'on');
			}

			css = {
				top: 0,
				left: 0
			};

			$.extend(css, options.dropDownCss);

			$dropdown = $('<div class="sceditor-dropdown sceditor-' + dropDownName + '" />')
				.css(css)
				.append(content)
				.appendTo($('body'))
				.click(function (e) {
					// stop clicks within the dropdown from being handled
					e.stopPropagation();
				});
			//move position
			var ddWidth = $dropdown.outerWidth();
			var ddHeight = $dropdown.outerHeight();
			var ddPos= menuItem.offset();
			ddPos.top += menuItem.outerHeight() + 5;
			if (ddPos.left + ddWidth > $('body').width())
				ddPos.left = ddPos.left + menuItem.outerWidth() - ddWidth;
			if (ddPos.top + ddHeight > $('body').height())
				ddPos.top = ddPos.top - menuItem.outerHeight() - 10 - ddHeight;
			if (ddPos.top < 0)
				ddPos.top = 0;
			if (ddPos.left < 0)
				ddPos.left = 0;
			$dropdown.offset(ddPos);
		};

		/**
		 * Handles any document click and closes the dropdown if open
		 * @private
		 */
		handleDocumentClick = function (e) {
			// ignore right clicks
			if(e.which !== 3)
				base.closeDropDown();
		};

		/**
		 * Handles the WYSIWYG editors paste event
		 * @private
		 */
		handlePasteEvt = function(e) {
			var	html, handlePaste,
				elm             = $wysiwygBody[0],
				doc             = $wysiwygDoc[0],
				checkCount      = 0,
				pastearea       = document.createElement('div'),
				prePasteContent = doc.createDocumentFragment();

			if (options.disablePasting)
				return false;

			if (!options.enablePasteFiltering)
				return;

			rangeHelper.saveRange();
			document.body.appendChild(pastearea);

			if (e && e.clipboardData && e.clipboardData.getData)
			{
				if ((html = e.clipboardData.getData('text/html')) || (html = e.clipboardData.getData('text/plain')))
				{
					pastearea.innerHTML = html;
					handlePasteData(elm, pastearea);
					return false;
				}
			}

			while(elm.firstChild)
				prePasteContent.appendChild(elm.firstChild);
// try make pastearea contenteditable and redirect to that? Might work.
// Check the tests if still exist, if not re-0create
			handlePaste = function (elm, pastearea) {
				if (elm.childNodes.length > 0)
				{
					while(elm.firstChild)
						pastearea.appendChild(elm.firstChild);

					while(prePasteContent.firstChild)
						elm.appendChild(prePasteContent.firstChild);

					handlePasteData(elm, pastearea);
				}
				else
				{
					// Allow max 25 checks before giving up.
					// Needed in case an empty string is pasted or
					// something goes wrong.
					if(checkCount > 25)
					{
						while(prePasteContent.firstChild)
							elm.appendChild(prePasteContent.firstChild);

						rangeHelper.restoreRange();
						return;
					}

					++checkCount;
					setTimeout(function () {
						handlePaste(elm, pastearea);
					}, 20);
				}
			};
			handlePaste(elm, pastearea);

			base.focus();
			return true;
		};

		/**
		 * Gets the pasted data, filters it and then inserts it.
		 * @param {Element} elm
		 * @param {Element} pastearea
		 * @private
		 */
		handlePasteData = function(elm, pastearea) {
			// fix any invalid nesting
			$.sceditor.dom.fixNesting(pastearea);
// TODO: Trigger custom paste event to allow filtering (pre and post converstion?)
			var pasteddata = pastearea.innerHTML;

			if(pluginManager.hasHandler('toSource'))
				pasteddata = pluginManager.callOnlyFirst('toSource', pasteddata, $(pastearea));

			pastearea.parentNode.removeChild(pastearea);

			if(pluginManager.hasHandler('toWysiwyg'))
				pasteddata = pluginManager.callOnlyFirst('toWysiwyg', pasteddata, true);

			rangeHelper.restoreRange();
			base.wysiwygEditorInsertHtml(pasteddata, null, true);
		};

		/**
		 * Closes any currently open drop down
		 *
		 * @param {bool} [focus=false] If to focus the editor after closing the drop down
		 * @function
		 * @name closeDropDown
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.closeDropDown = function (focus) {
			if($dropdown) {
				$dropdown.unbind().remove();
				$dropdown = null;
			}

			if(focus === true)
				base.focus();
		};

		/**
		 * Gets the WYSIWYG editors document
		 * @private
		 */
		getWysiwygDoc = function () {
			if (wysiwygEditor.contentDocument)
				return wysiwygEditor.contentDocument;

			if (wysiwygEditor.contentWindow && wysiwygEditor.contentWindow.document)
				return wysiwygEditor.contentWindow.document;

			if (wysiwygEditor.document)
				return wysiwygEditor.document;

			return null;
		};


		/**
		 * <p>Inserts HTML into WYSIWYG editor.</p>
		 *
		 * <p>If endHtml is specified, any selected text will be placed between html
		 * and endHtml. If there is no selected text html and endHtml will just be
		 * concated together.</p>
		 *
		 * @param {string} html
		 * @param {string} [endHtml=null]
		 * @param {boolean} [overrideCodeBlocking=false] If to insert the html into code tags, by default code tags only support text.
		 * @function
		 * @name wysiwygEditorInsertHtml
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.wysiwygEditorInsertHtml = function (html, endHtml, overrideCodeBlocking) {
			var	scrollTo, $marker,
				marker = '<span id="sceditor-cursor">&nbsp;</span>';

			base.focus();

// TODO: This code tag should be configurable and should maybe convert the HTML into text
			// don't apply to code elements
			if(!overrideCodeBlocking && ($(currentBlockNode).is('code') || $(currentBlockNode).parents('code').length !== 0))
				return;

			if(endHtml)
				endHtml += marker;
			else
				html += marker;

			rangeHelper.insertHTML(html, endHtml);

			// Scroll the editor to after the inserted HTML
			$marker  = $wysiwygBody.find('#sceditor-cursor');
			scrollTo = ($marker.offset().top + ($marker.outerHeight(true) * 2)) - $wysiwygEditor.height();
			$marker.remove();

// TODO: check if already in range and don't scroll if it is
			$wysiwygDoc.scrollTop(scrollTo);
			$wysiwygBody.scrollTop(scrollTo);

			rangeHelper.saveRange();
			replaceEmoticons($wysiwygBody[0]);
			rangeHelper.restoreRange();

			appendNewLine();
		};

		/**
		 * Like wysiwygEditorInsertHtml except it will convert any HTML into text
		 * before inserting it.
		 *
		 * @param {String} text
		 * @param {String} [endText=null]
		 * @function
		 * @name wysiwygEditorInsertText
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.wysiwygEditorInsertText = function (text, endText) {
			base.wysiwygEditorInsertHtml($.sceditor.escapeEntities(text), $.sceditor.escapeEntities(endText));
		};

		/**
		 * <p>Inserts text into the WYSIWYG or source editor depending on which
		 * mode the editor is in.</p>
		 *
		 * <p>If endText is specified any selected text will be placed between
		 * text and endText. If no text is selected text and endText will
		 * just be concated together.</p>
		 *
		 * @param {String} text
		 * @param {String} [endText=null]
		 * @since 1.3.5
		 * @function
		 * @name insertText
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.insertText = function (text, endText) {
			if(base.inSourceMode())
				base.sourceEditorInsertText(text, endText);
			else
				base.wysiwygEditorInsertText(text, endText);

			return this;
		};

		/**
		 * <p>Like wysiwygEditorInsertHtml but inserts text into the
		 * source mode editor instead.</p>
		 *
		 * <p>If endText is specified any selected text will be placed between
		 * text and endText. If no text is selected text and endText will
		 * just be concated together.</p>
		 *
		 * <p>The cursor will be placed after the text param. If endText is
		 * specified the cursor will be placed before endText, so passing:<br />
		 *
		 * '[b]', '[/b]'</p>
		 *
		 * <p>Would cause the cursor to be placed:<br />
		 *
		 * [b]Selected text|[/b]</p>
		 *
		 * @param {String} text
		 * @param {String} [endText=null]
		 * @since 1.4.0
		 * @function
		 * @name sourceEditorInsertText
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.sourceEditorInsertText = function (text, endText) {
			var range, start, end, txtLen, scrollTop;

			scrollTop = sourceEditor.scrollTop;
			sourceEditor.focus();

			if(typeof sourceEditor.selectionStart !== 'undefined')
			{
				start  = sourceEditor.selectionStart;
				end    = sourceEditor.selectionEnd;
				txtLen = text.length;

				if(endText)
					text += sourceEditor.value.substring(start, end) + endText;

				sourceEditor.value = sourceEditor.value.substring(0, start) + text + sourceEditor.value.substring(end, sourceEditor.value.length);

				sourceEditor.selectionStart = (start + text.length) - (endText ? endText.length : 0);
				sourceEditor.selectionEnd = sourceEditor.selectionStart;
			}
			else if(typeof document.selection.createRange !== 'undefined')
			{
				range = document.selection.createRange();

				if(endText)
					text += range.text + endText;

				range.text = text;

				if(endText)
					range.moveEnd('character', 0-endText.length);

				range.moveStart('character', range.End - range.Start);
				range.select();
			}
			else
				sourceEditor.value += text + endText;

			sourceEditor.scrollTop = scrollTop;
			sourceEditor.focus();
		};

		/**
		 * Gets the current instance of the rangeHelper class
		 * for the editor.
		 *
		 * @return jQuery.sceditor.rangeHelper
		 * @function
		 * @name getRangeHelper
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.getRangeHelper = function () {
			return rangeHelper;
		};

		/**
		 * <p>Gets the value of the editor.</p>
		 *
		 * <p>If the editor is in WYSIWYG mode it will return the filtered
		 * HTML from it (converted to BBCode if using the BBCode plugin).
		 * It it's in Source Mode it will return the unfiltered contents
		 * of the source editor (if using the BBCode plugin this will be
		 * BBCode again).</p>
		 *
		 * @since 1.3.5
		 * @return {string}
		 * @function
		 * @name val
		 * @memberOf jQuery.sceditor.prototype
		 */
		/**
		 * <p>Sets the value of the editor.</p>
		 *
		 * <p>If filter set true the val will be passed through the filter
		 * function. If using the BBCode plugin it will pass the val to
		 * the BBCode filter to convert any BBCode into HTML.</p>
		 *
		 * @param {String} val
		 * @param {Boolean} [filter=true]
		 * @return {this}
		 * @since 1.3.5
		 * @function
		 * @name val^2
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.val = function (val, filter) {
			if(typeof val === "string")
			{
				if(base.inSourceMode())
					base.setSourceEditorValue(val);
				else
				{
					if(filter !== false && pluginManager.hasHandler('toWysiwyg'))
						val = pluginManager.callOnlyFirst('toWysiwyg', val);

					base.setWysiwygEditorValue(val);
				}

				return this;
			}

			return base.inSourceMode() ?
				base.getSourceEditorValue(false) :
				base.getWysiwygEditorValue();
		};

		/**
		 * <p>Inserts HTML/BBCode into the editor</p>
		 *
		 * <p>If end is supplied any selected text will be placed between
		 * start and end. If there is no selected text start and end
		 * will be concated together.</p>
		 *
		 * <p>If the filter param is set to true, the HTML/BBCode will be
		 * passed through any plugin filters. If using the BBCode plugin
		 * this will convert any BBCode into HTML.</p>
		 *
		 * @param {String} start
		 * @param {String} [end=null]
		 * @param {Boolean} [filter=true]
		 * @param {Boolean} [convertEmoticons=true] If to convert emoticons
		 * @return {this}
		 * @since 1.3.5
		 * @function
		 * @name insert
		 * @memberOf jQuery.sceditor.prototype
		 */
		/**
		 * <p>Inserts HTML/BBCode into the editor</p>
		 *
		 * <p>If end is supplied any selected text will be placed between
		 * start and end. If there is no selected text start and end
		 * will be concated together.</p>
		 *
		 * <p>If the filter param is set to true, the HTML/BBCode will be
		 * passed through any plugin filters. If using the BBCode plugin
		 * this will convert any BBCode into HTML.</p>
		 *
		 * <p>If the allowMixed param is set to true, HTML any will not be escaped</p>
		 *
		 * @param {String} start
		 * @param {String} [end=null]
		 * @param {Boolean} [filter=true]
		 * @param {Boolean} [convertEmoticons=true] If to convert emoticons
		 * @param {Boolean} [allowMixed=false]
		 * @return {this}
		 * @since 1.4.3
		 * @function
		 * @name insert^2
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.insert = function (start, end, filter, convertEmoticons, allowMixed) {
			if(base.inSourceMode())
				base.sourceEditorInsertText(start, end);
			else
			{
				// Add the selection between start and end
				if(end)
				{
					var	html = base.getRangeHelper().selectedHtml(),
						frag = $('<div>').appendTo($('body')).hide().html(html);

					if(filter !== false && pluginManager.hasHandler('toSource'))
						html = pluginManager.callOnlyFirst('toSource', html, frag);

					frag.remove();

					start += html + end;
				}

				if(filter !== false && pluginManager.hasHandler('toWysiwyg'))
					start = pluginManager.callOnlyFirst('toWysiwyg', start, true);

				// Convert any escaped HTML back into HTML if mixed is allowed
				if(filter !== false && allowMixed === true)
				{
					start = start.replace(/&lt;/g, '<')
						.replace(/&gt;/g, '>')
						.replace(/&amp;/g, '&');
				}

				base.wysiwygEditorInsertHtml(start);
			}

			return this;
		};

		/**
		 * Gets the WYSIWYG editors HTML value.
		 *
		 * If using a plugin that filters the Ht Ml like the BBCode plugin
		 * it will return the result of the filtering (BBCode) unless the
		 * filter param is set to false.
		 *
		 * @param {bool} [filter=true]
		 * @return {string}
		 * @function
		 * @name getWysiwygEditorValue
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.getWysiwygEditorValue = function(filter) {
			var	html, ieBookmark,
				hasSelection = rangeHelper.hasSelection();

			if(hasSelection)
				rangeHelper.saveRange();
			// IE <= 8 bookmark the current TextRange position
			// and restore it after
			else if(lastRange && lastRange.getBookmark)
				ieBookmark = lastRange.getBookmark();

			$.sceditor.dom.fixNesting($wysiwygBody[0]);

			// filter the HTML and DOM through any plugins
			html = $wysiwygBody.html();

			if(filter !== false && pluginManager.hasHandler('toSource'))
				html = pluginManager.callOnlyFirst('toSource', html, $wysiwygBody);

			if(hasSelection)
			{
				// remove the last stored range for IE as it no longer applies
				rangeHelper.restoreRange();
				lastRange = null;
			}
			else if(ieBookmark)
			{
				lastRange.moveToBookmark(ieBookmark);
				lastRange = null;
			}

			return html;
		};

		/**
		 * Gets the WYSIWYG editor's iFrame Body.
		 *
		 * @return {jQuery}
		 * @function
		 * @since 1.4.3
		 * @name getBody
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.getBody = function () {
			return $wysiwygBody;
		};

		/**
		 * Gets the WYSIWYG editors container area (whole iFrame).
		 *
		 * @return {Node}
		 * @function
		 * @since 1.4.3
		 * @name getContentAreaContainer
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.getContentAreaContainer = function () {
			return $wysiwygEditor;
		};

		/**
		 * Gets the text editor value
		 *
		 * If using a plugin that filters the text like the BBCode plugin
		 * it will return the result of the filtering which is BBCode to
		 * HTML so it will return HTML. If filter is set to false it will
		 * just return the contents of the source editor (BBCode).
		 *
		 * @param {bool} [filter=true]
		 * @return {string}
		 * @function
		 * @since 1.4.0
		 * @name getSourceEditorValue
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.getSourceEditorValue = function (filter) {
			var val = $sourceEditor.val();

			if(filter !== false && pluginManager.hasHandler('toWysiwyg'))
				val = pluginManager.callOnlyFirst('toWysiwyg', val);

			return val;
		};

		/**
		 * Sets the WYSIWYG HTML editor value. Should only be the HTML
		 * contained within the body tags
		 *
		 * @param {string} value
		 * @function
		 * @name setWysiwygEditorValue
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.setWysiwygEditorValue = function (value) {
			if(!value)
				value = '<p>' + ($.sceditor.ie ? '' : '<br />') + '</p>';

			$wysiwygBody[0].innerHTML = value;
			replaceEmoticons($wysiwygBody[0]);

			appendNewLine();
		};

		/**
		 * Sets the text editor value
		 *
		 * @param {string} value
		 * @function
		 * @name setSourceEditorValue
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.setSourceEditorValue = function (value) {
			$sourceEditor.val(value);
		};

		/**
		 * Updates the textarea that the editor is replacing
		 * with the value currently inside the editor.
		 *
		 * @function
		 * @name updateOriginal
		 * @since 1.4.0
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.updateOriginal = function() {
			$original.val(base.val());
		};

		/**
		 * Replaces any emoticon codes in the passed HTML with their emoticon images
		 * @private
		 */
		replaceEmoticons = function(node) {
// TODO: Make this tag configurable.
			if(!options.emoticonsEnabled || $(node).parents('code').length)
				return;

			var	doc           = node.ownerDocument,
				emoticonCodes = [],
				emoticonRegex = [],
				emoticons     = $.extend({}, options.emoticons.more, options.emoticons.dropdown, options.emoticons.hidden);

			$.each(emoticons, function (key) {
				if(options.emoticonsCompat)
					emoticonRegex[key] = new RegExp('(>|^|\\s|\xA0|\u2002|\u2003|\u2009|&nbsp;)' + $.sceditor.regexEscape(key) + '(\\s|$|<|\xA0|\u2002|\u2003|\u2009|&nbsp;)');

				emoticonCodes.push(key);
			});

			(function convertEmoticons(node) {
				node = node.firstChild;

				while(node != null)
				{
					var	parts, key, emoticon, parsedHtml, emoticonIdx, nextSibling, startIdx,
						nodeParent  = node.parentNode,
						nodeValue   = node.nodeValue;

					// All none textnodes
					if(node.nodeType !== 3)
					{
// TODO: Make this tag configurable.
						if(!$(node).is('code'))
							 convertEmoticons(node);
					}
					else if(nodeValue)
					{
						emoticonIdx = emoticonCodes.length;
						while(emoticonIdx--)
						{
							key      = emoticonCodes[emoticonIdx];
							startIdx = options.emoticonsCompat ? nodeValue.search(emoticonRegex[key]) : nodeValue.indexOf(key);

							if(startIdx > -1)
							{
								nextSibling    = node.nextSibling;
								emoticon       = emoticons[key];
								parts          = nodeValue.substr(startIdx).split(key);
								nodeValue      = nodeValue.substr(0, startIdx) + parts.shift();
								node.nodeValue = nodeValue;

								parsedHtml = $.sceditor.dom.parseHTML(_tmpl('emoticon', {
									key: key,
									url: emoticon.url || emoticon,
									tooltip: emoticon.tooltip || key
								}), doc);

								nodeParent.insertBefore(parsedHtml[0], nextSibling);
								nodeParent.insertBefore(doc.createTextNode(parts.join(key)), nextSibling);
							}
						}
					}

					node = node.nextSibling;
				}
			}(node));

			if(options.emoticonsCompat)
				currentEmoticons = $wysiwygBody.find('img[data-sceditor-emoticon]');
		};

		/**
		 * If the editor is in source code mode
		 *
		 * @return {bool}
		 * @function
		 * @name inSourceMode
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.inSourceMode = function () {
			return $editorContainer.hasClass('sourceMode');
		};

		/**
		 * Gets if the editor is in sourceMode
		 *
		 * @return boolean
		 * @function
		 * @name sourceMode
		 * @memberOf jQuery.sceditor.prototype
		 */
		/**
		 * Sets if the editor is in sourceMode
		 *
		 * @param {bool} enable
		 * @return {this}
		 * @function
		 * @name sourceMode^2
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.sourceMode = function (enable) {
			if(typeof enable !== 'boolean')
				return base.inSourceMode();

			if((base.inSourceMode() && !enable) || (!base.inSourceMode() && enable))
				base.toggleSourceMode();

			return this;
		};

		/**
		 * Switches between the WYSIWYG and source modes
		 *
		 * @function
		 * @name toggleSourceMode
		 * @since 1.4.0
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.toggleSourceMode = function () {
			// don't allow switching to WYSIWYG if doesn't support it
			if(!$.sceditor.isWysiwygSupported && base.inSourceMode())
				return;

			base.blur();

			if(base.inSourceMode())
				base.setWysiwygEditorValue(base.getSourceEditorValue());
			else
				base.setSourceEditorValue(base.getWysiwygEditorValue());

			lastRange = null;
			$sourceEditor.toggle();
			$wysiwygEditor.toggle();

			if(!base.inSourceMode())
				$editorContainer.removeClass('wysiwygMode').addClass('sourceMode');
			else
				$editorContainer.removeClass('sourceMode').addClass('wysiwygMode');

			updateToolBar();
			updateActiveButtons();
		};

		/**
		 * Gets the selected text of the source editor
		 * @return {String}
		 * @private
		 */
		sourceEditorSelectedText = function () {
			sourceEditor.focus();

			if(sourceEditor.selectionStart != null)
				return sourceEditor.value.substring(sourceEditor.selectionStart, sourceEditor.selectionEnd);
			else if(document.selection.createRange)
				return document.selection.createRange().text;
		};

		/**
		 * Handles the passed command
		 * @private
		 */
		handleCommand = function (caller, command) {
			// check if in text mode and handle text commands
			if(base.inSourceMode())
			{
				if(command.txtExec)
				{
					if($.isArray(command.txtExec))
						base.sourceEditorInsertText.apply(base, command.txtExec);
					else
						command.txtExec.call(base, caller, sourceEditorSelectedText());
				}

				return;
			}

			if(!command.exec)
				return;

			if($.isFunction(command.exec))
				command.exec.call(base, caller);
			else
				base.execCommand(command.exec, command.hasOwnProperty('execParam') ? command.execParam : null);
		};

		/**
		 * Saves the current range. Needed for IE because it forgets
		 * where the cursor was and what was selected
		 * @private
		 */
		saveRange = function () {
			/* this is only needed for IE */
			if($.sceditor.ie)
				lastRange = rangeHelper.selectedRange();
		};

		/**
		 * Executes a command on the WYSIWYG editor
		 *
		 * @param {String} command
		 * @param {String|Boolean} [param]
		 * @function
		 * @name execCommand
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.execCommand = function (command, param) {
			var	executed    = false,
				$parentNode = $(rangeHelper.parentNode());

			base.focus();

			// don't apply any commands to code elements
			if($parentNode.is('code') || $parentNode.parents('code').length !== 0)
				return;

			try
			{
				executed = $wysiwygDoc[0].execCommand(command, false, param);
			}
			catch (e) {}

			// show error if execution failed and an error message exists
			if(!executed && base.commands[command] && base.commands[command].errorMessage)
				alert(base._(base.commands[command].errorMessage));
		};

		/**
		 * Checks if the current selection has changed and triggers
		 * the selectionchanged event if it has.
		 *
		 * In browsers other than IE, it will check at most once every 100ms.
		 * This is because only IE has a selection changed event.
		 * @private
		 */
		checkSelectionChanged = function() {
			var check = function() {
				// rangeHelper could be null if editor was destroyed
				// before the timeout had finished
				if(rangeHelper && !rangeHelper.compare(currentSelection))
				{
					currentSelection = rangeHelper.cloneSelected();
					$editorContainer.trigger($.Event('selectionchanged'));
				}

				isSelectionCheckPending = false;
			};

			if(isSelectionCheckPending)
				return;

			isSelectionCheckPending = true;

			// In IE, this is only called on the selectionchanged event so no need to
			// limit checking as it should always be valid to do.
			if($.sceditor.ie)
				check();
			else
				setTimeout(check, 100);
		};

		/**
		 * Checks if the current node has changed and triggers
		 * the nodechanged event if it has
		 * @private
		 */
		checkNodeChanged = function() {
			// check if node has changed
			var	oldNode,
				node = rangeHelper.parentNode();

			if(currentNode !== node)
			{
				oldNode          = currentNode;
				currentNode      = node;
				currentBlockNode = rangeHelper.getFirstBlockParent(node);

				$editorContainer.trigger($.Event('nodechanged', { oldNode: oldNode, newNode: currentNode }));
			}
		};

		/**
		 * <p>Gets the current node that contains the selection/caret in WYSIWYG mode.</p>
		 *
		 * <p>Will be null in sourceMode or if there is no selection.</p>
		 * @return {Node}
		 * @function
		 * @name currentNode
		 * @memberOf jQuery.sceditor.prototype
		 */
		base.currentNode = function() {
			return currentNode;
		};

		/**
		 * <p>Gets the first block level node that contains the selection/caret in WYSIWYG mode.</p>
		 *
		 * <p>Will be null in sourceMode or if there is no selection.</p>
		 * @return {Node}
		 * @function
		 * @name currentBlockNode
		 * @memberOf jQuery.sceditor.prototype
		 * @since 1.4.4
		 */
		base.currentBlockNode = function() {
			return currentBlockNode;
		};

		/**
		 * Updates if buttons are active or not
		 * @private
		 */
		updateActiveButtons = function(e) {
			var	state, stateHandler, firstBlock, $button, parent,
				doc          = $wysiwygDoc[0],
				i            = btnStateHandlers.length,
				inSourceMode = base.sourceMode();

			if(!base.sourceMode() && !base.readOnly())
			{
				parent     = e ? e.newNode : rangeHelper.parentNode();
				firstBlock = rangeHelper.getFirstBlockParent(parent);

				while(i--)
				{
					state        = 0;
					stateHandler = btnStateHandlers[i];
					$button      = $toolbar.find('.sceditor-button-' + stateHandler.name);

					if(inSourceMode && !$button.data('sceditor-txtmode'))
						$button.addClass('disabled');
					else if (!inSourceMode && !$button.data('sceditor-wysiwygmode'))
						$button.addClass('disabled');
					else
					{
						if(typeof stateHandler.state === 'string')
						{
							try
							{
								state = doc.queryCommandEnabled(stateHandler.state) ? 0 : -1;

								if(state > -1)
									state = doc.queryCommandState(stateHandler.state) ? 1 : 0;
							}
							catch(ex) {}
						}
						else
							state = stateHandler.state.call(base, parent, firstBlock);

						if(state < 0)
							$button.addClass('disabled');
						else
							$button.removeClass('disabled');

						if(state > 0)
							$button.addClass('active');
						else
							$button.removeClass('active');
					}
				}
			}
			else
				$toolbar.find('.sceditor-button').removeClass('active');
		};

		/**
		 * Handles any key press in the WYSIWYG editor
		 *
		 * @private
		 */
		handleKeyPress = function(e) {
			var	$parentNode,
				i = keyPressFuncs.length;

			base.closeDropDown();

			$parentNode = $(currentNode);

			// "Fix" (OK it's a cludge) for blocklevel elements being duplicated in some browsers when
			// enter is pressed instead of inserting a newline
			if(e.which === 13)
			{
				if($parentNode.is('code,blockquote,pre') || $parentNode.parents('code,blockquote,pre').length !== 0)
				{
					lastRange = null;
					base.wysiwygEditorInsertHtml('<br />', null, true);
					return false;
				}
				else if (base.opts.keyEnter) {
					base.opts.keyEnter();
					e.preventDefault();
					return false;
				}
			}
			if (base.opts.keyPress) {
				base.opts.keyPress();
			}

// TODO: Remove keyPressFuncs, which are deprecated
			// don't apply to code elements
			if($parentNode.is('code') || $parentNode.parents('code').length !== 0)
				return;

			while(i--)
				keyPressFuncs[i].call(base, e, wysiwygEditor, $sourceEditor);
		};

		/**
		 * Makes sure that if there is a code or quote tag at the
		 * end of the editor, that there is a new line after it.
		 *
		 * If there wasn't a new line at the end you wouldn't be able
		 * to enter any text after a code/quote tag
		 * @return {void}
		 * @private
		 */
		appendNewLine = function() {
			var name, requiresNewLine, div;

			$.sceditor.dom.rTraverse($wysiwygBody[0], function(node) {
				name = node.nodeName.toLowerCase();
// TODO: Replace requireNewLineFix with just a block level fix for any block that has styling and
// any block that isn't a plain <p> or <div>
				if($.inArray(name, requireNewLineFix) > -1)
					requiresNewLine = true;

				// find the last non-empty text node or line break.
				if((node.nodeType === 3 && !/^\s*$/.test(node.nodeValue)) || name === 'br' ||
					($.sceditor.ie && !node.firstChild && !$.sceditor.dom.isInline(node, false)))
				{
					// this is the last text or br node, if its in a code or quote tag
					// then add a newline to the end of the editor
					if(requiresNewLine)
					{
						div = $wysiwygBody[0].ownerDocument.createElement('div');
						div.className = 'sceditor-nlf';
						div.innerHTML = !$.sceditor.ie ? '<br />' : '';
						$wysiwygBody[0].appendChild(div);
					}

					return false;
				}
			});
		};

		/**
		 * Handles form reset event
		 * @private
		 */
		handleFormReset = function() {
			base.val($original.val());
		};

		/**
		 * Handles any mousedown press in the WYSIWYG editor
		 * @private
		 */
		handleMouseDown = function() {
			base.closeDropDown();
			lastRange = null;
		};

		/**
		 * Handles the window resize event. Needed to resize then editor
		 * when the window size changes in fluid designs.
		 * @ignore
		 */
		handleWindowResize = function() {
			var	height = options.height,
				width  = options.width;

			if(!base.maximize())
			{
				if(height && height.toString().indexOf("%") > -1)
					base.height(height);

				if(width && width.toString().indexOf("%") > -1)
					base.width(width);
			}
			else
				base.dimensions('100%', '100%', false);
		};

		/**
		 * Translates the string into the locale language.
		 *
		 * Replaces any {0}, {1}, {2}, ect. with the params provided.
		 *
		 * @param {string} str
		 * @param {...String} args
		 * @return {string}
		 * @function
		 * @name _
		 * @memberOf jQuery.sceditor.prototype
		 */
		base._ = function() {
			var args = arguments;

			if(locale && locale[args[0]])
				args[0] = locale[args[0]];

			return args[0].replace(/\{(\d+)\}/g, function(str, p1) {
				return typeof args[p1-0+1] !== 'undefined' ?
					args[p1-0+1] :
					'{' + p1 + '}';
			});
		};

		/**
		 * Passes events on to any handlers
		 * @private
		 * @return void
		 */
		handleEvent = function(e) {
			var	customEvent,
				clone = $.extend({}, e);

			// Send event to all plugins
			pluginManager.call(clone.type + 'Event', e, base);

			// convert the event into a custom event to send
			delete clone.type;
			customEvent = $.Event((e.target === sourceEditor ? 'scesrc' : 'scewys') + e.type, clone);

			$editorContainer.trigger.apply($editorContainer, [customEvent, base]);

			if(customEvent.isDefaultPrevented())
				e.preventDefault();

			if(customEvent.isImmediatePropagationStopped())
				customEvent.stopImmediatePropagation();

			if(customEvent.isPropagationStopped())
				customEvent.stopPropagation();
		};

		/**
		 * <p>Binds a handler to the specified events</p>
		 *
		 * <p>This function only binds to a limited list of supported events.<br />
		 * The supported events are:
		 * <ul>
		 *   <li>keyup</li>
		 *   <li>keydown</li>
		 *   <li>Keypress</li>
		 *   <li>blur</li>
		 *   <li>focus</li>
		 *   <li>nodechanged<br />
		 *       When the current node containing the selection changes in WYSIWYG mode</li>
		 *   <li>contextmenu</li>
		 * </ul>
		 * </p>
		 *
		 * <p>The events param should be a string containing the event(s)
		 * to bind this handler to. If multiple, they should be separated
		 * by spaces.</p>
		 *
		 * @param  {String} events
		 * @param  {Function} handler
		 * @param  {Boolean} excludeWysiwyg If to exclude adding this handler to the WYSIWYG editor
		 * @param  {Boolean} excludeSource  if to exclude adding this handler to the source editor
		 * @return {this}
		 * @function
		 * @name bind
		 * @memberOf jQuery.sceditor.prototype
		 * @since 1.4.1
		 */
		base.bind = function(events, handler, excludeWysiwyg, excludeSource) {
			var i  = events.length;
			events = events.split(" ");

			while(i--)
			{
				if($.isFunction(handler))
				{
					// Use custom events to allow passing the instance as the 2nd argument.
					// Also allows unbinding without unbinding the editors own event handlers.
					if(!excludeWysiwyg)
						$editorContainer.bind('scewys' + events[i], handler);

					if(!excludeSource)
						$editorContainer.bind('scesrc' + events[i], handler);
				}
			}

			return this;
		};

		/**
		 * Unbinds an event that was bound using bind().
		 *
		 * @param  {String} events
		 * @param  {Function} handler
		 * @param  {Boolean} excludeWysiwyg If to exclude unbinding this handler from the WYSIWYG editor
		 * @param  {Boolean} excludeSource  if to exclude unbinding this handler from the source editor
		 * @return {this}
		 * @function
		 * @name unbind
		 * @memberOf jQuery.sceditor.prototype
		 * @since 1.4.1
		 * @see bind
		 */
		base.unbind = function(events, handler, excludeWysiwyg, excludeSource) {
			var i  = events.length;
			events = events.split(" ");

			while(i--)
			{
				if($.isFunction(handler))
				{
					if(!excludeWysiwyg)
						$editorContainer.unbind('scewys' + events[i], handler);

					if(!excludeSource)
						$editorContainer.unbind('scesrc' + events[i], handler);
				}
			}

			return this;
		};

		/**
		 * Blurs the editors input area
		 *
		 * @return {this}
		 * @function
		 * @name blur
		 * @memberOf jQuery.sceditor.prototype
		 * @since 1.3.6
		 */
		/**
		 * Adds a handler to the editors blur event
		 *
		 * @param  {Function} handler
		 * @param  {Boolean} excludeWysiwyg If to exclude adding this handler to the WYSIWYG editor
		 * @param  {Boolean} excludeSource  if to exclude adding this handler to the source editor
		 * @return {this}
		 * @function
		 * @name blur^2
		 * @memberOf jQuery.sceditor.prototype
		 * @since 1.4.1
		 */
		base.blur = function(handler, excludeWysiwyg, excludeSource) {
			if($.isFunction(handler))
				base.bind('blur', handler, excludeWysiwyg, excludeSource);
			else if(!base.sourceMode())
			{
				// Must use an element that isn't display:hidden or visibility:hidden for iOS
				// so create a special blur element to use
				if(!$blurElm)
					$blurElm = $('<input style="position:absolute;width:0;height:0;opacity:0;border:0;padding:0;filter:alpha(opacity=0)" type="text" />').appendTo($editorContainer);

				$blurElm.removeAttr('disabled').show().focus().blur().hide().attr('disabled', 'disabled');
			}
			else
				$sourceEditor.blur();

			return this;
		};

		/**
		 * Fucuses the editors input area
		 *
		 * @return {this}
		 * @function
		 * @name focus
		 * @memberOf jQuery.sceditor.prototype
		 */
		/**
		 * Adds an event handler to the focus event
		 *
		 * @param  {Function} handler
		 * @param  {Boolean} excludeWysiwyg If to exclude adding this handler to the WYSIWYG editor
		 * @param  {Boolean} excludeSource  if to exclude adding this handler to the source editor
		 * @return {this}
		 * @function
		 * @name focus^2
		 * @memberOf jQuery.sceditor.prototype
		 * @since 1.4.1
		 */
		base.focus = function (handler, excludeWysiwyg, excludeSource) {
			if($.isFunction(handler))
				base.bind('focus', handler, excludeWysiwyg, excludeSource);
			else
			{
				if(!base.inSourceMode())
				{
					wysiwygEditor.contentWindow.focus();
					$wysiwygBody[0].focus();

					// Needed for IE < 9
					if(lastRange)
					{
						rangeHelper.selectRange(lastRange);

						// remove the stored range after being set.
						// If the editor loses focus it should be
						// saved again.
						lastRange = null;
					}
				}
				else
					sourceEditor.focus();
			}

			return this;
		};

		/**
		 * Adds a handler to the key down event
		 *
		 * @param  {Function} handler
		 * @param  {Boolean} excludeWysiwyg If to exclude adding this handler to the WYSIWYG editor
		 * @param  {Boolean} excludeSource  if to exclude adding this handler to the source editor
		 * @return {this}
		 * @function
		 * @name keyDown
		 * @memberOf jQuery.sceditor.prototype
		 * @since 1.4.1
		 */
		base.keyDown = function(handler, excludeWysiwyg, excludeSource) {
			return base.bind('keydown', handler, excludeWysiwyg, excludeSource);
		};

		/**
		 * Adds a handler to the key press event
		 *
		 * @param  {Function} handler
		 * @param  {Boolean} excludeWysiwyg If to exclude adding this handler to the WYSIWYG editor
		 * @param  {Boolean} excludeSource  if to exclude adding this handler to the source editor
		 * @return {this}
		 * @function
		 * @name keyPress
		 * @memberOf jQuery.sceditor.prototype
		 * @since 1.4.1
		 */
		base.keyPress = function(handler, excludeWysiwyg, excludeSource) {
			return base.bind('keypress', handler, excludeWysiwyg, excludeSource);
		};

		/**
		 * Adds a handler to the key up event
		 *
		 * @param  {Function} handler
		 * @param  {Boolean} excludeWysiwyg If to exclude adding this handler to the WYSIWYG editor
		 * @param  {Boolean} excludeSource  if to exclude adding this handler to the source editor
		 * @return {this}
		 * @function
		 * @name keyUp
		 * @memberOf jQuery.sceditor.prototype
		 * @since 1.4.1
		 */
		base.keyUp = function(handler, excludeWysiwyg, excludeSource) {
			return base.bind('keyup', handler, excludeWysiwyg, excludeSource);
		};

		/**
		 * <p>Adds a handler to the node changed event.</p>
		 *
		 * <p>Happends whenever the node containing the selection/caret changes in WYSIWYG mode.</p>
		 *
		 * @param  {Function} handler
		 * @return {this}
		 * @function
		 * @name nodeChanged
		 * @memberOf jQuery.sceditor.prototype
		 * @since 1.4.1
		 */
		base.nodeChanged = function(handler) {
			return base.bind('nodechanged', handler, false, true);
		};

		/**
		 * <p>Adds a handler to the selection changed event</p>
		 *
		 * <p>Happens whenever the selection changes in WYSIWYG mode.</p>
		 *
		 * @param  {Function} handler
		 * @return {this}
		 * @function
		 * @name selectionChanged
		 * @memberOf jQuery.sceditor.prototype
		 * @since 1.4.1
		 */
		base.selectionChanged = function(handler) {
			return base.bind('selectionchanged', handler, false, true);
		};

		/**
		 * Emoticons keypress handler
		 * @private
		 */
		emoticonsKeyPress = function (e) {
			var	pos     = 0,
				curChar = String.fromCharCode(e.which);
// TODO: Make configurable
			if($(currentBlockNode).is('code') || $(currentBlockNode).parents('code').length)
				return;

			if(!base.emoticonsCache)
			{
				base.emoticonsCache = [];
				
				$.each($.extend({}, options.emoticons.more, options.emoticons.dropdown, options.emoticons.hidden), function(key, url) {
					base.emoticonsCache[pos++] = [
						key,
						_tmpl('emoticon', {
							key: key,
							url: url.url || url,
							tooltip: url.tooltip || key
						})
					];
				});

				base.emoticonsCache.sort(function(a, b) {
					return a[0].length - b[0].length;
				});

				base.longestEmoticonCode = base.emoticonsCache[base.emoticonsCache.length - 1][0].length;
			}

			if(base.getRangeHelper().raplaceKeyword(base.emoticonsCache, true, true, base.longestEmoticonCode, options.emoticonsCompat, curChar))
			{
				if(options.emoticonsCompat)
					currentEmoticons = $wysiwygBody.find('img[data-sceditor-emoticon]');

				return (/^\s$/.test(curChar) && options.emoticonsCompat);
			}
		};

		/**
		 * Makes sure emoticons are surrounded by whitespace
		 * @private
		 */
		emoticonsCheckWhitespace = function() {
			if(!currentEmoticons.length)
				return;

			var	prev, next, parent, range, previousText, rangeStartContainer,
				currentBlock = base.currentBlockNode(),
				rangeStart   = false,
				noneWsRegex  = /[^\s\xA0\u2002\u2003\u2009]+/;

			currentEmoticons = $.map(currentEmoticons, function(emoticon) {
				// Ignore emotiocons that have been removed from DOM
				if(!emoticon || !emoticon.parentNode)
					return null;

				if(!$.contains(currentBlock, emoticon))
					return emoticon;

				prev         = emoticon.previousSibling;
				next         = emoticon.nextSibling;
				previousText = prev.nodeValue;

				// For IE's HTMLPhraseElement
				if(previousText === null)
					previousText = prev.innerText || '';

				if((!prev || !noneWsRegex.test(prev.nodeValue.slice(-1))) && (!next || !noneWsRegex.test((next.nodeValue || '')[0])))
					return emoticon;

				parent              = emoticon.parentNode;
				range               = rangeHelper.cloneSelected();
				rangeStartContainer = range.startContainer;
				previousText        = previousText + $(emoticon).data('sceditor-emoticon');

				// Store current caret position
				if(rangeStartContainer === next)
					rangeStart = previousText.length + range.startOffset;
				else if(rangeStartContainer === currentBlock && currentBlock.childNodes[range.startOffset] === next)
					rangeStart = previousText.length;
				else if(rangeStartContainer === prev)
					rangeStart = range.startOffset;

				if(!next || next.nodeType !== 3)
					next = parent.insertBefore(parent.ownerDocument.createTextNode(''), next);

				next.insertData(0, previousText);
				parent.removeChild(prev);
				parent.removeChild(emoticon);

				// Need to update the range starting position if it has been modified
				if(rangeStart !== false)
				{
					range.setStart(next, rangeStart);
					range.collapse(true);
					rangeHelper.selectRange(range);
				}

				return null;
			});
		};

		/**
		 * Gets if emoticons are currently enabled
		 * @return {boolean}
		 * @function
		 * @name emoticons
		 * @memberOf jQuery.sceditor.prototype
		 * @since 1.4.2
		 */
		/**
		 * Enables/disables emoticons
		 *
		 * @param {boolean} enable
		 * @return {this}
		 * @function
		 * @name emoticons^2
		 * @memberOf jQuery.sceditor.prototype
		 * @since 1.4.2
		 */
		base.emoticons = function(enable) {
			if(!enable && enable !== false)
				return options.emoticonsEnabled;

			options.emoticonsEnabled = enable;

			if(enable)
			{
				$wysiwygBody.keypress(emoticonsKeyPress);

				if(!base.sourceMode())
				{
					rangeHelper.saveRange();

					replaceEmoticons($wysiwygBody[0]);
					currentEmoticons = $wysiwygBody.find('img[data-sceditor-emoticon]');

					rangeHelper.restoreRange();
				}
			}
			else
			{
				$wysiwygBody.find('img[data-sceditor-emoticon]').replaceWith(function() {
					return $(this).data('sceditor-emoticon');
				});

				currentEmoticons = [];
				$wysiwygBody.unbind('keypress', emoticonsKeyPress);
			}

			return this;
		};

		/**
		 * Gets the current WYSIWYG editors inline CSS
		 *
		 * @return {string}
		 * @function
		 * @name css
		 * @memberOf jQuery.sceditor.prototype
		 * @since 1.4.3
		 */
		/**
		 * Sets inline CSS for the WYSIWYG editor
		 *
		 * @param {string} css
		 * @return {this}
		 * @function
		 * @name css^2
		 * @memberOf jQuery.sceditor.prototype
		 * @since 1.4.3
		 */
		base.css = function(css) {
			if(!inlineCss)
				inlineCss = $('<style id="#inline" />').appendTo($wysiwygDoc.find('head'))[0];

			if(typeof css != 'string')
				return inlineCss.styleSheet ? inlineCss.styleSheet.cssText : inlineCss.innerHTML;

			if(inlineCss.styleSheet)
				inlineCss.styleSheet.cssText = css;
			else
				inlineCss.innerHTML = css;

			return this;
		};

		/**
		 * Handles the keydown event, used for shortcuts
		 * @private
		 */
		handleKeyDown = function(e) {
			var	shortcut   = [],
				shift_keys = {
					'`':'~', '1':'!', '2':'@', '3':'#', '4':'$', '5':'%', '6':'^',
					'7':'&', '8':'*', '9':'(', '0':')', '-':'_', '=':'+', ';':':',
					'\'':'"', ',':'<', '.':'>', '/':'?', '\\':'|', '[':'{', ']':'}'
				},
				special_keys = {
					8:'backspace', 9:'tab', 13:'enter', 19:'pause', 20:'capslock', 27:'esc',
					32:'space', 33:'pageup', 34:'pagedown', 35:'end', 36:'home', 37:'left',
					38:'up', 39:'right', 40:'down', 45:'insert', 46:'del', 91: 'win', 92: 'win',
					93:'select', 96:'0', 97:'1', 98:'2', 99:'3', 100:'4', 101:'5', 102:'6',
					103:'7', 104:'8', 105:'9', 106:'*', 107:'+', 109:'-', 110:'.', 111:'/',
					112:'f1', 113:'f2', 114:'f3', 115:'f4', 116:'f5', 117:'f6', 118:'f7',
					119:'f8', 120:'f9', 121:'f10', 122:'f11', 123:'f12', 144:'numlock',
					145:'scrolllock', 186:';', 187:'=', 188:',', 189:'-', 190:'.', 191:'/',
					192:'`', 219:'[', 220:'\\', 221:']', 222:'\''
				},
				numpad_shift_keys = {
					109:'-', 110:'del', 111:'/', 96:'0', 97:'1', 98:'2', 99:'3',
					100:'4', 101:'5', 102:'6', 103:'7', 104:'8', 105:'9'
				},
				which     = e.which,
				character = special_keys[which] || String.fromCharCode(which).toLowerCase();

			if(e.ctrlKey)
				shortcut.push('ctrl');

			if(e.altKey)
				shortcut.push('alt');

			if(e.shiftKey)
			{
				shortcut.push('shift');

				if(numpad_shift_keys[which])
					character = numpad_shift_keys[which];
				else if(shift_keys[character])
					character = shift_keys[character];
			}

			// Shift is 16, ctrl is 17 and alt is 18
			if(character && (which < 16 || which > 18))
				shortcut.push(character);

			shortcut = shortcut.join('+');
			if(shortcutHandlers[shortcut])
				return shortcutHandlers[shortcut].call(base);
		};

		/**
		 * Adds a shortcut handler to the editor
		 * @param  {String}          shortcut
		 * @param  {String|Function} cmd
		 * @return {jQuery.sceditor}
		 */
		base.addShortcut = function(shortcut, cmd) {
			shortcut = shortcut.toLowerCase();

			if(typeof cmd === "string")
			{
				shortcutHandlers[shortcut] = function() {
					handleCommand($toolbar.find('.sceditor-button-' + cmd), base.commands[cmd]);

					return false;
				};
			}
			else
				shortcutHandlers[shortcut] = cmd;

			return this;
		};

		/**
		 * Removes a shortcut handler
		 * @param  {String} shortcut
		 * @return {jQuery.sceditor}
		 */
		base.removeShortcut = function(shortcut) {
			delete shortcutHandlers[shortcut.toLowerCase()];

			return this;
		};

		/**
		 * Handles the backspace key press
		 *
		 * Will remove block styling like quotes/code ect if at the start.
		 * @private
		 */
		handleBackSpace = function(e) {
			var	node, offset, tmpRange, range, parent;

			// 8 is the backspace key
			if($.sceditor.ie || options.disableBlockRemove || e.which !== 8 || !(range = rangeHelper.selectedRange()))
				return;

			if(!window.getSelection)
			{
				node     = range.parentElement();
				tmpRange = $wysiwygDoc[0].selection.createRange();

				// Select te entire parent and set the end as start of the current range
				tmpRange.moveToElementText(node);
				tmpRange.setEndPoint('EndToStart', range);

				// Number of characters selected is the start offset
				// relative to the parent node
				offset = tmpRange.text.length;
			}
			else
			{
				node   = range.startContainer;
				offset = range.startOffset;
			}

			if(offset !== 0 || !(parent = currentStyledBlockNode()))
				return;

			while(node !== parent)
			{
				while(node.previousSibling)
				{
					node = node.previousSibling;

					// Everything but empty text nodes before the cursor
					// should prevent the style from being removed
					if(node.nodeType !== 3 || node.nodeValue)
						return;
				}

				if(!(node = node.parentNode))
					return;
			}

			if(!parent || $(parent).is('body'))
				return;

			// The backspace was pressed at the start of
			// the container so clear the style
			base.clearBlockFormatting(parent);
			return false;
		};

		/**
		 * Gets the first styled block node that contains the cursor
		 * @return {HTMLElement}
		 */
		currentStyledBlockNode = function() {
			var block = currentBlockNode;

			while(!$.sceditor.dom.hasStyling(block))
			{
				if(!(block = block.parentNode) || $(block).is('body'))
					return;
			}

			return block;
		};

		/**
		 * Clears the formatting of the passed block element.
		 *
		 * If block is false, if will clear the styling of the first
		 * block level element that contains the cursor.
		 * @param  {HTMLElement} block
		 * @since 1.4.4
		 */
		base.clearBlockFormatting = function(block) {
			block = block || currentStyledBlockNode();

			if(!block || $(block).is('body'))
				return this;

			rangeHelper.saveRange();

			lastRange       = null;
			block.className = '';

			$(block).attr('style', '');

			if(!$(block).is('p,div'))
				$.sceditor.dom.convertElement(block, 'p');

			rangeHelper.restoreRange();
			return this;
		};

		// run the initializer
		init();
	};

	/**
	 * <p>Detects the version of IE is being used if any.</p>
	 *
	 * <p>Returns the IE version number or undefined if not IE.</p>
	 *
	 * <p>Source: https://gist.github.com/527683 with extra code for IE 10 detection</p>
	 * @function
	 * @name ie
	 * @memberOf jQuery.sceditor
	 * @type {int}
	 */
	$.sceditor.ie = (function(){
		var	undef,
			v   = 3,
			div = document.createElement('div'),
			all = div.getElementsByTagName('i');

		do {
			div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->';
		} while (all[0]);

		// Detect IE 10 as it doesn't support conditional comments.
		if((document.documentMode && document.all && window.atob))
			v = 10;

		// Detect IE 11
		if(v === 4 && document.documentMode)
			v = 11;

		return v > 4 ? v : undef;
	}());

	/**
	 * <p>Detects if the browser is iOS</p>
	 *
	 * <p>Needed to fix iOS specific bugs/</p>
	 *
	 * @function
	 * @name ios
	 * @memberOf jQuery.sceditor
	 * @type {Boolean}
	 */
	$.sceditor.ios = /iPhone|iPod|iPad| wosbrowser\//i.test(navigator.userAgent);

	/**
	 * If the browser supports WYSIWYG editing (e.g. older mobile browsers).
	 * @function
	 * @name isWysiwygSupported
	 * @memberOf jQuery.sceditor
	 * @return {Boolean}
	 */
	$.sceditor.isWysiwygSupported = (function() {
		var	match, isUnsupported,
			contentEditable          = $('<div contenteditable="true">')[0].contentEditable,
			contentEditableSupported = typeof contentEditable !== 'undefined' && contentEditable !== 'inherit',
			userAgent                = navigator.userAgent;

		if(!contentEditableSupported)
			return false;

		// I think blackberry supports it or will at least
		// give a valid value for the contentEditable detection above
		// so it's not included here.

		// I hate having to use UA sniffing but some mobile browsers say they support
		// contentediable/design mode when it isn't usable (i.e. you can't enter text, ect.).
		// This is the only way I can think of to detect them which is also how every other
		// editor I've seen deals with this
		isUnsupported = /Opera Mobi|Opera Mini/i.test(userAgent);

		if(/Android/i.test(userAgent))
		{
			isUnsupported = true;

			if(/Safari/.test(userAgent))
			{
				// Android browser 534+ supports content editable
				// This also matches Chrome which supports content editable too
				match = /Safari\/(\d+)/.exec(userAgent);
				isUnsupported = (!match || !match[1] ? true : match[1] < 534);
			}
		}

		// Amazon Silk doesn't but as it uses webkit like android
		// it might in a later version if it uses version >= 534
		if(/ Silk\//i.test(userAgent))
		{
			match = /AppleWebKit\/(\d+)/.exec(userAgent);
			isUnsupported = (!match || !match[1] ? true : match[1] < 534);
		}

		// iOS 5+ supports content editable
		if($.sceditor.ios)
			isUnsupported = !/OS [5-9](_\d)+ like Mac OS X/i.test(userAgent);

		// FireFox does support WYSIWYG on mobiles so override
		// any previous value if using FF
		if(/fennec/i.test(userAgent))
			isUnsupported = false;

		if(/OneBrowser/i.test(userAgent))
			isUnsupported = false;

		// UCBrowser works but doesn't give a unique user agent
		if(navigator.vendor === 'UCWEB')
			isUnsupported = false;

		return !isUnsupported;
	}());

	/**
	 * Escapes a string so it's safe to use in regex
	 *
	 * @param {String} str
	 * @return {String}
	 * @name regexEscape
	 * @memberOf jQuery.sceditor
	 */
	$.sceditor.regexEscape = function(str) {
		return str.replace(/[\$\?\[\]\.\*\(\)\|\\]/g, '\\$&');
	};

	/**
	 * Escapes all HTML entities in a string
	 *
	 * @param {String} str
	 * @return {String}
	 * @name escapeEntities
	 * @memberOf jQuery.sceditor
	 * @since 1.4.1
	 */
	$.sceditor.escapeEntities = function(str) {
		if(!str)
			return str;

		return str.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/ {2}/g, ' &nbsp;')
			.replace(/\r\n|\r/g, '\n')
			.replace(/\n/g, '<br />');
	};

	/**
	 * Map containing the loaded SCEditor locales
	 * @type {Object}
	 * @name locale
	 * @memberOf jQuery.sceditor
	 */
	$.sceditor.locale = {};

	/**
	 * Map of all the commands for SCEditor
	 * @type {Object}
	 * @name commands
	 * @memberOf jQuery.sceditor
	 */
	$.sceditor.commands = {
		// START_COMMAND: Bold
		bold: {
			exec: 'bold',
			tooltip: 'Bold',
			shortcut: 'ctrl+b'
		},
		// END_COMMAND
		// START_COMMAND: Italic
		italic: {
			exec: 'italic',
			tooltip: 'Italic',
			shortcut: 'ctrl+i'
		},
		// END_COMMAND
		// START_COMMAND: Underline
		underline: {
			exec: 'underline',
			tooltip: 'Underline',
			shortcut: 'ctrl+u'
		},
		// END_COMMAND
		// START_COMMAND: Strikethrough
		strike: {
			exec: 'strikethrough',
			tooltip: 'Strikethrough'
		},
		// END_COMMAND
		// START_COMMAND: Subscript
		subscript: {
			exec: 'subscript',
			tooltip: 'Subscript'
		},
		// END_COMMAND
		// START_COMMAND: Superscript
		superscript: {
			exec: 'superscript',
			tooltip: 'Superscript'
		},
		// END_COMMAND

		// START_COMMAND: Left
		left: {
			exec: 'justifyleft',
			tooltip: 'Align left'
		},
		// END_COMMAND
		// START_COMMAND: Centre
		center: {
			exec: 'justifycenter',
			tooltip: 'Center'
		},
		// END_COMMAND
		// START_COMMAND: Right
		right: {
			exec: 'justifyright',
			tooltip: 'Align right'
		},
		// END_COMMAND
		// START_COMMAND: Justify
		justify: {
			exec: 'justifyfull',
			tooltip: 'Justify'
		},
		// END_COMMAND

		// START_COMMAND: Font
		font: {
			_dropDown: function(editor, caller, callback) {
				var	fonts   = editor.opts.fonts.split(','),
					content = $('<div />'),
					/** @private */
					clickFunc = function () {
						callback($(this).data('font'));
						editor.closeDropDown(true);
						return false;
					};

				for (var i=0; i < fonts.length; i++)
					content.append(_tmpl('fontOpt', {font: fonts[i]}, true).click(clickFunc));

				editor.createDropDown(caller, 'font-picker', content);
			},
			exec: function (caller) {
				var editor = this;

				$.sceditor.command.get('font')._dropDown(
					editor,
					caller,
					function(fontName) {
						editor.execCommand('fontname', fontName);
					}
				);
			},
			tooltip: 'Font Name'
		},
		// END_COMMAND
		// START_COMMAND: Size
		size: {
			_dropDown: function(editor, caller, callback) {
				var	content   = $('<div />'),
					/** @private */
					clickFunc = function (e) {
						callback($(this).data('size'));
						editor.closeDropDown(true);
						e.preventDefault();
					};

				for (var i=1; i<= 7; i++)
					content.append(_tmpl('sizeOpt', {size: i}, true).click(clickFunc));

				editor.createDropDown(caller, 'fontsize-picker', content);
			},
			exec: function (caller) {
				var editor = this;

				$.sceditor.command.get('size')._dropDown(
					editor,
					caller,
					function(fontSize) {
						editor.execCommand('fontsize', fontSize);
					}
				);
			},
			tooltip: 'Font Size'
		},
		// END_COMMAND
		// START_COMMAND: Colour
		color: {
			_dropDown: function(editor, caller, callback) {
				var	i, x, color, colors,
					genColor     = {r: 255, g: 255, b: 255},
					content      = $('<div />'),
					colorColumns = editor.opts.colors?editor.opts.colors.split('|'):new Array(21),
					// IE is slow at string concation so use an array
					html         = [],
					cmd          = $.sceditor.command.get('color');

				if(!cmd._htmlCache)
				{
					for (i=0; i < colorColumns.length; ++i)
					{
						colors = colorColumns[i]?colorColumns[i].split(','):new Array(21);

						html.push('<div class="sceditor-color-column">');
						for (x=0; x < colors.length; ++x)
						{
							// use pre defined colour if can otherwise use the generated color
							color = colors[x] || "#" + genColor.r.toString(16) + genColor.g.toString(16) + genColor.b.toString(16);

							html.push('<a href="#" class="sceditor-color-option" style="background-color: '+color+'" data-color="'+color+'"></a>');

							// calculate the next generated color
							if(x%5===0)
							{
								genColor.g -= 51;
								genColor.b = 255;
							}
							else
								genColor.b -= 51;
						}
						html.push('</div>');

						// calculate the next generated color
						if(i%5===0)
						{
							genColor.r -= 51;
							genColor.g = 255;
							genColor.b = 255;
						}
						else
						{
							genColor.g = 255;
							genColor.b = 255;
						}
					}

					cmd._htmlCache = html.join('');
				}

				content.append(cmd._htmlCache)
					.find('a')
					.click(function (e) {
						callback($(this).attr('data-color'));
						editor.closeDropDown(true);
						e.preventDefault();
					});

				editor.createDropDown(caller, 'color-picker', content);
			},
			exec: function (caller) {
				var editor = this;

				$.sceditor.command.get('color')._dropDown(
					editor,
					caller,
					function(color) {
						editor.execCommand('forecolor', color);
					}
				);
			},
			tooltip: 'Font Color'
		},
		// END_COMMAND
		// START_COMMAND: Remove Format
		removeformat: {
			exec: 'removeformat',
			tooltip: 'Remove Formatting'
		},
		// END_COMMAND

		// START_COMMAND: Cut
		cut: {
			exec: 'cut',
			tooltip: 'Cut',
			errorMessage: 'Your browser does not allow the cut command. Please use the keyboard shortcut Ctrl/Cmd-X'
		},
		// END_COMMAND
		// START_COMMAND: Copy
		copy: {
			exec: 'copy',
			tooltip: 'Copy',
			errorMessage: 'Your browser does not allow the copy command. Please use the keyboard shortcut Ctrl/Cmd-C'
		},
		// END_COMMAND
		// START_COMMAND: Paste
		paste: {
			exec: 'paste',
			tooltip: 'Paste',
			errorMessage: 'Your browser does not allow the paste command. Please use the keyboard shortcut Ctrl/Cmd-V'
		},
		// END_COMMAND
		// START_COMMAND: Paste Text
		pastetext: {
			exec: function (caller) {
				var	val,
					editor  = this,
					content = _tmpl('pastetext', {
						label: editor._('Paste your text inside the following box:'),
						insert: editor._('Insert')
					}, true);

				content.find('.button').click(function (e) {
					val = content.find('#txt').val();

					if(val)
						editor.wysiwygEditorInsertText(val);

					editor.closeDropDown(true);
					e.preventDefault();
				});

				editor.createDropDown(caller, 'pastetext', content);
			},
			tooltip: 'Paste Text'
		},
		// END_COMMAND
		// START_COMMAND: Bullet List
		bulletlist: {
			exec: 'insertunorderedlist',
			tooltip: 'Bullet list'
		},
		// END_COMMAND
		// START_COMMAND: Ordered List
		orderedlist: {
			exec: 'insertorderedlist',
			tooltip: 'Numbered list'
		},
		// END_COMMAND

		// START_COMMAND: Table
		table: {
			exec: function (caller) {
				var	editor  = this,
					content = _tmpl('table', {
						rows: editor._('Rows:'),
						cols: editor._('Cols:'),
						insert: editor._('Insert')
					}, true);

				content.find('.button').click(function (e) {
					var	rows = content.find('#rows').val() - 0,
						cols = content.find('#cols').val() - 0,
						html = '<table>';

					if(rows < 1 || cols < 1)
						return;

					for (var row=0; row < rows; row++) {
						html += '<tr>';

						for (var col=0; col < cols; col++)
							html += '<td>' + ($.sceditor.ie ? '' : '<br />') + '</td>';

						html += '</tr>';
					}

					html += '</table>';

					editor.wysiwygEditorInsertHtml(html);
					editor.closeDropDown(true);
					e.preventDefault();
				});

				editor.createDropDown(caller, 'inserttable', content);
			},
			tooltip: 'Insert a table'
		},
		// END_COMMAND

		// START_COMMAND: Horizontal Rule
		horizontalrule: {
			exec: 'inserthorizontalrule',
			tooltip: 'Insert a horizontal rule'
		},
		// END_COMMAND

		// START_COMMAND: Code
		code: {
			forceNewLineAfter: ['code'],
			exec: function () {
				this.wysiwygEditorInsertHtml('<code>', '<br /></code>');
			},
			tooltip: 'Code'
		},
		// END_COMMAND

		// START_COMMAND: Image
		image: {
			exec: function (caller) {
				var	editor  = this,
					content = _tmpl('image', {
						url: editor._('URL:'),
						width: editor._('Width (optional):'),
						height: editor._('Height (optional):'),
						insert: editor._('Insert')
					}, true);

				content.find('.button').click(function (e) {
					var	val    = content.find('#image').val(),
						width  = content.find('#width').val(),
						height = content.find('#height').val(),
						attrs  = '';

					if(width)
						attrs += ' width="' + width + '"';
					if(height)
						attrs += ' height="' + height + '"';

					if(val && val !== 'http://')
						editor.wysiwygEditorInsertHtml('<img' + attrs + ' src="' + val + '" />');

					editor.closeDropDown(true);
					e.preventDefault();
				});

				editor.createDropDown(caller, 'insertimage', content);
			},
			tooltip: 'Insert an image'
		},
		// END_COMMAND

		// START_COMMAND: E-mail
		email: {
			exec: function (caller) {
				var	editor  = this,
					content = _tmpl('email', {
						label: editor._('E-mail:'),
						insert: editor._('Insert')
					}, true);

				content.find('.button').click(function (e) {
					var val = content.find('#email').val();

					if(val)
					{
						// needed for IE to reset the last range
						editor.focus();

						if(!editor.getRangeHelper().selectedHtml())
							editor.wysiwygEditorInsertHtml('<a href="' + 'mailto:' + val + '">' + val + '</a>');
						else
							editor.execCommand('createlink', 'mailto:' + val);
					}

					editor.closeDropDown(true);
					e.preventDefault();
				});

				editor.createDropDown(caller, 'insertemail', content);
			},
			tooltip: 'Insert an email'
		},
		// END_COMMAND

		// START_COMMAND: Link
		link: {
			exec: function (caller) {
				var	editor  = this,
					content = _tmpl('link', {
						url: editor._('URL:'),
						desc: editor._('Description (optional):'),
						ins: editor._('Insert')
					}, true);

				content.find('.button').click(function (e) {
					var	val         = content.find('#link').val(),
						description = content.find('#des').val();

					if(val && val !== 'http://') {
						// needed for IE to reset the last range
						editor.focus();

						if(!editor.getRangeHelper().selectedHtml() || description)
						{
							if(!description)
								description = val;

							editor.wysiwygEditorInsertHtml('<a href="' + val + '">' + description + '</a>');
						}
						else
							editor.execCommand('createlink', val);
					}

					editor.closeDropDown(true);
					e.preventDefault();
				});

				editor.createDropDown(caller, 'insertlink', content);
			},
			tooltip: 'Insert a link'
		},
		// END_COMMAND

		// START_COMMAND: Unlink
		unlink: {
			state: function() {
				var $current = $(this.currentNode());
				return $current.is('a') || $current.parents('a').length > 0 ? 0 : -1;
			},
			exec: function() {
				var	$current = $(this.currentNode()),
					$anchor  = $current.is('a') ? $current : $current.parents('a').first();

				if($anchor.length)
					$anchor.replaceWith($anchor.contents());
			},
			tooltip: 'Unlink'
		},
		// END_COMMAND


		// START_COMMAND: Quote
		quote: {
			forceNewLineAfter: ['blockquote'],
			exec: function (caller, html, author) {
				var	before = '<blockquote>',
					end    = '</blockquote>';

				// if there is HTML passed set end to null so any selected
				// text is replaced
				if(html)
				{
					author = (author ? '<cite>' + author + '</cite>' : '');
					before = before + author + html + end;
					end    = null;
				}
				// if not add a newline to the end of the inserted quote
				else if(this.getRangeHelper().selectedHtml() === '')
					end = $.sceditor.ie ? '' : '<br />' + end;

				this.wysiwygEditorInsertHtml(before, end);
			},
			tooltip: 'Insert a Quote'
		},
		// END_COMMAND

		// START_COMMAND: Emoticons
		emoticon: {
			exec: function (caller) {
				var editor = this;

				var createContent = function(includeMore) {
					var	emoticonsCompat = editor.opts.emoticonsCompat,
						rangeHelper     = editor.getRangeHelper(),
						startSpace      = emoticonsCompat && rangeHelper.getOuterText(true, 1)  !== ' ' ? ' ' : '',
						endSpace        = emoticonsCompat && rangeHelper.getOuterText(false, 1) !== ' ' ? ' ' : '',
						$content        = $('<div />'),
						$line           = $('<div />').appendTo($content),
						emoticons       = $.extend({}, editor.opts.emoticons.dropdown, includeMore ? editor.opts.emoticons.more : {}),
						perLine         = 0;

					$.each(emoticons, function() {
						perLine++;
					});
					perLine = Math.sqrt(perLine);

					$.each(emoticons, function(code, emoticon) {
						$line.append(
							$('<img />').attr({
								src: emoticon.url || emoticon,
								alt: code,
								title: emoticon.tooltip || code
							}).click(function() {
								editor.insert(startSpace + $(this).attr('alt') + endSpace, null, false).closeDropDown(true);
								return false;
							})
						);

						if($line.children().length >= perLine)
							$line = $('<div />').appendTo($content);
					});

					if(!includeMore && editor.opts.emoticons.more)
					{
						$content.append(
							$(editor._('<a class="sceditor-more">{0}</a>', editor._('More'))).click(function () {
								editor.createDropDown(caller, 'more-emoticons', createContent(true));
								return false;
							})
						);
					}

					return $content;
				};

				editor.createDropDown(caller, 'emoticons', createContent(false));
			},
			txtExec: function(caller) {
				$.sceditor.command.get('emoticon').exec.call(this, caller);
			},
			tooltip: 'Insert an emoticon'
		},
		// END_COMMAND

		// START_COMMAND: YouTube
		youtube: {
			_dropDown: function (editor, caller, handleIdFunc) {
				var	matches,
					content = _tmpl('youtubeMenu', {
						label: editor._('Video URL:'),
						insert: editor._('Insert')
					}, true);

				content.find('.button').click(function (e) {
					var val = content.find('#link').val().replace('http://', '');

					if (val !== '') {
						matches = val.match(/(?:v=|v\/|embed\/|youtu.be\/)(.{11})/);

						if (matches)
							val = matches[1];

						if (/^[a-zA-Z0-9_\-]{11}$/.test(val))
							handleIdFunc(val);
						else
							alert('Invalid YouTube video');
					}

					editor.closeDropDown(true);
					e.preventDefault();
				});

				editor.createDropDown(caller, 'insertlink', content);
			},
			exec: function (caller) {
				var editor = this;

				$.sceditor.command.get('youtube')._dropDown(
					editor,
					caller,
					function(id) {
						editor.wysiwygEditorInsertHtml(_tmpl('youtube', { id: id }));
					}
				);
			},
			tooltip: 'Insert a YouTube video'
		},
		// END_COMMAND

		// START_COMMAND: Date
		date: {
			_date: function (editor) {
				var	now   = new Date(),
					year  = now.getYear(),
					month = now.getMonth()+1,
					day   = now.getDate();

				if(year < 2000)
					year = 1900 + year;
				if(month < 10)
					month = '0' + month;
				if(day < 10)
					day = '0' + day;

				return editor.opts.dateFormat.replace(/year/i, year).replace(/month/i, month).replace(/day/i, day);
			},
			exec: function () {
				this.insertText($.sceditor.command.get('date')._date(this));
			},
			txtExec: function () {
				this.insertText($.sceditor.command.get('date')._date(this));
			},
			tooltip: 'Insert current date'
		},
		// END_COMMAND

		// START_COMMAND: Time
		time: {
			_time: function () {
				var	now   = new Date(),
					hours = now.getHours(),
					mins  = now.getMinutes(),
					secs  = now.getSeconds();

				if(hours < 10)
					hours = '0' + hours;
				if(mins < 10)
					mins = '0' + mins;
				if(secs < 10)
					secs = '0' + secs;

				return hours + ':' + mins + ':' + secs;
			},
			exec: function () {
				this.insertText($.sceditor.command.get('time')._time());
			},
			txtExec: function () {
				this.insertText($.sceditor.command.get('time')._time());
			},
			tooltip: 'Insert current time'
		},
		// END_COMMAND


		// START_COMMAND: Ltr
		ltr: {
			state: function(parents, firstBlock) {
				return firstBlock && firstBlock.style.direction === 'ltr';
			},
			exec: function() {
				var	editor = this,
					elm    = editor.getRangeHelper().getFirstBlockParent(),
					$elm   = $(elm);

				editor.focus();

				if(!elm || $elm.is('body'))
				{
					editor.execCommand('formatBlock', 'p');

					elm  = editor.getRangeHelper().getFirstBlockParent();
					$elm = $(elm);

					if(!elm || $elm.is('body'))
						return;
				}

				if($elm.css('direction') === 'ltr')
					$elm.css('direction', '');
				else
					$elm.css('direction', 'ltr');
			},
			tooltip: 'Left-to-Right'
		},
		// END_COMMAND

		// START_COMMAND: Rtl
		rtl: {
			state: function(parents, firstBlock) {
				return firstBlock && firstBlock.style.direction === 'rtl';
			},
			exec: function() {
				var	editor = this,
					elm    = editor.getRangeHelper().getFirstBlockParent(),
					$elm   = $(elm);

				editor.focus();

				if(!elm || $elm.is('body'))
				{
					editor.execCommand('formatBlock', 'p');

					elm  = editor.getRangeHelper().getFirstBlockParent();
					$elm = $(elm);

					if(!elm || $elm.is('body'))
						return;
				}

				if($elm.css('direction') === 'rtl')
					$elm.css('direction', '');
				else
					$elm.css('direction', 'rtl');
			},
			tooltip: 'Right-to-Left'
		},
		// END_COMMAND


		// START_COMMAND: Print
		print: {
			exec: 'print',
			tooltip: 'Print'
		},
		// END_COMMAND

		// START_COMMAND: Maximize
		maximize: {
			state: function() {
				return this.maximize();
			},
			exec: function () {
				this.maximize(!this.maximize());
			},
			txtExec: function () {
				this.maximize(!this.maximize());
			},
			tooltip: 'Maximize',
			shortcut: 'ctrl+shift+m'
		},
		// END_COMMAND

		// START_COMMAND: Source
		source: {
			exec: function () {
				this.toggleSourceMode();
			},
			txtExec: function () {
				this.toggleSourceMode();
			},
			tooltip: 'View source',
			shortcut: 'ctrl+shift+s'
		},
		// END_COMMAND

		// this is here so that commands above can be removed
		// without having to remove the , after the last one.
		// Needed for IE.
		ignore: {}
	};

	/**
	 * Range helper class
	 * @class rangeHelper
	 * @name jQuery.sceditor.rangeHelper
	 */
	$.sceditor.rangeHelper = function(w, d) {
		var	win, doc, init, _createMarker, _isOwner,
			isW3C        = true,
			startMarker  = 'sceditor-start-marker',
			endMarker    = 'sceditor-end-marker',
			characterStr = 'character', // Used to improve minification
			base         = this;

		/**
		 * @constructor
		 * @param Window window
		 * @param Document document
		 * @private
		 */
		init = function (window, document) {
			doc   = document || window.contentDocument || window.document;
			win   = window;
			isW3C = !!window.getSelection;
		}(w, d);

		/**
		 * <p>Inserts HTML into the current range replacing any selected
		 * text.</p>
		 *
		 * <p>If endHTML is specified the selected contents will be put between
		 * html and endHTML. If there is nothing selected html and endHTML are
		 * just concated together.</p>
		 *
		 * @param {string} html
		 * @param {string} endHTML
		 * @return False on fail
		 * @function
		 * @name insertHTML
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.insertHTML = function(html, endHTML) {
			var	node, div,
				range = base.selectedRange();

			if(endHTML)
				html += base.selectedHtml() + endHTML;

			if(isW3C)
			{
				div           = doc.createElement('div');
				node          = doc.createDocumentFragment();
				div.innerHTML = html;

				while(div.firstChild)
					node.appendChild(div.firstChild);

				base.insertNode(node);
			}
			else
			{
				if(!range)
					return false;

				range.pasteHTML(html);
			}
		};

		/**
		 * <p>The same as insertHTML except with DOM nodes instead</p>
		 *
		 * <p><strong>Warning:</strong> the nodes must belong to the
		 * document they are being inserted into. Some browsers
		 * will throw exceptions if they don't.</p>
		 *
		 * @param {Node} node
		 * @param {Node} endNode
		 * @return False on fail
		 * @function
		 * @name insertNode
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.insertNode = function(node, endNode) {
			if(isW3C)
			{
				var	selection, selectAfter,
					toInsert = doc.createDocumentFragment(),
					range    = base.selectedRange();

				if(!range)
					return false;

				toInsert.appendChild(node);

				if(endNode)
				{
					toInsert.appendChild(range.extractContents());
					toInsert.appendChild(endNode);
				}

				selectAfter = toInsert.lastChild;

				// If the last child is undefined then there is nothing to insert so return
				if(!selectAfter)
					return;

				range.deleteContents();
				range.insertNode(toInsert);

				selection = doc.createRange();
				selection.setStartAfter(selectAfter);
				base.selectRange(selection);
			}
			else
				base.insertHTML(node.outerHTML, endNode?endNode.outerHTML:null);
		};

		/**
		 * <p>Clones the selected Range</p>
		 *
		 * <p>IE <= 8 will return a TextRange, all other browsers
		 * will return a Range object.</p>
		 *
		 * @return {Range|TextRange}
		 * @function
		 * @name cloneSelected
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.cloneSelected = function() {
			var range = base.selectedRange();

			if(range)
				return isW3C ? range.cloneRange() : range.duplicate();
		};

		/**
		 * <p>Gets the selected Range</p>
		 *
		 * <p>IE <= 8 will return a TextRange, all other browsers
		 * will return a Range object.</p>
		 *
		 * @return {Range|TextRange}
		 * @function
		 * @name selectedRange
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.selectedRange = function() {
			var	range, firstChild,
				sel = isW3C ? win.getSelection() : doc.selection;

			if(!sel)
				return;

			// When creating a new range, set the start to the body
			// element to avoid errors in FF.
			if(sel.getRangeAt && sel.rangeCount <= 0)
			{
				firstChild = doc.body;
				while(firstChild.firstChild)
					firstChild = firstChild.firstChild;

				range = doc.createRange();
				range.setStart(firstChild, 0);
				sel.addRange(range);
			}

			if(isW3C)
				range = sel.getRangeAt(0);

			if(!isW3C && sel.type !== 'Control')
				range = sel.createRange();

			// IE fix to make sure only return selections that are part of the WYSIWYG iframe
			return _isOwner(range) ? range : null;
		};

		/**
		 * Checks if an IE TextRange range belongs to
		 * this document or not.
		 *
		 * Returns true if the range isn't an IE range or
		 * if the range is null.
		 *
		 * @private
		 */
		_isOwner = function(range) {
			var parent;

			// IE fix to make sure only return selections that are part of the WYSIWYG iframe
			return (range && range.parentElement && (parent = range.parentElement())) ?
				parent.ownerDocument === doc :
				true;
		};

		/**
		 * Gets if there is currently a selection
		 *
		 * @return {Boolean}
		 * @function
		 * @name hasSelection
		 * @since 1.4.4
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.hasSelection = function() {
			var	range,
				sel = isW3C ? win.getSelection() : doc.selection;

			if(isW3C || !sel)
				return sel && sel.rangeCount > 0;

			range = sel.createRange();

			return range && _isOwner(range);
		};

		/**
		 * Gets the currently selected HTML
		 *
		 * @return {string}
		 * @function
		 * @name selectedHtml
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.selectedHtml = function() {
			var	div,
				range = base.selectedRange();

			if(!range)
				return '';

			// IE < 9
			if(!isW3C && range.text !== '' && range.htmlText)
				return range.htmlText;


			// IE9+ and all other browsers
			if(isW3C)
			{
				div = doc.createElement('div');
				div.appendChild(range.cloneContents());

				return div.innerHTML;
			}

			return '';
		};

		/**
		 * Gets the parent node of the selected contents in the range
		 *
		 * @return {HTMLElement}
		 * @function
		 * @name parentNode
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.parentNode = function() {
			var range = base.selectedRange();

			if(range)
				return range.parentElement ? range.parentElement() : range.commonAncestorContainer;
		};

		/**
		 * Gets the first block level parent of the selected
		 * contents of the range.
		 *
		 * @return {HTMLElement}
		 * @function
		 * @name getFirstBlockParent
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		/**
		 * Gets the first block level parent of the selected
		 * contents of the range.
		 *
		 * @param {Node} n The element to get the first block level parent from
		 * @return {HTMLElement}
		 * @function
		 * @name getFirstBlockParent^2
		 * @since 1.4.1
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.getFirstBlockParent = function(n) {
			var func = function(node) {
				if(!$.sceditor.dom.isInline(node, true))
					return node;

				node = node ? node.parentNode : null;

				return node ? func(node) : null;
			};

			return func(n || base.parentNode());
		};

		/**
		 * Inserts a node at either the start or end of the current selection
		 *
		 * @param {Bool} start
		 * @param {Node} node
		 * @function
		 * @name insertNodeAt
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.insertNodeAt = function(start, node) {
			var	currentRange = base.selectedRange(),
				range        = base.cloneSelected();

			if(!range)
				return false;

			range.collapse(start);

			if(range.insertNode)
				range.insertNode(node);
			else
				range.pasteHTML(node.outerHTML);

			// Reselect the current range.
			// Fixes issue with Chrome losing the selection. Issue#82
			base.selectRange(currentRange);
		};

		/**
		 * Creates a marker node
		 *
		 * @param {String} id
		 * @return {Node}
		 * @private
		 */
		_createMarker = function(id) {
			base.removeMarker(id);

			var marker              = doc.createElement('span');
			marker.id               = id;
			marker.style.lineHeight = '0';
			marker.style.display    = 'none';
			marker.className        = 'sceditor-selection sceditor-ignore';
			marker.innerHTML        = ' ';

			return marker;
		};

		/**
		 * Inserts start/end markers for the current selection
		 * which can be used by restoreRange to re-select the
		 * range.
		 *
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 * @function
		 * @name insertMarkers
		 */
		base.insertMarkers = function() {
			base.insertNodeAt(true, _createMarker(startMarker));
			base.insertNodeAt(false, _createMarker(endMarker));
		};

		/**
		 * Gets the marker with the specified ID
		 *
		 * @param {String} id
		 * @return {Node}
		 * @function
		 * @name getMarker
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.getMarker = function(id) {
			return doc.getElementById(id);
		};

		/**
		 * Removes the marker with the specified ID
		 *
		 * @param {String} id
		 * @function
		 * @name removeMarker
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.removeMarker = function(id) {
			var marker = base.getMarker(id);

			if(marker)
				marker.parentNode.removeChild(marker);
		};

		/**
		 * Removes the start/end markers
		 *
		 * @function
		 * @name removeMarkers
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.removeMarkers = function() {
			base.removeMarker(startMarker);
			base.removeMarker(endMarker);
		};

		/**
		 * Saves the current range location. Alias of insertMarkers()
		 *
		 * @function
		 * @name saveRage
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.saveRange = function() {
			base.insertMarkers();
		};

		/**
		 * Select the specified range
		 *
		 * @param {Range|TextRange} range
		 * @function
		 * @name selectRange
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.selectRange = function(range) {
			if(isW3C)
			{
				win.getSelection().removeAllRanges();
				win.getSelection().addRange(range);
			}
			else
				range.select();
		};

		/**
		 * Restores the last range saved by saveRange() or insertMarkers()
		 *
		 * @function
		 * @name restoreRange
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.restoreRange = function() {
			var	marker,
				range = base.selectedRange(),
				start = base.getMarker(startMarker),
				end   = base.getMarker(endMarker);

			if(!start || !end || !range)
				return false;

			if(!isW3C)
			{
				range  = doc.body.createTextRange();
				marker = doc.body.createTextRange();

				marker.moveToElementText(start);
				range.setEndPoint('StartToStart', marker);
				range.moveStart(characterStr, 0);

				marker.moveToElementText(end);
				range.setEndPoint('EndToStart', marker);
				range.moveEnd(characterStr, 0);

				base.selectRange(range);
			}
			else
			{
				range = doc.createRange();

				range.setStartBefore(start);
				range.setEndAfter(end);

				base.selectRange(range);
			}

			base.removeMarkers();
		};

		/**
		 * Selects the text left and right of the current selection
		 * @param {int} left
		 * @param {int} right
		 * @since 1.4.3
		 * @function
		 * @name selectOuterText
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.selectOuterText = function(left, right) {
			var range = base.cloneSelected();

			if(!range)
				return false;

			range.collapse(false);

			if(!isW3C)
			{
				range.moveStart(characterStr, 0-left);
				range.moveEnd(characterStr, right);
			}
			else
			{
				range.setStart(range.startContainer, range.startOffset-left);
				range.setEnd(range.endContainer, range.endOffset+right);
			}

			base.selectRange(range);
		};

		/**
		 * Gets the text left or right of the current selection
		 * @param {Boolean} before
		 * @param {Int} length
		 * @since 1.4.3
		 * @function
		 * @name selectOuterText
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.getOuterText = function(before, length) {
			var	ret   = '',
				range = base.cloneSelected();

			if(!range)
				return '';

			range.collapse(false);

			if(before)
			{
				if(!isW3C)
				{
					range.moveStart(characterStr, 0-length);
					ret = range.text;
				}
				else
				{
					ret = range.startContainer.textContent.substr(0, range.startOffset);
					ret = ret.substr(Math.max(0, ret.length - length));
				}
			}
			else
			{
				if(!isW3C)
				{
					range.moveEnd(characterStr, length);
					ret = range.text;
				}
				else
					ret = range.startContainer.textContent.substr(range.startOffset, length);
			}

			return ret;
		};

		/**
		 * Replaces keywords with values based on the current caret position
		 *
		 * @param {Array}   keywords
		 * @param {Boolean} includeAfter      If to include the text after the current caret position or just text before
		 * @param {Boolean} keywordsSorted    If the keywords array is pre sorted shortest to longest
		 * @param {Int}     longestKeyword    Length of the longest keyword
		 * @param {Boolean} requireWhiteSpace If the key must be surrounded by whitespace
		 * @param {String}  currrentChar      If this is being called from a keypress event, this should be set to the pressed character
		 * @return {Boolean}
		 * @function
		 * @name raplaceKeyword
		 * @memberOf jQuery.sceditor.rangeHelper.prototype
		 */
		base.raplaceKeyword = function(keywords, includeAfter, keywordsSorted, longestKeyword, requireWhiteSpace, currrentChar) {
			if(!keywordsSorted)
			{
				keywords.sort(function(a, b){
					return a.length - b.length;
				});
			}

			var	beforeStr, str, keywordIdx, numberCharsLeft, keywordRegex, startIdx, keyword,
				i         = keywords.length,
				maxKeyLen = longestKeyword || keywords[i-1][0].length;

			if(requireWhiteSpace)
			{
				// requireWhiteSpace doesn't work with textRanges as they select text on the
				// other side of elements causing space-img-key to match when it shouldn't.
				if(!isW3C)
					return false;

				++maxKeyLen;
			}

			beforeStr = base.getOuterText(true, maxKeyLen);
			str       = beforeStr + (currrentChar != null ? currrentChar : '');

			if(includeAfter)
				str += base.getOuterText(false, maxKeyLen);

			while(i--)
			{
				keyword      = keywords[i][0];
				keywordRegex = new RegExp('(?:[\\s\xA0\u2002\u2003\u2009])' + $.sceditor.regexEscape(keyword) + '(?=[\\s\xA0\u2002\u2003\u2009])');
				startIdx     = beforeStr.length - 1 - keyword.length;

				if(requireWhiteSpace)
					--startIdx;

				startIdx = Math.max(0, startIdx);

				if((keywordIdx = requireWhiteSpace ? str.substr(startIdx).search(keywordRegex) : str.indexOf(keyword, startIdx)) > -1)
				{

					if(requireWhiteSpace)
						keywordIdx += startIdx + 1;

					// Make sure the substr is between beforeStr and after not entirely in one or the other
					if(keywordIdx > beforeStr.length || (keywordIdx + keyword.length + (requireWhiteSpace ? 1 : 0)) < beforeStr.length)
						continue;

					numberCharsLeft = beforeStr.length - keywordIdx;
					base.selectOuterText(numberCharsLeft, keyword.length - numberCharsLeft - (currrentChar != null && /^\S/.test(currrentChar) ? 1 : 0));
					base.insertHTML(keywords[i][1]);
					return true;
				}
			}

			return false;
		};

		/**
		 * Compares two ranges.
		 * @param  {Range|TextRange} rangeA
		 * @param  {Range|TextRange} rangeB If undefined it will be set to the current selected range
		 * @return {Boolean}
		 */
		base.compare = function(rangeA, rangeB) {
			if(!rangeB)
				rangeB = base.selectedRange();

			if(!rangeA || !rangeB)
				return !rangeA && !rangeB;

			if(!isW3C)
			{
				return _isOwner(rangeA) && _isOwner(rangeB) &&
					rangeA.compareEndPoints('EndToEnd', rangeB)  === 0 &&
					rangeA.compareEndPoints('StartToStart', rangeB) === 0;
			}

			return rangeA.compareBoundaryPoints(Range.END_TO_END, rangeB)  === 0 &&
				rangeA.compareBoundaryPoints(Range.START_TO_START, rangeB) === 0;
		};

	};

	/**
	 * Static DOM helper class
	 * @class dom
	 * @name jQuery.sceditor.dom
	 */
	$.sceditor.dom =
	/** @lends jQuery.sceditor.dom */
	{
		/**
		 * Loop all child nodes of the passed node
		 *
		 * The function should accept 1 parameter being the node.
		 * If the function returns false the loop will be exited.
		 *
		 * @param {HTMLElement}	node
		 * @param {function}	func		Function that is called for every node, should accept 1 param for the node
		 * @param {bool}	innermostFirst	If the innermost node should be passed to the function before it's parents
		 * @param {bool}	siblingsOnly	If to only traverse the nodes siblings
		 * @param {bool}	reverse		If to traverse the nodes in reverse
		 */
		traverse: function(node, func, innermostFirst, siblingsOnly, reverse) {
			if(node)
			{
				node = reverse ? node.lastChild : node.firstChild;

				while(node != null)
				{
					var next = reverse ? node.previousSibling : node.nextSibling;

					if(!innermostFirst && func(node) === false)
						return false;

					// traverse all children
					if(!siblingsOnly && this.traverse(node, func, innermostFirst, siblingsOnly, reverse) === false)
						return false;

					if(innermostFirst && func(node) === false)
						return false;

					// move to next child
					node = next;
				}
			}
		},

		/**
		 * Like traverse but loops in reverse
		 * @see traverse
		 */
		rTraverse: function(node, func, innermostFirst, siblingsOnly) {
			this.traverse(node, func, innermostFirst, siblingsOnly, true);
		},

		/**
		 * Parses HTML
		 * @param
		 * @since 1.4.4
		 * @return {Array}
		 */
		parseHTML: function(html, context) {
			var	ret = [],
				tmp = (context || document).createElement('div');

			tmp.innerHTML = html;

			$.merge(ret, tmp.childNodes);

			return ret;
		},

		/**
		 * Checks if an element is not a p or div element and if it has any styling.
		 * @param  {HTMLElement} elm
		 * @return {Boolean}
		 * @since 1.4.4
		 */
		hasStyling: function(elm) {
			var $elm = $(elm);

			return elm && (!$elm.is('p,div') || elm.className || $elm.attr('style') || !$.isEmptyObject($elm.data()));
		},

		/**
		 * Converts an element from one type to another.
		 *
		 * For example it can convert the element <b> to <strong>
		 * @param  {HTMLElement} elm
		 * @param  {String} newElement
		 * @return {HTMLElement}
		 * @since 1.4.4
		 */
		convertElement: function(elm, newElement) {
			var	child, attr,
				i      = elm.attributes.length,
				newTag = elm.ownerDocument.createElement(newElement);

			while(i--)
			{
				attr = elm.attributes[i];

				// IE < 8 returns all possible attribtues, not just specified ones
				if(!$.sceditor.ie || attr.specified)
				{
					// IE < 8 doesn't return the CSS for the style attribute
					if($.sceditor.ie < 8 && /style/i.test(attr.name))
						elm.style.cssText = elm.style.cssText;
					else
						newTag.setAttribute(attr.name, attr.value);
				}
			}

			while((child = elm.firstChild))
				newTag.appendChild(child);

			elm.parentNode.replaceChild(newTag, elm);

			return newTag;
		},

		/**
		 * List of block level elements separated by bars (|)
		 * @type {string}
		 */
		blockLevelList: '|body|hr|p|div|h1|h2|h3|h4|h5|h6|address|pre|form|table|tbody|thead|tfoot|th|tr|td|li|ol|ul|blockquote|center|',

		/**
		 * Checks if an element is inline
		 *
		 * @return {bool}
		 */
		isInline: function(elm, includeCodeAsBlock) {
			if(!elm || elm.nodeType !== 1)
				return true;

			elm = elm.tagName.toLowerCase();

			if(elm === 'code')
				return !includeCodeAsBlock;

			return $.sceditor.dom.blockLevelList.indexOf('|' + elm + '|') < 0;
		},

		/**
		 * <p>Copys the CSS from 1 node to another.</p>
		 *
		 * <p>Only copies CSS defined on the element e.g. style attr.</p>
		 *
		 * @param {HTMLElement} from
		 * @param {HTMLElement} to
		 */
		copyCSS: function(from, to) {
			to.style.cssText = from.style.cssText + to.style.cssText;
		},

		/**
		 * Fixes block level elements inside in inline elements.
		 *
		 * @param {HTMLElement} node
		 */
		fixNesting: function(node) {
			var	base = this,
				getLastInlineParent = function(node) {
					while(base.isInline(node.parentNode, true))
						node = node.parentNode;

					return node;
				};

			base.traverse(node, function(node) {
				// if node is an element, and it is blocklevel and the parent isn't block level
				// then it needs fixing
				if(node.nodeType === 1 && !base.isInline(node, true) && base.isInline(node.parentNode, true))
				{
					var	parent  = getLastInlineParent(node),
						rParent = parent.parentNode,
						before  = base.extractContents(parent, node),
						middle  = node;

					// copy current styling so when moved out of the parent
					// it still has the same styling
					base.copyCSS(parent, middle);

					rParent.insertBefore(before, parent);
					rParent.insertBefore(middle, parent);
				}
			});
		},

		/**
		 * Finds the common parent of two nodes
		 *
		 * @param {HTMLElement} node1
		 * @param {HTMLElement} node2
		 * @return {HTMLElement}
		 */
		findCommonAncestor: function(node1, node2) {
			// not as fast as making two arrays of parents and comparing
			// but is a lot smaller and as it's currently only used with
			// fixing invalid nesting so it doesn't need to be very fast
			return $(node1).parents().has($(node2)).first();
		},

		getSibling: function(node, previous) {
			var sibling;

			if(!node)
				return null;

			if((sibling = node[previous ? 'previousSibling' : 'nextSibling']))
				return sibling;

			return $.sceditor.dom.getSibling(node.parentNode, previous);
		},

		/**
		 * Removes unused whitespace from the root and all it's children
		 *
		 * @name removeWhiteSpace^1
		 * @param HTMLElement root
		 * @return void
		 */
		/**
		 * Removes unused whitespace from the root and all it's children.
		 *
		 * If preserveNewLines is true, new line characters will not be removed
		 *
		 * @name removeWhiteSpace^2
		 * @param HTMLElement root
		 * @param Boolean preserveNewLines
		 * @return void
		 * @since 1.4.3
		 */
		removeWhiteSpace: function(root, preserveNewLines) {
			var	nodeValue, nodeType, next, previous, cssWS, nextNode, trimStart, sibling,
				getSibling        = $.sceditor.dom.getSibling,
				isInline          = $.sceditor.dom.isInline,
				node              = root.firstChild,
				whitespace        = /[\t ]+/g,
				witespaceAndLines = /[\t\n\r ]+/g;

			while(node)
			{
				nextNode  = node.nextSibling;
				nodeValue = node.nodeValue;
				nodeType  = node.nodeType;

				// 1 = element
				if(nodeType === 1 && node.firstChild)
				{
					cssWS = $(node).css('whiteSpace');

					// pre || pre-wrap with any vendor prefix
					if(!/pre(?:\-wrap)?$/i.test(cssWS))
						$.sceditor.dom.removeWhiteSpace(node, /line$/i.test(cssWS));
				}

				// 3 = textnode
				if(nodeType === 3 && nodeValue)
				{
					next      = getSibling(node);
					previous  = getSibling(node, true);
					sibling   = previous;
					trimStart = false;

					while($(sibling).hasClass('sceditor-ignore'))
						sibling = getSibling(sibling, true);

					// If last sibling is not inline or is a textnode ending in whitespace,
					// the start whitespace should be stripped
					if(isInline(node) && sibling)
					{
						while(sibling.lastChild)
							sibling = sibling.lastChild;

						trimStart = sibling.nodeType === 3 ? /[\t\n\r ]$/.test(sibling.nodeValue) : !isInline(sibling);
					}

					if(!isInline(node) || !previous || !isInline(previous) || trimStart)
						nodeValue = nodeValue.replace(/^[\t\n\r ]+/, '');

					if(!isInline(node) || !next || !isInline(next))
						nodeValue = nodeValue.replace(/[\t\n\r ]+$/, '');

					// Remove empty text nodes
					if(!nodeValue.length)
						root.removeChild(node);
					else
						node.nodeValue = nodeValue.replace(preserveNewLines ? whitespace : witespaceAndLines, ' ');
				}

				node = nextNode;
			}
		},

		/**
		 * Extracts all the nodes between the start and end nodes
		 *
		 * @param {HTMLElement} startNode	The node to start extracting at
		 * @param {HTMLElement} endNode		The node to stop extracting at
		 * @return {DocumentFragment}
		 */
		extractContents: function(startNode, endNode) {
			var	base            = this,
				$commonAncestor = base.findCommonAncestor(startNode, endNode),
				commonAncestor  = !$commonAncestor ? null : $commonAncestor[0],
				startReached    = false,
				endReached      = false;

			return (function extract(root) {
				var df = startNode.ownerDocument.createDocumentFragment();

				base.traverse(root, function(node) {
					// if end has been reached exit loop
					if(endReached || (node === endNode && startReached))
					{
						endReached = true;
						return false;
					}

					if(node === startNode)
						startReached = true;

					var c, n;
					if(startReached)
					{
						// if the start has been reached and this elm contains
						// the end node then clone it
						if(jQuery.contains(node, endNode) && node.nodeType === 1)
						{
							c = extract(node);
							n = node.cloneNode(false);

							n.appendChild(c);
							df.appendChild(n);
						}
						// otherwise just move it
						else
							df.appendChild(node);
					}
					// if this node contains the start node then add it
					else if(jQuery.contains(node, startNode) && node.nodeType === 1)
					{
						c = extract(node);
						n = node.cloneNode(false);

						n.appendChild(c);
						df.appendChild(n);
					}
				}, false);

				return df;
			}(commonAncestor));
		}
	};

	/**
	 * Object containing SCEditor plugins
	 * @type {Object}
	 * @name plugins
	 * @memberOf jQuery.sceditor
	 */
	$.sceditor.plugins = {};

	/**
	 * Plugin Manager class
	 * @class PluginManager
	 * @name jQuery.sceditor.PluginManager
	 */
	$.sceditor.PluginManager = function(owner) {
		/**
		 * Alias of this
		 * @private
		 * @type {Object}
		 */
		var base = this;

		/**
		 * Array of all currently registered plugins
		 * @type {Array}
		 * @private
		 */
		var plugins = [];

		/**
		 * Editor instance this plugin manager belongs to
		 * @type {jQuery.sceditor}
		 * @private
		 */
		var editorInstance = owner;


		/**
		 * Changes a signals name from "name" into "signalName".
		 * @param  {String} signal
		 * @return {String}
		 * @private
		 */
		var formatSignalName = function(signal) {
			return 'signal' + signal.charAt(0).toUpperCase() + signal.slice(1);
		};

		/**
		 * Calls handlers for a signal
		 * @see call()
		 * @see callOnlyFirst()
		 * @param  {Array}   args
		 * @param  {Boolean} returnAtFirst
		 * @return {Mixed}
		 * @private
		 */
		var callHandlers = function(args, returnAtFirst) {
			args = [].slice.call(args);

			var	i      = plugins.length,
				signal = formatSignalName(args.shift());

			while(i--)
			{
				if(signal in plugins[i])
				{
					if(returnAtFirst)
						return plugins[i][signal].apply(editorInstance, args);

					plugins[i][signal].apply(editorInstance, args);
				}
			}
		};

		/**
		 * Calls all handlers for the passed signal
		 * @param  {String}    signal
		 * @param  {...String} args
		 * @return {Void}
		 * @function
		 * @name call
		 * @memberOf jQuery.sceditor.PluginManager.prototype
		 */
		base.call = function() {
			callHandlers(arguments, false);
		};

		/**
		 * Calls the first handler for a signal, and returns the result
		 * @param  {String}    signal
		 * @param  {...String} args
		 * @return {Mixed} The result of calling the handler
		 * @function
		 * @name callOnlyFirst
		 * @memberOf jQuery.sceditor.PluginManager.prototype
		 */
		base.callOnlyFirst = function() {
			return callHandlers(arguments, true);
		};

		/**
		 * <p>Returns an object which has callNext and hasNext methods.</p>
		 *
		 * <p>callNext takes arguments to pass to the handler and returns the
		 * result of calling the handler</p>
		 *
		 * <p>hasNext checks if there is another handler</p>
		 *
		 * @param {String} signal
		 * @return {Object} Object with hasNext and callNext methods
		 * @function
		 * @name iter
		 * @memberOf jQuery.sceditor.PluginManager.prototype
		 */
		base.iter = function(signal) {
			signal = formatSignalName(signal);

			return (function () {
				var i = plugins.length;

				return {
					callNext: function(args) {
						while(i--)
							if(plugins[i] && signal in plugins[i])
								return plugins[i].apply(editorInstance, args);
					},
					hasNext: function() {
						var j = i;

						while(j--)
							if(plugins[j] && signal in plugins[j])
								return true;

						return false;
					}
				};
			}());
		};

		/**
		 * Checks if a signal has a handler
		 * @param  {String} signal
		 * @return {Boolean}
		 * @function
		 * @name hasHandler
		 * @memberOf jQuery.sceditor.PluginManager.prototype
		 */
		base.hasHandler = function(signal) {
			var i  = plugins.length;
			signal = formatSignalName(signal);

			while(i--)
				if(signal in plugins[i])
					return true;

			return false;
		};

		/**
		 * Checks if the plugin exists in jQuery.sceditor.plugins
		 * @param  {String} plugin
		 * @return {Boolean}
		 * @function
		 * @name exists
		 * @memberOf jQuery.sceditor.PluginManager.prototype
		 */
		base.exsists = function(plugin) {
			if(plugin in $.sceditor.plugins)
			{
				plugin = $.sceditor.plugins[plugin];

				return typeof plugin === 'function' && typeof plugin.prototype === 'object';
			}

			return false;
		};

		/**
		 * Checks if the passed plugin is currently registered.
		 * @param  {String} plugin
		 * @return {Boolean}
		 * @function
		 * @name isRegistered
		 * @memberOf jQuery.sceditor.PluginManager.prototype
		 */
		base.isRegistered = function(plugin) {
			var i = plugins.length;

			if(!base.exsists(plugin))
				return false;

			while(i--)
				if(plugins[i] instanceof $.sceditor.plugins[plugin])
					return true;

			return false;
		};

		/**
		 * Registers a plugin to receive signals
		 * @param  {String} plugin
		 * @return {Boolean}
		 * @function
		 * @name register
		 * @memberOf jQuery.sceditor.PluginManager.prototype
		 */
		base.register = function(plugin) {
			if(!base.exsists(plugin))
				return false;

			plugin = new $.sceditor.plugins[plugin]();
			plugins.push(plugin);

			if('init' in plugin)
				plugin.init.apply(editorInstance);

			return true;
		};

		/**
		 * Deregisters a plugin.
		 * @param  {String} plugin
		 * @return {Boolean}
		 * @function
		 * @name deregister
		 * @memberOf jQuery.sceditor.PluginManager.prototype
		 */
		base.deregister = function(plugin) {
			var	removedPlugin,
				i   = plugins.length,
				ret = false;

			if(!base.isRegistered(plugin))
				return false;

			while(i--)
			{
				if(plugins[i] instanceof $.sceditor.plugins[plugin])
				{
					removedPlugin = plugins.splice(i, 1)[0];
					ret    = true;

					if('destroy' in removedPlugin)
						removedPlugin.destroy.apply(editorInstance);
				}
			}

			return ret;
		};

		/**
		 * <p>Clears all plugins and removes the owner reference.</p>
		 *
		 * <p>Calling any functions on this object after calling destroy will cause a JS error.</p>
		 * @return {Void}
		 * @function
		 * @name destroy
		 * @memberOf jQuery.sceditor.PluginManager.prototype
		 */
		base.destroy = function() {
			var i = plugins.length;

			while(i--)
				if('destroy' in plugins[i])
					plugins[i].destroy.apply(editorInstance);

			plugins        = null;
			editorInstance = null;
		};
	};

	/**
	 * Static command helper class
	 * @class command
	 * @name jQuery.sceditor.command
	 */
	$.sceditor.command =
	/** @lends jQuery.sceditor.command */
	{
		/**
		 * Gets a command
		 *
		 * @param {String} name
		 * @return {Object|null}
		 * @since v1.3.5
		 */
		get: function(name) {
			return $.sceditor.commands[name] || null;
		},

		/**
		 * <p>Adds a command to the editor or updates an existing
		 * command if a command with the specified name already exists.</p>
		 *
		 * <p>Once a command is add it can be included in the toolbar by
		 * adding it's name to the toolbar option in the constructor. It
		 * can also be executed manually by calling {@link jQuery.sceditor.execCommand}</p>
		 *
		 * @example
		 * $.sceditor.command.set("hello",
		 * {
		 *     exec: function() {
		 *         alert("Hello World!");
		 *     }
		 * });
		 *
		 * @param {String} name
		 * @param {Object} cmd
		 * @return {this|false} Returns false if name or cmd is false
		 * @since v1.3.5
		 */
		set: function(name, cmd) {
			if(!name || !cmd)
				return false;

			// merge any existing command properties
			cmd = $.extend($.sceditor.commands[name] || {}, cmd);

			cmd.remove = function() { $.sceditor.command.remove(name); };

			$.sceditor.commands[name] = cmd;
			return this;
		},

		/**
		 * Removes a command
		 *
		 * @param {String} name
		 * @return {this}
		 * @since v1.3.5
		 */
		remove: function(name) {
			if($.sceditor.commands[name])
				delete $.sceditor.commands[name];

			return this;
		}
	};

	/**
	 * Default options for SCEditor
	 * @type {Object}
	 * @class defaultOptions
	 * @name jQuery.sceditor.defaultOptions
	 */
	$.sceditor.defaultOptions = {
		/** @lends jQuery.sceditor.defaultOptions */
		/**
		 * Toolbar buttons order and groups. Should be comma separated and have a bar | to separate groups
		 * @type {String}
		 */
		toolbar:	'bold,italic,underline,strike|color,emoticon',

		/**
		 * Comma separated list of commands to excludes from the toolbar
		 * @type {String}
		 */
		toolbarExclude: null,

		/**
		 * Stylesheet to include in the WYSIWYG editor. Will style the WYSIWYG elements
		 * @type {String}
		 */
		style: 'extension/sceditor/design/sceditortheme/css/sceditor.default.css',

		/**
		 * Comma separated list of fonts for the font selector
		 * @type {String}
		 */
		fonts: 'Arial,Arial Black,Comic Sans MS,Courier New,Georgia,Impact,Sans-serif,Serif,Times New Roman,Trebuchet MS,Verdana',

		/**
		 * Colors should be comma separated and have a bar | to signal a new column.
		 *
		 * If null the colors will be auto generated.
		 * @type {string}
		 */
		colors: null,

		/**
		 * The locale to use.
		 * @type {String}
		 */
		locale: 'en',

		/**
		 * The Charset to use
		 * @type {String}
		 */
		charset: 'utf-8',

		/**
		 * Compatibility mode for emoticons.
		 *
		 * Helps if you have emoticons such as :/ which would put an emoticon inside http://
		 *
		 * This mode requires emoticons to be surrounded by whitespace or end of line chars.
		 * This mode has limited As You Type emoticon conversion support. It will not replace
		 * AYT for end of line chars, only emoticons surrounded by whitespace. They will still
		 * be replaced correctly when loaded just not AYT.
		 * @type {Boolean}
		 */
		emoticonsCompat: false,

		/**
		 * If to enable emoticons. Can be changes at runtime using the emoticons() method.
		 * @type {Boolean}
		 * @since 1.4.2
		 */
		emoticonsEnabled: true,

		/**
		 * Emoticon root URL
		 * @type {String}
		 */
		emoticonsRoot: '/extension/sceditor/design/sceditortheme/images/smileys/',
		emoticons: { //Refer : http://messenger.yahoo.com/features/emoticons/, must sync with lhbbcode.php, exclude: >, <, ",/,\,^
			dropdown: {
				":)" : "1.gif",
				":(" : "2.gif",
				";)" : "3.gif",
				":D" : "4.gif",
				";;)" : "5.gif",
				":-/" : "7.gif",
				":x" : "8.gif",
				':-$' : "32.gif",
				":P" : "10.gif",
				":-*" : "11.gif",
				"=((" : "12.gif",
				":-O" : "13.gif",
				":v" : "15.gif",
				"B-)" : "16.gif",
				":-S" : "17.gif",
				"v:)" : "19.gif",
				":[[" : "20.gif",
				":]]" : "21.gif",
				":|" : "22.gif",
				"(:|" : "37.gif",
				"=D" : "41.gif",
				":-w" : "45.gif",
				"/:)" : "23.gif",
				"@-)" : "43.gif",
				":-h" : "103.gif",
				"=))" : "24.gif",
				":-b" : "100.gif",
				":-c" : "101.gif",
			},
			hidden: {
				"B)" : "26.gif",
				"#:-S" : "18.gif",
				">:D<" : "6.gif",
				"X(" : "14.gif",
				"O:-)" : "25.gif",
				":!!" : "110.gif",
				":-v" : "111.gif",
				":-q" : "112.gif",
				":-d" : "113.gif",
				":-e" : "cheer.gif",
				"@_@" : "studying.gif",
				"~X(" : "102.gif",
				
			}
		},

		/**
		 * Width of the editor. Set to null for automatic with
		 * @type {int}
		 */
		width: null,

		/**
		 * Height of the editor including toolbar. Set to null for automatic height
		 * @type {int}
		 */
		height: 68,

		/**
		 * If to allow the editor to be resized
		 * @type {Boolean}
		 */
		resizeEnabled: true,

		/**
		 * Min resize to width, set to null for half textarea width or -1 for unlimited
		 * @type {int}
		 */
		resizeMinWidth: null,
		/**
		 * Min resize to height, set to null for half textarea height or -1 for unlimited
		 * @type {int}
		 */
		resizeMinHeight: null,
		/**
		 * Max resize to height, set to null for double textarea height or -1 for unlimited
		 * @type {int}
		 */
		resizeMaxHeight: null,
		/**
		 * Max resize to width, set to null for double textarea width or -1 for unlimited
		 * @type {int}
		 */
		resizeMaxWidth: null,
		/**
		 * If resizing by height is enabled
		 * @type {Boolean}
		 */
		resizeHeight: true,
		/**
		 * If resizing by width is enabled
		 * @type {Boolean}
		 */
		resizeWidth: true,

		getHtmlHandler: null,
		getTextHandler: null,

		/**
		 * Date format, will be overridden if locale specifies one.
		 *
		 * The words year, month and day will be replaced with the users current year, month and day.
		 * @type {String}
		 */
		dateFormat: 'year-month-day',

		/**
		 * Element to inset the toolbar into.
		 * @type {HTMLElement}
		 */
		toolbarContainer: null,

		/**
		 * If to enable paste filtering. This is currently experimental, please report any issues.
		 * @type {Boolean}
		 */
		enablePasteFiltering: false,

		/**
		 * If to completely disable pasting into the editor
		 * @type {Boolean}
		 */
		disablePasting: false,

		/**
		 * If the editor is read only.
		 * @type {Boolean}
		 */
		readOnly: false,

		/**
		 * If to set the editor to right-to-left mode.
		 *
		 * If set to null the direction will be automatically detected.
		 * @type {Boolean}
		 */
		rtl: false,

		/**
		 * If to auto focus the editor on page load
		 * @type {Boolean}
		 */
		autofocus: false,

		/**
		 * If to auto focus the editor to the end of the content
		 * @type {Boolean}
		 */
		autofocusEnd: true,

		/**
		 * If to auto expand the editor to fix the content
		 * @type {Boolean}
		 */
		autoExpand: false,

		/**
		 * If to auto update original textbox on blur
		 * @type {Boolean}
		 */
		autoUpdate: false,

		/**
		 * If to enable the browsers built in spell checker
		 * @type {Boolean}
		 */
		spellcheck: true,

		/**
		 * If to run the source editor when there is no WYSIWYG support. Only really applies to mobile OS's.
		 * @type {Boolean}
		 */
		runWithoutWysiwygSupport: false,

		/**
		 * Optional ID to give the editor.
		 * @type {String}
		 */
		id: null,

		/**
		 * Comma separated list of plugins
		 * @type {String}
		 */
		plugins: 'bbcode',

		/**
		 * z-index to set the editor container to. Needed for jQuery UI dialog.
		 * @type {Int}
		 */
		zIndex: null,

		/**
		 * If to trim the BBCode. Removes any spaces at the start and end of the BBCode string.
		 * @type {Boolean}
		 */
		bbcodeTrim: true,

		/**
		 * If to disable removing block level elements by pressing backspace at the start of them
		 * @type {Boolean}
		 */
		disableBlockRemove: false,

		/**
		 * BBCode parser options, only applies if using the editor in BBCode mode.
		 *
		 * See $.sceditor.BBCodeParser.defaults for list of valid options
		 * @type {Object}
		 */
		parserOptions: { },

		/**
		 * CSS that will be added to the to dropdown menu (eg. z-index)
		 * @type {Object}
		 */
		dropDownCss: { },
		customizeToolbar: null, //new, append html to toolbar
		keyEnter: null, //new, process enter key event
		keyPress: null //new, process key press event
	};

	/**
	 * Creates an instance of sceditor on all textareas
	 * matched by the jQuery selector.
	 *
	 * If options is set to "state" it will return bool value
	 * indicating if the editor has been initilised on the
	 * matched textarea(s). If there is only one textarea
	 * it will return the bool value for that textarea.
	 * If more than one textarea is matched it will
	 * return an array of bool values for each textarea.
	 *
	 * If options is set to "instance" it will return the
	 * current editor instance for the textarea(s). Like the
	 * state option, if only one textarea is matched this will
	 * return just the instance for that textarea. If more than
	 * one textarea is matched it will return an array of
	 * instances each textarea.
	 *
	 * @param  {Object|String} options Should either be an Object of options or the strings "state" or "instance"
	 * @return {this|Array|jQuery.sceditor|Bool}
	 */
	$.fn.sceditor = function (options) {
		var	$this,
			ret = [];

		options = options || {};

		if(!options.runWithoutWysiwygSupport && !$.sceditor.isWysiwygSupported)
			return;

		this.each(function () {

			$this = this.jquery ? this : $(this);
			// Don't allow the editor to be initilised on it's own source editor
			if($this.parents('.sceditor-container').length > 0)
				return;

			// Add state of instance to ret if that is what options is set to
			if(options === 'state')
				ret.push(!!$this.data('sceditor'));
			else if(options === 'instance')
				ret.push($this.data('sceditor'));
			else if(!$this.data('sceditor'))
				(new $.sceditor(this, options));
		});

		// If nothing in the ret array then must be init so return this
		if(!ret.length)
			return this;

		return ret.length === 1 ? ret[0] : $(ret);
	};
})(jQuery, window, document);

/**
 * SCEditor BBCode Plugin
 * http://www.sceditor.com/
 *
 * Copyright (C) 2011-2013, Sam Clarke (samclarke.com)
 *
 * SCEditor is licensed under the MIT license:
 *	http://www.opensource.org/licenses/mit-license.php
 *
 * @fileoverview SCEditor BBCode Plugin
 * @author Sam Clarke
 * @requires jQuery
 */

// ==ClosureCompiler==
// @output_file_name bbcode.min.js
// @compilation_level SIMPLE_OPTIMIZATIONS
// ==/ClosureCompiler==

/*jshint smarttabs: true, jquery: true, eqnull:true, curly: false */
/*global prompt: true*/

(function($, window, document) {
	'use strict';

	/**
	 * SCEditor BBCode parser class
	 *
	 * @param {Object} options
	 * @class BBCodeParser
	 * @name jQuery.sceditor.BBCodeParser
	 * @since v1.4.0
	 */
	$.sceditor.BBCodeParser = function(options) {
		// make sure this is not being called as a function
		if(!(this instanceof $.sceditor.BBCodeParser))
			return new $.sceditor.BBCodeParser(options);

		var base = this;

		// Private methods
		var	init,
			tokenizeTag,
			tokenizeAttrs,
			parseTokens,
			normaliseNewLines,
			fixNesting,
			isChildAllowed,
			removeEmpty,
			fixChildren,
			convertToHTML,
			convertToBBCode,
			hasTag,
			quote,
			lower,
			last;

		/**
		 * Enum of valid token types
		 * @type {Object}
		 * @private
		 */
		var tokenType = {
			open:    'open',
			content: 'content',
			newline: 'newline',
			close:   'close'
		};

		/**
		 * Tokenize token class
		 *
		 * @param  {String} type The type of token this is, should be one of tokenType
		 * @param  {String} name The name of this token
		 * @param  {String} val The originally matched string
		 * @param  {Array} attrs Any attributes. Only set on tokenType.open tokens
		 * @param  {Array} children Any children of this token
		 * @param  {TokenizeToken} closing This tokens closing tag. Only set on tokenType.open tokens
		 * @class TokenizeToken
		 * @name TokenizeToken
		 * @memberOf jQuery.sceditor.BBCodeParser.prototype
		 */
		var TokenizeToken = function(type, name, val, attrs, children, closing) {
			var base      = this;
			base.type     = type;
			base.name     = name;
			base.val      = val;
			base.attrs    = attrs || {};
			base.children = children || [];
			base.closing  = closing || null;
		};

		// Declaring methods via prototype instead of in the constructor
		// to reduce memory usage as there could be a lot or these
		// objects created.
		TokenizeToken.prototype = {
			/** @lends jQuery.sceditor.BBCodeParser.prototype.TokenizeToken */
			/**
			 * Clones this token
			 * @param  {Bool} includeChildren If to include the children in the clone. Defaults to false.
			 * @return {TokenizeToken}
			 */
			clone: function(includeChildren) {
				var base = this;
				return new TokenizeToken(
					base.type,
					base.name,
					base.val,
					base.attrs,
					includeChildren ? base.children : [],
					base.closing ? base.closing.clone() : null
				);
			},
			/**
			 * Splits this token at the specified child
			 * @param  {TokenizeToken|Int} splitAt The child to split at or the index of the child
			 * @return {TokenizeToken} The right half of the split token or null if failed
			 */
			splitAt: function(splitAt) {
				var	clone,
					base          = this,
					splitAtLength = 0,
					childrenLen   = base.children.length;

				if(typeof object !== 'number')
					splitAt = $.inArray(splitAt, base.children);

				if(splitAt < 0 || splitAt > childrenLen)
					return null;

				// Work out how many items are on the right side of the split
				// to pass to splice()
				while(childrenLen--)
				{
					if(childrenLen >= splitAt)
						splitAtLength++;
					else
						childrenLen = 0;
				}

				clone          = base.clone();
				clone.children = base.children.splice(splitAt, splitAtLength);
				return clone;
			}
		};


		init = function() {
			base.opts    = $.extend({}, $.sceditor.BBCodeParser.defaults, options);
			base.bbcodes = $.sceditor.plugins.bbcode.bbcodes;
		};

		/**
		 * Takes a BBCode string and splits it into open, content and close tags.
		 *
		 * It does no checking to verify a tag has a matching open or closing tag
		 * or if the tag is valid child of any tag before it. For that the tokens
		 * should be passed to the parse function.
		 *
		 * @param {String} str
		 * @return {Array}
		 * @memberOf jQuery.sceditor.BBCodeParser.prototype
		 */
		base.tokenize = function(str) {
			var	matches, type, i,
				toks   = [],
				tokens = [
					// Close must come before open as they are
					// the same except close has a / at the start.
					{
						type: 'close',
						regex: /^\[\/[^\[\]]+\]/
					},
					{
						type: 'open',
						regex: /^\[[^\[\]]+\]/
					},
					{
						type: 'newline',
						regex: /^(\r\n|\r|\n)/
					},
					{
						type: 'content',
						regex: /^([^\[\r\n]+|\[)/
					}
				];

			tokens.reverse();

			strloop:
			while(str.length)
			{
				i = tokens.length;
				while(i--)
				{
					type = tokens[i].type;

					// Check if the string matches any of the tokens
					if(!(matches = str.match(tokens[i].regex)) || !matches[0])
						continue;

					// Add the match to the tokens list
					toks.push(tokenizeTag(type, matches[0]));

					// Remove the match from the string
					str = str.substr(matches[0].length);

					// The token has been added so start again
					continue strloop;
				}

				// If there is anything left in the string which doesn't match
				// any of the tokens then just assume it's content and add it.
				if(str.length)
					toks.push(tokenizeTag(tokenType.content, str));

				str = '';
			}

			return toks;
		};

		/**
		 * Extracts the name an params from a tag
		 *
		 * @param {Object} token
		 * @return {Object}
		 * @private
		 */
		tokenizeTag = function(type, val) {
			var matches, attrs, name;

			// Extract the name and attributes from opening tags and
			// just the name from closing tags.
			if(type === 'open' && (matches = val.match(/\[([^\]\s=]+)(?:([^\]]+))?\]/)))
			{
				name = lower(matches[1]);

				if(matches[2] && (matches[2] = $.trim(matches[2])))
					attrs = tokenizeAttrs(matches[2]);
			}
			else if(type === 'close' && (matches = val.match(/\[\/([^\[\]]+)\]/)))
				name = lower(matches[1]);
			else if(type === 'newline')
				name = '#newline';

			// Treat all tokens without a name and all unknown BBCodes as content
			if(!name || (type === 'open' || type === 'close') && !$.sceditor.plugins.bbcode.bbcodes[name])
			{
				type = 'content';
				name = '#';
			}

			return new TokenizeToken(type, name, val, attrs);
		};

		/**
		 * Extracts the individual attributes from a string containing
		 * all the attributes.
		 *
		 * @param {String} attrs
		 * @return {Array} Assoc array of attributes
		 * @private
		 */
		tokenizeAttrs = function(attrs) {
			var	matches,
				/*
				([^\s=]+)					Anything that's not a space or equals
				=						Equals =
				(?:
					(?:
						(["'])				The opening quote
						(
							(?:\\\2|[^\2])*?	Anything that isn't the unescaped opening quote
						)
						\2				The opening quote again which will now close the string
					)
						|				If not a quoted string then match
					(
						(?:.(?!\s\S+=))*.?		Anything that isn't part of [space][non-space][=] which would be a new attribute
					)
				)
				*/
				atribsRegex = /([^\s=]+)=(?:(?:(["'])((?:\\\2|[^\2])*?)\2)|((?:.(?!\s\S+=))*.))/g,
				unquote     = $.sceditor.plugins.bbcode.stripQuotes,
				ret         = {};

			// if only one attribute then remove the = from the start and strip any quotes
			if(attrs.charAt(0) === '=' && attrs.indexOf('=', 1) < 0)
				ret.defaultattr = unquote(attrs.substr(1));
			else
			{
				if(attrs.charAt(0) === '=')
					attrs = 'defaultattr' + attrs;

				// No need to strip quotes here, the regex will do that.
				while((matches = atribsRegex.exec(attrs)))
					ret[lower(matches[1])] = unquote(matches[3]) || matches[4];
			}

			return ret;
		};

		/**
		 * Parses a string into an array of BBCodes
		 *
		 * @param {String} str
		 * @param {Bool} preserveNewLines If to preserve all new lines, not strip any based on the passed formatting options
		 * @return {Array} Array of BBCode objects
		 * @memberOf jQuery.sceditor.BBCodeParser.prototype
		 */
		base.parse = function(str, preserveNewLines) {
			var ret = parseTokens(base.tokenize(str));

			if(base.opts.fixInvalidChildren)
				fixChildren(ret);

			if(base.opts.removeEmptyTags)
				removeEmpty(ret);

			if(base.opts.fixInvalidNesting)
				fixNesting(ret);

			normaliseNewLines(ret, null, preserveNewLines);

			if(base.opts.removeEmptyTags)
				removeEmpty(ret);

			return ret;
		};

		/**
		 * Checks if an array of TokenizeToken's contains the
		 * specified token.
		 *
		 * Checks the tokens name and type match another tokens
		 * name and type in the array.
		 *
		 * @param  {string}    name
		 * @param  {tokenType} type
		 * @param  {Array}     arr
		 * @return {Boolean}
		 * @private
		 */
		hasTag = function(name, type, arr) {
			var i = arr.length;

			while(i--)
				if(arr[i].type === type && arr[i].name === name)
					return true;

			return false;
		};

		/**
		 * Checks if the child tag is allowed as one
		 * of the parent tags children.
		 *
		 * @param  {TokenizeToken}  parent
		 * @param  {TokenizeToken}  child
		 * @return {Boolean}
		 * @private
		 */
		isChildAllowed = function(parent, child) {
			var	bbcode          = parent ? base.bbcodes[parent.name] : null,
				allowedChildren = bbcode ? bbcode.allowedChildren : null;

			if(!base.opts.fixInvalidChildren || !allowedChildren)
				return true;

			if(allowedChildren && $.inArray(child.name || '#', allowedChildren) < 0)
				return false;

			return true;
		};

// TODO: Tidy this parseTokens() function up a bit.
		/**
		 * Parses an array of tokens created by tokenize()
		 *
		 * @param  {Array} toks
		 * @return {Array} Parsed tokens
		 * @see tokenize()
		 * @private
		 */
		parseTokens = function(toks) {
			var	token, bbcode, curTok, clone, i, previous, next,
				cloned     = [],
				output     = [],
				openTags   = [],
				/**
				 * Returns the currently open tag or undefined
				 * @return {TokenizeToken}
				 */
				currentOpenTag = function() {
					return last(openTags);
				},
				/**
				 * Adds a tag to either the current tags children
				 * or to the output array.
				 * @param {TokenizeToken} token
				 * @private
				 */
				addTag = function(token) {
					if(currentOpenTag())
						currentOpenTag().children.push(token);
					else
						output.push(token);
				},
				/**
				 * Checks if this tag closes the current tag
				 * @param  {String} name
				 * @return {Void}
				 */
				closesCurrentTag = function(name) {
					return currentOpenTag() &&
						(bbcode = base.bbcodes[currentOpenTag().name]) &&
						bbcode.closedBy &&
						$.inArray(name, bbcode.closedBy) > -1;
				};

			while((token = toks.shift()))
			{
				next = toks[0];

				switch(token.type)
				{
					case tokenType.open:
						// Check it this closes a parent, i.e. for lists [*]one [*]two
						if(closesCurrentTag(token.name))
							openTags.pop();

						addTag(token);
						bbcode = base.bbcodes[token.name];

						// If this tag is not self closing and it has a closing tag then it is open and has children so
						// add it to the list of open tags. If has the closedBy property then it is closed by other tags
						// so include everything as it's children until one of those tags is reached.
						if((!bbcode || !bbcode.isSelfClosing) && (bbcode.closedBy || hasTag(token.name, tokenType.close, toks)))
							openTags.push(token);
						else if(!bbcode || !bbcode.isSelfClosing)
							token.type = tokenType.content;
						break;

					case tokenType.close:
						// check if this closes the current tag, e.g. [/list] would close an open [*]
						if(currentOpenTag() && token.name !== currentOpenTag().name && closesCurrentTag('/' + token.name))
							openTags.pop();

						// If this is closing the currently open tag just pop the close
						// tag off the open tags array
						if(currentOpenTag() && token.name === currentOpenTag().name)
						{
							currentOpenTag().closing = token;
							openTags.pop();
						}
						// If this is closing an open tag that is the parent of the current
						// tag then clone all the tags including the current one until
						// reaching the parent that is being closed. Close the parent and then
						// add the clones back in.
						else if(hasTag(token.name, tokenType.open, openTags))
						{
							// Remove the tag from the open tags
							while((curTok = openTags.pop()))
							{
								// If it's the tag that is being closed then
								// discard it and break the loop.
								if(curTok.name === token.name)
								{
									curTok.closing = token;
									break;
								}

								// Otherwise clone this tag and then add any
								// previously cloned tags as it's children
								clone = curTok.clone();

								if(cloned.length > 1)
									clone.children.push(last(cloned));

								cloned.push(clone);
							}

							// Add the last cloned child to the now current tag
							// (the parent of the tag which was being closed)
							addTag(last(cloned));

							// Add all the cloned tags to the open tags list
							i = cloned.length;
							while(i--)
								openTags.push(cloned[i]);

							cloned.length = 0;
						}
						// This tag is closing nothing so treat it as content
						else
						{
							token.type = tokenType.content;
							addTag(token);
						}
						break;

					case tokenType.newline:
						// handle things like
						//     [*]list\nitem\n[*]list1
						// where it should come out as
						//     [*]list\nitem[/*]\n[*]list1[/*]
						// instead of
						//     [*]list\nitem\n[/*][*]list1[/*]
						if(currentOpenTag() && next && closesCurrentTag((next.type === tokenType.close ? '/' : '') + next.name))
						{
							// skip if the next tag is the closing tag for the option tag, i.e. [/*]
							if(!(next.type === tokenType.close && next.name === currentOpenTag().name))
							{
								bbcode = base.bbcodes[currentOpenTag().name];

								if(bbcode && bbcode.breakAfter)
									openTags.pop();
								else if(bbcode && bbcode.isInline === false && base.opts.breakAfterBlock && bbcode.breakAfter !== false)
									openTags.pop();
							}
						}

						addTag(token);
						break;

					default: // content
						addTag(token);
						break;
				}

				previous = token;
			}

			return output;
		};

		/**
		 * Normalise all new lines
		 *
		 * Removes any formatting new lines from the BBCode
		 * leaving only content ones. I.e. for a list:
		 *
		 * [list]
		 * [*] list item one
		 * with a line break
		 * [*] list item two
		 * [/list]
		 *
		 * would become
		 *
		 * [list] [*] list item one
		 * with a line break [*] list item two [/list]
		 *
		 * Which makes it easier to convert to HTML or add
		 * the formatting new lines back in when converting
		 * back to BBCode
		 *
		 * @param  {Array} children
		 * @param  {TokenizeToken} parent
		 * @param  {Bool} onlyRemoveBreakAfter
		 * @return {void}
		 */
		normaliseNewLines = function(children, parent, onlyRemoveBreakAfter) {
			var	token, left, right, parentBBCode, bbcode,
				removedBreakEnd, removedBreakBefore, remove,
				childrenLength = children.length,
				i              = childrenLength;

			if(parent)
				parentBBCode = base.bbcodes[parent.name];

			while(i--)
			{
				if(!(token = children[i]))
					continue;

				if(token.type === tokenType.newline)
				{
					left   = i > 0 ? children[i - 1] : null;
					right  = i < childrenLength - 1 ? children[i+1] : null;
					remove = false;

					// Handle the start and end new lines e.g. [tag]\n and \n[/tag]
					if(!onlyRemoveBreakAfter && parentBBCode && parentBBCode.isSelfClosing !== true)
					{
						// First child of parent so must be opening line break (breakStartBlock, breakStart) e.g. [tag]\n
						if(!left)
						{
							if(parentBBCode.isInline === false && base.opts.breakStartBlock && parentBBCode.breakStart !== false)
								remove = true;

							if(parentBBCode.breakStart)
								remove = true;
						}
						// Last child of parent so must be end line break (breakEndBlock, breakEnd) e.g. \n[/tag]
						// remove last line break (breakEndBlock, breakEnd)
						else if (!removedBreakEnd && !right)
						{
							if(parentBBCode.isInline === false && base.opts.breakEndBlock && parentBBCode.breakEnd !== false)
								remove = true;

							if(parentBBCode.breakEnd)
								remove = true;

							removedBreakEnd = remove;
						}
					}

					if(left && left.type === tokenType.open)
					{
						if((bbcode = base.bbcodes[left.name]))
						{
							if(!onlyRemoveBreakAfter)
							{
								if(bbcode.isInline === false && base.opts.breakAfterBlock && bbcode.breakAfter !== false)
									remove = true;

								if(bbcode.breakAfter)
									remove = true;
							}
							else if(bbcode.isInline === false)
								remove = true;
						}
					}

					if(!onlyRemoveBreakAfter && !removedBreakBefore && right && right.type === tokenType.open)
					{
						if((bbcode = base.bbcodes[right.name]))
						{
							if(bbcode.isInline === false && base.opts.breakBeforeBlock && bbcode.breakBefore !== false)
								remove = true;

							if(bbcode.breakBefore)
								remove = true;

							removedBreakBefore = remove;

							if(remove)
							{
								children.splice(i, 1);
								continue;
							}
						}
					}

					if(remove)
						children.splice(i, 1);

					// reset double removedBreakBefore removal protection.
					// This is needed for cases like \n\n[\tag] where
					// only 1 \n should be removed but without this they both
					// would be.
					removedBreakBefore = false;
				}
				else if(token.type === tokenType.open)
					normaliseNewLines(token.children, token, onlyRemoveBreakAfter);
			}
		};

		/**
		 * Fixes any invalid nesting.
		 *
		 * If it is a block level element inside 1 or more inline elements
		 * then those inline elements will be split at the point where the
		 * block level is and the block level element placed between the split
		 * parts. i.e.
		 *     [inline]textA[blocklevel]textB[/blocklevel]textC[/inline]
		 * Will become:
		 *     [inline]textA[/inline][blocklevel]textB[/blocklevel][inline]textC[/inline]
		 *
		 * @param {Array} children
		 * @param {Array} [parents] Null if there is no parents
		 * @param {Array} [insideInline] Boolean, if inside an inline element
		 * @param {Array} [rootArr] Root array if there is one
		 * @return {Array}
		 * @private
		 */
		fixNesting = function(children, parents, insideInline, rootArr) {
			var	token, i, parent, parentIndex, parentParentChildren, right,
				isInline = function(token) {
					var bbcode = base.bbcodes[token.name];

					return !bbcode || bbcode.isInline !== false;
				};

			parents = parents || [];
			rootArr = rootArr || children;

			// this must check length each time as the length
			// can change as tokens are moved around to fix the nesting.
			for(i=0; i<children.length; i++)
			{
				if(!(token = children[i]) || token.type !== tokenType.open)
					continue;

				if(!isInline(token) && insideInline)
				{
					// if this is a blocklevel element inside an inline one then split
					// the parent at the block level element
					parent               = last(parents);
					right                = parent.splitAt(token);
					parentParentChildren = parents.length > 1 ? parents[parents.length - 2].children : rootArr;

					if((parentIndex = $.inArray(parent, parentParentChildren)) > -1)
					{
						// remove the block level token from the right side of the split
						// inline element
						right.children.splice($.inArray(token, right.children), 1);

						// insert the block level token and the right side after the left
						// side of the inline token
						parentParentChildren.splice(parentIndex+1, 0, token, right);

						// return to parents loop as the children have now increased
						return;
					}

				}

				parents.push(token);
				fixNesting(token.children, parents, insideInline || isInline(token), rootArr);
				parents.pop(token);
			}
		};

		/**
		 * Fixes any invalid children.
		 *
		 * If it is an element which isn't allowed as a child of it's parent
		 * then it will be converted to content of the parent element. i.e.
		 *     [code]Code [b]only[/b] allows text.[/code]
		 * Will become:
		 *     <code>Code [b]only[/b] allows text.</code>
		 * Instead of:
		 *     <code>Code <b>only</b> allows text.</code>
		 *
		 * @param {Array} children
		 * @param {Array} [parent] Null if there is no parents
		 * @private
		 */
		fixChildren = function(children, parent) {
			var	token, args,
				i = children.length;

			while(i--)
			{
				if(!(token = children[i]))
					continue;

				if(!isChildAllowed(parent, token))
				{
					// if it is not then convert it to text and see if it
					// is allowed
					token.name = null;
					token.type = tokenType.content;

					if(isChildAllowed(parent, token))
					{
						args = [i+1, 0].concat(token.children);

						if(token.closing)
						{
							token.closing.name = null;
							token.closing.type = tokenType.content;
							args.push(token.closing);
						}

						i += args.length - 1;
						Array.prototype.splice.apply(children, args);
					}
					else
						parent.children.splice(i, 1);
				}

				if(token.type === tokenType.open)
					fixChildren(token.children, token);
			}
		};

		/**
		 * Removes any empty BBCodes which are not allowed to be empty.
		 *
		 * @param {Array} tokens
		 * @private
		 */
		removeEmpty = function(tokens) {
			var	token, bbcode, isTokenWhiteSpace,
				i = tokens.length;

			/**
			 * Checks if all children are whitespace or not
			 * @private
			 */
			isTokenWhiteSpace = function(children) {
				var j = children.length;

				while(j--)
				{
					if(children[j].type === tokenType.open)
						return false;

					if(children[j].type === tokenType.close)
						return false;

					if(children[j].type === tokenType.content && children[j].val && /\S|\u00A0/.test(children[j].val))
						return false;
				}

				return true;
			};

			while(i--)
			{
				// only tags can be empty, content can't be empty. So skip anything that isn't a tag.
				if(!(token = tokens[i]) || token.type !== tokenType.open)
					continue;

				bbcode = base.bbcodes[token.name];

				// remove any empty children of this tag first so that if they are all
				// removed this one doesn't think it's not empty.
				removeEmpty(token.children);

				if(isTokenWhiteSpace(token.children) && bbcode && !bbcode.isSelfClosing && !bbcode.allowsEmpty)
					tokens.splice.apply(tokens, $.merge([i, 1], token.children));
			}
		};

		/**
		 * Converts a BBCode string to HTML
		 * @param {String} str
		 * @param {Bool}   preserveNewLines If to preserve all new lines, not strip any based on the passed formatting options
		 * @return {String}
		 * @memberOf jQuery.sceditor.BBCodeParser.prototype
		 */
		base.toHTML = function(str, preserveNewLines) {
			return convertToHTML(base.parse(str, preserveNewLines), true);
		};

		/**
		 * @private
		 */
		convertToHTML = function(tokens, isRoot) {
			var	token, bbcode, content, html, needsBlockWrap, blockWrapOpen,
				isInline, lastChild,
				ret = [];

			isInline = function(bbcode) {
				return (!bbcode || (typeof bbcode.isHtmlInline !== 'undefined' ? bbcode.isHtmlInline : bbcode.isInline)) !== false;
			};

			while(tokens.length > 0)
			{
				if(!(token = tokens.shift()))
					continue;

				if(token.type === tokenType.open)
				{
					lastChild      = token.children[token.children.length - 1] || {};
					bbcode         = base.bbcodes[token.name];
					needsBlockWrap = isRoot && isInline(bbcode);
					content        = convertToHTML(token.children, false);

					if(bbcode && bbcode.html)
					{
						// Only add a line break to the end if this is blocklevel and the last child wasn't block-level
						if(!isInline(bbcode) && isInline(base.bbcodes[lastChild.name]) && !bbcode.isPreFormatted && !bbcode.skipLastLineBreak)
						{
							// Add placeholder br to end of block level elements in all browsers apart from IE < 9 which
							// handle new lines differently and doesn't need one.
							if(!$.sceditor.ie)
								content += '<br />';
						}

						if($.isFunction(bbcode.html))
							html = bbcode.html.call(base, token, token.attrs, content);
						else
							html = $.sceditor.plugins.bbcode.formatString(bbcode.html, content);
					}
					else
						html = token.val + content + (token.closing ? token.closing.val : '');
				}
				else if(token.type === tokenType.newline)
				{
					if(!isRoot)
					{
						ret.push('<br />');
						continue;
					}

					// If not already in a block wrap then start a new block
					if(!blockWrapOpen)
					{
						ret.push('<div>');

						// If it's an empty DIV and compatibility mode is below IE8 then
						// we must add a non-breaking space to the div otherwise the div
						// will be collapsed. Adding a BR works but when you press enter
						// to make a newline it suddenly goes back to the normal IE div
						// behaviour and creates two lines, one for the newline and one
						// for the BR. I'm sure there must be a better fix but I've yet to
						// find one.
						// Cannot do zoom: 1; or set a height on the div to fix it as that
						// causes resize handles to be added to the div when it's clicked on/
						if((document.documentMode && document.documentMode < 8) || $.sceditor.ie < 8)
							ret.push('\u00a0');
					}

					// Putting BR in a div in IE causes it to do a double line break.
					if(!$.sceditor.ie)
						ret.push('<br />');

					// Normally the div acts as a line-break with by moving whatever comes
					// after onto a new line.
					// If this is the last token, add an extra line-break so it shows as
					// there will be nothing after it.
					if(!tokens.length)
						ret.push('<br />');

					ret.push('</div>\n');
					blockWrapOpen = false;
					continue;
				}
				else // content
				{
					needsBlockWrap = isRoot;
					html           = $.sceditor.escapeEntities(token.val);
				}

				if(needsBlockWrap && !blockWrapOpen)
				{
					ret.push('<div>');
					blockWrapOpen = true;
				}
				else if(!needsBlockWrap && blockWrapOpen)
				{
					ret.push('</div>\n');
					blockWrapOpen = false;
				}

				ret.push(html);
			}

			if(blockWrapOpen)
				ret.push('</div>\n');

			return ret.join('');
		};

		/**
		 * Takes a BBCode string, parses it then converts it back to BBCode.
		 *
		 * This will auto fix the BBCode and format it with the specified options.
		 *
		 * @param {String} str
		 * @param {Bool} preserveNewLines If to preserve all new lines, not strip any based on the passed formatting options
		 * @return {String}
		 * @memberOf jQuery.sceditor.BBCodeParser.prototype
		 */
		base.toBBCode = function(str, preserveNewLines) {
			return convertToBBCode(base.parse(str, preserveNewLines));
		};

		/**
		 * Converts parsed tokens back into BBCode with the
		 * formatting specified in the options and with any
		 * fixes specified.
		 *
		 * @param  {Array} toks Array of parsed tokens from base.parse()
		 * @return {String}
		 * @private
		 */
		convertToBBCode = function(toks) {
			var	token, attr, bbcode, isBlock, isSelfClosing, quoteType,
				breakBefore, breakStart, breakEnd, breakAfter,
				// Create an array of strings which are joined together
				// before being returned as this is faster in slow browsers.
				// (Old versions of IE).
				ret = [];

			while(toks.length > 0)
			{
				if(!(token = toks.shift()))
					continue;

				bbcode        = base.bbcodes[token.name];
				isBlock       = !(!bbcode || bbcode.isInline !== false);
				isSelfClosing = bbcode && bbcode.isSelfClosing;
				breakBefore   = ((isBlock && base.opts.breakBeforeBlock && bbcode.breakBefore !== false) || (bbcode && bbcode.breakBefore));
				breakStart    = ((isBlock && !isSelfClosing && base.opts.breakStartBlock && bbcode.breakStart !== false) || (bbcode && bbcode.breakStart));
				breakEnd      = ((isBlock && base.opts.breakEndBlock && bbcode.breakEnd !== false) || (bbcode && bbcode.breakEnd));
				breakAfter    = ((isBlock && base.opts.breakAfterBlock && bbcode.breakAfter !== false) || (bbcode && bbcode.breakAfter));
				quoteType     = (bbcode ? bbcode.quoteType : null) || base.opts.quoteType || $.sceditor.BBCodeParser.QuoteType.auto;

				if(!bbcode && token.type === tokenType.open)
				{
					ret.push(token.val);

					if(token.children)
						ret.push(convertToBBCode(token.children));

					if(token.closing)
						ret.push(token.closing.val);
				}
				else if(token.type === tokenType.open)
				{
					if(breakBefore)
						ret.push('\n');

					// Convert the tag and it's attributes to BBCode
					ret.push('[' + token.name);
					if(token.attrs)
					{
						if(token.attrs.defaultattr)
						{
							ret.push('=' + quote(token.attrs.defaultattr, quoteType, 'defaultattr'));
							delete token.attrs.defaultattr;
						}

						for(attr in token.attrs)
							if(token.attrs.hasOwnProperty(attr))
								ret.push(' ' + attr + '=' + quote(token.attrs[attr], quoteType, attr));
					}
					ret.push(']');

					if(breakStart)
						ret.push('\n');

					// Convert the tags children to BBCode
					if(token.children)
						ret.push(convertToBBCode(token.children));

					// add closing tag if not self closing
					if(!isSelfClosing && !bbcode.excludeClosing)
					{
						if(breakEnd)
							ret.push('\n');

						ret.push('[/' + token.name + ']');
					}

					if(breakAfter)
						ret.push('\n');

					// preserve whatever was recognised as the closing tag if
					// it is a self closing tag
					if(token.closing && isSelfClosing)
						ret.push(token.closing.val);
				}
				else
					ret.push(token.val);
			}

			return ret.join('');
		};

		/**
		 * Quotes an attribute
		 *
		 * @param {String} str
		 * @param {$.sceditor.BBCodeParser.QuoteType} quoteType
		 * @param {String} name
		 * @return {String}
		 * @private
		 */
		quote = function(str, quoteType, name) {
			var	QuoteTypes  = $.sceditor.BBCodeParser.QuoteType,
				needsQuotes = /\s|=/.test(str);

			if($.isFunction(quoteType))
				return quoteType(str, name);

			if(quoteType === QuoteTypes.never || (quoteType === QuoteTypes.auto && !needsQuotes))
				return str;

			return '"' + str.replace('\\', '\\\\').replace('"', '\\"') + '"';
		};

		/**
		 * Returns the last element of an array or null
		 *
		 * @param {Array} arr
		 * @return {Object} Last element
		 * @private
		 */
		last = function(arr) {
			if(arr.length)
				return arr[arr.length - 1];

			return null;
		};

		/**
		 * Converts a string to lowercase.
		 *
		 * @param {String} str
		 * @return {String} Lowercase version of str
		 * @private
		 */
		lower = function(str) {
			return str.toLowerCase();
		};

		init();
	};

	/**
	 * Quote type
	 * @type {Object}
	 * @class QuoteType
	 * @name jQuery.sceditor.BBCodeParser.QuoteType
	 * @since v1.4.0
	 */
	$.sceditor.BBCodeParser.QuoteType = {
		/** @lends jQuery.sceditor.BBCodeParser.QuoteType */
		/**
		 * Always quote the attribute value
		 * @type {Number}
		 */
		always: 1,

		/**
		 * Never quote the attributes value
		 * @type {Number}
		 */
		never: 2,

		/**
		 * Only quote the attributes value when it contains spaces to equals
		 * @type {Number}
		 */
		auto: 3
	};

	/**
	 * Default BBCode parser options
	 * @type {Object}
	 */
	$.sceditor.BBCodeParser.defaults = {
		/**
		 * If to add a new line before block level elements
		 * @type {Boolean}
		 */
		breakBeforeBlock: false,

		/**
		 * If to add a new line after the start of block level elements
		 * @type {Boolean}
		 */
		breakStartBlock: false,

		/**
		 * If to add a new line before the end of block level elements
		 * @type {Boolean}
		 */
		breakEndBlock: false,

		/**
		 * If to add a new line after block level elements
		 * @type {Boolean}
		 */
		breakAfterBlock: true,

		/**
		 * If to remove empty tags
		 * @type {Boolean}
		 */
		removeEmptyTags: true,

		/**
		 * If to fix invalid nesting, i.e. block level elements inside inline elements.
		 * @type {Boolean}
		 */
		fixInvalidNesting: true,

		/**
		 * If to fix invalid children. i.e. A tag which is inside a parent that doesn't allow that type of tag.
		 * @type {Boolean}
		 */
		fixInvalidChildren: true,

		/**
		 * Attribute quote type
		 * @type {$.sceditor.BBCodeParser.QuoteType}
		 * @since 1.4.1
		 */
		quoteType: $.sceditor.BBCodeParser.QuoteType.auto
	};

	/**
	 * Deprecated, use $.sceditor.plugins.bbcode
	 *
	 * @class sceditorBBCodePlugin
	 * @name jQuery.sceditor.sceditorBBCodePlugin
	 * @deprecated
	 */
	$.sceditorBBCodePlugin =
	/**
	 * BBCode plugin for SCEditor
	 *
	 * @class bbcode
	 * @name jQuery.sceditor.plugins.bbcode
	 * @since 1.4.1
	 */
	$.sceditor.plugins.bbcode = function() {
		var base = this;

		/**
		 * Private methods
		 * @private
		 */
		var	buildBbcodeCache,
			handleStyles,
			handleTags,
			formatString,
			getStyle,
			mergeSourceModeCommands,
			removeFirstLastDiv;

		formatString     = $.sceditor.plugins.bbcode.formatString;
		base.bbcodes     = $.sceditor.plugins.bbcode.bbcodes;
		base.stripQuotes = $.sceditor.plugins.bbcode.stripQuotes;

		/**
		 * cache of all the tags pointing to their bbcodes to enable
		 * faster lookup of which bbcode a tag should have
		 * @private
		 */
		var tagsToBbcodes = {};

		/**
		 * Same as tagsToBbcodes but instead of HTML tags it's styles
		 * @private
		 */
		var stylesToBbcodes = {};

		/**
		 * Allowed children of specific HTML tags. Empty array if no
		 * children other than text nodes are allowed
		 * @private
		 */
		var validChildren = {
			ul: ['li', 'ol', 'ul'],
			ol: ['li', 'ol', 'ul'],
			table: ['tr'],
			tr: ['td', 'th'],
			code: ['br', 'p', 'div']
		};

		/**
		 * Cache of CamelCase versions of CSS properties
		 * @type {Object}
		 */
		var propertyCache = {};


		/**
		 * Initializer
		 * @private
		 */
		base.init = function() {
			base.opts = this.opts;

			// build the BBCode cache
			buildBbcodeCache();
			mergeSourceModeCommands(this);

			// Add BBCode helper methods
			this.toBBCode   = base.signalToSource;
			this.fromBBCode = base.signalToWysiwyg;
		};

		mergeSourceModeCommands = function(editor) {
			var getCommand = $.sceditor.command.get;

			var merge = {
				bold: { txtExec: ['[b]', '[/b]'] },
				italic: { txtExec: ['[i]', '[/i]'] },
				underline: { txtExec: ['[u]', '[/u]'] },
				strike: { txtExec: ['[s]', '[/s]'] },
				subscript: { txtExec: ['[sub]', '[/sub]'] },
				superscript: { txtExec: ['[sup]', '[/sup]'] },
				left: { txtExec: ['[left]', '[/left]'] },
				center: { txtExec: ['[center]', '[/center]'] },
				right: { txtExec: ['[right]', '[/right]'] },
				justify: { txtExec: ['[justify]', '[/justify]'] },
				font: {
					txtExec: function(caller) {
						var editor = this;

						getCommand('font')._dropDown(
							editor,
							caller,
							function(fontName) {
								editor.insertText('[font='+fontName+']', '[/font]');
							}
						);
					}
				},
				size: {
					txtExec: function(caller) {
						var editor = this;

						getCommand('size')._dropDown(
							editor,
							caller,
							function(fontSize) {
								editor.insertText('[size='+fontSize+']', '[/size]');
							}
						);
					}
				},
				color: {
					txtExec: function(caller) {
						var editor = this;

						getCommand('color')._dropDown(
							editor,
							caller,
							function(color) {
								editor.insertText('[color='+color+']', '[/color]');
							}
						);
					}
				},
				bulletlist: {
					txtExec: function(caller, selected) {
						var content = '';

						$.each(selected.split(/\r?\n/), function() {
							content += (content ? '\n' : '') + '[li]' + this + '[/li]';
						});

						editor.insertText('[ul]\n' + content + '\n[/ul]');
					}
				},
				orderedlist: {
					txtExec: function(caller, selected) {
						var content = '';

						$.each(selected.split(/\r?\n/), function() {
							content += (content ? '\n' : '') + '[li]' + this + '[/li]';
						});

						$.sceditor.plugins.bbcode.bbcode.get('');

						editor.insertText('[ol]\n' + content + '\n[/ol]');
					}
				},
				table: { txtExec: ['[table][tr][td]', '[/td][/tr][/table]'] },
				horizontalrule: { txtExec: ['[hr]'] },
				code: { txtExec: ['[code]', '[/code]'] },
				image: {
					txtExec: function(caller, selected) {
						var url = prompt(this._('Enter the image URL:'), selected);

						if(url)
							this.insertText('[img]' + url + '[/img]');
					}
				},
				email: {
					txtExec: function(caller, selected) {
						var	display = selected && selected.indexOf('@') > -1 ? null : selected,
							email	= prompt(this._('Enter the e-mail address:'), (display ? '' : selected)),
							text	= prompt(this._('Enter the displayed text:'), display || email) || email;

						if(email)
							this.insertText('[email=' + email + ']' + text + '[/email]');
					}
				},
				link: {
					txtExec: function(caller, selected) {
						var	display = selected && selected.indexOf('http://') > -1 ? null : selected,
							url	= prompt(this._('Enter URL:'), (display ? 'http://' : selected)),
							text	= prompt(this._('Enter the displayed text:'), display || url) || url;

						if(url)
							this.insertText('[url=' + url + ']' + text + '[/url]');
					}
				},
				quote: { txtExec: ['[quote]', '[/quote]'] },
				youtube: {
					txtExec: function(caller) {
						var editor = this;

						getCommand('youtube')._dropDown(
							editor,
							caller,
							function(id) {
								editor.insertText('[youtube]' + id + '[/youtube]');
							}
						);
					}
				},
				rtl: { txtExec: ['[rtl]', '[/rtl]'] },
				ltr: { txtExec: ['[ltr]', '[/ltr]'] }
			};

			editor.commands = $.extend(true, {}, merge, editor.commands);
		};

		/**
		 * Populates tagsToBbcodes and stylesToBbcodes to enable faster lookups
		 *
		 * @private
		 */
		buildBbcodeCache = function() {
			$.each(base.bbcodes, function(bbcode) {
				if(base.bbcodes[bbcode].tags)
					$.each(base.bbcodes[bbcode].tags, function(tag, values) {
						var isBlock = base.bbcodes[bbcode].isInline === false;
						tagsToBbcodes[tag] = (tagsToBbcodes[tag] || {});
						tagsToBbcodes[tag][isBlock] = (tagsToBbcodes[tag][isBlock] || {});
						tagsToBbcodes[tag][isBlock][bbcode] = values;
					});

				if(base.bbcodes[bbcode].styles)
					$.each(base.bbcodes[bbcode].styles, function(style, values) {
						var isBlock = base.bbcodes[bbcode].isInline === false;
						stylesToBbcodes[isBlock] = (stylesToBbcodes[isBlock] || {});
						stylesToBbcodes[isBlock][style] = (stylesToBbcodes[isBlock][style] || {});
						stylesToBbcodes[isBlock][style][bbcode] = values;
					});
			});
		};

		/**
		 * Gets the value of a style property on the passed element
		 * @private
		 */
		getStyle = function(element, property) {
			var	$elm, ret, dir, textAlign, name,
				style = element.style;

			if(!style)
				return null;

			if(!propertyCache[property])
				propertyCache[property] = $.camelCase(property);

			name = propertyCache[property];

			// add exception for align
			if('text-align' === property)
			{
				$elm      = $(element);
				dir       = style.direction;
				textAlign = style[name] || $elm.css(property);

				if($elm.parent().css(property) !== textAlign &&
					$elm.css('display') === 'block' && !$elm.is('hr') && !$elm.is('th'))
					ret = textAlign;

				// IE changes text-align to the same as the current direction so skip unless overridden by user
				if(dir && ret && ((/right/i.test(ret) && dir === 'rtl') || (/left/i.test(ret) && dir === 'ltr')))
					return null;

				return ret;
			}

			return style[name];
		};

		/**
		 * Checks if any bbcode styles match the elements styles
		 *
		 * @return string Content with any matching bbcode tags wrapped around it.
		 * @private
		 */
		handleStyles = function($element, content, blockLevel) {
			var	elementPropVal;

			// convert blockLevel to boolean
			blockLevel = !!blockLevel;

			if(!stylesToBbcodes[blockLevel])
				return content;

			$.each(stylesToBbcodes[blockLevel], function(property, bbcodes) {
				elementPropVal = getStyle($element[0], property);

				// if the parent has the same style use that instead of this one
				// so you don't end up with [i]parent[i]child[/i][/i]
				if(!elementPropVal || getStyle($element.parent()[0], property) === elementPropVal)
					return;

				$.each(bbcodes, function(bbcode, values) {
					if(!values || $.inArray(elementPropVal.toString(), values) > -1)
					{
						if($.isFunction(base.bbcodes[bbcode].format))
							content = base.bbcodes[bbcode].format.call(base, $element, content);
						else
							content = formatString(base.bbcodes[bbcode].format, content);
					}
				});
			});

			return content;
		};

		/**
		 * Handles a HTML tag and finds any matching bbcodes
		 *
		 * @param {jQuery} element The element to convert
		 * @param {String} content The Tags text content
		 * @param {Bool} blockLevel If to convert block level tags
		 * @return {String} Content with any matching bbcode tags wrapped around it.
		 * @private
		 */
		handleTags = function($element, content, blockLevel) {
			var	convertBBCode,
				element = $element[0],
				tag     = element.nodeName.toLowerCase();

			// convert blockLevel to boolean
			blockLevel = !!blockLevel;

			if(tagsToBbcodes[tag] && tagsToBbcodes[tag][blockLevel]) {
				// loop all bbcodes for this tag
				$.each(tagsToBbcodes[tag][blockLevel], function(bbcode, bbcodeAttribs) {
					// if the bbcode requires any attributes then check this has
					// all needed
					if(bbcodeAttribs)
					{
						convertBBCode = false;

						// loop all the bbcode attribs
						$.each(bbcodeAttribs, function(attrib, values) {
							// if the $element has the bbcodes attribute and the bbcode attribute
							// has values check one of the values matches
							if(!$element.attr(attrib) || (values && $.inArray($element.attr(attrib), values) < 0))
								return;

							// break this loop as we have matched this bbcode
							convertBBCode = true;
							return false;
						});

						if(!convertBBCode)
							return;
					}

					if($.isFunction(base.bbcodes[bbcode].format))
						content = base.bbcodes[bbcode].format.call(base, $element, content);
					else
						content = formatString(base.bbcodes[bbcode].format, content);
				});
			}

			if(blockLevel && (!$.sceditor.dom.isInline(element, true) || tag === 'br'))
			{
				var	parent		    = element.parentNode,
					parentLastChild = parent.lastChild,
					previousSibling = element.previousSibling,
					parentIsInline	= $.sceditor.dom.isInline(parent, true);

				// skips selection makers and other ignored items
				while(previousSibling && $(previousSibling).hasClass('sceditor-ignore'))
					previousSibling = previousSibling.previousSibling;

				while($(parentLastChild).hasClass('sceditor-ignore'))
					parentLastChild = parentLastChild.previousSibling;

				// If this is
				//	A br/block element inside an inline element.
				//	The last block level as the last block level is collapsed.
				//	Is an li element.
				//	Is IE and the tag is BR. IE never collapses BR's
				if(parentIsInline || parentLastChild !== element || tag === 'li' || (tag === 'br' && $.sceditor.ie))
					content += '\n';

				// Check for <div>text<div>This needs a newline prepended</div></div>
				if('br' !== tag && previousSibling && previousSibling.nodeName.toLowerCase() !== 'br' && $.sceditor.dom.isInline(previousSibling, true))
					content = '\n' + content;
			}

			return content;
		};

		/**
		 * Converts HTML to BBCode
		 * @param string	html	Html string, this function ignores this, it works off domBody
		 * @param HtmlElement	$body	Editors dom body object to convert
		 * @return string BBCode which has been converted from HTML
		 * @memberOf jQuery.plugins.bbcode.prototype
		 */
		base.signalToSource = function(html, $body) {
			var	$tmpContainer, bbcode,
				parser = new $.sceditor.BBCodeParser(base.opts.parserOptions);

			if(!$body)
			{
				if(typeof html === 'string')
				{
					$tmpContainer = $('<div />').css('visibility', 'hidden').appendTo(document.body).html(html);
					$body = $tmpContainer;
				}
				else
					$body = $(html);
			}

			if(!$body || !$body.jquery)
				return '';

			$.sceditor.dom.removeWhiteSpace($body[0]);
			bbcode = base.elementToBbcode($body);

			if($tmpContainer)
				$tmpContainer.remove();

			bbcode = parser.toBBCode(bbcode, true);

			if(base.opts.bbcodeTrim)
				bbcode = $.trim(bbcode);

			return bbcode;
		};

		/**
		 * Converts a HTML dom element to BBCode starting from
		 * the innermost element and working backwards
		 *
		 * @private
		 * @param HtmlElement	element		The element to convert to BBCode
		 * @param array		vChildren	Valid child tags allowed
		 * @return string BBCode
		 * @memberOf jQuery.plugins.bbcode.prototype
		 */
		base.elementToBbcode = function($element) {
			return (function toBBCode(node, vChildren) {
				var ret = '';
// TODO: Move to BBCode class?
				$.sceditor.dom.traverse(node, function(node) {
					var	$node        = $(node),
						curTag       = '',
						nodeType     = node.nodeType,
						tag          = node.nodeName.toLowerCase(),
						vChild       = validChildren[tag],
						firstChild   = node.firstChild,
						isValidChild = true;

					if(typeof vChildren === 'object')
					{
						isValidChild = $.inArray(tag, vChildren) > -1;

						// Emoticons should always be converted
						if($node.is('img') && $node.data('sceditor-emoticon'))
							isValidChild = true;

						// if this tag is one of the parents allowed children
						// then set this tags allowed children to whatever it allows,
						// otherwise set to what the parent allows
						if(!isValidChild)
							vChild = vChildren;
					}

					// 3 = text and 1 = element
					if(nodeType !== 3 && nodeType !== 1)
						return;

					if(nodeType === 1)
					{
						// skip ignored elements
						if($node.hasClass('sceditor-ignore'))
							return;

						// skip empty nlf elements (new lines automatically added after block level elements like quotes)
						if($node.hasClass('sceditor-nlf'))
						{
							if(!firstChild || (!$.sceditor.ie && node.childNodes.length === 1 && /br/i.test(firstChild.nodeName)))
							{
								return;
							}
						}

						// don't loop inside iframes
						if(tag !== 'iframe')
							curTag = toBBCode(node, vChild);

// TODO: isValidChild is no longer needed. Should use valid children bbcodes instead by
// creating BBCode tokens like the parser.
						if(isValidChild)
						{
							// code tags should skip most styles
							if(tag !== 'code')
							{
								// handle inline bbcodes
								curTag = handleStyles($node, curTag);
								curTag = handleTags($node, curTag);

								// handle blocklevel bbcodes
								curTag = handleStyles($node, curTag, true);
							}

							ret += handleTags($node, curTag, true);
						}
						else
							ret += curTag;
					}
					else if(node.wholeText && (!node.previousSibling || node.previousSibling.nodeType !== 3))
					{
// TODO:This should check for CSS white-space, should pass it in the function to reduce css lookups which are SLOW!
						if($node.parents('code').length === 0)
							ret += node.wholeText.replace(/ +/g, " ");
						else
							ret += node.wholeText;
					}
					else if(!node.wholeText)
						ret += node.nodeValue;
				}, false, true);

				return ret;
			}($element[0]));
		};

		/**
		 * Converts BBCode to HTML
		 *
		 * @param {String} text
		 * @param {Bool} asFragment
		 * @return {String} HTML
		 * @memberOf jQuery.plugins.bbcode.prototype
		 */
		base.signalToWysiwyg = function(text, asFragment) {
			var	parser = new $.sceditor.BBCodeParser(base.opts.parserOptions),
				html   = parser.toHTML(base.opts.bbcodeTrim ? $.trim(text) : text);

			return asFragment ? removeFirstLastDiv(html) : html;
		};

		/**
		 * Removes the first and last divs from the HTML.
		 *
		 * This is needed for pasting
		 * @param  {String} html
		 * @return {String}
		 * @private
		 */
		removeFirstLastDiv = function(html) {
			var	node, next, removeDiv,
				$output = $('<div />').hide().appendTo(document.body),
				output  = $output[0];

			removeDiv = function(node, isFirst) {
				// Don't remove divs that have styling
				if($.sceditor.dom.hasStyling(node))
					return;

				if($.sceditor.ie || (node.childNodes.length !== 1 || !$(node.firstChild).is('br')))
				{
					while((next = node.firstChild))
						output.insertBefore(next, node);
				}

				if(isFirst)
				{
					var lastChild = output.lastChild;

					if(node !== lastChild && $(lastChild).is('div') && node.nextSibling === lastChild)
						output.insertBefore(document.createElement('br'), node);
				}

				output.removeChild(node);
			};

			output.innerHTML = html.replace(/<\/div>\n/g, '</div>');

			if((node = output.firstChild) && $(node).is('div'))
				removeDiv(node, true);

			if((node = output.lastChild) && $(node).is('div'))
				removeDiv(node);

			output = output.innerHTML;
			$output.remove();

			return output;
		};
	};

	/**
	 * Removes any leading or trailing quotes ('")
	 *
	 * @return string
	 * @since v1.4.0
	 */
	$.sceditor.plugins.bbcode.stripQuotes = function(str) {
		return str ? str.replace(/\\(.)/g, '$1').replace(/^(["'])(.*?)\1$/, '$2') : str;
	};

	/**
	 * Formats a string replacing {0}, {1}, {2}, ect. with
	 * the params provided
	 *
	 * @param {String} str The string to format
	 * @param {string} args... The strings to replace
	 * @return {String}
	 * @since v1.4.0
	 */
	$.sceditor.plugins.bbcode.formatString = function() {
		var args = arguments;
		return args[0].replace(/\{(\d+)\}/g, function(str, p1) {
			return typeof args[p1-0+1] !== 'undefined' ?
				args[p1-0+1] :
				'{' + p1 + '}';
		});
	};

	/**
	 * Converts CSS RGB and hex shorthand into hex
	 *
	 * @since v1.4.0
	 * @param {String} color
	 * @return {String}
	 */
	var normaliseColour = $.sceditor.plugins.bbcode.normaliseColour = function(color) {
		var m, toHex;

		toHex = function (n) {
			n = parseInt(n, 10);

			if(isNaN(n))
				return '00';

			n = Math.max(0, Math.min(n, 255)).toString(16);

			return n.length < 2 ? '0' + n : n;
		};

		color = color || '#000';

		// rgb(n,n,n);
		if((m = color.match(/rgb\((\d{1,3}),\s*?(\d{1,3}),\s*?(\d{1,3})\)/i)))
			return '#' + toHex(m[1]) + toHex(m[2]-0) + toHex(m[3]-0);

		// expand shorthand
		if((m = color.match(/#([0-f])([0-f])([0-f])\s*?$/i)))
			return '#' + m[1] + m[1] + m[2] + m[2] + m[3] + m[3];

		return color;
	};

	$.sceditor.plugins.bbcode.bbcodes = {
		// START_COMMAND: Bold
		b: {
			tags: {
				b: null,
				strong: null
			},
			styles: {
				// 401 is for FF 3.5
				'font-weight': ['bold', 'bolder', '401', '700', '800', '900']
			},
			format: '[b]{0}[/b]',
			html: '<strong>{0}</strong>'
		},
		// END_COMMAND

		// START_COMMAND: Italic
		i: {
			tags: {
				i: null,
				em: null
			},
			styles: {
				'font-style': ['italic', 'oblique']
			},
			format: "[i]{0}[/i]",
			html: '<em>{0}</em>'
		},
		// END_COMMAND

		// START_COMMAND: Underline
		u: {
			tags: {
				u: null
			},
			styles: {
				'text-decoration': ['underline']
			},
			format: '[u]{0}[/u]',
			html: '<u>{0}</u>'
		},
		// END_COMMAND

		// START_COMMAND: Strikethrough
		s: {
			tags: {
				s: null,
				strike: null
			},
			styles: {
				'text-decoration': ['line-through']
			},
			format: '[s]{0}[/s]',
			html: '<s>{0}</s>'
		},
		// END_COMMAND

		// START_COMMAND: Subscript
		sub: {
			tags: {
				sub: null
			},
			format: '[sub]{0}[/sub]',
			html: '<sub>{0}</sub>'
		},
		// END_COMMAND

		// START_COMMAND: Superscript
		sup: {
			tags: {
				sup: null
			},
			format: '[sup]{0}[/sup]',
			html: '<sup>{0}</sup>'
		},
		// END_COMMAND

		// START_COMMAND: Font
		font: {
			tags: {
				font: {
					face: null
				}
			},
			styles: {
				'font-family': null
			},
			quoteType: $.sceditor.BBCodeParser.QuoteType.never,
			format: function(element, content) {
				var font;

				if(element[0].nodeName.toLowerCase() !== 'font' || !(font = element.attr('face')))
					font = element.css('font-family');

				return '[font=' + this.stripQuotes(font) + ']' + content + '[/font]';
			},
			html: function(token, attrs, content) {
				return '<font face="' + attrs.defaultattr + '">' + content + '</font>';
			}
		},
		// END_COMMAND

		// START_COMMAND: Size
		size: {
			tags: {
				font: {
					size: null
				}
			},
			styles: {
				'font-size': null
			},
			format: function(element, content) {
				var	fontSize = element.attr('size'),
					size     = 1;

				if(!fontSize)
					fontSize = element.css('fontSize');

				// Most browsers return px value but IE returns 1-7
				if(fontSize.indexOf('px') > -1) {
					// convert size to an int
					fontSize = fontSize.replace('px', '') - 0;

					if(fontSize > 12)
						size = 2;
					if(fontSize > 15)
						size = 3;
					if(fontSize > 17)
						size = 4;
					if(fontSize > 23)
						size = 5;
					if(fontSize > 31)
						size = 6;
					if(fontSize > 47)
						size = 7;
				}
				else
					size = fontSize;

				return '[size=' + size + ']' + content + '[/size]';
			},
			html: function(token, attrs, content) {
				return '<font size="' + attrs.defaultattr + '">' + content + '</font>';
			}
		},
		// END_COMMAND

		// START_COMMAND: Color
		color: {
			tags: {
				font: {
					color: null
				}
			},
			styles: {
				color: null
			},
			quoteType: $.sceditor.BBCodeParser.QuoteType.never,
			format: function($element, content) {
				var	color,
					element = $element[0];

				if(element.nodeName.toLowerCase() !== 'font' || !(color = $element.attr('color')))
					color = element.style.color || $element.css('color');

				return '[color=' + normaliseColour(color) + ']' + content + '[/color]';
			},
			html: function(token, attrs, content) {
				return '<font color="' + normaliseColour(attrs.defaultattr) + '">' + content + '</font>';
			}
		},
		// END_COMMAND

		// START_COMMAND: Lists
		ul: {
			tags: {
				ul: null
			},
			breakStart: true,
			isInline: false,
			skipLastLineBreak: true,
			format: '[ul]{0}[/ul]',
			html: '<ul>{0}</ul>'
		},
		list: {
			breakStart: true,
			isInline: false,
			skipLastLineBreak: true,
			html: '<ul>{0}</ul>'
		},
		ol: {
			tags: {
				ol: null
			},
			breakStart: true,
			isInline: false,
			skipLastLineBreak: true,
			format: '[ol]{0}[/ol]',
			html: '<ol>{0}</ol>'
		},
		li: {
			tags: {
				li: null
			},
			isInline: false,
			closedBy: ['/ul', '/ol', '/list', '*', 'li'],
			format: '[li]{0}[/li]',
			html: '<li>{0}</li>'
		},
		'*': {
			isInline: false,
			closedBy: ['/ul', '/ol', '/list', '*', 'li'],
			html: '<li>{0}</li>'
		},
		// END_COMMAND

		// START_COMMAND: Table
		table: {
			tags: {
				table: null
			},
			isInline: false,
			isHtmlInline: true,
			skipLastLineBreak: true,
			format: '[table]{0}[/table]',
			html: '<table>{0}</table>'
		},
		tr: {
			tags: {
				tr: null
			},
			isInline: false,
			skipLastLineBreak: true,
			format: '[tr]{0}[/tr]',
			html: '<tr>{0}</tr>'
		},
		th: {
			tags: {
				th: null
			},
			allowsEmpty: true,
			isInline: false,
			format: '[th]{0}[/th]',
			html: '<th>{0}</th>'
		},
		td: {
			tags: {
				td: null
			},
			allowsEmpty: true,
			isInline: false,
			format: '[td]{0}[/td]',
			html: '<td>{0}</td>'
		},
		// END_COMMAND

		// START_COMMAND: Emoticons
		emoticon: {
			allowsEmpty: true,
			tags: {
				img: {
					src: null,
					'data-sceditor-emoticon': null
				}
			},
			format: function(element, content) {
				return element.data('sceditor-emoticon') + content;
			},
			html: '{0}'
		},
		// END_COMMAND

		// START_COMMAND: Horizontal Rule
		hr: {
			tags: {
				hr: null
			},
			allowsEmpty: true,
			isSelfClosing: true,
			isInline: false,
			format: '[hr]{0}',
			html: '<hr />'
		},
		// END_COMMAND

		// START_COMMAND: Image
		img: {
			allowsEmpty: true,
			tags: {
				img: {
					src: null
				}
			},
			quoteType: $.sceditor.BBCodeParser.QuoteType.never,
			format: function($element, content) {
				var	w, h,
					attribs   = '',
					element   = $element[0],
					style     = function(name) {
						return element.style ? element.style[name] : null;
					};

				// check if this is an emoticon image
				if($element.attr('data-sceditor-emoticon'))
					return content;

				w = $element.attr('width') || style('width');
				h = $element.attr('height') || style('height');

				// only add width and height if one is specified
				if((element.complete && (w || h)) || (w && h))
					attribs = "=" + $element.width() + "x" + $element.height();

				return '[img' + attribs + ']' + $element.attr('src') + '[/img]';
			},
			html: function(token, attrs, content) {
				var	parts,
					attribs = '';

				// handle [img width=340 height=240]url[/img]
				if(typeof attrs.width !== "undefined")
					attribs += ' width="' + attrs.width + '"';
				if(typeof attrs.height !== "undefined")
					attribs += ' height="' + attrs.height + '"';

				// handle [img=340x240]url[/img]
				if(attrs.defaultattr) {
					parts = attrs.defaultattr.split(/x/i);

					attribs = ' width="' + parts[0] + '"' +
						' height="' + (parts.length === 2 ? parts[1] : parts[0]) + '"';
				}

				return '<img' + attribs + ' src="' + content + '" />';
			}
		},
		// END_COMMAND

		// START_COMMAND: URL
		url: {
			allowsEmpty: true,
			tags: {
				a: {
					href: null
				}
			},
			quoteType: $.sceditor.BBCodeParser.QuoteType.never,
			format: function(element, content) {
				var url = element.attr('href');

				// make sure this link is not an e-mail, if it is return e-mail BBCode
				if(url.substr(0, 7) === 'mailto:')
					return '[email="' + url.substr(7) + '"]' + content + '[/email]';

				return '[url=' + decodeURI(url) + ']' + content + '[/url]';
			},
			html: function(token, attrs, content) {
				return '<a href="' + encodeURI(attrs.defaultattr || content) + '">' + content + '</a>';
			}
		},
		// END_COMMAND

		// START_COMMAND: E-mail
		email: {
			quoteType: $.sceditor.BBCodeParser.QuoteType.never,
			html: function(token, attrs, content) {
				return '<a href="mailto:' + (attrs.defaultattr || content) + '">' + content + '</a>';
			}
		},
		// END_COMMAND

		// START_COMMAND: Quote
		quote: {
			tags: {
				blockquote: null
			},
			isInline: false,
			quoteType: $.sceditor.BBCodeParser.QuoteType.never,
			format: function(element, content) {
				var	author = '',
					$elm  = $(element),
					$cite = $elm.children('cite').first();

				if($cite.length === 1 || $elm.data('author'))
				{
					author = $cite.text() || $elm.data('author');

					$elm.data('author', author);
					$cite.remove();

					content	= this.elementToBbcode($(element));
					author  = '=' + author;

					$elm.prepend($cite);
				}

				return '[quote' + author + ']' + content + '[/quote]';
			},
			html: function(token, attrs, content) {
				if(attrs.defaultattr)
					content = '<cite>' + attrs.defaultattr + '</cite>' + content;

				return '<blockquote>' + content + '</blockquote>';
			}
		},
		// END_COMMAND

		// START_COMMAND: Code
		code: {
			tags: {
				code: null
			},
			isInline: false,
			allowedChildren: ['#', '#newline'],
			format: '[code]{0}[/code]',
			html: '<code>{0}</code>'
		},
		// END_COMMAND


		// START_COMMAND: Left
		left: {
			styles: {
				'text-align': ['left', '-webkit-left', '-moz-left', '-khtml-left']
			},
			isInline: false,
			format: '[left]{0}[/left]',
			html: '<div align="left">{0}</div>'
		},
		// END_COMMAND

		// START_COMMAND: Centre
		center: {
			styles: {
				'text-align': ['center', '-webkit-center', '-moz-center', '-khtml-center']
			},
			isInline: false,
			format: '[center]{0}[/center]',
			html: '<div align="center">{0}</div>'
		},
		// END_COMMAND

		// START_COMMAND: Right
		right: {
			styles: {
				'text-align': ['right', '-webkit-right', '-moz-right', '-khtml-right']
			},
			isInline: false,
			format: '[right]{0}[/right]',
			html: '<div align="right">{0}</div>'
		},
		// END_COMMAND

		// START_COMMAND: Justify
		justify: {
			styles: {
				'text-align': ['justify', '-webkit-justify', '-moz-justify', '-khtml-justify']
			},
			isInline: false,
			format: '[justify]{0}[/justify]',
			html: '<div align="justify">{0}</div>'
		},
		// END_COMMAND

		// START_COMMAND: YouTube
		youtube: {
			allowsEmpty: true,
			tags: {
				iframe: {
					'data-youtube-id': null
				}
			},
			format: function(element, content) {
				element = element.attr('data-youtube-id');

				return element ? '[youtube]' + element + '[/youtube]' : content;
			},
			html: '<iframe width="560" height="315" src="http://www.youtube.com/embed/{0}?wmode=opaque' +
				'" data-youtube-id="{0}" frameborder="0" allowfullscreen></iframe>'
		},
		// END_COMMAND


		// START_COMMAND: Rtl
		rtl: {
			styles: {
				'direction': ['rtl']
			},
			format: '[rtl]{0}[/rtl]',
			html: '<div style="direction: rtl">{0}</div>'
		},
		// END_COMMAND

		// START_COMMAND: Ltr
		ltr: {
			styles: {
				'direction': ['ltr']
			},
			format: '[ltr]{0}[/ltr]',
			html: '<div style="direction: ltr">{0}</div>'
		},
		// END_COMMAND

		// this is here so that commands above can be removed
		// without having to remove the , after the last one.
		// Needed for IE.
		ignore: {}
	};

	/**
	 * Static BBCode helper class
	 * @class command
	 * @name jQuery.plugins.bbcode.bbcode
	 */
	$.sceditor.plugins.bbcode.bbcode =
	/** @lends jQuery.plugins.bbcode.bbcode */
	{
		/**
		 * Gets a BBCode
		 *
		 * @param {String} name
		 * @return {Object|null}
		 * @since v1.3.5
		 */
		get: function(name) {
			return $.sceditor.plugins.bbcode.bbcodes[name] || null;
		},

		/**
		 * <p>Adds a BBCode to the parser or updates an existing
		 * BBCode if a BBCode with the specified name already exists.</p>
		 *
		 * @param {String} name
		 * @param {Object} bbcode
		 * @return {this|false} Returns false if name or bbcode is false
		 * @since v1.3.5
		 */
		set: function(name, bbcode) {
			if(!name || !bbcode)
				return false;

			// merge any existing command properties
			bbcode        = $.extend($.sceditor.plugins.bbcode.bbcodes[name] || {}, bbcode);
			bbcode.remove = function() { $.sceditor.plugins.bbcode.bbcode.remove(name); };

			$.sceditor.plugins.bbcode.bbcodes[name] = bbcode;

			return this;
		},

		/**
		 * Renames a BBCode
		 *
		 * This does not change the format or HTML handling, those must be
		 * changed manually.
		 *
		 * @param  {String} name    [description]
		 * @param  {String} newName [description]
		 * @return {this|false}
		 * @since v1.4.0
		 */
		rename: function(name, newName) {
			if (this.hasOwnProperty(name))
			{
				this[newName] = this[name];
				this.remove(name);
			}
			else
				return false;

			return this;
		},

		/**
		 * Removes a BBCode
		 *
		 * @param {String} name
		 * @return {this}
		 * @since v1.3.5
		 */
		remove: function(name) {
			if($.sceditor.plugins.bbcode.bbcodes[name])
				delete $.sceditor.plugins.bbcode.bbcodes[name];

			return this;
		}
	};

	/**
	 * Deprecated, use plugins: option instead. I.e.:
	 *
	 * $('textarea').sceditor({
	 *      plugins: 'bbcode'
	 * });
	 *
	 * @deprecated
	 */
	$.fn.sceditorBBCodePlugin = function (options) {
		options = options || {};

		if($.isPlainObject(options))
			options.plugins = (options.plugins ? options.plugins : '') + 'bbcode' ;

		return this.sceditor(options);
	};
})(jQuery, window, document);
