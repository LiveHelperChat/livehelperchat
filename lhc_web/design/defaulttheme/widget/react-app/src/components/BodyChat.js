import React, { Component } from 'react';
import { connect } from "react-redux";

import HeaderChat from './HeaderChat';
import StartChat from './StartChat';
import OnlineChat from './OnlineChat';

import { endChat } from "../actions/chatActions"
import { helperFunctions } from "../lib/helperFunctions";
import { Suspense, lazy } from 'react';

import { STATUS_CLOSED_CHAT, STATUS_BOT_CHAT, STATUS_SUB_SURVEY_SHOW, STATUS_SUB_USER_CLOSED_CHAT, STATUS_SUB_CONTACT_FORM } from "../constants/chat-status";

const OfflineChat = React.lazy(() => import('./OfflineChat'));
const ProactiveInvitation = React.lazy(() => import('./ProactiveInvitation'));

@connect((store) => {
    return {
        chatwidget: store.chatwidget
    };
})

class BodyChat extends Component {

    state = {

    };

    constructor(props) {
        super(props);
        this.endChat = this.endChat.bind(this);
        this.popupChat = this.popupChat.bind(this);
        this.cancelClose = this.cancelClose.bind(this);
        this.setProfile = this.setProfile.bind(this);
        this.setMessages = this.setMessages.bind(this);
        this.setHideMessageField = this.setHideMessageField.bind(this);
        this.lastHeiht = 0;

        this.profileHTML = null;
        this.messagesHTML = null;
        this.hideMessageField = false;
    }

    cancelClose() {
        this.props.dispatch({'type' : 'UI_STATE', 'data' : {'attr': 'confirm_close', 'val': 0}})
    }

    endChat() {

        let surveyMode = false;
        let navigateToSurvey = false;

        let surveyByVisitor = (this.props.chatwidget.hasIn(['chatLiveData','status_sub']) && (this.props.chatwidget.getIn(['chatLiveData','status_sub']) == STATUS_SUB_CONTACT_FORM || this.props.chatwidget.getIn(['chatLiveData','status_sub']) == STATUS_SUB_SURVEY_SHOW || (this.props.chatwidget.getIn(['chatLiveData','status_sub']) == STATUS_SUB_USER_CLOSED_CHAT && (
            this.props.chatwidget.getIn(['chatLiveData','uid']) > 0 ||
            this.props.chatwidget.getIn(['chatLiveData','status']) === STATUS_BOT_CHAT ||
            this.props.chatwidget.getIn(['chatLiveData','status']) == STATUS_CLOSED_CHAT
        ))));
        
        let surveyByOperator = (this.props.chatwidget.getIn(['chatLiveData','status']) == STATUS_CLOSED_CHAT && this.props.chatwidget.getIn(['chatLiveData','uid']) > 0);

        if ((surveyByVisitor == true || surveyByOperator) && this.props.chatwidget.hasIn(['chat_ui','survey_id'])) {

            // If survey button is required and we have not went to survey yet
            if ((!this.props.chatwidget.hasIn(['chat_ui','survey_button']) || this.props.chatwidget.getIn(['chat_ui_state','show_survey']) === 1) || surveyByVisitor == true) {
                surveyMode = true;
            } else {
                navigateToSurvey = true;
            }
        }

        // User has to confirm close
        if (surveyMode === false && this.props.chatwidget.hasIn(['chat_ui','confirm_close']) && this.props.chatwidget.getIn(['chat_ui_state','confirm_close']) === 0) {
            this.props.dispatch({'type' : 'UI_STATE', 'data' : {'attr': 'confirm_close', 'val': 1}});
            return;
        }

        // User confirmed to close
        if (this.props.chatwidget.getIn(['chat_ui_state','confirm_close']) === 1) {
            this.props.dispatch({'type' : 'UI_STATE', 'data' : {'attr': 'confirm_close', 'val': 2}});
        }

        if (navigateToSurvey === true) {
            // Forward user to survey on close
            // Means chat was closed by operator but visitor is still not in survey mode
            this.props.dispatch({'type' : 'UI_STATE', 'data' : {'attr': 'show_survey', 'val': 1}});
            return;
        }

        if (this.props.chatwidget.get('initClose') === false && this.props.chatwidget.hasIn(['chat_ui','survey_id']) && surveyMode == false && (this.props.chatwidget.getIn(['chatLiveData','uid']) > 0 || this.props.chatwidget.getIn(['chatLiveData','status']) === STATUS_BOT_CHAT)) {
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

    setProfile(profile) {
        this.profileHTML = profile;
    }

    setMessages(messages) {
        this.messagesHTML = messages;
    }

    setHideMessageField(hide) {
        this.hideMessageField = hide;
    }

    render() {

        if (this.props.chatwidget.get('loadedCore') === false) {
            return null;
        }

        if (this.props.chatwidget.getIn(['proactive','pending']) === true) {
            return  <Suspense fallback="..."><ProactiveInvitation /></Suspense>
        }

        var className = 'd-flex flex-column flex-grow-1 overflow-auto reset-container-margins';

        if (this.props.chatwidget.get('mode') == 'widget') {
            className = className + (this.props.chatwidget.get('isMobile') == true ? ' mobile-body' : ' desktop-body');
        } else if (this.props.chatwidget.get('mode') == 'embed') {
            className = className + (this.props.chatwidget.get('isMobile') == true ? ' mobile-embed-body' : ' desktop-embed-body');
        }

        if (this.props.chatwidget.get('isChatting') === true) {
            return (<React.Fragment>{this.props.chatwidget.hasIn(['chat_ui','custom_html_header']) && <div className="lhc-custom-header-above" dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','custom_html_header'])}}></div>}{this.props.chatwidget.get('mode') == 'widget' && <HeaderChat popupChat={this.popupChat} endChat={this.endChat} />}<div className={className}><OnlineChat hideMessageField={this.hideMessageField} profileBefore={this.profileHTML} messagesBefore={this.messagesHTML} cancelClose={this.cancelClose} endChat={this.endChat} /></div></React.Fragment>)
        } else if (this.props.chatwidget.get('isOnline') === true) {
            return (<React.Fragment>{this.props.chatwidget.hasIn(['chat_ui','custom_html_header']) && <div className="lhc-custom-header-above" dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','custom_html_header'])}}></div>}{this.props.chatwidget.get('mode') == 'widget' && <HeaderChat popupChat={this.popupChat} endChat={this.endChat} />}<div className={className}><StartChat setHideMessageField={this.setHideMessageField} setProfile={this.setProfile} setMessages={this.setMessages} /></div></React.Fragment>)
        } else {
            return (<React.Fragment>{this.props.chatwidget.hasIn(['chat_ui','custom_html_header']) && <div className="lhc-custom-header-above" dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','custom_html_header'])}}></div>}{this.props.chatwidget.get('mode') == 'widget' && <HeaderChat popupChat={this.popupChat} endChat={this.endChat} />}<div className={className}><Suspense fallback=""><OfflineChat /></Suspense></div></React.Fragment>)
        }
    }
}

export default BodyChat;
