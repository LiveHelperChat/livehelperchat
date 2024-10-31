<svelte:options customElement={{
		tag: 'lhc-editor',
		shadow: 'none'}}/>

<script>
    let textContent = ''

    import {tokenizeInputLHC} from './tokenizeInputLHC.js'
    import {writable} from 'svelte/store';
    import {setEndOfContenteditable} from './cursorManager.js';

    let	html = writable('');
    let myInput;

    function disable(e){
        var ret=true;
        if(e.ctrlKey) {
            /*switch(e.keyCode) {
                case 66: //ctrl+B or ctrl+b
                case 98: ret=false;
                    break;
                case 73: //ctrl+I or ctrl+i
                case 105: ret=false;
                    break;
                case 85: //ctrl+U or ctrl+u
                case 117: ret=false;
                    break;
            }*/
        } else if (e.shiftKey) {
            switch(e.keyCode) {
                case 13: //shift+enter
                        ret=false;
                        html.set($html + "<br><br>");
                    break;
            }
        } else if (e.keyCode === 13) {
            ret=false;
        }

        if (ret === false) {
            e.preventDefault();
            e.stopPropagation();
        }
    } //

    function contentChanged(){
        html.set(myInput.innerHTML);
    }

    setTimeout(function(){
        //$html = "updated";
    },1500);

    html.subscribe((value) => {
        console.log(value);
    });


    function handlePaste(e) {
        var clipboardData, pastedData;

        // Stop data actually being pasted into div
        e.stopPropagation();
        e.preventDefault();

        // Get pasted data via clipboard API
        clipboardData = e.clipboardData || window.clipboardData;
        pastedData = clipboardData.getData('Text');

        html.set(pastedData.replaceAll("\r\n", "<br>").replaceAll("\n","<br>"));
    }

</script>

<!--plaintext-only-->

<div contenteditable="true"
     placeholder="Enter text"
     on:paste={handlePaste}
     on:keydown={disable}
     bind:innerHTML={$html}
     on:input={contentChanged}
     bind:this={myInput}
     bind:textContent
     use:tokenizeInputLHC={$html}
     ></div>





<!--use:tokenizeInput={$html}
use:setEndOfContenteditable={$html}
-->

<div>{@html $html}</div>
<div>{$html}</div>

<pre>{textContent}</pre>

<style>
    div {
        border: 1px solid red;
        margin-bottom: 10px;
    }
</style>