import React, { Component } from 'react';

class NodeTriggerActionQuickReplyListPreview extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        if (this.props.items)
        {
            return (
                <div className="gen-btn-list">
                    {this.props.items.map((item, index) =>
                        <a key={index} className="btn btn-default btn-xs">{item.getIn(['content','name'])}</a>
                    )}
                </div>
            );
        } else {
            return null;
        }
    }
}

export default NodeTriggerActionQuickReplyListPreview;
