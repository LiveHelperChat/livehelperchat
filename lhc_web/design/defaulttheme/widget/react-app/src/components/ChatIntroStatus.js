import React, { PureComponent } from 'react';

class ChatIntroStatus extends PureComponent {

    constructor(props) {
        super(props);
    }

    render() {
        return <React.Fragment>
            {this.props.profileBefore !== null && <div dangerouslySetInnerHTML={{__html:this.props.profileBefore}}></div>}
            <div className={this.props.msg_expand} id="messagesBlock" dangerouslySetInnerHTML={{__html:this.props.messagesBefore}}></div>
            {!this.props.hideMessageField && <div className="d-flex flex-row border-top position-relative message-send-area">
                <div className="btn-group dropup disable-select pl-2 pt-2"><i className="material-icons settings text-muted" aria-haspopup="true" aria-expanded="false">&#xf100;</i></div>
                <div className="mx-auto pb-1 w-100">
                    <textarea aria-label="Type your message here..." placeholder={this.props.placeholderMessage} id="CSChatMessage" rows="1" className="pl-0 no-outline form-control rounded-0 form-control border-left-0 border-right-0 border-0" />
                </div>
                <div className="disable-select">
                    <div className="user-chatwidget-buttons pt-1" id="ChatSendButtonContainer">
                        <i className="material-icons text-muted settings mr-0">&#xf113;</i>
                    </div>
                </div>
            </div>}
        </React.Fragment>;
    }
}

export default ChatIntroStatus;
