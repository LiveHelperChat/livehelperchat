import React, { PureComponent } from 'react';
import parse, { domToReact } from 'html-react-parser';
import { connect } from "react-redux";
import {voteAction, transferToHumanAction} from "../actions/chatActions"
import { helperFunctions } from "../lib/helperFunctions";
import axios from "axios";

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
        this.checkSwitchButtom = this.checkSwitchButtom.bind(this);
    }

    abstractClick(attrs) {
        if (attrs.onclick) {
            if (attrs.onclick == 'lhinst.voteAction($(this))') {
                voteAction({id : this.props.chat.get('id'), hash: this.props.chat.get('hash'), type : attrs['data-id']}).then((response) => {
                    this.props.updateStatus()
                })
            } else if (attrs.onclick == 'notificationsLHC.sendNotification()') {
                helperFunctions.sendMessageParent('subscribeEvent',[{'pk' : this.props.chat_ui.get('notifications_pk')}]);
            } else if (attrs.onclick.indexOf('lhinst.transferToHuman') !== -1) {
                transferToHumanAction({id : this.props.chat.get('id'), hash: this.props.chat.get('hash')}).then((response) => {
                    this.props.updateStatus()
                });
            } else {
                helperFunctions.emitEvent('StatusClick',[attrs, this.props.dispatch]);
            }
        }
    }

    checkSwitchButtom(){
        if (this.props.chat_ui.has('switch_to_human') && this.props.vtm && this.props.vtm >= this.props.chat_ui.get('switch_to_human')) {
            axios.get(window.lhcChat['base_url'] + "restapi/isonlinechat/" + this.props.chat.get('id')+ '?exclude_bot=true').then((response) => {
                if (response.data.isonline){
                    var transferButton = document.getElementById('transfer-to-human-btn');
                    if (transferButton !== null) {
                        transferButton.classList.remove('hide');
                    }
                }
            });
        }
    }

    componentDidMount() {
        this.checkSwitchButtom();
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        this.checkSwitchButtom();
    }

    render() {
         return parse(this.props.status, {
            replace: domNode => {
                if (domNode.attribs && domNode.attribs.onclick) {
                    if (domNode.name && (domNode.name == 'i' || domNode.name == 'a')) {

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
