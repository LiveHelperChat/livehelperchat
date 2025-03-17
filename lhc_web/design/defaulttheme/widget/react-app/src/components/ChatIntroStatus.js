import React, { PureComponent } from 'react';
import SharedTextarea from './SharedTextarea';

class ChatIntroStatus extends PureComponent {

    constructor(props) {
        super(props);
    }

    componentDidMount() {
        let elm = document.getElementById('messages-scroll');
        if (elm) {
            elm.scrollTop = elm.scrollHeight + 1000;
        }
    }

    render() {
        return <React.Fragment>
            {this.props.profileBefore !== null && <div dangerouslySetInnerHTML={{__html:this.props.profileBefore}}></div>}
            <div className={this.props.msg_expand} id="messagesBlock" dangerouslySetInnerHTML={{__html:this.props.messagesBefore}}></div>
            {!this.props.hideMessageField && <div className="d-flex flex-row border-top position-relative message-send-area">
                <div className="btn-group dropup disable-select ps-1 pt-2"><button type="button" class="border-0 p-0 material-icons settings text-muted" id="chat-dropdown-options" role="button" data-bs-toggle="dropdown" tabindex="0" aria-haspopup="true" aria-expanded="false">&#xf100;</button></div>
                <div className="mx-auto w-100">
                    {/* <textarea aria-label="Type your message here..." defaultValue={this.props.value} placeholder={this.props.placeholderMessage} readOnly="readOnly" id="CSChatMessage" rows="1" className="ps-0 no-outline form-control rounded-0 form-control rounded-start-0 rounded-end-0 border-0" /> */}
                    <SharedTextarea
                        text={this.props.value}
                        textMaxLength="500"
                        textAutoFocus={true}
                        classNameText="ps-0 no-outline form-control rounded-0 form-control rounded-start-0 rounded-end-0 border-0"
                        textPlaceholder={this.props.placeholderMessage}
                        textareaRef={this.props.textMessageRef}
                    />
                </div>
                <div className="disable-select">
                    <div className="user-chatwidget-buttons pt-2 pe-1" id="ChatSendButtonContainer">
                        <i className="material-icons text-muted-light settings me-0">&#xf113;</i>
                    </div>
                </div>
            </div>}
        </React.Fragment>;
    }
}

export default ChatIntroStatus;
