<script>
    import {writable} from 'svelte/store';

    export let text;

    let resultTokens = writable('');

    const tokenize = (text) => {
        const regexp = /(test)/g;
        // split into array, keep the separator
        const splits = text.split(regexp);
        $resultTokens = '';
        let t = [];
        for(let i = 0; i < splits.length; i++){
            if(splits[i].match(regexp)){
                console.log('here');
                $resultTokens += `<source-token type="ambiguity">${splits[i]}</source-token>`
            }else{
                $resultTokens += splits[i];
            }
        }
    }

    $: {
        tokenize(text);
    }

</script>

{@html $resultTokens}

<style>
    :global(span.ambiguity){
        background:red;
    }
</style>
