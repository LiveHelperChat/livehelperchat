import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeTriggerList from './NodeTriggerList';

class NodeTriggerActionAttribute extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.removeAction = this.removeAction.bind(this);
        this.onchangeAttr = this.onchangeAttr.bind(this);
        this.showHelp = this.showHelp.bind(this);
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
    
    showHelp(e) {
        lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/'+e});
    }

    render() {
        return (
            <div>
                <div className="d-flex flex-row">
                    <div>
                        <div className="btn-group float-start" role="group" aria-label="Trigger actions">
                            <button disabled="disabled" className="btn btn-sm btn-info">{this.props.id + 1}</button>
                            {this.props.isFirst == false && <button className="btn btn-secondary btn-sm" onClick={(e) => this.props.upField(this.props.id)}><i className="material-icons me-0">keyboard_arrow_up</i></button>}
                            {this.props.isLast == false && <button className="btn btn-secondary btn-sm" onClick={(e) => this.props.downField(this.props.id)}><i className="material-icons me-0">keyboard_arrow_down</i></button>}
                        </div>
                    </div>
                    <div className="flex-grow-1 px-2">
                        <NodeTriggerActionType onChange={this.changeType} type={this.props.action.get('type')} />
                    </div>
                    <div className="pe-2">
                        <div className="input-group input-group-sm">
                            <span className="input-group-text" id="basic-addon1"><span className="material-icons">vpn_key</span></span>
                            <input type="text" className="form-control" readOnly="true" value={this.props.action.getIn(['_id'])} title="Action ID"/>
                        </div>
                    </div>
                    <div className="pe-2 pt-1 text-nowrap">
                        <label className="form-check-label" title="Response will not be executed. Usefull for a quick testing."><input onChange={(e) => this.props.onChangeContent({id : this.props.id, 'path' : ['skip_resp'], value : e.target.checked})} defaultChecked={this.props.action.getIn(['skip_resp'])} type="checkbox"/> Skip</label>
                    </div>
                    <div>
                        <button onClick={this.removeAction} type="button" className="btn btn-danger btn-sm float-end">
                            <i className="material-icons me-0">delete</i>
                        </button>
                    </div>
                </div>

                <div className="row">
                    <div className="col-6">
                        <div className="form-group">
                            <label>Attribute identifier <a title="Need help?" className="float-end" onClick={(e) => this.showHelp('attribute_identifier')}><i className="material-icons me-0">help</i></a></label>
                            <input type="text" placeholder="Attribute identifier" className="form-control" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','identifier'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','attr_options','identifier'])} />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Attribute name <a title="Need help?" className="float-end" onClick={(e) => this.showHelp('attribute_name')}><i className="material-icons me-0">help</i></a></label>
                            <input type="text" placeholder="Attribute name" className="form-control" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','name'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','attr_options','name'])} />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Preg match rule. <a title="Need help?" className="float-end" onClick={(e) => this.showHelp('preg_match')}><i className="material-icons me-0">help</i></a></label>
                            <input type="text" placeholder="Attribute name" className="form-control" onChange={(e) => this.onchangeAttr({'path' : ['preg_match'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','preg_match'])} />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Custom event to validate</label>
                            <input type="text" placeholder="Event name" className="form-control" onChange={(e) => this.onchangeAttr({'path' : ['event'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','event'])} />
                        </div>
                    </div>

                    <div className="col-6">
                        <div className="form-group">
                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','cancel_button_enabled'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','cancel_button_enabled'])} />Cancel button enabled on failed validation</label>
                        </div>
                    </div>

                    <div className="col-6">
                        <div className="form-group">
                            <label>Cancel button text</label>
                            <input type="text" placeholder="Cancel" className="form-control" onChange={(e) => this.onchangeAttr({'path' : ['cancel_button'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','cancel_button'])} />
                        </div>
                    </div>

                    <div className="col-12">
                        <div className="form-group">
                            <label>Intro message</label>
                            <textarea className="form-control" defaultValue={this.props.action.getIn(['content','intro_message'])} onChange={(e) => this.onchangeAttr({'path' : ['intro_message'], 'value' : e.target.value})}></textarea>
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Execute trigger on validation failure</label>
                            <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['attr_options','collection_callback_fail'], 'value' : e})} payload={this.props.action.getIn(['content','attr_options','collection_callback_fail'])} />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Validation error message</label>
                            <textarea className="form-control" defaultValue={this.props.action.getIn(['content','validation_error'])} onChange={(e) => this.onchangeAttr({'path' : ['validation_error'], 'value' : e.target.value})}></textarea>
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Execute trigger on success</label>
                            <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['attr_options','collection_callback_pattern'], 'value' : e})} payload={this.props.action.getIn(['content','attr_options','collection_callback_pattern'])} />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Success message</label>
                            <textarea className="form-control" defaultValue={this.props.action.getIn(['content','success_message'])} onChange={(e) => this.onchangeAttr({'path' : ['success_message'], 'value' : e.target.value})}></textarea>
                        </div>
                    </div>
                    <div className="col-12">
                        <div className="row">
                            <div className="col-6">
                                <div className="form-group">
                                    <label>Execute trigger on cancelation, overrides message on cancelation</label>
                                    <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['attr_options','collection_callback_cancel'], 'value' : e})} payload={this.props.action.getIn(['content','attr_options','collection_callback_cancel'])} />
                                </div>
                            </div>

                            <div className="col-6">
                                <div className="form-group">
                                    <label>Message on cancelation</label>
                                    <textarea className="form-control" defaultValue={this.props.action.getIn(['content','cancel_message'])} onChange={(e) => this.onchangeAttr({'path' : ['cancel_message'], 'value' : e.target.value})}></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="col-12">
                        <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['soft_event'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','soft_event'])} /> Soft event. If this event is found while cliking another button - we will automatically terminate it.</label>
                    </div>

                </div>
                <hr className="hr-big" />

            </div>
        );
    }
}

export default NodeTriggerActionAttribute;
