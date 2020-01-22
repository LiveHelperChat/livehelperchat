import React, { Component } from 'react';
import { connect } from "react-redux";
import { hideInvitation } from "../actions/chatActions"
import { helperFunctions } from "../lib/helperFunctions";

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
    }

    componentDidMount() {
        helperFunctions.sendMessageParent('showInvitation', []);
        if (document.getElementById('id-invitation-height')) {
            setTimeout(()=> {
                helperFunctions.sendMessageParent('widgetHeight', [{
                    'force_width' : (document.getElementById('id-invitation-height').offsetWidth + 80),
                    'force_height' : document.getElementById('id-invitation-height').offsetHeight}]);
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

    render() {

        let className = "proactive-need-help p-2 float-right pointer";
        if (this.state.shown === false) {
            className += " invisible";
        } else {
            className += " fade-in";
        }

        return (
            <div className={className} onClick={this.fullInvitation} id="id-invitation-height">
                <button title="Close" onClick={(e) => this.hideInvitation(e)} className="float-right btn btn-sm rounded"><i className="material-icons mr-0">close</i></button>
                <div className="fs14"><b>{this.props.chatwidget.getIn(['proactive','data','name_support']) || this.props.chatwidget.getIn(['proactive','data','extra_profile'])}</b></div>
                <p className="fs12">{this.props.chatwidget.getIn(['proactive','data','message'])}</p>
            </div>
        );
    }
}

export default ProactiveInvitation;
