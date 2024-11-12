<svelte:options customElement={{
		tag: 'lhc-editor',
		shadow: 'none',
		extend: (customElementConstructor) => {
          return class extends customElementConstructor {
            constructor() {
              super();
              this.host = this; // or this.shadowRoot, or whatever you need
            }
          };
    }
}}/>

<script>
    let textContent = ''

    import {tokenizeInputLHC,handlePaste, insertFormatingLHC, insertContentLHC, saveSelection, setCursorAtEnd, replaceRangeLHC} from './tokenizeInputLHC.js'
    import {LHCEditorStore} from './LHCEditorStore.js'
    import {writable} from 'svelte/store';
    import { onMount } from 'svelte';
    export let host;
    export let scope = 'chat';
    export let record_id = '0';
    export let debug = false;
    export let warning_area = false;
    export let placeholder = "";
    export let readonly = null;
    export let whisper;
    export let enable_canned_suggester;
    export let disable_key_listeners;
    export let data_rows_default = 2;

    const conversionBBCodePairs = {
        '&amp;' : '&',
        '&lt;' : '<',
        '&gt;' : '>',
        '<br>' : "\n",
        '<b>' : "[b]",
        '<i>' : "[i]",
        '</i>' : "[/i]",
        '</b>' : "[/b]",
        '<u>' : "[u]",
        '</u>' : "[/u]",
        '</strike>' : "[/s]",
        '<strike>' : "[s]",
        '&nbsp;' : ' '
    };

    const conversionHTMLBBPairs = {
        "\r\n" : "",
        "\n" : "",
        '<br>' : "\n",
        '<b>':"[b]",
        '</b>' : "[/b]",
        '<i>' : "[i]",
        '</i>' :"[/i]",
        '<u>' : "[u]",
        '</u>' : "[/u]",
        '</strike>' : "[/s]",
        '<strike>' : "[s]",
        '&nbsp;' : ' ',
        '&amp;' : '&',
        '&lt;' : '<',
        '&gt;' : '>'
    };

    let rangeRestore = null;
    let hideSuggester = false;
    let ignoreMeta = false;
    let	html = writable('');
    let history = LHCEditorStore({'index' : 0, 'current' : '', 'history' : ['']});
    let myInput;
    let ignoreChange = false;

    // History attributes
    let historyTimeout = null;
    let historyTimeoutDuration = 500;
    let pendingHistory = false;
    let contenteditable = "true";

    onMount(() => {
        // Make is if chat is is loaded it's not in background
        let tab = document.getElementById('chat-tab-li-'+record_id);
        if (tab && tab.firstChild && tab.firstChild.classList.contains("active")) {
            setFocus(myInput);
        }

        if (enable_canned_suggester) {
            var cannedMessageSuggest = new LHCCannedMessageAutoSuggest({
                'textarea': myInput,
                'chat_id': record_id,
                'uppercase_enabled': confLH.auto_uppercase
            });
        }

        if (document.getElementById('fileupload-'+record_id) && window['file_upload_'+record_id]) {
            lhinst.addFileUpload(window['file_upload_'+record_id]);
        }

        ee.emitEvent('adminChatEditorLoaded', [record_id, myInput]);

        return () => {
            clearInterval(historyTimeout);
            cannedMessageSuggest.unbindEvents();
        };
    });

    export function getEditor(){
        return myInput;
    }

    export function setContent(content, options) {
       hideSuggester = true;

       if (options) {
           if (options['ignore_meta']) {
               ignoreMeta = true;
           }
           if (options['convert_bbcode']) {
               content = cleanupForEditor(content)
           }
       }

       html.set(content.trim());
    }

    function cleanupForEditor(content) {
        content = content.replaceAll("\r\n","\n");
        for (const [key, value] of Object.entries(conversionBBCodePairs)) {
            content = content.replaceAll(value, key);
        }
        return content;
    }

    export function insertContent(content,options) {
        hideSuggester = true;

        if (options) {
            if (options['convert_bbcode']) {
                content = cleanupForEditor(content);
            }

            if (options['new_line'] && myInput.innerHTML !== '') {
                content = "\n" + content;
            }
        }

        insertContentLHC(content, rangeRestore, myInput, html, options);
    }

    // Replaces range by provided content
    export function replaceRange(content) {
        rangeRestore = saveSelection();
        replaceRangeLHC(content, rangeRestore, myInput, html);
    }

    export function removeSuggester() {
        let elements = myInput.getElementsByTagName('suggester');
        for (const cell of elements) {
            myInput.removeChild(cell);
        }
    }

    export function setSuggester(content) {
        removeSuggester();

        if (content && !myInput.classList.contains('hide-suggester') && $html.substr($html.length - 4) != '<br>') { // Append auto completely if we are not on a new line
            const template = document.createElement('template');
            template.innerHTML = "<suggester style='color: #cecece; user-select: none' contentEditable=\"false\">" + content + "</suggester>";
            myInput.appendChild(template.content.firstChild);
        }
    }

    export function insertFormating(start,end) {
       if ([
           'b','i','u','s',
           'quote','youtube','html','code',
           'fs10','fs11','fs12','fs13','fs14','fs15','fs16',
       ].indexOf(start) !== -1 || start.indexOf('color=') === 0) {
           insertFormatingLHC(start, end, rangeRestore, myInput, html);
       }
    }

    export function getContent() {
        return cleanupForStore($history['current']).trim();
    }

    export function getContentLive() {
        return cleanupForStore($html.replace(/<suggester.*?>.*?<\/suggester>/g,'')).trim();
    }

    export function setFocus() {
        setTimeout(() => {
            setCursorAtEnd(myInput);
        },1);
    }

    function syncHistory(){
        if (pendingHistory === true) {
            clearTimeout(historyTimeout);
            history.addHistory($html.replace(/<suggester.*?>.*?<\/suggester>/g,''));
            pendingHistory = false;
        }
    }

    function cleanupForStore(text) {

        for (const [key, value] of Object.entries(conversionHTMLBBPairs)) {
            text = text.replaceAll(key, value);
        }

        return text;
    }

    function disable(e){
        var ret=true;
        let forceHistory = false;

        if (disable_key_listeners) {
            return;
        }


        if (e.altKey && (e.keyCode === 38 || e.keyCode === 40)) {
            ee.emitEvent('activateNextTab',[record_id,(e.keyCode === 38)]);
            return;
        }

        if (readonly) {
            e.preventDefault();
            e.stopPropagation();
            return;
        }

        // Inform NodeJS extension
        record_id && lhinst.operatorTypingCallback(record_id);

        if(e.ctrlKey) {


            switch(e.keyCode) {

                case 90: // ctrl+z
                    hideSuggester = true;
                    syncHistory();
                    ret=false;
                    history.goPrev();
                    ignoreChange = true;
                    html.set($history['history'][$history['index']]);
                    setFocus();
                    ignoreChange = false;
                    break;

                case 89: // ctrl+y
                    hideSuggester = true;
                    syncHistory();
                    ret=false;
                    ignoreChange = true;
                    history.goNext();
                    html.set($history['history'][$history['index']]);
                    setFocus();
                    ignoreChange = false;
                    break;

                case 66: //ctrl+B
                    insertFormating('b','b');
                    ret = false;
                    break;

                case 73: //ctrl+I or ctrl+i
                case 105:
                    insertFormating('i','i');
                    ret = false;
                    break;

                case 85: //ctrl+U or ctrl+u
                case 117:
                    insertFormating('u','u');
                    ret=false;
                    break;

            }
        } else if (e.shiftKey) {
            switch(e.keyCode) {
                case 13:
                        forceHistory = true;
                        // shift+enter. Not needed presently
                        // ret=false;
                        // addNewLine(myInput,html);
                    break;
            }
        } else if (e.keyCode === 13) { // Plain enter
            ret=false;
            syncHistory();
            let value = cleanupForStore($history['current']).trim();
            if (value) {
                ee.emitEvent('svelte_'+scope+'_'+record_id+'_msg', [value]);
                lhinst.addmsgadmin(record_id);
                ee.emitEvent('afterAdminMessageSent',[record_id]);
            }
        } else if (e.keyCode === 38) { // Up keyboard to edit previous
            syncHistory();
            lhinst.editPrevious(record_id);
        }

        if (forceHistory === true || e.keyCode === 32) { // Space. On space always record history
            historyTimeoutDuration = 0;
            hideSuggester = true;
        } else {
            historyTimeoutDuration = 500;
        }

        if (ret === false) {
            e.preventDefault();
            e.stopPropagation();
        }
    }

    function checkCursorPosition(e) {
        rangeRestore = saveSelection();

        if (e && e.keyCode && (e.keyCode === 40 || e.keyCode === 37)) { // Keyboard up and left navigation
            hideSuggester = true;
            return;
        } else {
            hideSuggester = false;
        }

        var sel, range, post_range, next_text, at_end;
        if (window.getSelection) {
            sel = window.getSelection();
            if (sel.getRangeAt && sel.rangeCount) {
                range = sel.getRangeAt(0);
                if (range.startOffset != range.endOffset) {
                    hideSuggester = true;
                }

                // Rinse and repeat for text after the cursor to determine if we're at the end.
                post_range = document.createRange();
                post_range.selectNodeContents(myInput);
                post_range.setStart(range.endContainer, range.endOffset);
                next_text = post_range.cloneContents();

                if (next_text.lastElementChild && next_text.lastElementChild.tagName == 'SUGGESTER'){
                    next_text.removeChild(next_text.lastElementChild);
                }

                at_end = next_text.textContent.length === 0;

                if (at_end === false) {
                    hideSuggester = true;
                }
            }
        }
    }

    export function isCursorAtEnd() {

        var sel, range, post_range, next_text, at_end;

        if (window.getSelection) {
            sel = window.getSelection();
            if (sel.getRangeAt && sel.rangeCount) {
                range = sel.getRangeAt(0);
                if (range.startOffset != range.endOffset) {
                    return false;
                }

                // Rinse and repeat for text after the cursor to determine if we're at the end.
                post_range = document.createRange();
                post_range.selectNodeContents(myInput);
                post_range.setStart(range.endContainer, range.endOffset);
                next_text = post_range.cloneContents();

                if (next_text.lastElementChild && next_text.lastElementChild.tagName == 'SUGGESTER'){
                    next_text.removeChild(next_text.lastElementChild);
                }

                at_end = next_text.textContent.length === 0;

                return true;
            }
        }

        return false;
    }


    export function getCaretPositionAndContent(options){
        if (window.getSelection) {
            var sel = window.getSelection();
            if (sel.rangeCount) {
                let range = sel.getRangeAt(0);
                //if ((options && options['ignore_parent']) || range.commonAncestorContainer.parentNode === myInput) { // If we want to allow return position if parent element is our editable node

                    let post_range = document.createRange();
                    post_range.selectNodeContents(myInput);
                    post_range.setStart(range.startContainer, 0);
                    post_range.setEnd(range.endContainer, range.endOffset);

                    let next_text = post_range.cloneContents();

                    if (next_text.lastElementChild && next_text.lastElementChild.tagName == 'SUGGESTER') {
                        next_text.removeChild(next_text.lastElementChild);
                    }

                    return {
                        'caret' : range.endOffset,
                        'content' : next_text.textContent.replace(/\u00a0/g, " "),
                        'full_content' : myInput.textContent.replace(/\u00a0/g, " ")
                    };
                //}
            }
        }

        return {
            'caret' : 0,
            'content' : "",
            'full_content': myInput.textContent.replace(/\u00a0/g, " ")
        }
    }

    function contentChanged(){
        html.set(myInput.innerHTML);
    }

    html.subscribe((value) => {

        let presentValue = value.replace(/<suggester.*?>.*?<\/suggester>/g,'');

        if (presentValue === '<br>') {
            presentValue = '';
            myInput.innerHTML = "";
        }

        if (presentValue === '') {
            host.removeAttribute('content_modified');
            if (ignoreMeta === false) {
                host.removeAttribute('subjects_ids');
                host.removeAttribute('canned_id');
            } else {
                ignoreMeta = false;
            }
        } else {
            host.setAttribute('content_modified', true);
        }

        if (ignoreChange === false) {
            pendingHistory = true;
            clearTimeout(historyTimeout);
            historyTimeout = setTimeout(() => {
                pendingHistory = false;
                history.addHistory(presentValue);
            },historyTimeoutDuration);
        } else {
            history.setCurrent(presentValue);
        }
    });

    /*history.subscribe((value) => {
       console.log(value);
    });*/

</script>

<div contenteditable="true"
     id={"editor-"+record_id}
     class={"lhc-editor form-control form-control-sm form-send-textarea form-group"+(whisper === "1" ? ' bg-light' : '')+(warning_area === true ? ' form-control-warning' : '')+(hideSuggester ? ' hide-suggester' : '')}
     placeholder={placeholder}
     on:paste={(e) => {
         if (readonly) {
            e.preventDefault();
            e.stopPropagation();
            return;
        }
        handlePaste(e, myInput, html);
     }}
     style:min-height={(data_rows_default * 27) + "px"}
     on:keydown={disable}
     on:click={checkCursorPosition}
     on:keyup={checkCursorPosition}
     on:focusout={checkCursorPosition}
     bind:innerHTML={$html}
     on:input={contentChanged}
     bind:this={myInput}
     bind:textContent
     use:tokenizeInputLHC={$html}></div>

{#if debug}
<div>{@html $html}</div>
<div>{$html}</div>
<pre>{textContent}</pre>
{/if}