import React, { Component } from 'react';

class NodeTriggerActionButtonsPreview extends Component {

    constructor(props) {
        super(props);
    }

    render() {

        if (this.props.action.hasIn(['content','buttons']))
        {
            return (
                <div>
                   <div className="message-row message-admin operator-changes">
                        <div className="msg-date">18:10:45</div>
                        <span className="usr-tit op-tit"><i className="material-icons chat-operators mi-fs15 mr-0">&#xE851;</i>Operator</span>
                        <div className="msg-body">{this.props.action.getIn(['content','buttons_options','message'])}</div>
                    </div>
                    <ul className="bot-btn-list">
                        {this.props.action.getIn(['content','buttons']).map((item, index) =>
                            <li key={index}>
                                <a href="#">{item.get('type') == 'url' && <i className="material-icons">open_in_new</i>}{item.getIn(['content','name'])}</a>
                            </li>
                        )}
                    </ul>
                </div>
            );
        } else {
            return null;
        }
    }
}

export default NodeTriggerActionButtonsPreview;
