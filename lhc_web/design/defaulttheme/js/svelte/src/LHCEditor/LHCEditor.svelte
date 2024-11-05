<svelte:options customElement={{
		tag: 'lhc-editor',
		shadow: 'none'}}/>

<script>
    let textContent = ''

    import {tokenizeInputLHC,handlePaste} from './tokenizeInputLHC.js'
    import {LHCEditorStore} from './LHCEditorStore.js'
    import {writable} from 'svelte/store';
    import { onMount } from 'svelte';

    export let scope = 'chat';
    export let record_id = '0';
    export let debug = false;

    let hideSuggester = false;

    let	html = writable('');
    let history = LHCEditorStore({'index' : 0, 'current' : '', 'history' : ['']});
    let myInput;
    let ignoreChange = false;

    // History attributes
    let historyTimeout = null;
    let historyTimeoutDuration = 500;
    let pendingHistory = false;

    onMount(() => {
        return () => clearInterval(historyTimeout);
    });

    export function setContent(content) {
       html.set(content.trim());
    }

    function setCursorAtEnd(){
        var range, selection;
        range = document.createRange();//Create a range (a range is a like the selection but invisible)
        range.selectNodeContents(myInput);//Select the entire contents of the element with the range
        range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
        selection = window.getSelection();//get the selection object (allows you to change selection)
        selection.removeAllRanges();//remove any selections already made
        selection.addRange(range);//make the range you have just created the visible selection
    }

    export function setFocus() {
        setTimeout(() => {
            setCursorAtEnd();
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
        return text
            .replaceAll('<br>',"\n")
            .replaceAll('<b>',"[b]")
            .replaceAll('<i>',"[i]")
            .replaceAll('</i>',"[/i]")
            .replaceAll('</b>',"[/b]")
            .replaceAll('<u>',"[u]")
            .replaceAll('</u>',"[/u]");
    }

    function disable(e){
        var ret=true;
        let forceHistory = false;

        if(e.ctrlKey) {

            switch(e.keyCode) {

                case 90: // ctrl+z
                    hideSuggester = true;
                    syncHistory();
                    ret=false;
                    history.goPrev();
                    ignoreChange = true;
                    $html = $history['history'][$history['index']];
                    ignoreChange = false;
                    break;

                case 89: // ctrl+y
                    hideSuggester = true;
                    syncHistory();
                    ret=false;
                    ignoreChange = true;
                    history.goNext();
                    $html = $history['history'][$history['index']];
                    ignoreChange = false;
                    break;

                /*case 73: //ctrl+I or ctrl+i
                case 105: ret=false;
                    break;

                case 85: //ctrl+U or ctrl+u
                case 117: ret=false;
                    break;*/

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
                html.set('');
            }
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

        if (e && e.keyCode && (e.keyCode === 40 || e.keyCode === 37)) { // Keyboard up and left navigation
            hideSuggester = true;
            return;
        } else {
            hideSuggester = false;
        }

        var sel, range, pre_range, this_text, at_start, post_range, next_text, at_end;
        if (window.getSelection) {
            sel = window.getSelection();
            if (sel.getRangeAt && sel.rangeCount) {
                range = sel.getRangeAt(0);
                if (range.startOffset != range.endOffset) {
                    hideSuggester = true;
                }

                // Create a new range to deal with text before the cursor
                //pre_range = document.createRange();
                // Have this range select the entire contents of the editable div
                //pre_range.selectNodeContents(myInput);
                // Set the end point of this range to the start point of the cursor
                //pre_range.setEnd(range.startContainer, range.startOffset);
                // Fetch the contents of this range (text before the cursor)
                //this_text = pre_range.cloneContents();
                // If the text's length is 0, we're at the start of the div.
                //at_start = this_text.textContent.length === 0;

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

    function contentChanged(){
        html.set(myInput.innerHTML);
    }

    html.subscribe((value) => {
        if (ignoreChange === false) {
            pendingHistory = true;
            clearTimeout(historyTimeout);
            historyTimeout = setTimeout(() => {
                pendingHistory = false;
                history.addHistory(value.replaceAll('&nbsp;',' ').replace(/<suggester.*?>.*?<\/suggester>/g,''));
            },historyTimeoutDuration);
        } else {
            history.setCurrent(value.replaceAll('&nbsp;',' ').replace(/<suggester.*?>.*?<\/suggester>/g,''));
        }
    });

    history.subscribe((value) => {
       console.log(value);
    });

</script>

<div contenteditable="true"
     class={hideSuggester ? 'hide-suggester' : ''}
     placeholder="Enter text"
     on:paste={(e) => handlePaste(e, myInput, html)}
     on:keydown={disable}
     on:click={checkCursorPosition}
     on:keyup={checkCursorPosition}
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

<style>
    div.hide-suggester suggester{
        display: none;
    }
</style>