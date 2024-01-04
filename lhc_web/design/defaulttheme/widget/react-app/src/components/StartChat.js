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
import ChatAbort from './ChatAbort';
import { initOnlineForm, submitOnlineForm, minimizeWidget } from "../actions/chatActions"

@connect((store) => {
    return {
        chatwidget: store.chatwidget
    };
})

class StartChat extends Component {

    constructor(props) {
        super(props);

        this.apiLoaded = false;
        this.customHTMLPriority = false;

        this.state = {showBBCode : null, Question:'', changeLanguage: false, hasBotData : false, textAreaHidden: false};
        this.botPayload = null;
        this.handleSubmit = this.handleSubmit.bind(this);
        this.enterKeyDown = this.enterKeyDown.bind(this);
        this.handleContentChange = this.handleContentChange.bind(this);
        this.handleContentChangeCustom = this.handleContentChangeCustom.bind(this);
        this.setBotPayload = this.setBotPayload.bind(this);
        this.toggleModal = this.toggleModal.bind(this);
        this.setLanguageAction = this.setLanguageAction.bind(this);
        this.changeLanguage = this.changeLanguage.bind(this);

        this.textMessageRef = React.createRef();
        this.messagesAreaRef = React.createRef();
    }

    changeLanguage() {
        this.setState({
            changeLanguage: !this.state.changeLanguage
        });
    }

    setLanguageAction(lng) {
        helperFunctions.setLocalStorage('_lng',lng);
        this.setState({
            changeLanguage: false
        });
        helperFunctions.emitEvent('change_language', [lng]);
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

        // Focus element so once OnlineChat component is mounted it remains focused
        var elm = document.getElementById('CSChatMessage');
        if (elm !== null) {
            elm.focus();
            this.props.setHideMessageField(false);
        }

        var fields = {...this.state};
        fields['jsvar'] = this.props.chatwidget.get('jsVars');
        fields['captcha_' + this.props.chatwidget.getIn(['captcha','hash'])] = this.props.chatwidget.getIn(['captcha','ts']);
        fields['tscaptcha'] = this.props.chatwidget.getIn(['captcha','ts']);
        fields['user_timezone'] = helperFunctions.getTimeZone();
        fields['URLRefer'] = '';

        try {
            var iframeMode = window.parent.location !== window.parent.parent.location;
            var popupMode = typeof window.lhcChat != 'undefined' && (window.lhcChat['mode'] == 'popup' || window.lhcChat['mode'] == 'embed') && (window.opener !== null || window.parent.opener !== null);

            if (iframeMode) {
                fields['URLRefer'] = parent.location.href.substring(parent.location.protocol.length);
            } else {
                var instWindow = null;
                if (window.opener !== null) {
                    instWindow = window.opener;
                } else {
                    instWindow = window.parent;
                }
                fields['URLRefer'] = instWindow.location.href.substring(instWindow.location.protocol.length);
            }

        } catch (e) {
            try {
                fields['URLRefer'] = String(window.document.location);
            } catch (e) {
                // Do nothing
            }
        }

        if (fields['URLRefer'] == 'blank') {
            fields['URLRefer'] = ''
        }

        fields['r'] = this.props.chatwidget.get('ses_ref');

        if (this.props.chatwidget.get('subject_id') != '') {
            fields['subject_id'] = this.props.chatwidget.get('subject_id');
        }

        if (this.props.chatwidget.get('bot_id') != '') {
            fields['bot_id'] = this.props.chatwidget.get('bot_id');
        }

        if (this.props.chatwidget.get('trigger_id') != '') {
            fields['trigger_id'] = this.props.chatwidget.get('trigger_id');
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

        let host = "";
        try {
            host = window.parent.location.origin;
        } catch (e) {

        }

        let submitData = {
            'department': this.props.chatwidget.get('department'),
            'theme' : this.props.chatwidget.get('theme'),
            'mode' : this.props.chatwidget.get('mode'),
            'vid' : this.props.chatwidget.get('vid'),
            'fields' : fields,
            'host': host
        };

        helperFunctions.setSessionStorage('_ttxt','');

        if (this.botPayload) {
            submitData['bpayload'] = this.botPayload;
            this.botPayload = null;
            this.setState({hasBotData:true});
            if (submitData['fields']['Question']) {
                helperFunctions.setSessionStorage('_ttxt',submitData['fields']['Question']);
                submitData['fields']['Question'] = '';
            }
        } else {
            this.setState({hasBotData:false});
        }

        if (this.props.chatwidget.hasIn(['proactive','data','invitation_id']) === true) {
            submitData['invitation_id'] = this.props.chatwidget.getIn(['proactive','data','invitation_id']);
        }

        this.props.dispatch(submitOnlineForm(submitData));

        if (event)
        event.preventDefault();
    }

    handleContentChange(obj) {

        if (this.props.chatwidget.get('processStatus') != 0) {
            return;
        }

        var currentState = this.state;
        currentState[obj.id] = obj.value;
        this.setState(currentState);

        if (obj.id == 'DepartamentID') {

            if (obj.set_default) {
                this.props.dispatch({'type' : 'dep_default', data : obj.value});
            }

            if (obj.subject_id) {
                this.props.dispatch({'type' : 'subject_id', data : obj.subject_id});
            } else {
                this.props.dispatch({'type' : 'subject_id', data : ''});
            }

            if (this.props.chatwidget.getIn(['onlineData','department','departments']).size > 0){
                this.props.chatwidget.getIn(['onlineData','department','departments']).map(dep => {
                    if (dep.get('value') == obj.value) {
                        if (dep.get('online') == false) {
                            this.props.dispatch({'type' : 'dep_default', data : obj.value});
                            this.props.dispatch({'type' : 'onlineStatus', data : false});
                        }

                        // Update online fields settings if different department
                        if (this.props.chatwidget.getIn(['onlineData','dep_forms']) != obj.value) {
                            this.updateOnlineFieldsInit(obj.value);
                        }
                    }
                })
            }
        }

        if (obj.id == 'Question')
        {
            if (this.props.chatwidget.getIn(['proactive','has']) === true &&
                obj.value != '' &&
                this.props.chatwidget.getIn(['chat_ui','proactive_once_typed']) === 1 &&
                this.props.chatwidget.getIn(['chat_ui','custom_html_priority']) === 1
            ) {
                this.props.dispatch({type: 'attr_set', attr : ['chat_ui','custom_html_priority'], data : 0});
                this.customHTMLPriority = true;
            } else if (obj.value == '' && this.customHTMLPriority == true) {
                this.props.dispatch({type: 'attr_set', attr : ['chat_ui','custom_html_priority'], data : 1});
            }
        }

    }

    handleContentChangeCustom(obj) {
        this.props.dispatch({'type' : 'CUSTOM_FIELDS_ITEM', data : {id : obj.field.get('index'), value : obj.value}});
    }

    componentDidMount() {
        helperFunctions.prefillFields(this);
        this.updateOnlineFields();

        if (this.props.botPayload !== null) {
            // This is required because prefill fields can be one of the required
            // If we just submit form instantly it might require one of prefilled fields.
            setTimeout(() => {
                this.setBotPayload(this.props.botPayload);
            },10);
        }

        // Just remove element if it exists
        var elm = document.getElementById('CSChatMessage-tmp');
        if (elm !== null) {
            document.body.removeChild(elm);
        }
    }

    componentWillUnmount() {
        var elm = document.getElementById('CSChatMessage');
        if (elm === null) {
            this.props.setHideMessageField(true);
        } else {
            // Because online component has it's own text area we loose focus once we mount that component
            // We keeps this element focused and just switch focus between elements. So we do not loose keyboard.
            this.props.setHideMessageField(false);
            elm.id = "CSChatMessage-tmp";
            elm.style.cssText = "position:absolute;left:-999px;bottom:0px;";
            document.body.appendChild(elm);
        }
    }

    setBotPayload(params) {
        this.botPayload = params;
        this.handleSubmit();
    }

    updateOnlineFieldsInit(dep_default) {
        // Init offline form with all attributes
        this.props.dispatch(initOnlineForm({
            'department':this.props.chatwidget.get('department'),
            'product':this.props.chatwidget.get('product'),
            'theme' : this.props.chatwidget.get('theme'),
            'mode' : this.props.chatwidget.get('mode'),
            'pvhash' : this.props.chatwidget.get('pvhash'),
            'phash' : this.props.chatwidget.get('phash'),
            'bot_id' : this.props.chatwidget.get('bot_id'),
            'trigger_id' : this.props.chatwidget.get('trigger_id'),
            'vid' : this.props.chatwidget.get('vid'),
            'dep_default' : (dep_default || this.props.chatwidget.get('departmentDefault') || 0),
            'online' : 1,
            'chat_ui' : this.props.chatwidget.get('chat_ui'),
        }));
    }

    updateOnlineFields() {
        if (this.props.chatwidget.getIn(['onlineData','fetched']) === false) {
            this.updateOnlineFieldsInit();
        }
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        this.updateOnlineFields();
        if (document.getElementById('id-container-fluid')) {
            helperFunctions.sendMessageParent('widgetHeight', [{'height' : document.getElementById('id-container-fluid').offsetHeight+40}]);
        }

        if (this.props.chatwidget.get('processStatus') === 1 && prevProps.chatwidget.get('processStatus') !== 1) {
            var messagesScroll = document.getElementById('messagesBlock');
            if (messagesScroll !== null) {
                this.props.setMessages(messagesScroll.innerHTML);
            }

            var profileBody = document.getElementById('lhc-profile-body');
            if (profileBody !== null) {
                this.props.setProfile(profileBody.innerHTML);
            }
        }

        let needFocus = false;
        if (this.apiLoaded === false && this.props.chatwidget.get('api_data') !== null) {
            this.apiLoaded = true;
            this.setState({...this.state, ...this.props.chatwidget.get('api_data')});
            needFocus = true;
        }

        // Auto focus if it's show operation
        if ((needFocus === true || (this.props.chatwidget.get('isMobile') == false && prevProps.chatwidget.get('shown') === false && this.props.chatwidget.get('shown') === true)) && this.props.chatwidget.get('mode') == 'widget' && this.textMessageRef.current) {
            this.textMessageRef.current.focus();
            this.scrollBottom();
        }

        // Rest API data was fetched we can scroll to bottomnow
        if (this.props.chatwidget.getIn(['onlineData','fetched']) === true && prevProps.chatwidget.getIn(['onlineData','fetched']) === false) {
            this.props.chatwidget.hasIn(['chat_ui','uprev']) && helperFunctions.emitEvent('play_sound', [{'type' : 'new_message','sound_on' : (this.props.chatwidget.getIn(['usersettings','soundOn']) === true), 'widget_open' : ((this.props.chatwidget.get('shown') && this.props.chatwidget.get('mode') == 'widget') || document.hasFocus())}]);
            this.scrollBottom();
        }

        // If parent pages changes default department we have to reload
        if (this.props.chatwidget.get('departmentDefault') !== prevProps.chatwidget.get('departmentDefault')) {
            this.setState({'DepartamentID': this.props.chatwidget.get('departmentDefault')});
            var elm = document.getElementById('id-department-field');
            if (elm !== null) {
                elm.value = this.props.chatwidget.get('departmentDefault');
            }
            this.updateOnlineFieldsInit();
        }
    }

    scrollBottom() {
        if (this.messagesAreaRef.current) {
            this.messagesAreaRef.current.scrollTop = this.messagesAreaRef.current.scrollHeight + 1000;
            setTimeout(() => {
                if (this.messagesAreaRef.current) {
                    this.messagesAreaRef.current.scrollTop = this.messagesAreaRef.current.scrollHeight + 1000;
                }
            },450);
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

            if (props.chatwidget.get('subject_id') != '') {
                fields['subject_id'] = props.chatwidget.get('subject_id');
            }

            if (props.chatwidget.get('trigger_id') != '') {
                fields['trigger_id'] = props.chatwidget.get('trigger_id');
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

            if (props.botPayload !== null) {
                submitData['bpayload'] = props.botPayload;
            }

            if (props.chatwidget.hasIn(['proactive','data','invitation_id']) === true) {
                submitData['invitation_id'] = props.chatwidget.getIn(['proactive','data','invitation_id']);
            }

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

    if (this.props.chatwidget.getIn(['onlineData','fields']).size > 0 && !(this.props.chatwidget.hasIn(['chat_ui','show_messages_box']) && this.props.chatwidget.getIn(['onlineData','fields_visible']) == 1 && this.props.chatwidget.getIn(['customData','fields']).size == 0)) {
        var mappedFields = this.props.chatwidget.getIn(['onlineData','fields']).map(field =><ChatField chatUI={this.props.chatwidget.get('chat_ui')} key={field.get('identifier')} isInvalid={this.props.chatwidget.hasIn(['validationErrors',field.get('identifier')])} defaultValueField={this.state[field.get('name')] || field.get('value')} attrPrefill={{'attr_prefill_admin' : this.props.chatwidget.get('attr_prefill_admin'), 'attr_prefill' : this.props.chatwidget.get('attr_prefill')}} onChangeContent={this.handleContentChange} field={field} />);
    } else {
        var mappedFields = "";
    }

    var hasVisibleCustomFields = false;
    var mappedFieldsCustom = "";

    if (this.props.chatwidget.getIn(['customData','fields']).size > 0) {
        this.props.chatwidget.getIn(['customData','fields']).map(field => hasVisibleCustomFields = !(field.has('type') && field.get('type') === 'hidden') ? true : hasVisibleCustomFields);
        if (hasVisibleCustomFields == true) {
            mappedFieldsCustom = this.props.chatwidget.getIn(['customData','fields']).map(field =><ChatField chatUI={this.props.chatwidget.get('chat_ui')} key={field.get('identifier')} isInvalid={this.props.chatwidget.hasIn(['validationErrors',field.get('identifier')])} defaultValueField={field.get('value')} onChangeContent={this.handleContentChangeCustom} field={field} />);
        }
    }

    if (this.props.chatwidget.hasIn(['onlineData','paid','error']) && this.props.chatwidget.getIn(['onlineData','paid','error'])) {
        return <p className="p-2">{this.props.chatwidget.getIn(['onlineData','paid','message'])}</p>
    }

    if (this.props.chatwidget.get('processStatus') == 0 || this.props.chatwidget.get('processStatus') == 1) {
            if (this.props.chatwidget.hasIn(['chat_ui','show_messages_box']) && this.props.chatwidget.getIn(['onlineData','department','departments']).size <= 1 && this.props.chatwidget.getIn(['onlineData','fields_visible']) <= 1 && (this.props.chatwidget.getIn(['customData','fields']).size == 0 || hasVisibleCustomFields === false)) {

                var classMessageInput = "ps-0 no-outline form-control rounded-0 form-control rounded-start-0 rounded-end-0 border-0 " + (this.props.chatwidget.get('shown') === true && this.textMessageRef.current && (/\r|\n/.exec(this.state.Question) || (this.state.Question.length > this.textMessageRef.current.offsetWidth/8.6)) ? 'msg-two-line' : 'msg-one-line');

                var msg_expand = "flex-grow-1 overflow-scroll position-relative";
                var bottom_messages = "bottom-message px-1";

                if (this.props.chatwidget.hasIn(['chat_ui','msg_expand']) && this.props.chatwidget.get('mode') == 'embed') {
                    msg_expand = "overflow-scroll position-relative";
                    bottom_messages += " position-relative";
                }

                if (this.props.chatwidget.getIn(['chat_ui','disabled'])) {
                    return <ChatAbort closeText={t('button.close')} full_height={true} close={(e) => this.props.dispatch(minimizeWidget(true))} as_html={true} text={this.props.chatwidget.getIn(['chat_ui','disabled'])} />;
                }

                return (
                    <React.Fragment>

                        {this.state.showBBCode && <ChatModal showModal={this.state.showBBCode} insertText={this.insertText} toggle={this.toggleModal} dataUrl={"/chat/bbcodeinsert?react=1"} />}

                        {this.state.changeLanguage && <ChatModal showModal={this.state.changeLanguage} setLanguage={this.setLanguageAction} toggle={this.changeLanguage} dataUrl={"/widgetrestapi/chooselanguage"} />}

                        {this.props.chatwidget.hasIn(['validationErrors','blocked_user']) && <ChatAbort closeText={t('button.close')} as_html={true} close={(e) => this.props.dispatch(minimizeWidget(true))} text={this.props.chatwidget.getIn(['validationErrors','blocked_user'])} />}

                        {
                            (this.props.chatwidget.getIn(['proactive','has']) === true && !this.props.chatwidget.hasIn(['proactive','data','std_header'])  && <ChatInvitationMessage mode='profile_only' invitation={this.props.chatwidget.getIn(['proactive','data'])} />)
                            ||
                            ((this.props.chatwidget.hasIn(['chat_ui','pre_chat_html']) || (this.props.chatwidget.hasIn(['chat_ui','operator_profile']) && this.props.chatwidget.getIn(['chat_ui','operator_profile']) != '')) && <div id="lhc-profile-body"><div id="chat-status-container" className="p-2 border-bottom" dangerouslySetInnerHTML={{__html:(this.props.chatwidget.hasIn(['chat_ui','pre_chat_html']) ? this.props.chatwidget.getIn(['chat_ui','pre_chat_html']) : '') + (this.props.chatwidget.hasIn(['chat_ui','operator_profile']) ? this.props.chatwidget.getIn(['chat_ui','operator_profile']) : '')}}></div></div>)
                        }

                        <div className={msg_expand} id="messagesBlock">
                            <div className={bottom_messages} id="messages-scroll" ref={this.messagesAreaRef}>
                                {(this.props.chatwidget.getIn(['proactive','has']) === true && !this.props.chatwidget.getIn(['chat_ui','custom_html_priority'])) && <ChatInvitationMessage mode="message" setBotPayload={this.setBotPayload} invitation={this.props.chatwidget.getIn(['proactive','data'])} />}

                                {!this.props.chatwidget.getIn(['proactive','has']) && this.props.chatwidget.hasIn(['chat_ui','cmmsg_widget']) && <ChatBotIntroMessage setTextAreaHidden={(e) => {this.state.textAreaHidden === false && this.setState({textAreaHidden:true});}} printButton={this.props.chatwidget.getIn(['chat_ui','print_btn_msg'])} processStatus={this.props.chatwidget.get('processStatus')} setBotPayload={this.setBotPayload} content={this.props.chatwidget.getIn(['chat_ui','cmmsg_widget'])} />}

                                {this.props.chatwidget.get('processStatus') == 1 && this.state.Question != '' && <div data-op-id="0" className="message-row response msg-to-store">
                                    {this.props.chatwidget.hasIn(['chat_ui','show_ts']) && !this.props.chatwidget.hasIn(['chat_ui','show_ts_below']) && <div className="msg-date">&nbsp;</div>}
                                    <span title="" className="usr-tit vis-tit"><i title={t('start_chat.visitor')} className="material-icons chat-operators mi-fs15 me-0">&#xf104;</i><span className="user-nick-title">{t('start_chat.visitor')}</span></span>
                                    <div className="msg-body">{this.state.Question}</div>
                                    {this.props.chatwidget.hasIn(['chat_ui','show_ts']) && this.props.chatwidget.hasIn(['chat_ui','show_ts_below']) && <div className="msg-date">&nbsp;</div>}
                                </div>}

                            </div>
                        </div>

                        {(!this.props.chatwidget.getIn(['proactive','has']) || this.props.chatwidget.getIn(['chat_ui','custom_html_priority']) === 1) && this.props.chatwidget.hasIn(['chat_ui','custom_html_widget']) && <div className={"custom-html-container "+(this.state.Question != "" ? "visitor-started-type" : "")} dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','custom_html_widget'])}}></div>}

                        {(this.props.chatwidget.getIn(['onlineData','fields_visible']) == 1 || (this.props.chatwidget.getIn(['onlineData','fields_visible']) == 0 && !this.props.chatwidget.hasIn(['chat_ui','hstr_btn']))) && this.state.textAreaHidden === false && <div className="d-flex flex-row border-top position-relative message-send-area">

                            {(this.props.chatwidget.hasIn(['validationErrors','question'])) && <div id="id-operator-typing" className="bg-white ps-1">{this.props.chatwidget.getIn(['validationErrors','question'])}</div>}

                            {this.props.chatwidget.getIn(['onlineData','fields_visible']) == 1 && <React.Fragment>
                                {(!this.props.chatwidget.hasIn(['chat_ui','bbc_btnh']) || this.props.chatwidget.hasIn(['chat_ui','lng_btnh'])) && <ChatStartOptions bbEnabled={!this.props.chatwidget.hasIn(['chat_ui','bbc_btnh'])} langEnabled={this.props.chatwidget.hasIn(['chat_ui','lng_btnh'])} changeLanguage={this.changeLanguage} toggleModal={this.toggleModal} />}

                                <div className="mx-auto w-100">
                                    <textarea autoFocus={this.props.chatwidget.get('isMobile') == false && this.props.chatwidget.get('mode') == 'widget' && this.props.chatwidget.get('shown') === true} onFocus={this.moveCaretAtEnd} maxLength={this.props.chatwidget.getIn(['chat_ui','max_length'])} aria-label="Type your message here..." id="CSChatMessage" value={this.props.chatwidget.get('processStatus') == 1 && this.state.hasBotData === false ? '' : this.state.Question} placeholder={this.props.chatwidget.hasIn(['chat_ui','placeholder_message']) ? this.props.chatwidget.getIn(['chat_ui','placeholder_message']) : t('chat.type_here')} onKeyDown={this.enterKeyDown} onChange={(e) => this.handleContentChange({'id' : 'Question' ,'value' : e.target.value})} ref={this.textMessageRef} rows="1" className={classMessageInput} />
                                </div>
                                <div className="disable-select" id="send-button-wrapper">
                                    <div className="user-chatwidget-buttons pt-2" id="ChatSendButtonContainer">
                                        {this.props.chatwidget.get('processStatus') != 1 && <a tabIndex="0" onKeyPress={(e) => { e.key === "Enter" ? this.handleSubmit() : '' }} onClick={this.handleSubmit} title={t('button.start_chat')}>
                                            <i className={"send-icon material-icons settings" + (this.state.Question.length == 0 ? ' text-muted-light' : ' text-muted')}>&#xf107;</i>
                                        </a>}

                                        {this.props.chatwidget.get('processStatus') == 1 && <i className="in-progress-icon material-icons text-muted settings me-0">&#xf113;</i>}

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
                            ((this.props.chatwidget.hasIn(['chat_ui','pre_chat_html']) || (this.props.chatwidget.hasIn(['chat_ui','operator_profile']) && this.props.chatwidget.getIn(['chat_ui','operator_profile']) != '')) && <div id="lhc-profile-body"><div id="chat-status-container" className={"p-2"+(this.props.chatwidget.hasIn(['chat_ui','np_border']) ? '' : ' border-bottom')} dangerouslySetInnerHTML={{__html:(this.props.chatwidget.hasIn(['chat_ui','pre_chat_html']) ? this.props.chatwidget.getIn(['chat_ui','pre_chat_html']) : '') + (this.props.chatwidget.hasIn(['chat_ui','operator_profile']) ? this.props.chatwidget.getIn(['chat_ui','operator_profile']) : '')}}></div></div>)
                    }
                    <div className="container-fluid">

                        <ChatErrorList errors={this.props.chatwidget.get('validationErrors')} />

                        {!this.props.chatwidget.getIn(['proactive','has']) && this.props.chatwidget.hasIn(['chat_ui','custom_html_widget']) && <div className="custom-html-container" dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','custom_html_widget'])}}></div>}

                        <form onSubmit={this.handleSubmit}>
                            <div className="row pt-2">
                                {mappedFields}
                                {mappedFieldsCustom}
                                {this.props.chatwidget.hasIn(['onlineData','department']) && <ChatDepartment defaultValueField={this.state['DepartamentID']} setDefaultValue={this.props.chatwidget.get('departmentDefault')} onChangeContent={this.handleContentChange} isInvalidProduct={this.props.chatwidget.hasIn(['validationErrors','ProductID'])} isInvalid={this.props.chatwidget.hasIn(['validationErrors','department'])} departments={this.props.chatwidget.getIn(['onlineData','department'])} />}
                            </div>
                            {(!this.props.chatwidget.hasIn(['chat_ui','hstr_btn']) || mappedFieldsCustom !== "" || mappedFields !== "" || this.props.chatwidget.getIn(['proactive','has']) === true) && <div className="row">
                                <div className="col-12 pb-3">
                                    <button disabled={this.props.chatwidget.get('processStatus') == 1} type="submit" className="btn btn-secondary btn-sm">{this.props.chatwidget.getIn(['chat_ui','custom_start_button']) || t('button.start_chat')}</button>
                                </div>
                            </div>}
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
