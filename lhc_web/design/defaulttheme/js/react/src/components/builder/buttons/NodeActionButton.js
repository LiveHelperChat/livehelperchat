import React, { Component } from 'react';
import NodeTriggerActionQuickReplyPayload from '../NodeTriggerActionQuickReplyPayload';

class NodeActionButton extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.onPayloadChange = this.onPayloadChange.bind(this);
        this.onChangeFieldType = this.onChangeFieldType.bind(this);
        this.onPayloadAttrChange = this.onPayloadAttrChange.bind(this);
    }

    changeType(e) {
        this.props.onChangeType({id : this.props.id, 'type' : e.target.value});
    }

    onChangeFieldName(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','name'], value : e.target.value});
    }

    onPayloadChange(payload) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content', 'payload'], value :  payload});
    }

    onChangeFieldType(type) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['type'], value : type});
    }

    onPayloadAttrChange(payload) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content', payload.attr], value :  payload.value});
    }

    deleteField() {
        this.props.onDeleteField(this.props.id);
    }

    upField() {
        this.props.onMoveUpField(this.props.id);
    }

    downField() {
        this.props.onMoveDownField(this.props.id);
    }

    render() {
        return (
            <div className="row">
                <div className="col-6">
                    <div className="form-group">
                        <label>{this.props.id + 1}. Button name*</label>
                        <input className="form-control" onChange={this.onChangeFieldName.bind(this)} type="text" defaultValue={this.props.button.getIn(['content','name'])}/>
                    </div>
                </div>
                <div className="col-6">
                    <div className="form-group">
                        <NodeTriggerActionQuickReplyPayload onPayloadAttrChange={this.onPayloadAttrChange} onPayloadTypeChange={this.onChangeFieldType} onPayloadChange={this.onPayloadChange} payloadType={this.props.button.get('type')} currentPayload={this.props.button.getIn(['content'])} />
                    </div>
                </div>

                <div className="col-12">
                    <div className="btn-group float-left" role="group" aria-label="Trigger actions">
                        {this.props.isFirst == false && <button className="btn btn-secondary btn-sm" onClick={this.upField.bind(this)}><i className="material-icons mr-0">keyboard_arrow_up</i></button>}
                        {this.props.isLast == false && <button className="btn btn-secondary btn-sm" onClick={this.downField.bind(this)}><i className="material-icons mr-0">keyboard_arrow_down</i></button>}
                    </div>

                    <div className="btn-group float-right" role="group" aria-label="Trigger actions">
                        <button className="btn btn-warning btn-sm" onClick={this.deleteField.bind(this)}>Delete</button>
                    </div>
                </div>

                <div className="col-12">
                    <hr/>
                </div>

            </div>
        );
    }
}

export default NodeActionButton;
