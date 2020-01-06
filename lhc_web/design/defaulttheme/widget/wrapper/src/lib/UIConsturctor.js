import {helperFunctions} from './helperFunctions';
import {settings} from './settings.js';
import {domEventsHandler} from '../util/domEventsHandler';

export class UIConsturctor {
    constructor(elementId, style, attributes, tagname, documentRef) {
        var f = this;
        this.elementId = elementId || "";
        this.style = style || "";
        this.tagName = tagname || "div";
        this.tmpl = "";
        this.elmDom = null;
        this.elmDomDoc = documentRef || document;
        this.attributes = {};
        this.classNames = [];

        this.elementId && (this.attributes.id = this.elementId);

        attributes && Object.keys(attributes).forEach(function (attr) {
            f.attributes[attr] = attributes[attr]
        })
    }

    constructUI(a) {
        this.elmDomDoc =
            a || this.elmDomDoc;
        this.elmDom = helperFunctions.initElement(this.elmDomDoc, this.tagName, this.attributes, this.style, this.tmpl);
        this.elmDom.className += this.classNames.join(" ");
        return this.elmDom
    };

    restyle(attr, style) {
        style &&
        (-1 === style.indexOf("!important") && (style += " !important"), this.elmDom ? this.elmDom.style.cssText += ";" + attr + ":" + style : this.style += ";" + attr + ":" + style)
    };

    attachUserEventListener(a, c, d, k) {
        var e;
        if (e = d ? this.getElementById(d) : this.elmDom) d = a.split(" "), 1 < d.length ? d.forEach(function (a) {
            domEventsHandler.listen(e, a, c, a + k)
        }) : domEventsHandler.listen(e, a, c, k)
    };

    getElementById(a) {
        return this.elmDom ? this.elmDomDoc.getElementById(a) : null
    };

    hide() {
        this.restyle("display", "none !important");
    }

    show() {
        this.restyle("display", "block !important");
    }

    insertCssFile(style, reset) {
        var d = this.elmDomDoc.getElementsByTagName("head")[0],
            k = this.elmDomDoc.createDocumentFragment(),
            e = helperFunctions.initElement(this.elmDomDoc, "style", {type: "text/css"}),
            f = this.elmDomDoc.createTextNode(reset ? style : settings.ResetStyle + "" + style);
        k.appendChild(e);
        d.appendChild(k);
        e.styleSheet ? e.styleSheet.cssText = f.nodeValue : e.appendChild(f)
    }

    insertCssRemoteFile(attr) {
        var d = this.elmDomDoc.getElementsByTagName("head")[0],
            k = this.elmDomDoc.createDocumentFragment(),
            e = this.elmDomDoc.createElement('link');

        e.rel = "stylesheet";
        e.crossOrigin = "*";

        for (var b in attr) e[b] = attr[b];

        k.appendChild(e);
        d.appendChild(k);
    }

    insertJSFile(src, async){
        var d = this.elmDomDoc.getElementsByTagName("head")[0],
            k = this.elmDomDoc.createDocumentFragment(),
            e = this.elmDomDoc.createElement('script');

            e.type = 'text/javascript';
            if (typeof async === 'undefined' || async === true) {
                e.async = true;
            }

            e.crossOrigin = "*";
            e.src = src;

            k.appendChild(e);
            d.appendChild(k);
    }

    massRestyle(a) {
        for (var b in a) a.hasOwnProperty(b) && this.restyle(b, a[b])
    }
};

