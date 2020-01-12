import React, { Component } from 'react';
import { connect } from "react-redux";

import ChatField from './ChatField';
import ChatErrorList from './ChatErrorList';
import ChatDepartment from './ChatDepartment';
import ChatModal from './ChatModal';
import ChatStartOptions from './ChatStartOptions';

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

        // Init offline form with all attributes
        this.props.dispatch(initOnlineForm({
            'department':this.props.chatwidget.get('department'),
            'theme' : this.props.chatwidget.get('theme'),
            'mode' : this.props.chatwidget.get('mode'),
            'online' : 1
        }));

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

    static getTimeZone() {

        var today = new Date();

        let stdTimezoneOffset = function() {
            var jan = new Date(today.getFullYear(), 0, 1);
            var jul = new Date(today.getFullYear(), 6, 1);
            return Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
        };

        var dst = function() {
            return today.getTimezoneOffset() < stdTimezoneOffset();
        };

        var timeZoneOffset = 0;

        if (dst()) {
            timeZoneOffset = today.getTimezoneOffset();
        } else {
            timeZoneOffset = today.getTimezoneOffset()-60;
        };

        return (((timeZoneOffset)/60) * -1);
    }

    static getCustomFieldsSubmit(customFields)
    {
        if (customFields.size > 0 ) {
            let customItems = {'name_items' : [],'values_req' : [], 'value_items' : [], 'value_types' : [], 'encattr' : [], 'value_show' : []};
            customFields.forEach(field => {
                customItems['value_items'].push(field.get('value'));
                customItems['name_items'].push(field.get('name'));
                customItems['values_req'].push(field.get('required') === true ? 't' : 'f');
                customItems['encattr'].push(field.get('encrypted') === true ? 't' : '');
                customItems['value_types'].push(field.get('type'));
                customItems['value_show'].push(field.get('show'));
            })
            return customItems;
        }

        return null;
    }

    handleSubmit(event) {

        var fields = this.state;
        fields['jsvar'] = this.props.chatwidget.get('jsVars');
        fields['captcha_' + this.props.chatwidget.getIn(['captcha','hash'])] = this.props.chatwidget.getIn(['captcha','ts']);
        fields['tscaptcha'] = this.props.chatwidget.getIn(['captcha','ts']);
        fields['user_timezone'] = StartChat.getTimeZone();
        fields['URLRefer'] = window.location.href.substring(window.location.protocol.length);
        fields['r'] = this.props.chatwidget.get('ses_ref');

        if (this.props.chatwidget.get('bot_id') != '') {
            fields['bot_id'] = this.props.chatwidget.get('bot_id');
        }

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

        this.props.dispatch(submitOnlineForm(submitData));

        event.preventDefault();
    }

    handleContentChange(obj) {
        var currentState = this.state;
        currentState[obj.id] = obj.value;
        this.setState(currentState);
    }

    handleContentChangeCustom(obj) {
        this.props.dispatch({'type' : 'CUSTOM_FIELDS_ITEM', data : {id : obj.field.get('index'), value : obj.value}});
    }

    static prefillFields(inst) {
        const prefillOptions = inst.props.chatwidget.get('attr_prefill');
        if (prefillOptions.length > 0) {
            prefillOptions.forEach((item) => {
                inst.setState(item);
            });
        }
    }

    componentDidMount() {
         StartChat.prefillFields(this);
    }

    static getDerivedStateFromProps(props, state) {

        if (props.chatwidget.getIn(['chat_ui','auto_start']) && props.chatwidget.get('processStatus') == 0) {
            let fields = {'jsvar' : props.chatwidget.get('jsVars')};
            fields['captcha_' + props.chatwidget.getIn(['captcha','hash'])] = props.chatwidget.getIn(['captcha','ts']);
            fields['tscaptcha'] = props.chatwidget.getIn(['captcha','ts']);
            fields['user_timezone'] = StartChat.getTimeZone();
            fields['URLRefer'] = window.location.href.substring(window.location.protocol.length);
            fields['r'] = props.chatwidget.get('ses_ref');

            const customFields = StartChat.getCustomFieldsSubmit(props.chatwidget.getIn(['customData','fields']));
            if (customFields !== null) {
                fields = {...fields, ...customFields};
            }

            props.dispatch(submitOnlineForm({
                'department':props.chatwidget.get('department'),
                'theme' : props.chatwidget.get('theme'),
                'mode' : props.chatwidget.get('mode'),
                'online' : 1,
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

    if (this.props.chatwidget.getIn(['onlineData','fetched']) === false || this.props.chatwidget.getIn(['chat_ui','auto_start']) === true)
    {
        return null;
    }

    if (this.props.chatwidget.get('onlineData').has('fields') && !(this.props.chatwidget.hasIn(['chat_ui','show_messages_box']) && this.props.chatwidget.getIn(['onlineData','fields']).size == 1)) {
        var mappedFields = this.props.chatwidget.getIn(['onlineData','fields']).map(field =><ChatField chatUI={this.props.chatwidget.get('chat_ui')} key={field.get('identifier')} isInvalid={this.props.chatwidget.hasIn(['validationErrors',field.get('identifier')])} defaultValueField={this.state[field.get('name')] || field.get('value')} onChangeContent={this.handleContentChange} field={field} />);
    } else {
        var mappedFields = "";
    }

    if (this.props.chatwidget.getIn(['customData','fields']).size > 0) {
        var mappedFieldsCustom = this.props.chatwidget.getIn(['customData','fields']).map(field =><ChatField chatUI={this.props.chatwidget.get('chat_ui')} key={field.get('identifier')} isInvalid={this.props.chatwidget.hasIn(['validationErrors',field.get('identifier')])} defaultValueField={field.get('value')} onChangeContent={this.handleContentChangeCustom} field={field} />);
    } else {
        var mappedFieldsCustom = "";
    }

    if (this.props.chatwidget.get('processStatus') == 0 || this.props.chatwidget.get('processStatus') == 1) {
            if (this.props.chatwidget.hasIn(['chat_ui','show_messages_box']) && this.props.chatwidget.getIn(['onlineData','fields']).size == 1 && this.props.chatwidget.getIn(['customData','fields']).size == 0) {
                return (
                    <React.Fragment>

                        {this.state.showBBCode && <ChatModal showModal={this.state.showBBCode} insertText={this.insertText} toggle={this.toggleModal} dataUrl={"/chat/bbcodeinsert?react=1"} />}

                        <div className="flex-grow-1 overflow-scroll position-relative" id="messagesBlock">
                            <div className="bottom-message pl-1 pr-1" id="messages-scroll">
                            </div>
                        </div>

                        <div className="d-flex flex-row border-top position-relative message-send-area">
                            <ChatStartOptions toggleModal={this.toggleModal} />
                            <div className="mx-auto pb-1 w-100">
                                <textarea aria-label="Type your message here..." id="CSChatMessage" value={this.state.Question} placeholder={this.props.chatwidget.hasIn(['chat_ui','placeholder_message']) ? this.props.chatwidget.getIn(['chat_ui','placeholder_message']) : "Type your message here..."} onKeyDown={this.enterKeyDown} onChange={(e) => this.handleContentChange({'id' : 'Question' ,'value' : e.target.value})} ref={this.textMessageRef} rows="1" className="pl-0 no-outline form-control rounded-0 form-control border-left-0 border-right-0 border-0" />
                            </div>
                            <div className="disable-select">
                                <div className="user-chatwidget-buttons" id="ChatSendButtonContainer">
                                    <a href="#" onClick={this.handleSubmit} title="Send">
                                        <i className="material-icons text-muted settings">send</i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </React.Fragment>
                )
            } else {
                return (
                    <div className="container-fluid" id="id-container-fluid">
                        <ChatErrorList errors={this.props.chatwidget.get('validationErrors')} />
                        <form onSubmit={this.handleSubmit}>
                            <div className="row pt-2">
                                {mappedFields}
                                {mappedFieldsCustom}
                                {this.props.chatwidget.hasIn(['onlineData','department']) && <ChatDepartment defaultValueField={this.state['DepartamentID']} onChangeContent={this.handleContentChange} isInvalid={this.props.chatwidget.hasIn(['validationErrors','department'])} departments={this.props.chatwidget.getIn(['onlineData','department'])} />}
                            </div>
                            <div className="row">
                                <div className="col-12">
                                    <button disabled={this.props.chatwidget.get('processStatus') == 1} type="submit" className="btn btn-secondary btn-sm">Start Chat</button>
                                </div>
                            </div>
                        </form>
                    </div>
                )
            }

        } else if (this.props.chatwidget.get('processStatus') == 2) {
            return (
                <div className="container-fluid" id="id-container-fluid">
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

export default StartChat;
