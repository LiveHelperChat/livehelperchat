import React, { Component } from 'react';

class NodeTriggerActionTypingPreview extends Component {

    constructor(props) {
        super(props);
    }

    render() {

        return (
            <div>
                <div className="message-row message-admin message-row-typing">
                    <div className="msg-body">

                        {this.props.action.getIn(['content','text']) != '' ? this.props.action.getIn(['content','text']) : 'Typing...'}

                        <br/><b>for {this.props.action.getIn(['content','duration'])} seconds.</b></div>
                </div>
            </div>
        );
    }
}

export default NodeTriggerActionTypingPreview;
