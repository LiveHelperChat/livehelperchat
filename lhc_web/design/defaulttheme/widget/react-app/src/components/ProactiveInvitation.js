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
    }

    componentDidMount() {
        helperFunctions.sendMessageParent('showInvitation', []);

        if (this.props.chatwidget.getIn(['proactive','data','play_sound'])) {
            helperFunctions.emitEvent('play_sound', [{'type' : 'new_invitation', 'sound_on' : (this.props.chatwidget.getIn(['proactive','data','play_sound']) === true), 'widget_open' : ((this.props.chatwidget.get('shown') && this.props.chatwidget.get('mode') == 'widget') || document.hasFocus())}]);
        }

        if (document.getElementById('id-invitation-height')) {
            setTimeout(()=> {
                console.log(document.getElementById('id-invitation-height').offsetHeight);
                helperFunctions.sendMessageParent('widgetHeight', [{
                    'force_width' : (this.props.chatwidget.hasIn(['proactive','data','message_width']) ? this.props.chatwidget.getIn(['proactive','data','message_width']) + 40 : 240),
                    'force_height' : document.getElementById('id-invitation-height').offsetHeight + 20}]);
                this.setState({shown : true});
             }, 50);
        }
    }

    componentWillUnmount() {
        helperFunctions.sendMessageParent('widgetHeight', [{'reset_height' : true}]);
    }

    hideInvitation(e) {
        this.props.dispatch(hideInvitation());
        e.preventDefault();
        e.stopPropagation();
    }

    fullInvitation() {
        helperFunctions.sendMessageParentDirect('hideInvitation', [{'full' : true}]);
        this.props.dispatch({
            'type' : 'FULL_INVITATION'
        });
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

        if (this.props.chatwidget.hasIn(['proactive','data','full_widget'])) {
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

                    {this.props.chatwidget.hasIn(['proactive','data','close_above_msg']) && <div className="text-right"><button title="Close" onClick={(e) => this.hideInvitation(e)} id="invitation-close-btn" className="btn btn-sm rounded"><i className="material-icons mr-0">&#xf10a;</i></button></div>}

                    <div className="p-2 pointer clearfix proactive-need-help" id="proactive-wrapper" style={{width:(this.props.chatwidget.hasIn(['proactive','data','message_width']) ? this.props.chatwidget.getIn(['proactive','data','message_width']) : 200)}} onClick={this.fullInvitation}>

                        {!this.props.chatwidget.hasIn(['proactive','data','close_above_msg']) && <button title="Close" onClick={(e) => this.hideInvitation(e)} id="invitation-close-btn" className="float-right btn btn-sm rounded"><i className="material-icons mr-0">&#xf10a;</i></button>}

                        {this.props.chatwidget.hasIn(['proactive','data','photo_left_column']) && this.props.chatwidget.getIn(['proactive','data','photo']) && <div className="d-flex">

                            <div className="proactive-image">
                                <img width="30" alt={this.props.chatwidget.getIn(['proactive','data','name_support']) || this.props.chatwidget.getIn(['proactive','data','extra_profile'])} title={this.props.chatwidget.getIn(['proactive','data','name_support']) || this.props.chatwidget.getIn(['proactive','data','extra_profile'])} className="mr-2 rounded" src={this.props.chatwidget.getIn(['proactive','data','photo'])} />
                            </div>

                            <div className="flex-grow-1">
                                
                                {!this.props.chatwidget.hasIn(['proactive','data','hide_op_name']) && <div className="fs14">
                                    <b>{this.props.chatwidget.getIn(['proactive','data','name_support']) || this.props.chatwidget.getIn(['proactive','data','extra_profile'])}</b>
                                </div>}
                                
                                <p className="fs13 mb-0 inv-msg-cnt" dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['proactive','data','message'])}}></p>
                                {this.props.chatwidget.hasIn(['proactive','data','bot_intro']) && <ChatBotIntroMessage setBotPayload={this.setBotPayload} content={this.props.chatwidget.getIn(['proactive','data','message_full'])} />}
                            </div>

                        </div>}

                        {(!this.props.chatwidget.hasIn(['proactive', 'data', 'photo_left_column']) || !this.props.chatwidget.getIn(['proactive', 'data', 'photo'])) &&
                            <div>
                                <div className="fs14">
                                    {this.props.chatwidget.getIn(['proactive', 'data', 'photo']) && <img width="30"
                                                                                                     alt={this.props.chatwidget.getIn(['proactive', 'data', 'name_support']) || this.props.chatwidget.getIn(['proactive', 'data', 'extra_profile'])}
                                                                                                     title={this.props.chatwidget.getIn(['proactive', 'data', 'name_support']) || this.props.chatwidget.getIn(['proactive', 'data', 'extra_profile'])}
                                                                                                     className="mr-2 rounded"
                                                                                                     src={this.props.chatwidget.getIn(['proactive', 'data', 'photo'])}/>}

                                    {!this.props.chatwidget.hasIn(['proactive','data','hide_op_name']) && <b>{this.props.chatwidget.getIn(['proactive', 'data', 'name_support']) || this.props.chatwidget.getIn(['proactive', 'data', 'extra_profile'])}</b>}
                                </div>
                                <p className="fs13 mb-0 inv-msg-cnt" dangerouslySetInnerHTML={{__html: this.props.chatwidget.getIn(['proactive', 'data', 'message'])}}></p>
                                {this.props.chatwidget.hasIn(['proactive','data','bot_intro']) && <ChatBotIntroMessage setBotPayload={this.setBotPayload} content={this.props.chatwidget.getIn(['proactive', 'data', 'message_full'])} />}
                            </div>
                        }


                    </div>
                </div>
        );
    }
}

export default ProactiveInvitation;
