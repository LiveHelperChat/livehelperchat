import React, { Component } from 'react';


class NodeTriggerActionSurveyPreview extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div>
                <div className="message-row message-admin operator-changes">
                    <div className="msg-date">18:10:45</div>
                    <span className="usr-tit op-tit"><i className="material-icons chat-operators mi-fs15 me-0">&#xE851;</i>Operator</span>
                    <div className="msg-body">Survey content</div>
                </div>
            </div>
        );
    }
}

export default NodeTriggerActionSurveyPreview;
