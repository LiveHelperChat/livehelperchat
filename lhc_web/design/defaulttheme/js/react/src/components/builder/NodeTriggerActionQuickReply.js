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

        this.onButtonIconContentChange = this.onButtonIconContentChange.bind(this);
        this.onButtonCSSClassChange = this.onButtonCSSClassChange.bind(this);
        
        this.state = {advanced : false};
    }

    onButtonIconContentChange(e) {
        this.props.onPayloadAttrChange({id : this.props.id, payload : {attr: 'button_icon', value: e.target.value}});
    }

    onButtonCSSClassChange(e) {
        this.props.onPayloadAttrChange({id : this.props.id, payload : {attr: 'button_class', value: e.target.value}});
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
            <div className="row border-top border-dark">
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

                    <div className="btn-group float-start mt-4 pt-2" role="group" aria-label="Trigger actions">
                        <button disabled="disabled" className="btn btn-xs btn-secondary">{this.props.id + 1}</button>
                        {this.props.isFirst == false && <button className="btn btn-secondary btn-xs" onClick={this.props.upField}><i className="material-icons me-0">keyboard_arrow_up</i></button>}
                        {this.props.isLast == false && <button className="btn btn-secondary btn-xs" onClick={this.props.downField}><i className="material-icons me-0">keyboard_arrow_down</i></button>}
                    </div>

                    <div className="form-group float-end mt-4 pt-1">
                        <div>
                            <a onClick={this.deleteReply}><i className="material-icons me-0">delete</i></a>
                        </div>
                    </div>
                </div>

                <div className="col-10">
                    <a onClick={(e) => this.setState({"advanced": !this.state.advanced})}><span className="material-icons">code</span>{this.state.advanced ? 'Hide' : 'Show'} advanced options</a>
                </div>

                {this.props.reply.get('type') == 'url' && this.state.advanced && <div className="col-12">
                    <div className="row">
                        <div className="col-6">
                            <div className="form-group">
                                <label>Button ID</label>
                                <input type="text" placeholder="Button ID" onChange={this.onButtonIDChange} defaultValue={this.props.reply.getIn(['content','button_id'])} className="form-control form-control-sm" />
                            </div>
                        </div>
                        <div className="col-6">
                            <div className="form-group">
                                <label>CSS Class element</label>
                                <input type="text" placeholder="Button CSS class" onChange={this.onButtonCSSClassChange} defaultValue={this.props.reply.getIn(['content','button_class'])} className="form-control form-control-sm" />
                            </div>
                        </div>
                        <div className="col-6">
                            <div className="form-group">
                                <label>Icon name. Based on material icons.</label>
                                <input type="text" placeholder="Icon name" onChange={this.onButtonIconContentChange} defaultValue={this.props.reply.getIn(['content','button_icon'])} className="form-control form-control-sm" />
                            </div>
                        </div>
                        <div className="col-12">
                            <label><input type="checkbox" onChange={(e) => this.props.onPayloadAttrChange({id : this.props.id,  payload: {attr: 'override_rest_api_button', value: e.target.checked}})} defaultChecked={this.props.reply.getIn(['content','override_rest_api_button'])} /> Override Rest API button content.</label>
                        </div>
                        <div className="col-12">
                            <div className="form-group">
                                <label>Rest API button custom content.</label>
                                <textarea onChange={(e) => this.props.onPayloadAttrChange({id : this.props.id,  payload: {attr: 'rest_api_button', value: e.target.value}})}  defaultValue={this.props.reply.getIn(['content','rest_api_button'])} className="form-control form-control-sm" ></textarea>
                            </div>
                        </div>
                    </div>
                </div>}

                {(this.props.reply.get('type') == 'trigger' || this.props.reply.get('type') == 'button') && this.state.advanced && <div className="col-10">
                    <div className="row">
                        <div className="col-6">
                            <div className="form-group">
                                <label>Store name</label>
                                <input type="text" title="If you do not set we will not save button click" placeholder="How should we name stored attribute?" onChange={this.onStoreNameChange} defaultValue={this.props.reply.getIn(['content','store_name'])} className="form-control form-control-sm" />
                            </div>
                        </div>
                        <div className="col-6">
                            <div className="form-group">
                                <label>Store value</label>
                                <input type="text" placeholder="Button name is used by default." onChange={this.onStoreValueChange} defaultValue={this.props.reply.getIn(['content','store_value'])} className="form-control form-control-sm" />
                            </div>
                        </div>
                        <div className="col-6">
                            <label><input type="checkbox" onChange={(e) => this.props.onButtonStoreTypeChange({id : this.props.id, value : e.target.checked})} defaultChecked={this.props.reply.getIn(['content','as_variable'])} /> Save value as chat variable.</label> <i className="material-icons" title="This will be invisible for the operator.">info</i>
                            <label><input type="checkbox" onChange={(e) => this.props.onButtonNoName({id : this.props.id, value : e.target.checked})} defaultChecked={this.props.reply.getIn(['content','no_name'])} /> Do not print button name on click</label> <i className="material-icons" title="This will avoid sending visitor message as button name.">info</i>
                        </div>
                        <div className="col-6">
                            <div className="form-group">
                                <label>Button ID, element id attribute.</label>
                                <input type="text" placeholder="Button ID" onChange={this.onButtonIDChange} defaultValue={this.props.reply.getIn(['content','button_id'])} className="form-control form-control-sm" />
                            </div>
                        </div>
                        <div className="col-6">
                            <div className="form-group">
                                <label>CSS Class element</label>
                                <input type="text" placeholder="Button CSS class" onChange={this.onButtonCSSClassChange} defaultValue={this.props.reply.getIn(['content','button_class'])} className="form-control form-control-sm" />
                            </div>
                        </div>
                        <div className="col-6">
                            <div className="form-group">
                                <label>Icon name. Based on material icons.</label>
                                <input type="text" placeholder="Icon name" onChange={this.onButtonIconContentChange} defaultValue={this.props.reply.getIn(['content','button_icon'])} className="form-control form-control-sm" />
                            </div>
                        </div>
                        <div className="col-12">
                            <label><input type="checkbox" onChange={(e) => this.props.onPayloadAttrChange({id : this.props.id,  payload: {attr: 'override_rest_api_button', value: e.target.checked}})} defaultChecked={this.props.reply.getIn(['content','override_rest_api_button'])} /> Override Rest API button content.</label>
                        </div>
                        <div className="col-12">
                            <div className="form-group">
                                <label>Rest API button custom content.</label>
                                <textarea onChange={(e) => this.props.onPayloadAttrChange({id : this.props.id,  payload: {attr: 'rest_api_button', value: e.target.value}})}  defaultValue={this.props.reply.getIn(['content','rest_api_button'])} className="form-control form-control-sm" ></textarea>
                            </div>
                        </div>
                    </div>
                </div>}

            </div>
        );
    }
}

export default NodeTriggerActionQuickReply;
