import {UIConsturctor} from './UIConsturctor';
import {helperFunctions} from './helperFunctions';

export class UIConstructorIframe extends UIConsturctor {

    constructor(elementId, style, attributes, tagname, documentRef) {
        super(elementId, style, attributes, tagname, documentRef);
        this.bodyId = '';
    }

    constructUIIframe(style, dir, cl, header, disableViewPort) {

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

        try {
            this.elmDomDoc.getElementsByTagName("head")[0].innerHTML = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'+(disableViewPort !== true ? '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />' : '')+header;

            if (this.bodyId != '') {
                this.elmDomDoc.body.id = this.bodyId;
            }

            var html = this.elmDomDoc.getElementsByTagName("html")[0];
            html.setAttribute("lang", cl);
            html.setAttribute("dir", dir);

            var nodeDoctype = document.implementation.createDocumentType(
                'html',
                '',
                ''
            );

            if (this.elmDomDoc.doctype) {
                this.elmDomDoc.replaceChild(nodeDoctype, this.elmDomDoc.doctype);
            } else {
                this.elmDomDoc.insertBefore(nodeDoctype, this.elmDomDoc.childNodes[0]);
            }

        } catch (e) {
            console.log(e);
        }

        this.insertCssFile(style);
        this.insertContent();
    };

    insertContent () {
        this.elmDomDoc.body.innerHTML = this.tmpl
    };
};

