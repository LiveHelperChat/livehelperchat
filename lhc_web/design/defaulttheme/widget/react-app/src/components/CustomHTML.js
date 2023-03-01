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

    }

    constructor(props) {
        super(props);
    }

    abstractClick(attrs, e) {
        JSON.parse(attrs['data-action']).forEach((item) => {
            if (item.action == 'add_css_class') {
                document.querySelector(item.target).classList.add(item.value);
            } else if (item.action == 'remove_css_class') {
                document.querySelector(item.target).classList.remove(item.value);
            } else if (item.action == 'chat_attr_global') {
                window.lhcChat[item.target] = item.value;
            } else if (item.action == 'set_state') {
                var params = {};
                params[item.target] = item.value;
                this.props.setStateParent(params);
            }
        });
    }

    render() {
        let html = this.props.chatwidget.getIn(['chat_ui',this.props.attr]);

        if (!this.props.has_new) {
            html = html.replace( /<newmessages>(.*)<\/newmessages>/gi, "");
        }

        return (
            <React.Fragment>
                {parse(html, {
                    replace: domNode => {
                        var cloneAttr = Object.assign({}, domNode.attribs);
                        if (domNode.attribs) {
                            if (domNode.name && domNode.name === 'button') {
                                return <button type="button" {...domNode.attribs} onClick={(e) => this.abstractClick(cloneAttr, e)}>{domToReact(domNode.children)}</button>
                            }
                        }
                    }})}
            </React.Fragment>
        );
    }
}

export default CustomHTML;