import React, { Component } from 'react';
import { connect } from "react-redux";
import { closeWidget, abtractAction } from "../actions/chatActions"
import { helperFunctions } from "../lib/helperFunctions";

@connect((store) => {
    return {
        chatwidget: store.chatwidget
    };
})

class HeaderChat extends Component {

    constructor(props) {
        super(props);
        this.closeWidget = this.closeWidget.bind(this);
        this.endChat = this.endChat.bind(this);
        this.popup = this.popup.bind(this);
    }

    closeWidget() {
        helperFunctions.sendMessageParent('closeWidget', [{'sender' : 'closeButton'}]);
    }

    endChat() {
        this.props.endChat();
    }

    popup() {
        this.props.popupChat();
    }
    
    componentDidMount() {
        var bsn = require("bootstrap.native/dist/bootstrap-native-v4");
        new bsn.Dropdown(document.getElementById('headerDropDown'));
    }

    render() {

        var className = 'row header-chat' + (this.props.chatwidget.get('isMobile') == true ? ' mobile-header' : ' desktop-header');
        var classNameMenu = 'col-6 pr-1' + (this.props.chatwidget.get('isChatting') === false && this.props.chatwidget.hasIn(['chat_ui','hide_popup']) ? ' d-none' : '');

        return (
            <div className={className}>
                <div className="col-6 pl-1">
                    <a href="#" className="text-dark" onClick={this.closeWidget}><i className="material-icons">close</i></a>
                </div>
                <div className={classNameMenu}>
                    <div className="d-flex">
                        <div className="ml-auto">
                            <a href="#" className="text-dark" id="headerDropDown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,5">
                                <i className="material-icons">menu</i>
                            </a>
                            <div className="dropdown-menu dropdown-menu-right mr-3" aria-labelledby="dropdownMenuOffset">
                                {this.props.chatwidget.get('isChatting') === true ? (
                                        <a className="dropdown-item" onClick={this.endChat} href="#">End Chat</a>
                                ) : ''}
                                {!this.props.chatwidget.hasIn(['chat_ui','hide_popup']) ? (<a className="dropdown-item" onClick={this.popup} href="#">Popup</a>) : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default HeaderChat;
