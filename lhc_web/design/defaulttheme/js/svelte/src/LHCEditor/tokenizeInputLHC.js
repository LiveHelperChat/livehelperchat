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
        txt.innerHTML = val
            .replaceAll('&gt;','___GT___')
            .replaceAll('&lt;','___LT___');
        return txt.value.replaceAll('___GT___','&gt;').replaceAll('___LT___','&lt;');
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
            .replaceAll("<u>","___UND___")
            .replaceAll("<strike>","___STRIKE___")
            .replaceAll("</strike>","___STRIKEC___")
            .replaceAll('&gt;','___GT___')
            .replaceAll('&lt;','___LT___');

        return txt.innerText.replaceAll('___BR___','<br>')
            .replaceAll("___BLD___","<b>")
            .replaceAll("___BLDC___","</b>")
            .replaceAll("___ITAC___","</i>")
            .replaceAll("___ITA___","<i>")
            .replaceAll("___UNDC___","</u>")
            .replaceAll("___UND___","<u>")
            .replaceAll("___STRIKE___","<strike>")
            .replaceAll("___STRIKEC___","</strike>")
            .replaceAll('___GT___','&gt;')
            .replaceAll('___LT___','&lt;');
    }

    return {
        update(val) {
            val = val
                .replaceAll('&nbsp;',' ')
                .replaceAll('&amp;','&')
                .replace(/<suggester.*?>.*?<\/suggester>/g,'');
            let valueOriginal = val;

            val = decodeHTMLCharacters(val);
            val = convertToPlainText(val);
            val = val.replace(/\u00a0/g, " "); //NBSP internal character

            if (val === "<br>") {
                target.innerHTML = "";
            } else if (JSON.stringify(val.replace(/\s+/g, "")) != JSON.stringify(valueOriginal.replace(/\s+/g, ""))) {
                target.innerHTML = val;
            } else {
                /*let elements = target.getElementsByTagName('suggester');
                for (const cell of elements) {
                    target.removeChild(cell);
                }
                if (!target.classList.contains('hide-suggester') && val.trim() !== '' && val.substr(val.length - 4) != '<br>') { // Append auto completely if we are not on a new line
                    const template = document.createElement('template');
                    template.innerHTML = "<suggester style='color: #cecece; margin-left:3px; user-select: none' contentEditable=\"false\">Suggested</suggester>";
                    target.appendChild(template.content.firstChild);
                }*/
            }
        }
    }
}

export function insertFormatingLHC(formating, formatingend, range, myInput, html) {
    restoreSelection(range, myInput);
    let commandsSupported = {'b' : 'bold','i' : 'italic','u' : 'underline','s' : 'strikethrough'};
    if (commandsSupported[formating]) {
        document.execCommand(commandsSupported[formating], false, null);
    } else {
        insertTextWrap('[' + formating +']', '[/' + formatingend+']');
    }
    html.set(myInput.innerHTML);

}

function insertTextWrap(wrapStart,wrapEnd) {
    var sel, range;
    if (window.getSelection) {
        sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
            range = sel.getRangeAt(0);
            let conentSelected = range.cloneContents();
            range.deleteContents();
            range.insertNode(document.createTextNode(wrapEnd));
            range.insertNode(conentSelected);
            range.insertNode(document.createTextNode(wrapStart));
            range.collapse(false);
        }
    }
}

export function setCursorAtEnd(myInput){
    var range, selection;
    range = document.createRange();//Create a range (a range is a like the selection but invisible)
    range.selectNodeContents(myInput);//Select the entire contents of the element with the range
    range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
    selection = window.getSelection();//get the selection object (allows you to change selection)
    selection.removeAllRanges();//remove any selections already made
    selection.addRange(range);//make the range you have just created the visible selection
}

export function saveSelection() {
    if (window.getSelection) {
        let sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
            return sel.getRangeAt(0);
        }
    } else if (document.selection && document.selection.createRange) {
        return document.selection.createRange();
    }
    return null;
}

export function replaceRangeLHC(content, rangeRestore, myInput, html) {
    if (rangeRestore) {
        if (window.getSelection) {

            rangeRestore.setStart(rangeRestore.startContainer, 0);
            rangeRestore.setEnd(rangeRestore.endContainer, rangeRestore.endOffset);
            rangeRestore.deleteContents();

            let fragment = rangeRestore.createContextualFragment(content);
            rangeRestore.insertNode(fragment);
            rangeRestore.collapse(false);

            let sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(rangeRestore);

        } else if (document.selection && range.select) {
            range.select();
        }
    }
}

function restoreSelection(range, myInput, options) {
    if (range) {
        if (window.getSelection) {
            let sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
            if (options) {
                if (options['replace_from']) {
                    range.setStart(range.startContainer, 0);
                    range.setEnd(range.endContainer, range.endOffset);
                    let content = range.toString();
                    let index = content.lastIndexOf('#');
                    if (index !== -1) {
                        range.setStart(range.startContainer, content.lastIndexOf('#'));
                        range.deleteContents();
                    }
                }
            }
        } else if (document.selection && range.select) {
            range.select();
        }
    } else {
        setCursorAtEnd(myInput);
    }
}

export function insertContentLHC(string, range, myInput, html, options) {
    restoreSelection(range, myInput, options);
    insertTextHTML(string);
    html.set(myInput.innerHTML);
}


function insertTextHTML(pastedData) {
    var sel, range;
    if (window.getSelection) {
        sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
            range = sel.getRangeAt(0);
            let fragment = range.createContextualFragment(pastedData);
            range.insertNode(fragment);
            range.collapse(false);
        }
    } else if (document.selection && document.selection.createRange) {
        document.selection.createRange().text = pastedData;
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