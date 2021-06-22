import React, { Component } from 'react';
import { connect } from "react-redux";
import ChatField from './ChatField';
import StartChat from './StartChat';
import { withTranslation } from 'react-i18next';
import { initOfflineForm, submitOfflineForm } from "../actions/chatActions"
import { helperFunctions } from "../lib/helperFunctions";
import ChatDepartment from './ChatDepartment';

@connect((store) => {
    return {
        chatwidget: store.chatwidget
    };
})

class OfflineChat extends Component {

    constructor(props) {
        super(props);

        this.state = {};
        
        this.initOfflineFormCall();

        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleContentChange = this.handleContentChange.bind(this);
        this.handleContentChangeCustom = this.handleContentChangeCustom.bind(this);
        this.goToChat = this.goToChat.bind(this);
    }

    initOfflineFormCall(dep_default){
        // Init offline form with all attributes
        this.props.dispatch(initOfflineForm({
            'department':this.props.chatwidget.get('department'),
            'theme' : this.props.chatwidget.get('theme'),
            'mode' : this.props.chatwidget.get('mode'),
            'bot_id' : this.props.chatwidget.get('bot_id'),
            'trigger_id' : this.props.chatwidget.get('trigger_id'),
            'online' : 0,
            'dep_default' : (dep_default || this.props.chatwidget.get('departmentDefault') || 0),
        }));
    }

    handleSubmit(event) {

        var fields = this.state;
        var hasFile = false;
        const formData = new FormData();

        if (typeof fields['File'] !== 'undefined') {
            hasFile = true;
            formData.append("File", fields['File'], fields['File'].name);
        }

        fields['jsvar'] = this.props.chatwidget.get('jsVars');
        fields['captcha_' + this.props.chatwidget.getIn(['captcha','hash'])] = this.props.chatwidget.getIn(['captcha','ts']);
        fields['tscaptcha'] = this.props.chatwidget.getIn(['captcha','ts']);
        fields['user_timezone'] = helperFunctions.getTimeZone();
        fields['URLRefer'] = window.location.href.substring(window.location.protocol.length);
        fields['r'] = this.props.chatwidget.get('ses_ref');

        if (this.props.chatwidget.get('operator') != '') {
            fields['operator'] = this.props.chatwidget.get('operator');
        }
        
        if (this.props.chatwidget.get('priority') !== null) {
            fields['priority'] = this.props.chatwidget.get('priority');
        }

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

        if (hasFile) {
            formData.append('document', JSON.stringify(submitData));
        }

        this.props.dispatch(submitOfflineForm(hasFile ? formData : submitData));
        event.preventDefault();
    }

    handleContentChange(obj) {
        var currentState = this.state;
        currentState[obj.id] = obj.value;
        this.setState(currentState);

        if (obj.id == 'DepartamentID') {
            if (this.props.chatwidget.getIn(['offlineData','department','departments']).size > 0){
                this.props.chatwidget.getIn(['offlineData','department','departments']).map(dep => {
                    if (dep.get('value') == obj.value) {
                        if (dep.get('online') == true) {
                            this.props.dispatch({'type' : 'dep_default', data : obj.value});
                            this.props.dispatch({'type' : 'onlineStatus', data : true});
                        }

                        // Update online fields settings if different department
                        if (this.props.chatwidget.getIn(['onlineData','dep_forms']) != obj.value) {
                            this.initOfflineFormCall(obj.value);
                        }

                    }
                })
            }
        }
    }

    componentDidMount() {
        helperFunctions.prefillFields(this);
    }

    handleContentChangeCustom(obj) {
        this.props.dispatch({'type' : 'CUSTOM_FIELDS_ITEM', data : {id : obj.field.get('index'), value : obj.value}});
    }

    goToChat() {
        this.props.dispatch({type : 'attr_set', attr : ['isOfflineMode'], data: false});
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if (document.getElementById('id-container-fluid')) {

            var headerContent = 0;

            if (document.getElementById('widget-header-content')){
                headerContent = document.getElementById('widget-header-content').offsetHeight;
            }

            helperFunctions.sendMessageParent('widgetHeight', [{
                'height' : document.getElementById('id-container-fluid').offsetHeight + headerContent + 10
            }]);
        }
    }
    
    render() {

        const { t } = this.props;

        if (this.props.chatwidget.getIn(['offlineData','fetched']) === true && this.props.chatwidget.getIn(['offlineData','disabled']) === true) {
            return (
                <div className="alert alert-danger m-2" role="alert">
                    {t('start_chat.cant_start_a_chat')}
                </div>
            )
        }

        if (this.props.chatwidget.getIn(['offlineData','fetched']) === false)
        {
            return null;
        }

        if (this.props.chatwidget.getIn(['offlineData','fields']).size > 0) {
            var mappedFields = this.props.chatwidget.getIn(['offlineData','fields']).map(field =><ChatField chatUI={this.props.chatwidget.get('chat_ui')} isInvalid={this.props.chatwidget.hasIn(['validationErrors',field.get('identifier')])} attrPrefill={{'attr_prefill_admin' : this.props.chatwidget.get('attr_prefill_admin'), 'attr_prefill' : this.props.chatwidget.get('attr_prefill')}} defaultValueField={this.state[field.get('name')] || field.get('value')} onChangeContent={this.handleContentChange} field={field} />);
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
                  <div id="id-container-fluid">
                    {this.props.chatwidget.get('leave_message') && this.props.chatwidget.hasIn(['chat_ui','operator_profile']) && this.props.chatwidget.getIn(['chat_ui','operator_profile']) != '' && <div className="py-2 px-3 offline-intro-operator" dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','operator_profile'])}}></div>}

                    {this.props.chatwidget.get('leave_message') && this.props.chatwidget.hasIn(['chat_ui','offline_intro']) && this.props.chatwidget.getIn(['chat_ui','offline_intro']) != '' && <p className="pb-1 mb-0 pt-2 px-3 font-weight-bold offline-intro" dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','offline_intro'])}}></p>}

                    {!this.props.chatwidget.get('leave_message') && <p className="pb-1 mb-0 pt-2 px-3 font-weight-bold offline-intro">{this.props.chatwidget.getIn(['chat_ui','chat_unavailable'])}</p>}

                    {this.props.chatwidget.get('leave_message') &&
                    <div className="container-fluid" >
                        <form onSubmit={this.handleSubmit} className="offline-form">
                            <div className="row pt-2">
                                {mappedFields}
                                {mappedFieldsCustom}
                                {this.props.chatwidget.hasIn(['offlineData','department']) && <ChatDepartment defaultValueField={this.state['DepartamentID']} setDefaultValue={this.props.chatwidget.get('departmentDefault')} onChangeContent={this.handleContentChange} isInvalid={this.props.chatwidget.hasIn(['validationErrors','department'])} departments={this.props.chatwidget.getIn(['offlineData','department'])} />}
                            </div>
                            {(!this.props.chatwidget.hasIn(['chat_ui','hstr_btn']) || mappedFieldsCustom !== "" || mappedFields !== "") && <div className="row">
                                <div className="col-12 pb-2">
                                    <button type="submit" disabled={this.props.chatwidget.get('processStatus') == 1} className="btn btn-secondary btn-sm">{this.props.chatwidget.get('processStatus') == 1 && <i className="material-icons">&#xf113;</i>}{this.props.chatwidget.getIn(['chat_ui','custom_start_button']) || t('start_chat.leave_a_message')}</button>
                                    {this.props.chatwidget.get('isOnline') === true && this.props.chatwidget.get('isOfflineMode') === true && <button type="button" onClick={this.goToChat} className="float-right btn btn-sm btn-link text-muted">&laquo; {t('button.back_to_chat')}</button>}
                                </div>
                            </div>}
                        </form>
                    </div>}
                      
                      
                  </div>
            )
        } else if (this.props.chatwidget.get('processStatus') == 2) {
            return (
                <div id="id-container-fluid">

                {this.props.chatwidget.hasIn(['chat_ui','operator_profile']) && this.props.chatwidget.getIn(['chat_ui','operator_profile']) != '' && <div className="py-2 px-3 offline-intro-operator" dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','operator_profile'])}}></div>}

                {this.props.chatwidget.hasIn(['chat_ui','offline_intro']) && this.props.chatwidget.getIn(['chat_ui','offline_intro']) != '' && <p className="pb-1 mb-0 pt-2 px-3 font-weight-bold offline-intro" dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','offline_intro'])}}></p>}

                    <div className="container-fluid">
                        <div className="row">
                            <div className="col-12">
                                <p className="pt-2">{this.props.chatwidget.getIn(['chat_ui','thank_feedback']) || t('start_chat.thank_you_for_feedback')}</p>
                            </div>
                        </div>
                    </div>

                </div>
            )
        }
    }
}

export default withTranslation()(OfflineChat);
