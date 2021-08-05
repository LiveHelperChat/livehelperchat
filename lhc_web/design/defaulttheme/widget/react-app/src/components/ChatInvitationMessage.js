import React, { PureComponent } from 'react';
import ChatBotIntroMessage from './ChatBotIntroMessage';
import { helperFunctions } from "../lib/helperFunctions";

class ChatInvitationMessage extends PureComponent {

    constructor(props) {
        super(props);
    }

    componentDidMount() {
        helperFunctions.sendMessageParent('readInvitation', [{name: this.props.invitation.invitation_name || "Manual"}]);
    }

    render() {

        if (this.props.mode == 'message') {

               return <React.Fragment>
                   {this.props.invitation.prev_msg && <ChatBotIntroMessage setBotPayload={this.props.setBotPayload} content={this.props.invitation.prev_msg} />}

                   {this.props.invitation.message && <div className="message-row message-admin">
                        <span className="usr-tit op-tit">
                             {<i title={this.props.invitation.name_support || this.props.invitation.extra_profile} className="chat-operators mi-fs15 mr-0">
                                 {this.props.invitation.bubble && this.props.invitation.photo && <img src={this.props.invitation.photo} alt="" className="profile-msg-pic" />}
                                 {(!this.props.invitation.photo || !this.props.invitation.bubble) && <i className={"material-icons " + (this.props.invitation.bubble ? "icon-assistant mr-0" : "")}>&#xf10d;</i>}
                             </i>}
                            {!this.props.invitation.bubble && (this.props.invitation.name_support || this.props.invitation.extra_profile)}
                         </span>
                        <div className="msg-body" dangerouslySetInnerHTML={{__html:this.props.invitation.message}}></div>
                   </div>}

                   {this.props.invitation.message_full && <ChatBotIntroMessage setBotPayload={this.props.setBotPayload} content={this.props.invitation.message_full} />}
               </React.Fragment>

        } else {
            return (
                <React.Fragment>
                    <div id="lhc-profile-body">
                        <div id="chat-status-container" className="operator-info d-flex border-bottom p-2">
                             <div>
                                 {this.props.invitation.photo && <img width="48" height="48" src={this.props.invitation.photo} title={this.props.invitation.photo_title} alt=""/>}
                                 {!this.props.invitation.photo && <i className="icon-assistant material-icons mr-0">&#xf10d;</i>}
                             </div>
                             <div className="p-1 pl-2 w-100">
                                 {this.props.invitation.name_support && !this.props.invitation.hide_name && <div className="font-weight-bold">{this.props.invitation.name_support}</div>}
                                 {this.props.invitation.extra_profile && <i>{this.props.invitation.extra_profile}</i>}
                            </div>
                        </div>
                    </div>
                    {this.props.mode != 'profile_only' && this.props.invitation.message &&
                    <div id="messagesBlock">
                        <div className="bottom-message position-relative">
                            <div className="message-row message-admin pt-1 text-left ml-3">
                                <div className="msg-body" dangerouslySetInnerHTML={{__html:this.props.invitation.message}}></div>
                            </div>
                        </div>
                    </div>}
                </React.Fragment>
            );
        }
    }
}

export default ChatInvitationMessage;
