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
                <div className="row">
                    <div className="col-11">
                        <NodeTriggerActionType onChange={this.changeType} type={this.props.action.get('type')} />
                    </div>
                    <div className="col-1">
                        <button onClick={this.removeAction} type="button" className="btn btn-danger btn-sm float-right">
                            <i className="material-icons mr-0">delete</i>
                        </button>
                    </div>
                </div>

                <div className="row">
                    <div className="col-6">
                        <div className="form-group">
                            <label>Attribute identifier <a title="Need help?" className="float-right" onClick={(e) => this.showHelp('attribute_identifier')}><i className="material-icons mr-0">help</i></a></label>
                            <input type="text" placeholder="Attribute identifier" className="form-control" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','identifier'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','attr_options','identifier'])} />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Attribute name <a title="Need help?" className="float-right" onClick={(e) => this.showHelp('attribute_name')}><i className="material-icons mr-0">help</i></a></label>
                            <input type="text" placeholder="Attribute name" className="form-control" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','name'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','attr_options','name'])} />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Preg match rule. <a title="Need help?" className="float-right" onClick={(e) => this.showHelp('preg_match')}><i className="material-icons mr-0">help</i></a></label>
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

                    <div className="col-6">
                        <div className="form-group">
                            <label>Intro message</label>
                            <textarea className="form-control" defaultValue={this.props.action.getIn(['content','intro_message'])} onChange={(e) => this.onchangeAttr({'path' : ['intro_message'], 'value' : e.target.value})}></textarea>
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Confirmation message</label>
                            <textarea className="form-control" defaultValue={this.props.action.getIn(['content','success_message'])} onChange={(e) => this.onchangeAttr({'path' : ['success_message'], 'value' : e.target.value})}></textarea>
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

                </div>
                <hr className="hr-big" />

            </div>
        );
    }
}

export default NodeTriggerActionAttribute;
