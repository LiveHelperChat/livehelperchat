import React, { Component } from 'react';
import { connect } from "react-redux";
import ChatField from './ChatField';
import StartChat from './StartChat';
import { withTranslation } from 'react-i18next';
import { initOfflineForm, submitOfflineForm } from "../actions/chatActions"
import { helperFunctions } from "../lib/helperFunctions";
import ChatStatus from './ChatStatus';

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
        fields['user_timezone'] = helperFunctions.getTimeZone();
        fields['URLRefer'] = window.location.href.substring(window.location.protocol.length);
        fields['r'] = this.props.chatwidget.get('ses_ref');

        const customFields = helperFunctions.getCustomFieldsSubmit(this.props.chatwidget.getIn(['customData','fields']));
        if (customFields !== null) {
            fields = {...fields, ...customFields};
        }

        let submitData = {
            'department': this.props.chatwidget.get('department'),
            'theme' : this.props.chatwidget.get('theme'),
            'mode' : this.props.chatwidget.get('mode'),
            'vid' : this.props.chatwidget.get('vid'),
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
        helperFunctions.prefillFields(this);
    }

    handleContentChangeCustom(obj) {
        this.props.dispatch({'type' : 'CUSTOM_FIELDS_ITEM', data : {id : obj.field.get('index'), value : obj.value}});
    }

    render() {

        const { t } = this.props;

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

                    {this.props.chatwidget.hasIn(['chat_ui','operator_profile']) && <ChatStatus status={this.props.chatwidget.getIn(['chat_ui','operator_profile'])} />}

                    <form onSubmit={this.handleSubmit}>
                        <div className="row pt-2">
                            {mappedFields}
                            {mappedFieldsCustom}
                        </div>
                        <div className="row">
                            <div className="col-12">
                                <button type="submit" className="btn btn-secondary btn-sm">{this.props.chatwidget.getIn(['chat_ui','custom_start_button']) || t('start_chat.leave_a_message')}</button>
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
                            <p>{t('start_chat.thank_you_for_feedback')}</p>
                        </div>
                    </div>
                </div>
            )
        }
    }
}

export default withTranslation()(OfflineChat);
