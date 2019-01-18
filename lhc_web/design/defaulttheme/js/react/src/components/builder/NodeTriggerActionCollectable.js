import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeCollectableField from './collectable/NodeCollectableField';
import NodeTriggerPayloadList from './NodeTriggerPayloadList';
import shortid from 'shortid';
import NodeTriggerList from './NodeTriggerList';

class NodeTriggerActionCollectable extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.removeAction = this.removeAction.bind(this);
        this.onchangeFieldAttr = this.onchangeFieldAttr.bind(this);
        this.onDeleteField = this.onDeleteField.bind(this);
        this.onMoveUpField = this.onMoveUpField.bind(this);
        this.onMoveDownField = this.onMoveDownField.bind(this);
        this.payloadChange = this.payloadChange.bind(this);
        this.onChangeMainAttr = this.onChangeMainAttr.bind(this);
    }

    changeType(e) {
        this.props.onChangeType({id : this.props.id, 'type' : e.target.value});
    }

    removeAction() {
        this.props.removeAction({id : this.props.id});
    }

    addField() {
        this.props.addSubelement({id : this.props.id, 'path' : ['content','collectable_fields'], 'default' : {'_id': shortid.generate(), 'type' : 'text', content : {'message' : '', 'name' : '', 'validation' : '', 'field' : ''}}});
    }

    onchangeFieldAttr(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','collectable_fields',e.id].concat(e.path), value : e.value});
    }

    onDeleteField(fieldIndex) {
        this.props.deleteSubelement({id : this.props.id, 'path' : ['content','collectable_fields',fieldIndex]});
    }

    onMoveUpField(fieldIndex) {
        this.props.moveUpSubelement({id : this.props.id, 'index' : fieldIndex, 'path' : ['content','collectable_fields']});
    }

    onMoveDownField(fieldIndex) {
        this.props.moveDownSubelement({id : this.props.id, 'index' : fieldIndex, 'path' : ['content','collectable_fields']});
    }

    payloadChange(e) {
        console.log(e.target.value);
    }

    onChangeMainAttr(field, e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','collectable_options',field], value : e});
    }

    render() {

        var collectable_fields = [];

        if (this.props.action.hasIn(['content','collectable_fields'])) {
            collectable_fields = this.props.action.getIn(['content','collectable_fields']).map((field, index) => {
                return <NodeCollectableField id={index} isFirst={index == 0} isLast={index +1 == this.props.action.getIn(['content','collectable_fields']).size} key={field.get('_id')} field={field} onMoveDownField={this.onMoveDownField} onMoveUpField={this.onMoveUpField} onDeleteField={this.onDeleteField} onChangeFieldAttr={this.onchangeFieldAttr}/>
            });
        }

        return (
            <div>
                <div className="row">
                    <div className="col-2">
                        <div className="btn-group float-left" role="group" aria-label="Trigger actions">
                            <button disabled="disabled" className="btn btn-sm btn-info">{this.props.id + 1}</button>
                            {this.props.isFirst == false && <button className="btn btn-secondary btn-sm" onClick={(e) => this.props.upField(this.props.id)}><i className="material-icons mr-0">keyboard_arrow_up</i></button>}
                            {this.props.isLast == false && <button className="btn btn-secondary btn-sm" onClick={(e) => this.props.downField(this.props.id)}><i className="material-icons mr-0">keyboard_arrow_down</i></button>}
                        </div>
                    </div>
                    <div className="col-9">
                        <NodeTriggerActionType onChange={this.changeType} type={this.props.action.get('type')} />
                    </div>
                    <div className="col-1">
                        <button onClick={this.removeAction} type="button" className="btn btn-danger btn-sm float-right">
                            <i className="material-icons mr-0">delete</i>
                        </button>
                    </div>
                </div>

                <div className="row">
                    <div className="col-12">
                        <div className="form-group">
                            <label>Collection identifier</label>
                            <input type="text" className="form-control" onChange={(e) => this.onChangeMainAttr('identifier_collection',e.target.value)} defaultValue={this.props.action.getIn(['content','collectable_options','identifier_collection'])} />
                        </div>
                    </div>
                    <div className="col-6">
                        <label><input type="checkbox" onChange={(e) => this.onChangeMainAttr('show_summary',e.target.checked)} defaultChecked={this.props.action.getIn(['content','collectable_options','show_summary'])} />Ask user to confirm collected information</label>
                    </div>
                    <div className="col-6">
                        <label><input type="checkbox" onChange={(e) => this.onChangeMainAttr('show_summary_checkbox',e.target.checked)} defaultChecked={this.props.action.getIn(['content','collectable_options','show_summary_checkbox'])} />Ask user to confirm information by checking checkbox</label>
                    </div>
                </div>

                <div className="row">
                    <div className="col-12">
                        <div className="form-group">
                            <label>Checkbox value</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onChangeMainAttr('show_summary_checkbox_name',e.target.value)} defaultValue={this.props.action.getIn(['content','collectable_options','show_summary_checkbox_name'])} />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Confirm button value</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onChangeMainAttr('show_summary_confirm_name',e.target.value)} defaultValue={this.props.action.getIn(['content','collectable_options','show_summary_confirm_name'])} />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Cancel button value</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onChangeMainAttr('show_summary_cancel_name',e.target.value)} defaultValue={this.props.action.getIn(['content','collectable_options','show_summary_cancel_name'])} />
                        </div>
                    </div>

                    <div className="col-6">
                        <div className="form-group">
                            <label>Custom summary formation event</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onChangeMainAttr('show_summary_callback',e.target.value)} defaultValue={this.props.action.getIn(['content','collectable_options','show_summary_callback'])} />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Dispatch this event then all steps are successfuly completed</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onChangeMainAttr('collection_callback',e.target.value)} defaultValue={this.props.action.getIn(['content','collectable_options','collection_callback'])} />
                        </div>
                    </div>

                    <div className="col-12">
                        <div className="form-group">
                            <label>Question to confirm collected information</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onChangeMainAttr('collection_confirm',e.target.value)} defaultValue={this.props.action.getIn(['content','collectable_options','collection_confirm'])} />
                        </div>
                    </div>

                    <div className="col-12">
                        <div className="form-group">
                            <label>Message if user types instead of clicking confirm button</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onChangeMainAttr('collection_confirm_missing',e.target.value)} defaultValue={this.props.action.getIn(['content','collectable_options','collection_confirm_missing'])} />
                        </div>
                    </div>

                    <div className="col-6">
                        <div className="form-group">
                            <label>Argument</label>
                            <input type="text" className="form-control form-control-sm" onChange={(e) => this.onChangeMainAttr('collection_argument',e.target.value)} defaultValue={this.props.action.getIn(['content','collectable_options','collection_argument'])} />
                        </div>
                    </div>

                    <div className="col-6">
                        <div className="form-group">
                            <label>Workflow from begining expires in n minutes, 0 for unlimited</label>
                            <input type="number" className="form-control form-control-sm" onChange={(e) => this.onChangeMainAttr('expires_in',e.target.value)} defaultValue={this.props.action.getIn(['content','collectable_options','expires_in'])} />
                        </div>
                    </div>


                    <div className="col-12">
                        <p>Then process is complete send this message to user or execute trigger</p>

                        <div className="row">
                            <div className="col-6">
                                <div className="form-group">
                                    <label>Confirmation message</label>
                                    <textarea className="form-control form-control-sm" onChange={(e) => this.onChangeMainAttr('confirmation_message',e.target.value)} defaultValue={this.props.action.getIn(['content','collectable_options','confirmation_message'])}></textarea>
                                </div>
                            </div>
                            <div className="col-6">
                                <div className="form-group">
                                    <label>Execute trigger</label>
                                    <NodeTriggerList onSetPayload={(e) => this.onChangeMainAttr('collection_callback_pattern',e)} payload={this.props.action.getIn(['content','collectable_options','collection_callback_pattern'])} />
                                </div>
                            </div>
                        </div>

                        <p>Then process is canceled send this message to user or execute trigger</p>

                        <div className="row">
                            <div className="col-6">
                                <div className="form-group">
                                    <label>Cancel message</label>
                                    <textarea className="form-control form-control-sm" onChange={(e) => this.onChangeMainAttr('cancel_message',e.target.value)} defaultValue={this.props.action.getIn(['content','collectable_options','cancel_message'])}></textarea>
                                </div>
                            </div>
                            <div className="col-6">
                                <div className="form-group">
                                    <label>Execute trigger</label>
                                    <NodeTriggerList onSetPayload={(e) => this.onChangeMainAttr('collection_cancel_callback_pattern',e)} payload={this.props.action.getIn(['content','collectable_options','collection_cancel_callback_pattern'])} />
                                </div>
                            </div>
                        </div>

                        <p>Then process is expired send this message to user or execute trigger</p>

                        <div className="row">
                            <div className="col-6">
                                <div className="form-group">
                                    <label>Expire message</label>
                                    <textarea className="form-control" onChange={(e) => this.onChangeMainAttr('expire_message',e.target.value)} defaultValue={this.props.action.getIn(['content','collectable_options','expire_message'])}></textarea>
                                </div>
                            </div>
                            <div className="col-6">
                                <div className="form-group">
                                    <label>Execute trigger</label>
                                    <NodeTriggerList onSetPayload={(e) => this.onChangeMainAttr('collection_expire_callback_pattern',e)} payload={this.props.action.getIn(['content','collectable_options','collection_expire_callback_pattern'])} />
                                </div>
                            </div>
                        </div>

                    </div>

                    <div className="col-12">
                        <div className="row">
                            <div className="col-6">
                                <div className="form-group">
                                    <label>Edit collected information image URL</label>
                                    <input type="text" className="form-control" onChange={(e) => this.onChangeMainAttr('edit_image_url',e.target.value)} defaultValue={this.props.action.getIn(['content','collectable_options','edit_image_url'])} />
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <hr/>
                {collectable_fields}
                <a className="btn btn-info btn-sm" onClick={this.addField.bind(this)}>Add field</a>
                <hr className="hr-big" />
            </div>
        );
    }
}

export default NodeTriggerActionCollectable;
