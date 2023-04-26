import React, { Component } from 'react';
import { connect } from "react-redux";
import { helperFunctions } from "../lib/helperFunctions";
import parse, { domToReact } from 'html-react-parser';

@connect((store) => {
    return {
        chatwidget: store.chatwidget
    };
})

class CustomHTML extends Component {

    state = {
        preg_match_rules : []
    }

    constructor(props) {
        super(props);
        this.listenerAdded = false;
    }

    handleParentMessage(e, item) {
        if (e.data.event == item.target) {
            let value = [];
            value[e['data']['event']] = e['data']['value'];
            this.setState(value);
        }
    }

    abstractClick(attrs, e) {
        JSON.parse(attrs['data-action']).forEach((item) => {
            if (item.action == 'add_css_class') {
                let elm = document.querySelector(item.target);
                elm && elm.classList.add(item.value);
            } else if (item.action == 'remove_css_class') {
                let elm = document.querySelector(item.target);
                elm && elm.classList.remove(item.value);
            } else if (item.action == 'chat_attr_global') {
                window.lhcChat[item.target] = item.value;
            } else if (item.action == 'set_state') {
                var params = {};
                params[item.target] = item.value;
                this.props.setStateParent(params);
            } else if (item.action == 'post_message') {
                document.getElementById(item.target).contentWindow.postMessage(item.value,'*');
            } else if (item.action == 'listen_post_message') {
                if (window.addEventListener) {
                    // FF
                    window.addEventListener("message", (evt) => {this.handleParentMessage(evt,item)}, false);
                } else if ( window.attachEvent ) {
                    // IE
                    window.attachEvent("onmessage", (evt) => {this.handleParentMessage(evt,item)});
                } else if ( document.attachEvent ) {
                    // IE
                    document.attachEvent("onmessage", (evt) => {this.handleParentMessage(evt,item)});
                };
            }
        });
    }

    render() {
        let html = this.props.chatwidget.getIn(['chat_ui',this.props.attr]);

        if (!this.props.has_new) {
            html = html.replace( /<newmessages>(.*)<\/newmessages>/gi, "");
        }
        
        this.state.preg_match_rules.forEach(rule => {
            html = html.replace(rule.search, rule.replace, "");
        });

        return (
            <React.Fragment>
                {parse(html, {
                    replace: domNode => {
                        var cloneAttr = Object.assign({}, domNode.attribs);
                        if (domNode.attribs) {
                            if (domNode.name && domNode.name === 'button') {
                                return <button type="button" {...domNode.attribs} onClick={(e) => this.abstractClick(cloneAttr, e)}>{domToReact(domNode.children)}</button>
                            } else if (domNode.name && domNode.name === 'lhcaction') {
                                if (this.listenerAdded == false) {
                                    this.abstractClick(cloneAttr, null);
                                    this.listenerAdded = true;
                                }
                                return "";
                            }
                        }
                    }})}
            </React.Fragment>
        );
    }
}

export default CustomHTML;