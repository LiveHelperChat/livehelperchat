import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeTriggerList from './NodeTriggerList';

class NodeTriggerActionActions extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.removeAction = this.removeAction.bind(this);
        this.onchangeAttr = this.onchangeAttr.bind(this);
    }

    changeType(e) {
        this.props.onChangeType({id : this.props.id, 'type' : e.target.value});
    }

    removeAction() {
        this.props.removeAction({id : this.props.id});
    }

    onchangeAttr(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content'].concat(e.path), value : e.value});
    }

    render() {
        return (
            <div>
                <div className="row">
                    <div className="col-xs-11">
                        <NodeTriggerActionType onChange={this.changeType} type={this.props.action.get('type')} />
                    </div>
                    <div className="col-xs-1">
                        <button onClick={this.removeAction} type="button" className="btn btn-danger btn-sm pull-right">
                            <i className="material-icons mr-0">delete</i>
                        </button>
                    </div>
                </div>

                <div className="form-group">
                    <label>Message before dispatching event</label>
                    <textarea className="form-control" defaultValue={this.props.action.getIn(['content','success_message'])} onChange={(e) => this.onchangeAttr({'path' : ['success_message'], 'value' : e.target.value})}></textarea>
                </div>

                <div className="form-group">
                    <label>Event identifier</label>
                    <input type="text" placeholder="Event name" className="form-control" onChange={(e) => this.onchangeAttr({'path' : ['event'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','event'])} />
                </div>

                <div className="form-group">
                    <label>Validation event identifier</label>
                    <input type="text" placeholder="Event name" className="form-control" onChange={(e) => this.onchangeAttr({'path' : ['event_validate'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','event_validate'])} />
                </div>

                <div className="row">
                    <div className="col-xs-6">
                        <div className="form-group">
                            <label>Execute trigger on failed format</label>
                            <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['attr_options','collection_callback_format'], 'value' : e})} payload={this.props.action.getIn(['content','attr_options','collection_callback_format'])} />
                        </div>
                    </div>
                    <div className="col-xs-6">
                        <div className="form-group">
                            <label>Execute trigger on fail</label>
                            <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['attr_options','collection_callback_cancel'], 'value' : e})} payload={this.props.action.getIn(['content','attr_options','collection_callback_cancel'])} />
                        </div>
                    </div>
                    <div className="col-xs-6">
                        <div className="form-group">
                            <label>Execute trigger on success</label>
                            <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['attr_options','collection_callback_pattern'], 'value' : e})} payload={this.props.action.getIn(['content','attr_options','collection_callback_pattern'])} />
                        </div>
                    </div>
                </div>
                <hr/>

            </div>
        );
    }
}

export default NodeTriggerActionActions;
