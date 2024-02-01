import React, { Component } from 'react';
import { connect } from "react-redux";
import { closeWidget, abtractAction, minimizeWidget } from "../actions/chatActions"
import { helperFunctions } from "../lib/helperFunctions";
import { withTranslation } from 'react-i18next';
import ChatOptions from './ChatOptions';

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
        this.switchColumn = this.switchColumn.bind(this);
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

    switchColumn(){
        this.props.switchColumn();
    }

    render() {
        const { t } = this.props;

        const closeInst = (!this.props.chatwidget.hasIn(['chat_ui','clinst']) || this.props.chatwidget.get('isMobile'));
        const hasHeader = this.props.chatwidget.hasIn(['chat_ui','custom_html_header_body']);
        const className = 'position-relative row header-chat' + (this.props.chatwidget.get('isMobile') == true ? ' mobile-header' : ' desktop-header') + (this.props.chatwidget.get('isChatting') === true || (this.props.chatwidget.get('isOnline') === true && this.props.chatwidget.get('isOfflineMode') === false) ? ' online-header' : ' offline-header');
        const hasPopup = !this.props.chatwidget.hasIn(['chat_ui','hide_popup']);
        const showClose = this.props.chatwidget.get('isChatting') === true && !this.props.chatwidget.hasIn(['chat_ui','hide_close']);
        var iconsNumber = 0, dropdownNumber = 0, headerIconsBeforeDropdown = false, dropdownDetected = false;

        const headerIcons = this.props.chatwidget.hasIn(['chat_ui','header_buttons']) && this.props.chatwidget.getIn(['chat_ui','header_buttons']).map((btn, index) => {
                let position = btn.get('pos');

                if (position == 'dropdown') {
                    dropdownDetected = true;
                    return;
                }

                if (dropdownDetected === true) {
                    headerIconsBeforeDropdown = true;
                }

                position = position == 'left'  ? 'start' : (position == 'right' ? 'end' : position);
                if (btn.get('btn') == 'min' && closeInst) {
                    iconsNumber++;
                    return <a tabIndex="0" onKeyPress={(e) => { e.key === "Enter" ? this.closeWidget() : '' }} className={"minimize-icon header-link float-"+position} title={this.props.chatwidget.getIn(['chat_ui','min_text']) || t('button.minimize')} onClick={this.closeWidget}>
                        {(this.props.chatwidget.hasIn(['chat_ui','img_icon_min']) && <img className="px-1" src={this.props.chatwidget.getIn(['chat_ui','img_icon_min'])} alt="" />) || <i className="material-icons">&#xf11c;</i>}
                    </a>;
                } else if (btn.get('btn') == 'popup' && hasPopup) {
                    iconsNumber++;
                    return <a tabIndex="0" onKeyPress={(e) => { e.key === "Enter" ? this.popup() : '' }} className={"header-link float-"+position} title={this.props.chatwidget.getIn(['chat_ui','popup_text']) || t('button.popup')} onClick={this.popup}>
                        {(this.props.chatwidget.hasIn(['chat_ui','img_icon_popup']) && <img className="px-1" src={this.props.chatwidget.getIn(['chat_ui','img_icon_popup'])} alt="" />) || <i className="material-icons">&#xf106;</i>}
                    </a>;
                } else if (btn.get('btn') == 'close' && showClose) {
                    const endText = this.props.chatwidget.getIn(['chat_ui','end_chat_text']) || t('button.end_chat');
                    iconsNumber++;
                    return <a tabIndex="0" onKeyPress={(e) => { e.key === "Enter" ? this.endChat() : '' }} title={endText} className={"header-link float-"+position} onClick={this.endChat}>
                        {(this.props.chatwidget.hasIn(['chat_ui','img_icon_close']) && <img className="px-1" src={this.props.chatwidget.getIn(['chat_ui','img_icon_close'])} alt="" />) || <i className="material-icons">&#xf10a;</i>}
                        {btn.get('print') && <span className="end-chat-text">{endText}</span>}
                    </a>;
                } else if (btn.get('btn') == 'fullheight' && !this.props.chatwidget.get('isMobile')) {
                    iconsNumber++;
                    let fheightText = '';
                    if (this.props.chatwidget.get('position_placement').includes('full_height')){
                        fheightText = this.props.chatwidget.getIn(['chat_ui','fheight_text_class']) || t('button.fheight_text_class');
                    } else {
                        fheightText = this.props.chatwidget.getIn(['chat_ui','fheight_text_col']) || t('button.fheight_text_col');
                    }
                    return <a tabIndex="0" title={fheightText} onKeyPress={(e) => { e.key === "Enter" ? this.switchColumn() : '' }} className={"header-link float-"+position} onClick={this.switchColumn}>
                        {(this.props.chatwidget.hasIn(['chat_ui','img_icon_fheight']) && <img className="px-1" src={this.props.chatwidget.getIn(['chat_ui','img_icon_fheight'])} alt="" />) || (<i className="material-icons">{this.props.chatwidget.get('position_placement').includes('full_height') ? <React.Fragment>&#xf123;</React.Fragment> : <React.Fragment>&#xf126;</React.Fragment>}</i>)}
                    </a>;
                }
        });

        const dropdownIcons = this.props.chatwidget.hasIn(['chat_ui','header_buttons']) && this.props.chatwidget.getIn(['chat_ui','header_buttons']).map((btn, index) => {
            let position = btn.get('pos');
            if (position != 'dropdown') {
                return;
            }
            if (btn.get('btn') == 'min' && closeInst) {
                dropdownNumber++;
                const minText = this.props.chatwidget.getIn(['chat_ui','min_text']) || t('button.minimize');
                return <a tabIndex="0" onKeyPress={(e) => { e.key === "Enter" ? this.closeWidget() : '' }} className={"minimize-icon header-link header-burger-link d-block text-nowrap py-1 ps-1"} title={minText} onClick={this.closeWidget}>
                    {(this.props.chatwidget.hasIn(['chat_ui','img_icon_min']) && <img className="px-1" src={this.props.chatwidget.getIn(['chat_ui','img_icon_min'])} alt="" />) || <i className="material-icons">&#xf11c;</i>}
                    <span className="menu-text text-nowrap">{minText}</span>
                </a>;
            } else if (btn.get('btn') == 'popup' && hasPopup) {
                dropdownNumber++;
                const popupText = this.props.chatwidget.getIn(['chat_ui','popup_text']) || t('button.popup');
                return <a tabIndex="0" onKeyPress={(e) => { e.key === "Enter" ? this.popup() : '' }} className={"header-link header-burger-link d-block text-nowrap  py-1 ps-1"} title={popupText} onClick={this.popup}>
                    {(this.props.chatwidget.hasIn(['chat_ui','img_icon_popup']) && <img className="px-1" src={this.props.chatwidget.getIn(['chat_ui','img_icon_popup'])} alt="" />) || <i className="material-icons">&#xf106;</i>}
                    <span className="menu-text text-nowrap">{popupText}</span>
                </a>;
            } else if (btn.get('btn') == 'close' && showClose) {
                dropdownNumber++;
                const endText = this.props.chatwidget.getIn(['chat_ui','end_chat_text']) || t('button.end_chat');
                return <a tabIndex="0" title={endText} onKeyPress={(e) => { e.key === "Enter" ? this.endChat() : '' }} className={"header-link header-burger-link  py-1 d-block text-nowrap ps-1"} onClick={this.endChat}>
                    {(this.props.chatwidget.hasIn(['chat_ui','img_icon_close']) && <img className="px-1" src={this.props.chatwidget.getIn(['chat_ui','img_icon_close'])} alt="" />) || <i className="material-icons">&#xf10a;</i>}
                    <span className="menu-text">{endText}</span>
                </a>;
            } else if (btn.get('btn') == 'fullheight' && !this.props.chatwidget.get('isMobile')) {
                dropdownNumber++;
                let fheightText = '';
                if (this.props.chatwidget.get('position_placement').includes('full_height')){
                    fheightText = this.props.chatwidget.getIn(['chat_ui','fheight_text_class']) || t('button.fheight_text_class');
                } else {
                    fheightText = this.props.chatwidget.getIn(['chat_ui','fheight_text_col']) || t('button.fheight_text_col');
                }
                return <a tabIndex="0" onKeyPress={(e) => { e.key === "Enter" ? this.switchColumn() : '' }} title={fheightText} className={"header-link header-burger-link py-1 d-block text-nowrap ps-1"} onClick={this.switchColumn}>
                    {(this.props.chatwidget.hasIn(['chat_ui','img_icon_fheight']) && <img className="px-1" src={this.props.chatwidget.getIn(['chat_ui','img_icon_fheight'])} alt="" />) || (<i className="material-icons">{this.props.chatwidget.get('position_placement').includes('full_height') ? <React.Fragment>&#xf123;</React.Fragment> : <React.Fragment>&#xf126;</React.Fragment>} </i>)}
                    <span className="menu-text text-nowrap">{fheightText}</span>
                </a>;
            }
        });

        return (
            <div id="widget-header-content" className={className}>
                {hasHeader && <div className="lhc-custom-header-inside" dangerouslySetInnerHTML={{__html:this.props.chatwidget.getIn(['chat_ui','custom_html_header_body'])}}></div>}
                {(iconsNumber > 0 || dropdownNumber > 0) && <div className="col-12 px-1 widget-header-menu">
                        {headerIconsBeforeDropdown === true && headerIcons}
                        {dropdownNumber > 0 && <div className="float-end position-relative">
                            <ChatOptions elementId="headerDropDown">
                                <div className="btn-group dropup disable-select">
                                    <button tabIndex="0" className="header-link border-0 p-0" id="headerDropDown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i className="material-icons" ></i></button>
                                    <div className="dropdown-menu shadow bg-white rounded lhc-dropdown-menu pe-2">
                                        {dropdownIcons}
                                    </div>
                                </div>
                            </ChatOptions>
                        </div>}
                        {headerIconsBeforeDropdown === false && headerIcons}
                </div>}
            </div>
        );
    }
}

export default withTranslation()(HeaderChat);
