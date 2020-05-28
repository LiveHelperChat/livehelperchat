import React, { Component } from 'react';
import { connect } from "react-redux";
import { withTranslation } from 'react-i18next';

import ChatField from './ChatField';
import ChatErrorList from './ChatErrorList';
import ChatDepartment from './ChatDepartment';
import ChatModal from './ChatModal';
import ChatStartOptions from './ChatStartOptions';
import { helperFunctions } from "../lib/helperFunctions";
import ChatInvitationMessage from './ChatInvitationMessage';
import { initOnlineForm, submitOnlineForm } from "../actions/chatActions"

@connect((store) => {
    return {
        chatwidget: store.chatwidget
    };
})

class StartChat extends Component {

    constructor(props) {
        super(props);

        this.state = {showBBCode : null, Question:'', };
        this.handleSubmit = this.handleSubmit.bind(this);
        this.enterKeyDown = this.enterKeyDown.bind(this);
        this.handleContentChange = this.handleContentChange.bind(this);
        this.handleContentChangeCustom = this.handleContentChangeCustom.bind(this);
        this.toggleModal = this.toggleModal.bind(this);
        this.textMessageRef = React.createRef();
    }

    toggleModal() {
        this.setState({
            showBBCode: !this.state.showBBCode
        })
    }

    enterKeyDown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            this.handleSubmit(e);
            e.preventDefault();
        }
    }

    handleSubmit(event) {

        if (this.props.chatwidget.get('processStatus') != 0) {
            return;
        }

        var fields = this.state;
        fields['jsvar'] = this.props.chatwidget.get('jsVars');
        fields['captcha_' + this.props.chatwidget.getIn(['captcha','hash'])] = this.props.chatwidget.getIn(['captcha','ts']);
        fields['tscaptcha'] = this.props.chatwidget.getIn(['captcha','ts']);
        fields['user_timezone'] = helperFunctions.getTimeZone();
        fields['URLRefer'] = window.location.href.substring(window.location.protocol.length);
        fields['r'] = this.props.chatwidget.get('ses_ref');

        if (this.props.chatwidget.get('bot_id') != '') {
            fields['bot_id'] = this.props.chatwidget.get('bot_id');
        }

        if (this.props.chatwidget.get('operator') != '') {
            fields['operator'] = this.props.chatwidget.get('operator');
        }

        if (this.props.chatwidget.get('pvhash') !== null) {
            fields['pvhash'] = this.props.chatwidget.get('pvhash');
        }

        if (this.props.chatwidget.get('priority') !== null) {
            fields['priority'] = this.props.chatwidget.get('priority');
        }

        if (this.props.chatwidget.get('phash') !== null) {
            fields['phash'] = this.props.chatwidget.get('phash');
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

        if (this.props.chatwidget.hasIn(['proactive','data','invitation_id']) === true) {
            submitData['invitation_id'] = this.props.chatwidget.getIn(['proactive','data','invitation_id']);
        }

        this.props.dispatch(submitOnlineForm(submitData));

        event.preventDefault();
    }

    handleContentChange(obj) {
        var currentState = this.state;
        currentState[obj.id] = obj.value;
        this.setState(currentState);

        if (obj.id == 'DepartamentID') {
            if (this.props.chatwidget.getIn(['onlineData','department','departments']).size > 0){
                this.props.chatwidget.getIn(['onlineData','department','departments']).map(dep => {
                    if (dep.get('value') == obj.value) {
                        if (dep.get('online') == false) {
                            this.props.dispatch({'type' : 'dep_default', data : obj.value});
                            this.props.dispatch({'type' : 'onlineStatus', data : false});
                        }
                    }
                })
            }
        }
    }

    handleContentChangeCustom(obj) {
        this.props.dispatch({'type' : 'CUSTOM_FIELDS_ITEM', data : {id : obj.field.get('index'), value : obj.value}});
    }

    componentDidMount() {
        helperFunctions.prefillFields(this);
        this.updateOnlineFields();
    }

    updateOnlineFields(){
        if (this.props.chatwidget.getIn(['onlineData','fetched']) === false) {
            // Init offline form with all attributes
            this.props.dispatch(initOnlineForm({
                'department':this.props.chatwidget.get('department'),
                'product':this.props.chatwidget.get('product'),
                'theme' : this.props.chatwidget.get('theme'),
                'mode' : this.props.chatwidget.get('mode'),
                'pvhash' : this.props.chatwidget.get('pvhash'),
                'phash' : this.props.chatwidget.get('phash'),
                'online' : 1
            }));
        }
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        this.updateOnlineFields();
        if (document.getElementById('id-container-fluid')) {
            helperFunctions.sendMessageParent('widgetHeight', [{'height' : document.getElementById('id-container-fluid').offsetHeight+40}]);
        }

        // Auto focus if it's show operation
        if (prevProps.chatwidget.get('shown') === false && this.props.chatwidget.get('shown') === true && this.props.chatwidget.get('mode') == 'widget' && this.textMessageRef.current) {
            this.textMessageRef.current.focus();
        }

    }

    static getDerivedStateFromProps(props, state) {

        if (props.chatwidget.getIn(['chat_ui','auto_start']) && props.chatwidget.get('processStatus') == 0 && (props.chatwidget.get('mode') == 'embed' || props.chatwidget.get('mode') == 'popup' || (props.chatwidget.get('mode') == 'widget' && props.chatwidget.get('shown') == 1) )) {
            let fields = {'jsvar' : props.chatwidget.get('jsVars')};
            fields['captcha_' + props.chatwidget.getIn(['captcha','hash'])] = props.chatwidget.getIn(['captcha','ts']);
            fields['tscaptcha'] = props.chatwidget.getIn(['captcha','ts']);
            fields['user_timezone'] = helperFunctions.getTimeZone();
            fields['URLRefer'] = window.location.href.substring(window.location.protocol.length);
            fields['r'] = props.chatwidget.get('ses_ref');

            if (props.chatwidget.get('bot_id') != '') {
                fields['bot_id'] = props.chatwidget.get('bot_id');
            }

            if (props.chatwidget.get('operator') != '') {
                fields['operator'] = props.chatwidget.get('operator');
            }

            if (props.chatwidget.get('priority') !== null) {
                fields['priority'] = props.chatwidget.get('priority');
            }

            if (props.chatwidget.get('pvhash') !== null) {
                fields['pvhash'] = props.chatwidget.get('pvhash');
            }

            if (props.chatwidget.get('phash') !== null) {
                fields['phash'] = props.chatwidget.get('phash');
            }

            const customFields = helperFunctions.getCustomFieldsSubmit(props.chatwidget.getIn(['customData','fields']));
            if (customFields !== null) {
                fields = {...fields, ...customFields};
            }

            props.dispatch(submitOnlineForm({
                'department':props.chatwidget.get('department'),
                'theme' : props.chatwidget.get('theme'),
                'mode' : props.chatwidget.get('mode'),
                'vid' : props.chatwidget.get('vid'),
                'fields' : fields
            }));
        }

        return null;
    }

    insertText = (text) => {
        var caretPos = this.textMessageRef.current.selectionStart;
        this.setState({'Question': (this.state['Question'].substring(0, caretPos) + text + this.state['Question'].substring(caretPos))});
    }

    render() {

    const { t } = this.props;

    if (this.props.chatwidget.getIn(['onlineData','fetched']) === true && this.props.chatwidget.getIn(['onlineData','disabled']) === true) {
        return (
            <div className="alert alert-danger m-2" role="alert">
                {t('start_chat.cant_start_a_chat')}
            </div>
        )
    }

    if (this.props.chatwidget.getIn(['onlineData','fetched']) === false || this.props.chatwidget.getIn(['chat_ui','auto_start']) === true)
    {
        return null;
    }

    if (this.props.chatwidget.get('onlineData').has('fields') && !(this.props.chatwidget.hasIn(['chat_ui','show_messages_box']) && this.props.chatwidget.getIn(['onlineData','fields_visible']) == 1 && this.props.chatwidget.getIn(['customData','fields']).size == 0)) {
        var mappedFields = this.props.chatwidget.getIn(['onlineData','fields']).map(field =><ChatField chatUI={this.props.chatwidget.get('chat_ui')} key={field.get('identifier')} isInvalid={this.props.chatwidget.hasIn(['validationErrors',field.get('identifier')])} defaultValueField={this.state[field.get('name')] || field.get('value')} attrPrefill={{'attr_prefill_admin' : this.props.chatwidget.get('attr_prefill_admin'), 'attr_prefill' : this.props.chatwidget.get('attr_prefill')}} onChangeContent={this.handleContentChange} field={field} />);
    } else {
        var mappedFields = "";
    }

    if (this.props.chatwidget.getIn(['customData','fields']).size > 0) {
        var mappedFieldsCustom = this.props.chatwidget.getIn(['customData','fields']).map(field =><ChatField chatUI={this.props.chatwidget.get('chat_ui')} key={field.get('identifier')} isInvalid={this.props.chatwidget.hasIn(['validationErrors',field.get('identifier')])} defaultValueField={field.get('value')} onChangeContent={this.handleContentChangeCustom} field={field} />);
    } else {
        var mappedFieldsCustom = "";
    }

    if (this.props.chatwidget.hasIn(['onlineData','paid','error']) && this.props.chatwidget.getIn(['onlineData','paid','error'])) {
        return <p className="p-2">{this.props.chatwidget.getIn(['onlineData','paid','message'])}</p>
    }

    if (this.props.chatwidget.get('processStatus') == 0 || this.props.chatwidget.get('processStatus') == 1) {
            if (this.props.chatwidget.hasIn(['chat_ui','show_messages_box']) && this.props.chatwidget.getIn(['onlineData','fields_visible']) == 1 && this.props.chatwidget.getIn(['customData','fields']).size == 0) {

                var classMessageInput = "pl-0 no-outline form-control rounded-0 form-control border-left-0 border-right-0 border-0";

                if (this.props.chatwidget.get('processStatus') == 1) {
                    classMessageInput += " d-none ";
                }

                return (
                    <React.Fragment>

                        {this.state.showBBCode && <ChatModal showModal={this.state.showBBCode} insertText={this.insertText} toggle={this.toggleModal} dataUrl={"/chat/bbcodeinsert?react=1"} />}

                        {
                            (this.props.chatwidget.getIn(['proactive','has']) === true && <ChatInvitationMessage mode='profile_only' invitation={this.props.chatwidget.getIn(['proactive','data'])} />)
                            ||
                            (this.props.chatwidget.hasIn(['chat_ui','operator_profile']) && <div className="p-2 border-bottom" dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','operator_profile'])}}></div>)
                        }

                        <div className="flex-grow-1 overflow-scroll position-relative" id="messagesBlock">
                            <div className="bottom-message px-1" id="messages-scroll">
                                {this.props.chatwidget.getIn(['proactive','has']) === true && <ChatInvitationMessage mode="message" invitation={this.props.chatwidget.getIn(['proactive','data'])} />}
                            </div>
                        </div>

                        {!this.props.chatwidget.getIn(['proactive','has']) && this.props.chatwidget.hasIn(['chat_ui','custom_html_widget']) && <div dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','custom_html_widget'])}}></div>}

                        <div className="d-flex flex-row border-top position-relative message-send-area">

                            {(this.props.chatwidget.hasIn(['validationErrors','question'])) && <div id="id-operator-typing" className="bg-white pl-1">{this.props.chatwidget.getIn(['validationErrors','question'])}</div>}

                            <ChatStartOptions toggleModal={this.toggleModal} />
                            <div className="mx-auto pb-1 w-100">
                                {(this.props.chatwidget.get('processStatus') == 1) && <div className="loader-submit"></div>}
                                <textarea disabled={this.props.chatwidget.get('processStatus') == 1} maxLength={this.props.chatwidget.getIn(['chat_ui','max_length'])} style={{height: this.props.chatwidget.get('shown') === true && this.textMessageRef.current && (/\r|\n/.exec(this.state.Question) || (this.state.Question.length > this.textMessageRef.current.offsetWidth/8.6)) ? '60px' : 'inherit'}} aria-label="Type your message here..." id="CSChatMessage" value={this.state.Question} placeholder={this.props.chatwidget.hasIn(['chat_ui','placeholder_message']) ? this.props.chatwidget.getIn(['chat_ui','placeholder_message']) : t('chat.type_here')} onKeyDown={this.enterKeyDown} onChange={(e) => this.handleContentChange({'id' : 'Question' ,'value' : e.target.value})} ref={this.textMessageRef} rows="1" className={classMessageInput} />
                            </div>
                            <div className="disable-select">
                                <div className="user-chatwidget-buttons pt-1" id="ChatSendButtonContainer">
                                    <a onClick={this.handleSubmit} title={t('button.start_chat')}>
                                        <i className="material-icons text-muted settings">send</i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </React.Fragment>
                )
            } else {
                return (
            <React.Fragment>
                    {
                            (this.props.chatwidget.getIn(['proactive','has']) === true && <ChatInvitationMessage mode='profile' invitation={this.props.chatwidget.getIn(['proactive','data'])} />)
                            ||
                            (this.props.chatwidget.hasIn(['chat_ui','operator_profile']) && <div className="p-2 border-bottom" dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','operator_profile'])}}></div>)
                    }

                    <div className="container-fluid" id="id-container-fluid">
                        <ChatErrorList errors={this.props.chatwidget.get('validationErrors')} />



                        {!this.props.chatwidget.getIn(['proactive','has']) && this.props.chatwidget.hasIn(['chat_ui','custom_html_widget']) && <div dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','custom_html_widget'])}}></div>}

                        <form onSubmit={this.handleSubmit}>
                            <div className="row pt-2">
                                {mappedFields}
                                {mappedFieldsCustom}
                                {this.props.chatwidget.hasIn(['onlineData','department']) && <ChatDepartment defaultValueField={this.state['DepartamentID']} setDefaultValue={this.props.chatwidget.get('departmentDefault')} onChangeContent={this.handleContentChange} isInvalidProduct={this.props.chatwidget.hasIn(['validationErrors','ProductID'])} isInvalid={this.props.chatwidget.hasIn(['validationErrors','department'])} departments={this.props.chatwidget.getIn(['onlineData','department'])} />}
                            </div>
                            <div className="row">
                                <div className="col-12 pb-3">
                                    <button disabled={this.props.chatwidget.get('processStatus') == 1} type="submit" className="btn btn-secondary btn-sm">{this.props.chatwidget.getIn(['chat_ui','custom_start_button']) || t('button.start_chat')}</button>
                                </div>
                            </div>
                        </form>
                    </div>
            </React.Fragment>
                )
            }

        } else if (this.props.chatwidget.get('processStatus') == 2) {
            return (
                <div className="container-fluid" id="id-container-fluid">
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

export default withTranslation()(StartChat);
