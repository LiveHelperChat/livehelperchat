import React, { Component } from 'react';
import { connect } from "react-redux";
import { updateMessageData } from "../actions/chatActions";

@connect((store) => {
    return {
        chatwidget: store.chatwidget
    };
})

class InlineIframe extends Component {

    constructor(props) {
        super(props);
    }

    getDocument(a) {
        return a.contentWindow ? a.contentWindow.document : a.contentDocument ? a.contentDocument : a.document ? a.document : null
    }

    insertCssRemoteFile(elmDomDoc, attr) {

        var elm = null;

        if (attr.id && attr.href && (elm = elmDomDoc.getElementById(attr.id)) !== null) {
            elm.href = attr.href
            return;
        }

        var d = elmDomDoc.getElementsByTagName("head")[0],
            k = elmDomDoc.createDocumentFragment(),
            e = elmDomDoc.createElement('link');

        e.rel = "stylesheet";
        e.crossOrigin = "*";

        for (var b in attr) e[b] = attr[b];

        k.appendChild(e);
        d.appendChild(k);
    }

    insertJSFile(elmDomDoc, src, async, attr) {
        var d = elmDomDoc.getElementsByTagName("head")[0],
            k = elmDomDoc.createDocumentFragment(),
            e = elmDomDoc.createElement('script');

        e.type = 'text/javascript';
        if (typeof async === 'undefined' || async === true) {
            e.async = true;
        }

        e.crossOrigin = "*";
        e.src = src;

        if (attr) {
            delete attr['src'];
            if (typeof attr['async'] !== 'undefined') {
                delete attr['async'];
            }
            Object.keys(attr).forEach(key => {
                e.setAttribute(key,attr[key]);
            })
        }

        k.appendChild(e);
        d.appendChild(k);
    }

    prepareIframe(iframe) {
        let documentFrame = this.getDocument(iframe);

        documentFrame.getElementsByTagName("head")[0].innerHTML = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />';

        var html = documentFrame.getElementsByTagName("html")[0];
        html.setAttribute("lang", 'en');
        html.setAttribute("dir", 'ltr');

        var nodeDoctype = document.implementation.createDocumentType(
            'html',
            '',
            ''
        );

        if (documentFrame.doctype) {
            documentFrame.replaceChild(nodeDoctype, documentFrame.doctype);
        } else {
            documentFrame.insertBefore(nodeDoctype, documentFrame.childNodes[0]);
        }

        if (this.props['data-css']) {
            JSON.parse(this.props['data-css']).forEach((item) => {
                this.insertCssRemoteFile(documentFrame, item);
            });
        }

        if (this.props['data-js']) {
            JSON.parse(this.props['data-js']).forEach((item) => {
                this.insertJSFile(documentFrame, item['src'], (item['async'] ? item['async'] : false), item);
            });
        }

        documentFrame.body.innerHTML = this.props['data-body'];

        return documentFrame;
    }

    componentDidMount() {
        const iframe = document.createElement("iframe");

        iframe.onload = () => {
            let documentFrame = this.prepareIframe(iframe);

            var closeActions = documentFrame.body.getElementsByClassName('lhc-iframe-close');

            for (let i = 0; i < closeActions.length; i++) {
                closeActions[i].addEventListener('click',() => {
                    updateMessageData({
                        'id' : this.props.chatwidget.getIn(['chatData','id']),
                        'hash' : this.props.chatwidget.getIn(['chatData','hash']),
                        'msg_id' : this.props['data-id']
                    }, {'action' : 'iframe_close'}).then(() => {
                        this.props.updateMessage(this.props['data-id']);
                    });
                });
            }

            let elmScroll = document.getElementById('messages-scroll');
            if (elmScroll !== null) {
                elmScroll.scrollTop = elmScroll.scrollHeight + 1000;
            }

            if (this.props['data-js-body']) {
                let js = documentFrame.createElement("script");
                js.textContent = this.props['data-js-body'];
                documentFrame.head.appendChild(js);
            }
        };

        iframe.onerror = function() {
            console.log("Something wrong happened");
        };

        if (this.props['data-style']) {
            iframe.style = this.props['data-style'];
        }

        if (this.props['data-iframe']) {
            let iframeOptions = JSON.parse(this.props['data-iframe']);

            // Remove any previous instances of same iframe if it's shown again
            if (iframeOptions['one_per_chat'] && iframeOptions['one_per_chat'] == true && iframeOptions['iframe-identifier']) {
                let sameIframes = document.getElementsByClassName(iframeOptions['iframe-identifier']);
                for (let i = 0; i < sameIframes.length; i++) {
                    if (sameIframes[i].parentNode) {
                        updateMessageData({
                            'id' : this.props.chatwidget.getIn(['chatData','id']),
                            'hash' : this.props.chatwidget.getIn(['chatData','hash']),
                            'msg_id' : sameIframes[i].getAttribute('data-msg-id')
                        }, {'action' : 'iframe_close'}).then(() => {
                            this.props.updateMessage(sameIframes[i].getAttribute('data-msg-id'));
                            //sameIframes[i].parentNode.removeChild(sameIframes[i]); // Not needed anymore becase default flow handles all taht
                        });
                    }
                }
            }

            if (iframeOptions['iframe-identifier']) {
                iframe.className = iframeOptions['iframe-identifier']; // Will be used to allow only one instance to be mounted
            }

            iframe.setAttribute('data-msg-id',this.props['data-id']);
        }

        document.getElementById("iframe-msg-"+this.props['data-id']).appendChild(iframe);
    }


    render() {

        const { t } = this.props;

        return (
            <div id={"iframe-msg-"+this.props['data-id']}></div>
        );
    }
}

export default InlineIframe;