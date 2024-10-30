import SourceToken from './SourceToken.svelte';

export function tokenizeInput(target, val){

    // removes the <span> elements that we are embedding
    const stripHTML = (val) => {
        // remove first <span>s
        let stripped = val.replace(/<source-token.*?>/g, '');
        // remove end <span>s
        stripped = stripped.replace(/<\/source-token>/g, '');
        return stripped;
    }

    // converts HTML entities into their counterpart, preserving HTML tags
    // i.e. &nbsp; turns into a space
    // 	https://stackoverflow.com/questions/7394748/whats-the-right-way-to-decode-a-string-that-has-special-html-entities-in-it/7394787#7394787
    const decodeHTMLCharacters = (val) => {
        var txt = document.createElement("textarea");
        txt.innerHTML = val;
        return txt.value;
    }

    const createSourceTokens = (text) => {
        // when backspacing all input, for some reason <br> is appended
        if(text === '<br>'){
            target.innerHTML = '';
            return;
        }
        // remove <span> tags from prior
        text = stripHTML(text);
        target.innerHTML = '';
        new SourceToken({target, props: {text}})
    }

    return {
        update(val) {
            val = decodeHTMLCharacters(val);
            createSourceTokens(val);
        }
    }
}

