import {get} from 'svelte/store'

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
            .replaceAll("</b>","___BLDC___")
            .replaceAll("</i>","___ITAC___")
            .replaceAll("<i>","___ITA___")
            .replaceAll("</u>","___UNDC___")
            .replaceAll("<u>","___UND___");

        return txt.innerText.replaceAll('___BR___','<br>')
            .replaceAll("___BLD___","<b>")
            .replaceAll("___BLDC___","</b>")
            .replaceAll("___ITAC___","</i>")
            .replaceAll("___ITA___","<i>")
            .replaceAll("___UNDC___","</u>")
            .replaceAll("___UND___","<u>");
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
                if (!target.classList.contains('hide-suggester') && val.trim() !== '' && val.substr(val.length - 4) != '<br>') { // Append auto completely if we are not on a new line
                    const template = document.createElement('template');
                    template.innerHTML = "<suggester style='color: #cecece; margin-left:3px; user-select: none' contentEditable=\"false\">Suggested</suggester>";
                    target.appendChild(template.content.firstChild);
                }
            }
        }
    }
}

export function handlePaste(e, editor, store) {
    var clipboardData, pastedData;

    // Stop data actually being pasted into div
    e.stopPropagation();
    e.preventDefault();

    // Get pasted data via clipboard API
    clipboardData = e.clipboardData || window.clipboardData;
    pastedData = clipboardData.getData('text/plain');

    insertText(pastedData);
    store.set(editor.innerHTML);
}

function insertText(pastedData) {
    pastedData = pastedData.replaceAll("\r\n", "\n");
    var linesInsert = pastedData.split("\n");
    var sel, range;
    if (window.getSelection) {
        sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
            range = sel.getRangeAt(0);
            range.deleteContents();
            for (var i = linesInsert.length - 1; i >= 0; i--) {
                if (i < linesInsert.length - 1) {
                    range.insertNode(document.createElement("br"));
                }
                range.insertNode(document.createTextNode(linesInsert[i]));
            }
            range.collapse(false);
        }
    } else if (document.selection && document.selection.createRange) {
        document.selection.createRange().text = pastedData;
    }
}


/*export function addNewLine(target, store) {

    /*var range,selection;

    store.set(get(store) + "<br><br>");

    range = document.createRange();//Create a range (a range is a like the selection but invisible)
    range.selectNodeContents(target);//Select the entire contents of the element with the range
    range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
    selection = window.getSelection();//get the selection object (allows you to change selection)
    selection.removeAllRanges();//remove any selections already made
    selection.addRange(range);//make the range you have just created the visible selection*/

    /*text = "asdasd";

    var sel, range;
    if (window.getSelection) {
        sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
            range = sel.getRangeAt(0);
            range.deleteContents();
            range.insertNode( document.createTextNode(text) );
        }
    } else if (document.selection && document.selection.createRange) {
        document.selection.createRange().text = text;
    }
}*/