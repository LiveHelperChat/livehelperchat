import React, { Component } from 'react';

import NodeTriggerActionQuickReplyListPreview from './NodeTriggerActionQuickReplyListPreview';

class NodeTriggerActionVideoPreview extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div>
                <div className="message-row message-admin operator-changes">
                    <div className="msg-date">18:10:45</div>
                    <span className="usr-tit op-tit"><i className="material-icons chat-operators mi-fs15 mr-0">&#xE851;</i>Operator</span>
                    <div className="msg-body">
                        <br/>
                        <div className="embed-responsive embed-responsive-16by9">
                            <video className="embed-responsive-item" autoPlay={this.props.action.getIn(['content','video_options','autoplay'])} controls={this.props.action.getIn(['content','video_options','controls'])}><source src={this.props.action.getIn(['content','payload'])} /></video>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default NodeTriggerActionVideoPreview;
