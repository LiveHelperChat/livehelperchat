import React, { PureComponent } from 'react';

class ChatInvitationMessage extends PureComponent {

    constructor(props) {
        super(props);
    }

    render() {

        let classProfile = "operator-info d-flex border-bottom p-2";

        if (this.props.mode == 'message') {
            return (
                <div className="message-row message-admin">
                    <span className="usr-tit op-tit">
                         {this.props.invitation.bubble && <i title={this.props.invitation.name_support || this.props.invitation.extra_profile} className="chat-operators mi-fs15 mr-0">
                             {this.props.invitation.photo && <img src={this.props.invitation.photo} alt="" className="profile-msg-pic" />}
                             {!this.props.invitation.photo && <i className="icon-assistant material-icons mr-0">account_box</i>}
                         </i>}
                        {!this.props.invitation.bubble && (this.props.invitation.name_support || this.props.invitation.extra_profile)}
                     </span>
                    <div className="msg-body" dangerouslySetInnerHTML={{__html:this.props.invitation.message}}></div>
                </div>
            );
        } else {
            return (
                <React.Fragment>
                    <div id="lhc-profile-body" className={classProfile}>
                         <div>
                             {this.props.invitation.photo && <img src={this.props.invitation.photo} title={this.props.invitation.photo_title} alt=""/>}
                             {!this.props.invitation.photo && <i className="icon-assistant material-icons mr-0">account_box</i>}
                         </div>
                         <div className="p-1 pl-2 w-100">
                             <div>
                                 <strong>{this.props.invitation.name_support || this.props.invitation.extra_profile}<br/></strong>
                                 {this.props.invitation.name_support && <span><i>{this.props.invitation.extra_profile}</i></span>}
                            </div>
                        </div>
                    </div>
                    {this.props.mode != 'profile_only' &&
                    <div className="message-row message-admin pt-1 text-left">
                        <div className="msg-body" dangerouslySetInnerHTML={{__html:this.props.invitation.message}}></div>
                    </div>}
                </React.Fragment>
            );
        }
    }
}

export default ChatInvitationMessage;
