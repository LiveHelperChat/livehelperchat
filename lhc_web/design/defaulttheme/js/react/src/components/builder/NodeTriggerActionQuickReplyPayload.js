import React, { Component } from 'react';
import { connect } from "react-redux";
import { addPayload } from "../../actions/nodePayloadActions"
import NodeTriggerList from './NodeTriggerList';

@connect((store) => {
    return {
        payloads: store.currenttrigger
    };
})

class NodeTriggerActionQuickReplyPayload extends Component {

    constructor(props) {
        super(props);
        this.state = {addingPayload : false, value : ''};
    }

    addPayload(){
        this.setState({addingPayload : true});
    }

    savePayload() {
        this.props.dispatch(addPayload({trigger_id : this.props.payloads.getIn(['currenttrigger','id']), name : this.props.currentPayload.get('name'), value : this.state.value}));

        this.props.onPayloadChange(this.state.value);
        this.setState({addingPayload : false});
    }

    cancelSavePayload(){
        this.setState({addingPayload : false});
    }

    onChange(e) {
        this.props.onPayloadChange(e.target.value);
    }

    onChangeType(e) {
        this.props.onPayloadTypeChange(e.target.value);
    }

    onPayloadNameChange(e) {
        this.setState({value: e.target.value});
    }

    onChangeMessageToVisitor(e) {
        this.props.onPayloadAttrChange({attr: 'payload_message', value : e.target.value});
    }

    onChangePayload(val) {
        this.props.onPayloadAttrChange({attr: 'payload', value : val});
    }

    render() {

        var list = this.props.payloads.get('payloads').map((option, index) => <option key={option.get('id')} value={option.get('payload')}>{option.get('name')+' [' + option.get('payload') + ']'}</option>);

        var controlPayload = "";

        if (this.props.payloadType == 'button') {
            controlPayload =
                <div className="col-9">
                    <div className="form-group">
                        <label>Payload</label>
                        {this.state.addingPayload == false ? (
                            <select className="form-control form-control-sm" onChange={this.onChange.bind(this)} value={this.props.currentPayload.get('payload')}><option value="">Select event</option>{list}</select>
                        ) : (
                            <input className="form-control form-control-sm" type="text" onChange={this.onPayloadNameChange.bind(this)} defaultValue="" />
                        )}
                    </div>
                </div>
        } else if (this.props.payloadType == 'url') {
            controlPayload =
                <div className="col-12">
                    <div className="row">
                        <div className="col-6">
                            <div className="form-group">
                                <label>URL</label>
                                <input className="form-control form-control-sm" type="text" onChange={this.onChange.bind(this)} defaultValue={this.props.currentPayload.get('payload')} />
                            </div>
                        </div>
                        <div className="col-6">
                            <div className="form-group">
                                <label>Payload</label>
                                <select className="form-control form-control-sm" onChange={this.onChangeMessageToVisitor.bind(this)} value={this.props.currentPayload.get('payload_message')}><option value="">Select event</option>{list}</select>
                            </div>
                        </div>
                    </div>
                </div>
        } else if (this.props.payloadType == 'updatechat') {
            controlPayload =
                <div className="col-12">
                    <div className="form-group">
                        <label>Select action</label>
                        <select className="form-control form-control-sm" value={this.props.currentPayload.get('payload')} onChange={this.onChange.bind(this)} >
                            <option value="">Select event</option>
                            <option value="transferToOperator">Transfer to operator</option>
                            <option value="transferToBot">Transfer to bot</option>
                            <option value="subscribeToNotifications">Subscribe to notifications</option>
                        </select>
                    </div>

                    {(this.props.currentPayload.get('payload') == 'transferToOperator' || this.props.currentPayload.get('payload') == 'transferToBot') &&
                    <div className="form-group">
                        <label>Message to user after transfer</label>
                        <input className="form-control form-control-sm" onChange={this.onChangeMessageToVisitor.bind(this)} defaultValue={this.props.currentPayload.get('payload_message')} type="text" placeholder="Message to visitor" />
                    </div>
                    }
                </div>
        } else if (this.props.payloadType == 'trigger') {
            controlPayload = <div className="col-12">
                <div className="form-group">
                   <label>Select what trigger to execute</label>
                   <NodeTriggerList onSetPayload={(e) => this.props.onPayloadChange(e)} payload={this.props.currentPayload.get('payload')} />
                </div>
            </div>
        }

        return (
            <div className="row">

                <div className="col-12">
                    <div className="form-group">
                        <label>Type</label>
                        <select className="form-control form-control-sm" defaultValue={this.props.payloadType} onChange={this.onChangeType.bind(this)} >
                            <option value="none">No action</option>
                            <option value="url">URL</option>
                            <option value="button">Click</option>
                            <option value="updatechat">Update chat</option>
                            <option value="trigger">Execute trigger</option>
                        </select>
                    </div>
                </div>

                {controlPayload}

                {this.props.payloadType == 'button' &&
                <div className="col-3">
                    <div className="form-group">
                        <label>&nbsp;</label>
                        <div>
                            {this.state.addingPayload == false ? (
                                <a title="Add new payload" onClick={this.addPayload.bind(this)}><i class="material-icons mr-0">add</i></a>
                            ) : (
                                <div>
                                    <a title="Save" onClick={this.savePayload.bind(this)}><i class="material-icons mr-0">check</i></a>
                                    <a title="Cancel" onClick={this.cancelSavePayload.bind(this)}><i class="material-icons mr-0">cancel</i></a>
                                </div>
                            )}
                        </div>
                    </div>
                </div>}

            </div>
        );
    }
}

export default NodeTriggerActionQuickReplyPayload;
