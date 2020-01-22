import React, { PureComponent } from 'react';

class ChatInvitationMessage extends PureComponent {

    constructor(props) {
        super(props);
    }

    render() {
        if (this.props.mode == 'message'){
            return (
                <div className="message-row message-admin">
                    <span className="usr-tit op-tit">{this.props.invitation.name_support || this.props.invitation.extra_profile}</span>
                    <div className="msg-body">{this.props.invitation.message}</div>
                </div>
            );
        } else {
            return (
                <React.Fragment>
                    <div className="operator-info d-flex mb10 round-profile mt-2">
                         <div>
                             {this.props.invitation.photo && <img src={this.props.invitation.photo} title={this.props.invitation.photo_title} alt=""/>}
                             {!this.props.invitation.photo && <i className="icon-assistant material-icons">account_box</i>}
                         </div>
                         <div className="p-1 w-100">
                             <div>
                                 <strong>{this.props.invitation.name_support || this.props.invitation.extra_profile}<br/></strong>
                                 {this.props.invitation.name_support && <span><i>{this.props.invitation.extra_profile}</i></span>}
                            </div>
                        </div>
                    </div>
                    <div className="message-row message-admin pt-1 text-left">
                        <div className="msg-body">{this.props.invitation.message}</div>
                    </div>
                </React.Fragment>

            );
        }
    }
}

export default ChatInvitationMessage;
