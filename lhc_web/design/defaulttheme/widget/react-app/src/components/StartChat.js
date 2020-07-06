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
import ChatBotIntroMessage from './ChatBotIntroMessage';
import { initOnlineForm, submitOnlineForm } from "../actions/chatActions"

@connect((store) => {
    return {
        chatwidget: store.chatwidget
    };
})

class StartChat extends Component {

    constructor(props) {
        super(props);

        this.apiLoaded = false;

        this.state = {showBBCode : null, Question:''};
        this.botPayload = null;
        this.handleSubmit = this.handleSubmit.bind(this);
        this.enterKeyDown = this.enterKeyDown.bind(this);
        this.handleContentChange = this.handleContentChange.bind(this);
        this.handleContentChangeCustom = this.handleContentChangeCustom.bind(this);
        this.setBotPayload = this.setBotPayload.bind(this);
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

        if (this.botPayload) {
            submitData['bpayload'] = this.botPayload;
        }

        if (this.props.chatwidget.hasIn(['proactive','data','invitation_id']) === true) {
            submitData['invitation_id'] = this.props.chatwidget.getIn(['proactive','data','invitation_id']);
        }

        this.props.dispatch(submitOnlineForm(submitData));

        if (event)
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

    componentWillUnmount() {
        var messagesScroll = document.getElementById('messagesBlock');
        if (messagesScroll !== null) {
            this.props.setMessages(messagesScroll.innerHTML);
        }

        var profileBody = document.getElementById('lhc-profile-body');
        if (profileBody !== null) {
            this.props.setProfile(profileBody.innerHTML);
        }

        if (document.getElementById('CSChatMessage') === null) {
            this.props.setHideMessageField(true);
        }
    }

    setBotPayload(params) {
        this.botPayload = params;
        this.handleSubmit();
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
                'bot_id' : this.props.chatwidget.get('bot_id'),
                'online' : 1
            }));
        }
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        this.updateOnlineFields();
        if (document.getElementById('id-container-fluid')) {
            helperFunctions.sendMessageParent('widgetHeight', [{'height' : document.getElementById('id-container-fluid').offsetHeight+40}]);
        }

        let needFocus = false;
        if (this.apiLoaded === false && this.props.chatwidget.get('api_data') !== null) {
            this.apiLoaded = true;
            this.setState({...this.state, ...this.props.chatwidget.get('api_data')});
            needFocus = true;
        }

        // Auto focus if it's show operation
        if ((needFocus === true || (prevProps.chatwidget.get('shown') === false && this.props.chatwidget.get('shown') === true)) && this.props.chatwidget.get('mode') == 'widget' && this.textMessageRef.current) {
            this.textMessageRef.current.focus();
        }
    }

    moveCaretAtEnd(e) {
        var temp_value = e.target.value;
        e.target.value = '';
        e.target.value = temp_value;
    }

    static getDerivedStateFromProps(props, state) {

        if (props.chatwidget.getIn(['chat_ui','auto_start']) && props.chatwidget.get('processStatus') == 0 && (props.chatwidget.get('mode') == 'embed' || props.chatwidget.get('mode') == 'popup' || (props.chatwidget.get('mode') == 'widget' && props.chatwidget.get('shown') == 1) )) {

            var fields = state;
            fields['jsvar'] = props.chatwidget.get('jsVars');
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

            if (props.chatwidget.get('api_data') !== null) {
                fields = {...fields, ...props.chatwidget.get('api_data')}
            }

            let submitData = {
                'department':props.chatwidget.get('department'),
                'theme' : props.chatwidget.get('theme'),
                'mode' : props.chatwidget.get('mode'),
                'vid' : props.chatwidget.get('vid'),
                'fields' : fields
            };

            props.dispatch(submitOnlineForm(submitData));
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
            if (this.props.chatwidget.hasIn(['chat_ui','show_messages_box']) && this.props.chatwidget.getIn(['onlineData','fields_visible']) <= 1 && this.props.chatwidget.getIn(['customData','fields']).size == 0) {

                var classMessageInput = "pl-0 no-outline form-control rounded-0 form-control border-left-0 border-right-0 border-0";

                var msg_expand = "flex-grow-1 overflow-scroll position-relative";
                var bottom_messages = "bottom-message px-1";

                if (this.props.chatwidget.hasIn(['chat_ui','msg_expand'])) {
                    msg_expand = "overflow-scroll position-relative";
                    bottom_messages += " position-relative";
                }

                return (
                    <React.Fragment>

                        {this.state.showBBCode && <ChatModal showModal={this.state.showBBCode} insertText={this.insertText} toggle={this.toggleModal} dataUrl={"/chat/bbcodeinsert?react=1"} />}

                        {
                            (this.props.chatwidget.getIn(['proactive','has']) === true && <ChatInvitationMessage mode='profile_only' invitation={this.props.chatwidget.getIn(['proactive','data'])} />)
                            ||
                            (this.props.chatwidget.hasIn(['chat_ui','operator_profile']) && <div id="lhc-profile-body"><div id="chat-status-container" className="p-2 border-bottom" dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','operator_profile'])}}></div></div>)
                        }

                        <div className={msg_expand} id="messagesBlock">
                            <div className={bottom_messages} id="messages-scroll">
                                {this.props.chatwidget.getIn(['proactive','has']) === true && <ChatInvitationMessage mode="message" setBotPayload={this.setBotPayload} invitation={this.props.chatwidget.getIn(['proactive','data'])} />}

                                {!this.props.chatwidget.getIn(['proactive','has']) && this.props.chatwidget.hasIn(['chat_ui','cmmsg_widget']) && <ChatBotIntroMessage setBotPayload={this.setBotPayload} content={this.props.chatwidget.getIn(['chat_ui','cmmsg_widget'])} />}

                                {this.props.chatwidget.get('processStatus') == 1 && this.state.Question != '' && <div data-op-id="0" className="message-row response">
                                    <div className="msg-date"></div>
                                    <span title="" className="usr-tit vis-tit"><i title={t('start_chat.visitor')} className="material-icons chat-operators mi-fs15 mr-0">&#xf104;</i><span className="user-nick-title">{t('start_chat.visitor')}</span></span>
                                    <div className="msg-body">{this.state.Question}</div>
                                </div>}

                            </div>
                        </div>

                        {!this.props.chatwidget.getIn(['proactive','has']) && this.props.chatwidget.hasIn(['chat_ui','custom_html_widget']) && <div dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','custom_html_widget'])}}></div>}

                        {(this.props.chatwidget.getIn(['onlineData','fields_visible']) == 1 || (this.props.chatwidget.getIn(['onlineData','fields_visible']) == 0 && !this.props.chatwidget.hasIn(['chat_ui','hstr_btn']))) && <div className="d-flex flex-row border-top position-relative message-send-area">

                            {(this.props.chatwidget.hasIn(['validationErrors','question'])) && <div id="id-operator-typing" className="bg-white pl-1">{this.props.chatwidget.getIn(['validationErrors','question'])}</div>}

                            {this.props.chatwidget.getIn(['onlineData','fields_visible']) == 1 && <React.Fragment>
                                <ChatStartOptions toggleModal={this.toggleModal} />
                                <div className="mx-auto pb-1 w-100">
                                    <textarea autoFocus={this.props.chatwidget.get('mode') == 'widget' && this.props.chatwidget.get('shown') === true} onFocus={this.moveCaretAtEnd} readOnly={this.props.chatwidget.get('processStatus') == 1} maxLength={this.props.chatwidget.getIn(['chat_ui','max_length'])} style={{height: this.props.chatwidget.get('shown') === true && this.textMessageRef.current && (/\r|\n/.exec(this.state.Question) || (this.state.Question.length > this.textMessageRef.current.offsetWidth/8.6)) ? '60px' : '36px'}} aria-label="Type your message here..." id="CSChatMessage" value={this.props.chatwidget.get('processStatus') == 1 ? '' : this.state.Question} placeholder={this.props.chatwidget.hasIn(['chat_ui','placeholder_message']) ? this.props.chatwidget.getIn(['chat_ui','placeholder_message']) : t('chat.type_here')} onKeyDown={this.enterKeyDown} onChange={(e) => this.handleContentChange({'id' : 'Question' ,'value' : e.target.value})} ref={this.textMessageRef} rows="1" className={classMessageInput} />
                                </div>
                                <div className="disable-select">
                                    <div className="user-chatwidget-buttons pt-1" id="ChatSendButtonContainer">
                                        {this.props.chatwidget.get('processStatus') != 1 && <a onClick={this.handleSubmit} title={t('button.start_chat')}>
                                            <i className="material-icons text-muted settings">&#xf107;</i>
                                        </a>}

                                        {this.props.chatwidget.get('processStatus') == 1 && <i className="material-icons text-muted settings mr-0">&#xf113;</i>}

                                    </div>
                                </div>
                            </React.Fragment>}

                            {this.props.chatwidget.getIn(['onlineData','fields_visible']) == 0 && !this.props.chatwidget.hasIn(['chat_ui','hstr_btn']) && <button className="mx-auto pb-1 w-100 btn btn-light rounded-0" onClick={this.handleSubmit} title={t('button.start_chat')}>
                                {this.props.chatwidget.getIn(['chat_ui','custom_start_button']) || t('button.start_chat_With_us')}
                            </button>}


                          </div>}
                    </React.Fragment>
                )
            } else {
                return (

                <div id="id-container-fluid">
                    {
                            (this.props.chatwidget.getIn(['proactive','has']) === true && <ChatInvitationMessage mode='profile' invitation={this.props.chatwidget.getIn(['proactive','data'])} />)
                            ||
                            (this.props.chatwidget.hasIn(['chat_ui','operator_profile']) && <div className="p-2 border-bottom" dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','operator_profile'])}}></div>)
                    }
                    <div className="container-fluid">
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
                </div>
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
