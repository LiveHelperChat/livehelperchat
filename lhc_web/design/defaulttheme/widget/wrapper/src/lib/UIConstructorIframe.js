import {UIConsturctor} from './UIConsturctor';
import {helperFunctions} from './helperFunctions';

export class UIConstructorIframe extends UIConsturctor {

    constructor(elementId, style, attributes, tagname, documentRef) {
        super(elementId, style, attributes, tagname, documentRef);
        this.bodyId = '';
    }

    constructUIIframe(style, dir, cl, header) {

        if (typeof dir === 'undefined'){
            dir = 'ltr';
        }

        if (typeof cl === 'undefined'){
            cl = 'en';
        }

        if (typeof header === 'undefined'){
            header = '';
        }

        this.elmDomDoc = helperFunctions.getDocument(this.elmDom);
        if (this.elmDomDoc === null) return null;
        this.elmDomDoc.open();
        this.elmDomDoc.writeln('<!DOCTYPE html><html dir="'+dir+'" lang="'+cl+'"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />'+header+'</head><body'+(this.bodyId != '' ? ' id="'+this.bodyId+'" ' : '')+'></body></html>');
        this.elmDomDoc.close();
        this.insertCssFile(style);
        this.insertContent();
    };

    insertContent () {
        this.elmDomDoc.body.innerHTML = this.tmpl
    };
};

