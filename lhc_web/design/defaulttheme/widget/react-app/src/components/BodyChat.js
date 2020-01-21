import React, { Component } from 'react';
import { connect } from "react-redux";

import OnlineChat from './OnlineChat';
import StartChat from './StartChat';
import OfflineChat from './OfflineChat';
import HeaderChat from './HeaderChat';
import ProactiveInvitation from './ProactiveInvitation';
import { endChat } from "../actions/chatActions"
import { helperFunctions } from "../lib/helperFunctions";

@connect((store) => {
    return {
        chatwidget: store.chatwidget
    };
})

class BodyChat extends Component {

    constructor(props) {
        super(props);
        this.endChat = this.endChat.bind(this);
        this.popupChat = this.popupChat.bind(this);
        this.lastHeiht = 0;
    }

    endChat() {

        let surveyMode = false
        
        if (((this.props.chatwidget.hasIn(['chatLiveData','status_sub']) && (this.props.chatwidget.getIn(['chatLiveData','status_sub']) == 2 || this.props.chatwidget.getIn(['chatLiveData','status_sub']) == 5 || this.props.chatwidget.getIn(['chatLiveData','status_sub']) == 3)) || (this.props.chatwidget.getIn(['chatLiveData','status']) == 2)) && this.props.chatwidget.hasIn(['chat_ui','survey_id'])) {
            surveyMode = true;
        }

        if (this.props.chatwidget.get('initClose') === false && this.props.chatwidget.hasIn(['chat_ui','survey_id']) && surveyMode == false) {
            this.props.dispatch(endChat({'noCloseReason' : 'SHOW_SURVEY', 'noClose' : true, 'vid' : this.props.chatwidget.get('vid'), 'chat': {id : this.props.chatwidget.getIn(['chatData','id']), hash : this.props.chatwidget.getIn(['chatData','hash'])}}));
        } else {
            this.props.dispatch(endChat({'vid' : this.props.chatwidget.get('vid'), 'chat': {id : this.props.chatwidget.getIn(['chatData','id']), hash : this.props.chatwidget.getIn(['chatData','hash'])}}));
        }
    }

    popupChat() {

        var eventEmiter = null;

        if (window.parent && window.parent.$_LHC && window.parent.closed === false) {
            eventEmiter = window.parent.$_LHC.eventListener;
        } else if (window.opener && window.opener.$_LHC && window.opener.closed === false) {
            eventEmiter = window.opener.$_LHC.eventListener;
        }

        if (eventEmiter !== null) {
            eventEmiter.emitEvent('openPopup');
        } else {
            helperFunctions.sendMessageParent('openPopup', []);
        }
    }

    render() {

        if (this.props.chatwidget.get('loadedCore') === false) {
            return null;
        }

        if (this.props.chatwidget.getIn(['proactive','pending']) === true) {
            return <ProactiveInvitation />
        }

        var className = 'd-flex flex-column flex-grow-1 overflow-auto reset-container-margins';

        if (this.props.chatwidget.get('mode') == 'widget') {
            className = className + (this.props.chatwidget.get('isMobile') == true ? ' mobile-body' : ' desktop-body');
        } else if (this.props.chatwidget.get('mode') == 'embed') {
            className = className + (this.props.chatwidget.get('isMobile') == true ? ' mobile-embed-body' : ' desktop-embed-body');
        }

        if (this.props.chatwidget.get('isChatting') === true) {
            return (<React.Fragment>{this.props.chatwidget.get('mode') == 'widget' && <HeaderChat popupChat={this.popupChat} endChat={this.endChat} />}<div className={className}><OnlineChat endChat={this.endChat} /></div></React.Fragment>)
        } else if (this.props.chatwidget.get('isOnline') === true) {
            return (<React.Fragment>{this.props.chatwidget.get('mode') == 'widget' && <HeaderChat popupChat={this.popupChat} endChat={this.endChat} />}<div className={className}><StartChat /></div></React.Fragment>)
        } else {
            return (<React.Fragment>{this.props.chatwidget.get('mode') == 'widget' && <HeaderChat popupChat={this.popupChat} endChat={this.endChat} />}<div className={className}><OfflineChat /></div></React.Fragment>)
        }
    }
}

export default BodyChat;
