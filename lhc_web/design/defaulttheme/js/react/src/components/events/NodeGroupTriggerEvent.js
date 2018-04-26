import React, { Component } from 'react';

import NodeTriggerPayloadList from '../builder/NodeTriggerPayloadList';

class NodeGroupTriggerEvent extends Component {

    constructor(props) {
        super(props);
        this.typeChange = this.typeChange.bind(this);
        this.textChange = this.textChange.bind(this);
        this.payloadChange = this.payloadChange.bind(this);
        this.deleteEvent = this.deleteEvent.bind(this);
    }

    typeChange(e) {
        this.props.updateEvent(this.props.event.set('type',e.target.value));
    }

    textChange(e) {
        this.props.updateEvent(this.props.event.set('pattern',e.target.value));
    }

    payloadChange(payload) {
        this.props.updateEvent(this.props.event.set('pattern',payload));
    }

    deleteEvent() {
        this.props.deleteEvent(this.props.event);
    }

    render() {
        return (
            <div className="row">
                <div className="col-xs-5">
                    <div className="form-group">
                        <label>Type</label>
                        <select className="form-control input-sm" defaultValue={this.props.event.get('type')} onChange={this.typeChange}>
                            <option value="0">Text</option>
                            <option value="1">Click</option>
                        </select>
                    </div>
                </div>
                <div className="col-xs-5">
                    <div className="form-group">
                        <label>Pattern or event name</label>
                        {this.props.event.get('type') == 0 ? (
                                 <input onChange={this.textChange} type="text" className="form-control input-sm" value={this.props.event.get('pattern')} />
                            ) : (
                             <NodeTriggerPayloadList onSetPayload={this.payloadChange} payload={this.props.event.get('pattern')} />
                        )}
                    </div>
                </div>
                <div className="col-xs-2">
                    <div className="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <a onClick={this.deleteEvent}><i className="material-icons mr-0">delete</i></a>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default NodeGroupTriggerEvent;
