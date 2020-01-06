import React, { PureComponent } from 'react';
import parse, { domToReact } from 'html-react-parser';
import { connect } from "react-redux";
import {voteAction} from "../actions/chatActions"
import { helperFunctions } from "../lib/helperFunctions";

@connect((store) => {
    return {
        chat: store.chatwidget.get('chatData'),
        chat_ui : store.chatwidget.get('chat_ui')
    };
})

class ChatStatus extends PureComponent {

    constructor(props) {
        super(props);
        this.abstractClick = this.abstractClick.bind(this);
    }

    abstractClick(attrs) {
        if (attrs.onclick) {
            if (attrs.onclick == 'lhinst.voteAction($(this))') {
                voteAction({id : this.props.chat.get('id'), hash: this.props.chat.get('hash'), type : attrs['data-id']}).then((response) => {
                    this.props.updateStatus()
                })
            } else if (attrs.onclick == 'notificationsLHC.sendNotification()') {
                helperFunctions.sendMessageParent('subscribeEvent',[{'pk' : this.props.chat_ui.get('notifications_pk')}]);
            }
        }
    }

    render() {
         return parse(this.props.status, {
            replace: domNode => {
                if (domNode.attribs && domNode.attribs.onclick) {
                    if (domNode.name && domNode.name == 'i') {

                        var cloneAttr = Object.assign({}, domNode.attribs);

                        if (domNode.attribs.class) {
                            domNode.attribs.className = domNode.attribs.class;
                            delete domNode.attribs.class;
                        }

                        if (domNode.attribs.onclick) {
                            delete domNode.attribs.onclick;
                        }

                        let result = <i {...domNode.attribs} onClick={(e) => this.abstractClick(cloneAttr)} >{domToReact(domNode.children)}</i>;

                        return result;
                    }
                }
            }
        });
    }
}

export default ChatStatus;
