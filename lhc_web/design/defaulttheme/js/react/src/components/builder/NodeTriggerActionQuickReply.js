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

        this.onPrecheckChange = this.onPrecheckChange.bind(this);
        this.onRenderArgsChange = this.onRenderArgsChange.bind(this);
        this.onStoreNameChange = this.onStoreNameChange.bind(this);
        this.onStoreValueChange = this.onStoreValueChange.bind(this);
        this.onButtonIDChange = this.onButtonIDChange.bind(this);
    }

    onStoreNameChange(e) {
        this.props.onStoreNameChange({id : this.props.id, value : e.target.value});
    }

    onStoreValueChange(e) {
        this.props.onStoreValueChange({id : this.props.id, value : e.target.value});
    }

    onButtonIDChange(e) {
        this.props.onButtonIDChange({id : this.props.id, value : e.target.value});
    }

    onPrecheckChange(e) {
        this.props.onPrecheckChange({id : this.props.id, value : e.target.value});
    }

    onRenderArgsChange(e) {
        this.props.onRenderArgsChange({id : this.props.id, value : e.target.value});
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
                <div className="col-5">
                    <div className="form-group">
                        <label className="font-weight-bold">Name</label>
                        <input type="text" onChange={this.onNameChange} defaultValue={this.props.reply.getIn(['content','name'])} className="form-control form-control-sm" />
                    </div>
                    <div className="row">
                        <div className="col-6">
                            <div className="form-group">
                                <label>Precheck event</label>
                                <input type="text" onChange={this.onPrecheckChange} defaultValue={this.props.reply.getIn(['content','render_precheck_function'])} className="form-control form-control-sm" />
                            </div>
                        </div>
                        <div className="col-6">
                            <div className="form-group">
                                <label>Arguments</label>
                                <input type="text" onChange={this.onRenderArgsChange} defaultValue={this.props.reply.getIn(['content','render_args'])} className="form-control form-control-sm" />
                            </div>
                        </div>
                    </div>

                </div>


                <div className="col-5">
                    <NodeTriggerActionQuickReplyPayload onPayloadAttrChange={this.onPayloadAttrChange} onPayloadTypeChange={this.onPayloadTypeChange} onPayloadChange={this.onPayloadChange} payloadType={this.props.reply.get('type')} currentPayload={this.props.reply.getIn(['content'])} />
                </div>
                <div className="col-2">

                    <div className="btn-group float-left mt-4 pt-2" role="group" aria-label="Trigger actions">
                        <button disabled="disabled" className="btn btn-xs btn-secondary">{this.props.id + 1}</button>
                        {this.props.isFirst == false && <button className="btn btn-secondary btn-xs" onClick={this.props.upField}><i className="material-icons mr-0">keyboard_arrow_up</i></button>}
                        {this.props.isLast == false && <button className="btn btn-secondary btn-xs" onClick={this.props.downField}><i className="material-icons mr-0">keyboard_arrow_down</i></button>}
                    </div>

                    <div className="form-group float-right mt-4 pt-1">
                        <div>
                            <a onClick={this.deleteReply}><i className="material-icons mr-0">delete</i></a>
                        </div>
                    </div>
                </div>

                {(this.props.reply.get('type') == 'trigger' || this.props.reply.get('type') == 'button') && <div className="col-10">
                    <div className="row">
                        <div className="col-4">
                            <div className="form-group">
                                <label>Store name</label>
                                <input type="text" title="If you do not set we will not save button click" placeholder="How should we name stored attribute?" onChange={this.onStoreNameChange} defaultValue={this.props.reply.getIn(['content','store_name'])} className="form-control form-control-sm" />
                            </div>
                        </div>
                        <div className="col-4">
                            <div className="form-group">
                                <label>Store value</label>
                                <input type="text" placeholder="Button name is used by default." onChange={this.onStoreValueChange} defaultValue={this.props.reply.getIn(['content','store_value'])} className="form-control form-control-sm" />
                            </div>
                        </div>
                        <div className="col-4">
                            <div className="form-group">
                                <label>Button ID</label>
                                <input type="text" placeholder="Button ID" onChange={this.onButtonIDChange} defaultValue={this.props.reply.getIn(['content','button_id'])} className="form-control form-control-sm" />
                            </div>
                        </div>
                    </div>
                </div>}

            </div>
        );
    }
}

export default NodeTriggerActionQuickReply;
