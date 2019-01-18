import React, { Component } from 'react';
import NodeTriggerActionQuickReplyListPreview from './NodeTriggerActionQuickReplyListPreview';

class NodeTriggerActionGenericPreview extends Component {

    constructor(props) {
        super(props);
    }

    render() {

        if (this.props.action.hasIn(['content','list']))
        {
            return (
            <div className="generic-carousel">
                {this.props.action.getIn(['content','list']).map((item, index) =>
                <div className="generic-bubble-item" key={index}>
                    <div className="generic-bubble-content">
                    {item.getIn(['content','img']) != '' && <img className="img-fluid" src={item.getIn(['content','img'])} />}

                    <h4>{item.getIn(['content','title'])}</h4>

                    <p>{item.getIn(['content','subtitle'])}</p>

                    {item.hasIn(['buttons']) && <ul className="bot-btn-list">
                        {item.getIn(['buttons']).map((item, index) =>
                            <li key={index}>
                                <a href="#">{item.get('type') == 'url' && <i className="material-icons">open_in_new</i>}{item.getIn(['content','name'])}</a>
                            </li>
                        )}
                    </ul>}
                    </div>
                </div>)}
            </div>);
        } else {
            return null;
        }

        }
    }

    export default NodeTriggerActionGenericPreview;
