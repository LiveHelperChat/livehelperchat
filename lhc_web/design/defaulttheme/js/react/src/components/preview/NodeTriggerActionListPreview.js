import React, { Component } from 'react';
import NodeTriggerActionQuickReplyListPreview from './NodeTriggerActionQuickReplyListPreview';

class NodeTriggerActionListPreview extends Component {

    constructor(props) {
        super(props);
    }

    render() {

        if (this.props.action.hasIn(['content','list']))
        {
            let listClass = 'list-group-element';
            let compactStyle = false;
            if (this.props.action.hasIn(['content','list_options','no_highlight']) && this.props.action.getIn(['content','list_options','no_highlight']) == true) {
                listClass = listClass + ' compact';
                compactStyle = true;
            } else {
                listClass = listClass + ' large';
            }

            return (
                <div className="list-group">
                    {this.props.action.getIn(['content','list']).map((item, index) =>
                    <div className={listClass} key={index}>
                        {compactStyle == false && index == 0 && item.getIn(['content','img']) != '' &&
                        <div className="element-background" style={{backgroundImage : `url(${item.getIn(['content','img'])})`}}>
                        </div>}
                        <div className="row element-description-row">
                            <div className="col-9">
                                <div className="element-description">
                                    <h4>{item.getIn(['content','title'])}</h4>
                                    <div>{item.getIn(['content','subtitle'])}</div>
                                    {item.hasIn(['buttons']) &&
                                        <NodeTriggerActionQuickReplyListPreview items={item.getIn(['buttons'])} />
                                    }
                                </div>
                            </div>
                            <div className="col-3">
                                {item.getIn(['content','img']) != '' && (index !== 0 || compactStyle == true)  &&
                                    <img className="float-right img-fluid" src={item.getIn(['content','img'])} />
                                }
                            </div>
                        </div>
                    </div>)}

                    {this.props.action.hasIn(['content','quick_replies']) && this.props.action.getIn(['content','quick_replies']).map((item, index) =>
                        <div className={listClass + " button-item"} key={index}>
                            <a key={index}>{item.get('type') == 'url' &&
                            <i className="material-icons">open_in_new</i>
                            }{item.getIn(['content','name'])}</a>
                        </div>
                    )}

                </div>
            );
        } else {
            return null;
        }

    }
}

export default NodeTriggerActionListPreview;
