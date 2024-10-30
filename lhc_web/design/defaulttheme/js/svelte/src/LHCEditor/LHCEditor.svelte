<svelte:options customElement={{
		tag: 'lhc-editor',
		shadow: 'none'}}/>

<script>
    let textContent = ''

    import {tokenizeInput} from './tokenizeInput.js'
    import {tokenizeInputLHC} from './tokenizeInputLHC.js'
    import {writable} from 'svelte/store';
    import {setEndOfContenteditable} from './cursorManager.js';

    let	html = writable('');
    let myInput;

    function disable(e){
        var ret=true;
        if(e.ctrlKey){
            switch(e.keyCode){
                case 66: //ctrl+B or ctrl+b
                case 98: ret=false;
                    break;
                case 73: //ctrl+I or ctrl+i
                case 105: ret=false;
                    break;
                case 85: //ctrl+U or ctrl+u
                case 117: ret=false;
                    break;
            }
        }
        if (ret === false) {
            e.preventDefault();
            e.stopPropagation();
        }
    } //

    function contentChanged(){
        /*console.log('changed');
        html.set(myInput.innerHTML);*/
    }

    setTimeout(function(){
        //$html = "updated";
    },1500);

</script>

<!--plaintext-only-->
<div contenteditable="true"
     placeholder="Enter text"
     on:keydown={disable}
     bind:innerHTML={$html}
     on:input={contentChanged}
     bind:this={myInput}
     bind:textContent
     use:tokenizeInputLHC={$html}></div>

<!--use:tokenizeInput={$html}
use:setEndOfContenteditable={$html}-->

<div>{@html $html}</div>
<div>{$html}</div>

<pre>{textContent}</pre>

<style>
    div {
        border: 1px solid red;
        margin-bottom: 10px;
    }
</style>