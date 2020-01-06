import React, { Component } from 'react';
import { connect } from "react-redux";
import ChatField from './ChatField';
import StartChat from './StartChat';

import { initOfflineForm, submitOfflineForm } from "../actions/chatActions"

@connect((store) => {
    return {
        chatwidget: store.chatwidget
    };
})

class OfflineChat extends Component {

    constructor(props) {
        super(props);

        this.state = {};
        
        // Init offline form with all attributes
        this.props.dispatch(initOfflineForm({
            'department':this.props.chatwidget.get('department'),
            'theme' : this.props.chatwidget.get('theme'),
            'mode' : this.props.chatwidget.get('mode'),
            'online' : 0
        }));

        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleContentChange = this.handleContentChange.bind(this);
        this.handleContentChangeCustom = this.handleContentChangeCustom.bind(this);
    }

    handleSubmit(event) {

        var fields = this.state;
        fields['jsvar'] = this.props.chatwidget.get('jsVars');
        fields['captcha_' + this.props.chatwidget.getIn(['captcha','hash'])] = this.props.chatwidget.getIn(['captcha','ts']);
        fields['tscaptcha'] = this.props.chatwidget.getIn(['captcha','ts']);
        fields['user_timezone'] = StartChat.getTimeZone();
        fields['URLRefer'] = window.location.href.substring(window.location.protocol.length);
        fields['r'] = this.props.chatwidget.get('ses_ref');

        const customFields = StartChat.getCustomFieldsSubmit(this.props.chatwidget.getIn(['customData','fields']));
        if (customFields !== null) {
            fields = {...fields, ...customFields};
        }

        let submitData = {
            'department': this.props.chatwidget.get('department'),
            'theme' : this.props.chatwidget.get('theme'),
            'mode' : this.props.chatwidget.get('mode'),
            'fields' : fields
        };

        this.props.dispatch(submitOfflineForm(submitData));
        event.preventDefault();
    }

    handleContentChange(obj) {
        var currentState = this.state;
        currentState[obj.id] = obj.value;
        this.setState(currentState);
    }

    componentDidMount() {
        StartChat.prefillFields(this);
    }

    handleContentChangeCustom(obj) {
        this.props.dispatch({'type' : 'CUSTOM_FIELDS_ITEM', data : {id : obj.field.get('index'), value : obj.value}});
    }

    render() {

        if (this.props.chatwidget.get('offlineData').has('fields')) {
            var mappedFields = this.props.chatwidget.getIn(['offlineData','fields']).map(field =><ChatField chatUI={this.props.chatwidget.get('chat_ui')} isInvalid={this.props.chatwidget.hasIn(['validationErrors',field.get('identifier')])} defaultValueField={this.state[field.get('name')] || field.get('value')} onChangeContent={this.handleContentChange} field={field} />);
        } else {
            var mappedFields = "";
        }

        if (this.props.chatwidget.getIn(['customData','fields']).size > 0) {
            var mappedFieldsCustom = this.props.chatwidget.getIn(['customData','fields']).map(field =><ChatField chatUI={this.props.chatwidget.get('chat_ui')} key={field.get('identifier')} isInvalid={this.props.chatwidget.hasIn(['validationErrors',field.get('identifier')])} defaultValueField={field.get('value')} onChangeContent={this.handleContentChangeCustom} field={field} />);
        } else {
            var mappedFieldsCustom = "";
        }

        if (this.props.chatwidget.get('processStatus') == 0 || this.props.chatwidget.get('processStatus') == 1) {
            return (
                <div className="container-fluid">
                    <form onSubmit={this.handleSubmit}>
                        <div className="row">
                            {mappedFields}
                            {mappedFieldsCustom}
                        </div>
                        <div className="row">
                            <div className="col-12">
                                <button type="submit" className="btn btn-secondary btn-sm">Leave a message</button>
                            </div>
                        </div>
                    </form>
                </div>
            )
        } else if (this.props.chatwidget.get('processStatus') == 2) {
            return (
                <div className="container-fluid">
                    <div className="row">
                        <div className="col-12">
                            <p>Thank you for your feedback...</p>
                        </div>
                    </div>
                </div>
            )
        }
    }
}

export default OfflineChat;
