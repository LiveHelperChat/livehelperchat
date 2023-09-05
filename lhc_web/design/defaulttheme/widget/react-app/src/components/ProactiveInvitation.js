import React, { Component } from 'react';
import { connect } from "react-redux";
import { hideInvitation } from "../actions/chatActions"
import { helperFunctions } from "../lib/helperFunctions";
import ChatBotIntroMessage from './ChatBotIntroMessage';

@connect((store) => {
    return {
        chatwidget: store.chatwidget
    };
})

class ProactiveInvitation extends Component {

    state = {
        shown: false
    }

    constructor(props) {
        super(props);
        this.hideInvitation = this.hideInvitation.bind(this);
        this.fullInvitation = this.fullInvitation.bind(this);
        this.setBotPayload = this.setBotPayload.bind(this);
        this.expireTimeout = null;
    }

    componentDidMount() {
        helperFunctions.sendMessageParent('showInvitation', [{name: this.props.chatwidget.getIn(['proactive','data','invitation_name'])}]);

        if (this.props.chatwidget.getIn(['proactive','data','play_sound'])) {
            helperFunctions.emitEvent('play_sound', [{'type' : 'new_invitation', 'sound_on' : (this.props.chatwidget.getIn(['proactive','data','play_sound']) === true), 'widget_open' : ((this.props.chatwidget.get('shown') && this.props.chatwidget.get('mode') == 'widget') || document.hasFocus())}]);
        }

        if (!(this.props.chatwidget.hasIn(['proactive','data','full_widget']) && !this.props.chatwidget.get('isMobile'))) {
            if (document.getElementById('id-invitation-height')) {
                setTimeout(()=> {
                    if (document.getElementById('id-invitation-height')) {
                        var heightSet = document.getElementById('id-invitation-height').offsetHeight + 20;
                        helperFunctions.sendMessageParent('hideAction', []);
                        helperFunctions.sendMessageParent('widgetHeight', [{
                            'force_width' : (this.props.chatwidget.hasIn(['proactive','data','message_width']) ? this.props.chatwidget.getIn(['proactive','data','message_width']) + 40 : 240),
                            'force_height' : heightSet,
                            'force_bottom' : (this.props.chatwidget.hasIn(['proactive','data','message_bottom']) ? this.props.chatwidget.getIn(['proactive','data','message_bottom']) : 75),
                            'force_right' : (this.props.chatwidget.hasIn(['proactive','data','message_right']) ? this.props.chatwidget.getIn(['proactive','data','message_right']) : 75),
                        }]);
                        setTimeout(() => {
                            helperFunctions.sendMessageParent('showAction', []);
                            this.setState({shown : true});
                        },100);
                    }
                 }, 50);
            }
        }

        if (this.props.chatwidget.hasIn(['proactive','data','inv_expires'])) {
            this.expireTimeout = setTimeout(() => {
                this.props.dispatch(hideInvitation(true));
            },this.props.chatwidget.getIn(['proactive','data','inv_expires'])*1000);
        }

        if (this.props.chatwidget.hasIn(['proactive','data','on_click'])) {
            this.appendScript(this.props.chatwidget.getIn(['proactive','data','on_click','src']), this.props.chatwidget.getIn(['proactive','data','on_click','id']));
        }

        if (this.props.chatwidget.hasIn(['proactive','data','site_css'])) {
            this.appendCSS(this.props.chatwidget.getIn(['proactive','data','site_css']), this.props.chatwidget.getIn(['proactive','data','site_css_id']));
        }
    }

    appendScript(src, id) {
        const script = document.createElement('script');
        script.src = src;
        script.id = id;
        script.async = true;
        document.body.appendChild(script);
    }

    appendCSS(styleContent, id) {
        const style = document.createElement('style');
        style.innerHTML = styleContent;
        style.id = id;
        document.body.appendChild(style);
    }

    componentWillUnmount() {
        clearTimeout(this.expireTimeout);
        helperFunctions.sendMessageParent('widgetHeight', [{'reset_height' : true}]);
        if (this.props.chatwidget.hasIn(['proactive','data','on_click'])) {
            var EObj = null;
            (EObj = document.getElementById(this.props.chatwidget.getIn(['proactive','data','on_click','id']))) ? EObj.parentNode.removeChild(EObj) : false;
        }

        if (this.props.chatwidget.hasIn(['proactive','data','site_css_id'])) {
            var EObj = null;
            (EObj = document.getElementById(this.props.chatwidget.getIn(['proactive','data','site_css_id']))) ? EObj.parentNode.removeChild(EObj) : false;
        }
    }

    hideInvitation(e) {
        this.props.dispatch(hideInvitation( this.props.chatwidget.hasIn(['proactive','data','hide_on_open']) ));
        e.preventDefault();
        e.stopPropagation();
    }

    fullInvitation() {
        if (this.props.chatwidget.hasIn(['proactive','data','hide_on_open'])){
            this.props.dispatch(hideInvitation(true, true));
            if (this.props.chatwidget.hasIn(['proactive','data','on_click'])) {
                window['callback_'+this.props.chatwidget.getIn(['proactive','data','on_click','id'])]();
            }
        } else {
            helperFunctions.sendMessageParentDirect('hideInvitation', [{'full' : true, name: this.props.chatwidget.getIn(['proactive','data','invitation_name'])}]);
            this.props.dispatch({
                'type' : 'FULL_INVITATION'
            });
        }
    }

    setBotPayload(params) {
        // Set payload parameter
        this.props.setBotPayload(params);

        // Set auto start
        // This way it's faster just user might see blank screen while submiting
        // So just decided to show full invitation and submit in the background.
        /*this.props.dispatch({
            'type' : 'attr_set',
            'attr' : ['chat_ui','auto_start'],
            'data' : true,
        });*/

        // Show full invitation show auto submit will work
        this.fullInvitation();
    }

    render() {

        if (this.props.chatwidget.hasIn(['proactive','data','full_widget']) && !this.props.chatwidget.get('isMobile')) {
            this.fullInvitation();
        }

        let className = "";
        if (this.state.shown === false) {
            className += " invisible";
        } else {
            className += " fade-in";
        }

        return (
                <div id="id-invitation-height" className={className} >

                    {this.props.chatwidget.hasIn(['proactive','data','close_above_msg']) && <div className="text-right"><button title="Close" onClick={(e) => this.hideInvitation(e)} id="invitation-close-btn" className="btn btn-sm rounded"><i className="material-icons me-0">&#xf10a;</i></button></div>}

                    <div className="p-2 pointer clearfix proactive-need-help" id="proactive-wrapper" style={{width:(this.props.chatwidget.hasIn(['proactive','data','message_width']) ? this.props.chatwidget.getIn(['proactive','data','message_width']) : 200)}} onClick={this.fullInvitation}>

                        {!this.props.chatwidget.hasIn(['proactive','data','close_above_msg']) && <button title="Close" onClick={(e) => this.hideInvitation(e)} id="invitation-close-btn" className="float-end btn btn-sm rounded"><i className="material-icons me-0">&#xf10a;</i></button>}

                        {this.props.chatwidget.hasIn(['proactive','data','photo_left_column']) && this.props.chatwidget.getIn(['proactive','data','photo']) && <div className="d-flex">

                            <div className="proactive-image">
                                <img width="30" alt={this.props.chatwidget.getIn(['proactive','data','name_support']) || this.props.chatwidget.getIn(['proactive','data','extra_profile'])} title={this.props.chatwidget.getIn(['proactive','data','name_support']) || this.props.chatwidget.getIn(['proactive','data','extra_profile'])} className="me-2 rounded" src={this.props.chatwidget.getIn(['proactive','data','photo'])} />
                            </div>

                            <div className="flex-grow-1">
                                {!this.props.chatwidget.hasIn(['proactive','data','hide_op_name']) && <div className="fs14">
                                    <b>{this.props.chatwidget.getIn(['proactive','data','name_support']) || this.props.chatwidget.getIn(['proactive','data','extra_profile'])}</b>
                                </div>}
                                <div id="inv-msg-wrapper">
                                    <p className="fs13 mb-0 inv-msg-cnt" dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['proactive','data','message'])}}></p>
                                    {this.props.chatwidget.hasIn(['proactive','data','bot_intro']) && <ChatBotIntroMessage printButton={false} setBotPayload={this.setBotPayload} content={this.props.chatwidget.getIn(['proactive','data','message_full'])} />}
                                </div>
                            </div>

                        </div>}

                        {(!this.props.chatwidget.hasIn(['proactive', 'data', 'photo_left_column']) || !this.props.chatwidget.getIn(['proactive', 'data', 'photo'])) &&
                            <div>
                                <div className="fs14">
                                    {this.props.chatwidget.getIn(['proactive', 'data', 'photo']) && <img width="30" height="30"
                                                                                                     alt={this.props.chatwidget.getIn(['proactive', 'data', 'name_support']) || this.props.chatwidget.getIn(['proactive', 'data', 'extra_profile'])}
                                                                                                     title={this.props.chatwidget.getIn(['proactive', 'data', 'name_support']) || this.props.chatwidget.getIn(['proactive', 'data', 'extra_profile'])}
                                                                                                     className="me-2 rounded"
                                                                                                     src={this.props.chatwidget.getIn(['proactive', 'data', 'photo'])}/>}

                                    {!this.props.chatwidget.hasIn(['proactive','data','hide_op_name']) && <b>{this.props.chatwidget.getIn(['proactive', 'data', 'name_support']) || this.props.chatwidget.getIn(['proactive', 'data', 'extra_profile'])}</b>}
                                </div>
                                <div id="inv-msg-wrapper">
                                    <p className="fs13 mb-0 inv-msg-cnt" dangerouslySetInnerHTML={{__html: this.props.chatwidget.getIn(['proactive', 'data', 'message'])}}></p>
                                    {this.props.chatwidget.hasIn(['proactive','data','bot_intro']) && <ChatBotIntroMessage printButton={false} setBotPayload={this.setBotPayload} content={this.props.chatwidget.getIn(['proactive', 'data', 'message_full'])} />}
                                </div>
                            </div>
                        }


                    </div>
                </div>
        );
    }
}

export default ProactiveInvitation;
