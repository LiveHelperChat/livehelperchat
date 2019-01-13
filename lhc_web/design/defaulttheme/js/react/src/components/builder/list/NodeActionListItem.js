import React, { Component } from 'react';
import NodeTriggerActionQuickReplyPayload from '../NodeTriggerActionQuickReplyPayload';
import NodeTriggerActionQuickReply from '../NodeTriggerActionQuickReply';
import shortid from 'shortid';

class NodeActionListItem extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.onPayloadChange = this.onPayloadChange.bind(this);
        this.onChangeFieldType = this.onChangeFieldType.bind(this);
        this.onPayloadAttrChange = this.onPayloadAttrChange.bind(this);
        this.onChangeMainAttr = this.onChangeMainAttr.bind(this);
        this.onQuickReplyNameChange = this.onQuickReplyNameChange.bind(this);
        this.onDeleteQuickReply = this.onDeleteQuickReply.bind(this);
        this.onPayloadButtonAttrChange = this.onPayloadButtonAttrChange.bind(this);
        this.onQuickReplyPayloadChange = this.onQuickReplyPayloadChange.bind(this);
        this.onQuickReplyPayloadTypeChange = this.onQuickReplyPayloadTypeChange.bind(this);
    }

    changeType(e) {
        this.props.onChangeType({id : this.props.id, 'type' : e.target.value});
    }

    onChangeMainAttr(attr, val) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content',attr], value :val});
    }

    onPayloadChange(payload) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content', 'payload'], value :  payload});
    }

    onChangeFieldType(type) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['type'], value : type});
    }

    onPayloadAttrChange(payload) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content', e.payload.attr], value :  e.payload.value});
    }

    addElementButton() {
        this.props.addSubelement({'path':[this.props.id,'buttons'], 'default':{'_id':shortid.generate(), 'type': 'url', 'content' : {'name':'New button', 'payload' : ''}}});
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

    onQuickReplyNameChange(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['buttons', e.id, 'content', 'name'], value :  e.value});
    }

    onDeleteQuickReply(e) {
        this.props.onDeleteSubField({path: [this.props.id, 'buttons', e.id]});
    }

    onPayloadButtonAttrChange(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['buttons', e.id,'content',e.payload.attr], value :  e.payload.value});
    }

    onQuickReplyPayloadChange(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['buttons',e.id,'content','payload'], value : e.value});
    }

    onQuickReplyPayloadTypeChange(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['buttons',e.id,'type'], value : e.value});
    }

    render() {

        var button_list = [];

        if (this.props.item.hasIn(['buttons'])) {
            button_list = this.props.item.getIn(['buttons']).map((field, index) => {
                return <NodeTriggerActionQuickReply id={index} key={field.get('_id')} onPayloadTypeChange={this.onQuickReplyPayloadTypeChange} onPayloadChange={this.onQuickReplyPayloadChange} onPayloadAttrChange={this.onPayloadButtonAttrChange} reply={field} deleteReply={this.onDeleteQuickReply} onNameChange={this.onQuickReplyNameChange}/>
            });
        }

        return (
            <div className="row">
                <div className="col-12">
                    <div className="form-group">
                        <label>{this.props.id + 1}. Title*</label>
                        <input className="form-control form-control-sm" onChange={(e) => this.onChangeMainAttr('title',e.target.value)} type="text" defaultValue={this.props.item.getIn(['content','title'])}/>
                    </div>
                </div>
                <div className="col-12">
                    <div className="form-group">
                        <label>Subtitle*</label>
                        <input className="form-control form-control-sm" onChange={(e) => this.onChangeMainAttr('subtitle',e.target.value)} type="text" defaultValue={this.props.item.getIn(['content','subtitle'])}/>
                    </div>
                </div>

                <div className="col-6">
                    <div className="form-group">
                        <label>Image URL</label>
                        <input className="form-control form-control-sm" onChange={(e) => this.onChangeMainAttr('img',e.target.value)} type="text" defaultValue={this.props.item.getIn(['content','img'])}/>
                    </div>
                </div>

                <div className="col-6">
                    <div className="form-group">
                        <NodeTriggerActionQuickReplyPayload onPayloadAttrChange={this.onPayloadAttrChange} onPayloadTypeChange={this.onChangeFieldType} onPayloadChange={this.onPayloadChange} payloadType={this.props.item.get('type')} currentPayload={this.props.item.getIn(['content'])} />
                    </div>
                </div>
                <div className="col-12">
                    {button_list}
                </div>

                <div className="col-12">
                    <div className="btn-group float-left" role="group" aria-label="Trigger actions">
                        {this.props.isFirst == false && <a className="btn btn-secondary btn-sm" onClick={this.upField.bind(this)}><i className="material-icons mr-0">keyboard_arrow_up</i></a>}
                        {this.props.isLast == false && <a className="btn btn-secondary btn-sm" onClick={this.downField.bind(this)}><i className="material-icons mr-0">keyboard_arrow_down</i></a>}
                    </div>

                    <div className="btn-group float-right" role="group" aria-label="Trigger actions">
                        <a className="btn btn-info btn-sm" onClick={this.addElementButton.bind(this)}>Add element button</a>
                        <a className="btn btn-warning btn-sm" onClick={this.deleteField.bind(this)}>Delete</a>
                    </div>

                </div>

                <div className="col-12">
                    <hr/>
                </div>

            </div>
        );
    }
}

export default NodeActionListItem;
