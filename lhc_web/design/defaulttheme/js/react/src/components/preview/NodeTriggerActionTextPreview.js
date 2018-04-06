import React, { Component } from 'react';

class NodeTriggerActionTextPreview extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div>
                <div className="message-row message-admin operator-changes">
                    <div className="msg-date">18:10:45</div>
                    <span className="usr-tit op-tit"><i className="material-icons chat-operators mi-fs15 mr-0">&#xE851;</i>Operator</span>{this.props.action.getIn(['content','text'])}
                </div>
            </div>
        );
    }
}

export default NodeTriggerActionTextPreview;
