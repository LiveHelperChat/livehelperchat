
export function tokenizeInputLHC(target, val){

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

    const convertToPlainText = (val) => {
        var txt = document.createElement("div");
        txt.innerHTML =
            val.replaceAll('<br>','___BR___')
            .replaceAll("\r\n","___BR___")
            .replaceAll("\n","___BR___")
            .replaceAll("<b>","___BLD___")
            .replaceAll("</b>","___BLDC___");
        return txt.innerText.replaceAll('___BR___','<br>').replaceAll("___BLD___","<b>").replaceAll("___BLDC___","</b>");
    }

   /* const createSourceTokens = (text) => {
        // when backspacing all input, for some reason <br> is appended
        if (text === '<br>') {
            target.innerHTML = '';
            return;
        }
        // remove <span> tags from prior
        text = stripHTML(text);
        target.innerHTML = '';
        new SourceToken({target, props: {text}})
    }*/

    return {
        update(val) {
            val = val.replaceAll('&nbsp;',' ').replace(/<suggester.*?>.*?<\/suggester>/g,'');
            let valueOriginal = val;
            val = decodeHTMLCharacters(val);
            val = convertToPlainText(val);

            if (JSON.stringify(val.replace(/\s+/g, "")) != JSON.stringify(valueOriginal.replace(/\s+/g, ""))) {
                target.innerHTML = val;
            } else {
                let elements = target.getElementsByTagName('suggester');
                for (const cell of elements) {
                    target.removeChild(cell);
                }
                if (val.substr(val.length - 4) != '<br>'){
                    const template = document.createElement('template');
                    template.innerHTML = "<suggester style='color: #cecece; padding-left:3px; user-select: none' contentEditable=\"false\">Suggested</suggester>";
                    target.appendChild(template.content.firstChild);
                }
            }
        }
    }
}

