import React, { Component } from 'react';
import { connect } from "react-redux";
import { closeWidget, abtractAction, minimizeWidget } from "../actions/chatActions"
import { helperFunctions } from "../lib/helperFunctions";
import { withTranslation } from 'react-i18next';

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
        this.props.dispatch(minimizeWidget());
    }

    endChat() {
        this.props.endChat();
    }

    popup() {
        this.props.popupChat();
    }
    
    componentDidMount() {
        var dropdown = document.getElementById('headerDropDown');
        if (dropdown) {
            var bsn = require("bootstrap.native/dist/bootstrap-native-v4");
            new bsn.Dropdown(dropdown);
        }
    }

    render() {
        const { t } = this.props;

        const closeInst = (!this.props.chatwidget.hasIn(['chat_ui','clinst']) || this.props.chatwidget.get('isMobile'));
        const hasHeader = this.props.chatwidget.hasIn(['chat_ui','custom_html_header_body']);
        const className = 'row header-chat' + (this.props.chatwidget.get('isMobile') == true ? ' mobile-header' : ' desktop-header');
        const hasPopup = !this.props.chatwidget.hasIn(['chat_ui','hide_popup']);
        const showClose = this.props.chatwidget.get('isChatting') === true && !this.props.chatwidget.hasIn(['chat_ui','hide_close']);
        var iconsNumber = 0;

        const headerIcons = this.props.chatwidget.hasIn(['chat_ui','header_buttons']) && this.props.chatwidget.getIn(['chat_ui','header_buttons']).map((btn, index) => {
                let position = btn.get('pos');
                if (window.lhcChat['staticJS']['dir'] == 'rtl') {
                    position = position == 'left' ? 'right' : 'left';
                }
                if (btn.get('btn') == 'min' && closeInst) {
                    iconsNumber++;
                    return <a className={"minimize-icon header-link float-"+position} title={this.props.chatwidget.getIn(['chat_ui','min_text']) || t('button.minimize')} onClick={this.closeWidget}>
                        {(this.props.chatwidget.hasIn(['chat_ui','img_icon_min']) && <img className="px-1" src={this.props.chatwidget.getIn(['chat_ui','img_icon_min'])} alt="" />) || <i className="material-icons">&#xf11c;</i>}
                    </a>;
                } else if (btn.get('btn') == 'popup' && hasPopup) {
                    iconsNumber++;
                    return <a className={"header-link float-"+position} title={this.props.chatwidget.getIn(['chat_ui','popup_text']) || t('button.popup')} onClick={this.popup}>
                        {(this.props.chatwidget.hasIn(['chat_ui','img_icon_popup']) && <img className="px-1" src={this.props.chatwidget.getIn(['chat_ui','img_icon_popup'])} alt="" />) || <i className="material-icons">&#xf106;</i>}
                    </a>;
                } else if (btn.get('btn') == 'close' && showClose) {
                    const endText = this.props.chatwidget.getIn(['chat_ui','end_chat_text']) || t('button.end_chat');
                    iconsNumber++;
                    return <a title={endText} className={"header-link float-"+position} onClick={this.endChat}>
                        {(this.props.chatwidget.hasIn(['chat_ui','img_icon_close']) && <img className="px-1" src={this.props.chatwidget.getIn(['chat_ui','img_icon_close'])} alt="" />) || <i className="material-icons">&#xf10a;</i>}
                        {btn.get('print') && <span className="end-chat-text">{endText}</span>}
                    </a>;
                }
        });

        return (
            <div id="widget-header-content" className={className}>
                {hasHeader && <div className="lhc-custom-header-inside" dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','custom_html_header_body'])}}></div>}
                {iconsNumber > 0 && <div className="col-12 px-1">
                        {headerIcons}
                </div>}

            </div>
        );
    }
}

export default withTranslation()(HeaderChat);
