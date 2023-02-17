import React, { Component } from 'react';
import { connect } from "react-redux";
import { hideInvitation } from "../actions/chatActions"
import { helperFunctions } from "../lib/helperFunctions";
import parse, { domToReact } from 'html-react-parser';

@connect((store) => {
    return {
        chatwidget: store.chatwidget
    };
})

class InlineSurvey extends Component {

    state = {
        currentQuestion: 1
    }

    constructor(props) {
        super(props);
        /*this.hideInvitation = this.hideInvitation.bind(this);
        this.fullInvitation = this.fullInvitation.bind(this);
        this.setBotPayload = this.setBotPayload.bind(this);
        this.expireTimeout = null;*/
    }

    componentDidMount() {
        /*helperFunctions.sendMessageParent('showInvitation', [{name: this.props.chatwidget.getIn(['proactive','data','invitation_name'])}]);

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
        }*/
    }


    componentWillUnmount() {
        /*clearTimeout(this.expireTimeout);
        helperFunctions.sendMessageParent('widgetHeight', [{'reset_height' : true}]);
        if (this.props.chatwidget.hasIn(['proactive','data','on_click'])) {
            var EObj = null;
            (EObj = document.getElementById(this.props.chatwidget.getIn(['proactive','data','on_click','id']))) ? EObj.parentNode.removeChild(EObj) : false;
        }

        if (this.props.chatwidget.hasIn(['proactive','data','site_css_id'])) {
            var EObj = null;
            (EObj = document.getElementById(this.props.chatwidget.getIn(['proactive','data','site_css_id']))) ? EObj.parentNode.removeChild(EObj) : false;
        }*/
    }

    hideInvitation(e) {
        /*this.props.dispatch(hideInvitation( this.props.chatwidget.hasIn(['proactive','data','hide_on_open']) ));
        e.preventDefault();
        e.stopPropagation();*/
    }

    fullInvitation() {
        /*if (this.props.chatwidget.hasIn(['proactive','data','hide_on_open'])){
            this.props.dispatch(hideInvitation(true, true));
            if (this.props.chatwidget.hasIn(['proactive','data','on_click'])) {
                window['callback_'+this.props.chatwidget.getIn(['proactive','data','on_click','id'])]();
            }
        } else {
            helperFunctions.sendMessageParentDirect('hideInvitation', [{'full' : true, name: this.props.chatwidget.getIn(['proactive','data','invitation_name'])}]);
            this.props.dispatch({
                'type' : 'FULL_INVITATION'
            });
        }*/
    }

    render() {

        var counter = 1;
        var totalQuestions = 0;

        domToReact(this.props.surveyOptions,{
            replace: domNode => {
                if (!domNode.attribs) {
                    return;
                }
                if (domNode.name === 'voteoption') {
                    totalQuestions++;
                }
            }
        })

        console.log(totalQuestions);

        return (
            <div>
                {domToReact(this.props.surveyOptions,{
                    replace: domNode => {
                        if (!domNode.attribs) {
                            return;
                        }
                        if (domNode.name === 'voteoption') {

                            let classNameItem = this.state.currentQuestion != counter ? 'd-none' : '';

                            counter++;

                            return <div {...domNode.attribs} className={classNameItem} >
                                    {domToReact(domNode.children)}
                                <div className="d-block pt-3">
                                    {this.state.currentQuestion > 1 && <input type="button" className="btn btn-outline-secondary btn-sm" onClick={(e) => this.setState({'currentQuestion' : this.state.currentQuestion - 1})} value="&#9001; Prev"  name="Prev"/>}
                                    {totalQuestions > this.state.currentQuestion && <input type="button" className="btn btn-outline-secondary btn-sm float-end" onClick={(e) => this.setState({'currentQuestion' : this.state.currentQuestion + 1})} value="Next &#9002;"  name="Next"/>}
                                    {totalQuestions == this.state.currentQuestion && <input type="button" className="btn btn-outline-secondary btn-sm float-end" onClick={(e) => this.setState({'currentQuestion' : this.state.currentQuestion + 1})} value="Submit"  name="Submit"/>}
                                </div>
                            </div>;
                        }
                    }
                })}
            </div>
        );
    }
}

export default InlineSurvey;
