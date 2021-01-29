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
        const classNameMenu = (closeInst ? 'col-6' : 'col-12') + ' pr-1' + (this.props.chatwidget.get('isChatting') === false && this.props.chatwidget.hasIn(['chat_ui','hide_popup']) ? ' d-none' : '');
        const hasPopup = !this.props.chatwidget.hasIn(['chat_ui','hide_popup']);
        const showClose = this.props.chatwidget.get('isChatting') === true && !this.props.chatwidget.hasIn(['chat_ui','hide_close']);

        return (
            <div id="widget-header-content" className={className}>
                {hasHeader && <div className="lhc-custom-header-inside" dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','custom_html_header_body'])}}></div>}

                {closeInst && <div className="col-6 pl-1 minimize-icon">
                    <span className="header-link" title={this.props.chatwidget.getIn(['chat_ui','min_text']) || t('button.minimize')} onClick={this.closeWidget}>
                        {(this.props.chatwidget.hasIn(['chat_ui','img_icon_min']) && <img className="px-1" src={this.props.chatwidget.getIn(['chat_ui','img_icon_min'])} alt="" />) || <i className="material-icons">&#xf103;</i>}
                    </span>
                </div>}

                {(hasPopup || showClose) && <div className={classNameMenu}>
                    <div className="d-flex">
                        <div className={(window.lhcChat['staticJS']['dir'] == 'rtl' ? "mr" : "ml")+"-auto"}>
                            {hasPopup && <a className="header-link" title={this.props.chatwidget.getIn(['chat_ui','popup_text']) || t('button.popup')} onClick={this.popup}>
                                    {(this.props.chatwidget.hasIn(['chat_ui','img_icon_popup']) && <img className="px-1" src={this.props.chatwidget.getIn(['chat_ui','img_icon_popup'])} alt="" />) || <i className="material-icons">&#xf106;</i>}
                                </a>}
                            {showClose && <a title={this.props.chatwidget.getIn(['chat_ui','end_chat_text']) || t('button.end_chat')} className="header-link" onClick={this.endChat}>
                                {(this.props.chatwidget.hasIn(['chat_ui','img_icon_close']) && <img className="px-1" src={this.props.chatwidget.getIn(['chat_ui','img_icon_close'])} alt="" />) || <i className="material-icons">&#xf10a;</i>}
                            </a>}
                        </div>
                    </div>
                </div>}

            </div>
        );
    }
}

export default withTranslation()(HeaderChat);
