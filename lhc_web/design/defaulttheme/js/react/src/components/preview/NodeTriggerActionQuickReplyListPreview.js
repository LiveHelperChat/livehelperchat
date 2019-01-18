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
                        <a key={index} className="btn btn-secondary btn-sm">{item.get('type') == 'url' &&
                        <i className="material-icons">open_in_new</i>
                        }{item.getIn(['content','name'])}</a>
                    )}
                </div>
            );
        } else {
            return null;
        }
    }
}

export default NodeTriggerActionQuickReplyListPreview;
