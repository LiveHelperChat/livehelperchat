import React, { Component } from 'react';
import { connect } from "react-redux";
import NodeTriggerActionQuickReplyPayload from './NodeTriggerActionQuickReplyPayload';

@connect((store) => {
    return {
        payloads: store.currenttrigger
    };
})

class NodeTriggerActionQuickReply extends Component {

    constructor(props) {
        super(props);
        this.onNameChange = this.onNameChange.bind(this);
        this.onPayloadChange = this.onPayloadChange.bind(this);
        this.deleteReply = this.deleteReply.bind(this);
        this.onPayloadTypeChange = this.onPayloadTypeChange.bind(this);
        this.onPayloadAttrChange = this.onPayloadAttrChange.bind(this);
    }

    onNameChange(e) {
        this.props.onNameChange({id : this.props.id, value : e.target.value});
    }

    onPayloadChange(payload) {
        this.props.onPayloadChange({id : this.props.id, value : payload});
    }

    onPayloadTypeChange(payload) {
        this.props.onPayloadChange({id : this.props.id, value : ""});
        this.props.onPayloadTypeChange({id : this.props.id, value : payload});
    }

    onPayloadAttrChange(payload)
    {
        this.props.onPayloadAttrChange({id : this.props.id, payload : payload});
    }

    deleteReply() {
        this.props.deleteReply({id : this.props.id});
    }

    render() {

        return (
            <div className="row">
                <div className="col-xs-5">
                    <div className="form-group">
                        <label>Name</label>
                        <input type="text" onChange={this.onNameChange} defaultValue={this.props.reply.getIn(['content','name'])} className="form-control input-sm" />
                    </div>
                </div>
                <div className="col-xs-5">
                    <NodeTriggerActionQuickReplyPayload onPayloadAttrChange={this.onPayloadAttrChange} onPayloadTypeChange={this.onPayloadTypeChange} onPayloadChange={this.onPayloadChange} payloadType={this.props.reply.get('type')} currentPayload={this.props.reply.getIn(['content'])} />
                </div>
                <div className="col-xs-2">
                    <div className="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <a onClick={this.deleteReply}><i className="material-icons mr-0">delete</i></a>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default NodeTriggerActionQuickReply;
