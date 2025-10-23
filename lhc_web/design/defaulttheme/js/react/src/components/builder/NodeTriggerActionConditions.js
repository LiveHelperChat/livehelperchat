import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeTriggerList from './NodeTriggerList';
import NodeActionConditionItem from './condition/NodeActionConditionItem';
import shortid from 'shortid';

class NodeTriggerActionConditions extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.removeAction = this.removeAction.bind(this);
        this.onchangeAttr = this.onchangeAttr.bind(this);
        this.addCondition = this.addCondition.bind(this);
        this.onDeleteField = this.onDeleteField.bind(this);
        this.onchangeFieldAttr = this.onchangeFieldAttr.bind(this);
        this.onMoveUpField = this.onMoveUpField.bind(this);
        this.onMoveDownField = this.onMoveDownField.bind(this);
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

    onchangeFieldAttr(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','conditions',e.id].concat(e.path), value : e.value});
    }

    addCondition() {
        this.props.addSubelement({id : this.props.id, 'path' : ['content','conditions'], 'default' : {'_id': shortid.generate(), 'type' : 'condition', content : {'field' : '','comp' : '','val' : ''}}});
    }

    onDeleteField(fieldIndex) {
        this.props.deleteSubelement({id : this.props.id, 'path' : ['content','conditions',fieldIndex]});
    }

    onMoveUpField(fieldIndex) {
        this.props.moveUpSubelement({id : this.props.id, 'index' : fieldIndex, 'path' : ['content','conditions']});
    }

    onMoveDownField(fieldIndex) {
        this.props.moveDownSubelement({id : this.props.id, 'index' : fieldIndex, 'path' : ['content','conditions']});
    }

    render() {

        var button_list = [];

        if (this.props.action.hasIn(['content','conditions'])) {
            button_list = this.props.action.getIn(['content','conditions']).map((field, index) => {
                return <NodeActionConditionItem id={index} isFirst={index == 0} isLast={index +1 == this.props.action.getIn(['content','conditions']).size} key={field.get('_id')} action={field} onMoveDownField={this.onMoveDownField} onMoveUpField={this.onMoveUpField} onDeleteField={this.onDeleteField} onChangeFieldAttr={this.onchangeFieldAttr}/>
            });
        }

        const hasCallbackMatch = this.props.action.getIn(['content','attr_options','callback_match']);
        const hasCallbackUnmatch = this.props.action.getIn(['content','attr_options','callback_unmatch']);
        const hasConditions = this.props.action.hasIn(['content','conditions']) && this.props.action.getIn(['content','conditions']).size > 0;
        const continueAllEnabled = this.props.action.getIn(['content','attr_options','continue_all']);
        const noticeParts = [];

        if (!continueAllEnabled) {
            if (hasConditions) {
                noticeParts.push('Once these conditions pass, execution stops unless "Continue responses" is enabled.');
            }
            if (hasCallbackMatch && hasCallbackUnmatch) {
                noticeParts.push('Match and unmatch callbacks run before processing stops. No further triggers will be processed.');
            } else {
                if (hasCallbackMatch) {
                    noticeParts.push('Match callback runs before processing stops. No further triggers will be processed.');
                }
                if (hasCallbackUnmatch) {
                    noticeParts.push('Unmatch callback runs before processing stops. No further triggers will be processed.');
                } else {
                    noticeParts.push('If conditions are not met, we will continue processing further triggers.');
                }
            }               
        }

        const continueNoticeMessage = noticeParts.length > 0 ? noticeParts : null;

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
                            <span className="input-group-text" id="basic-addon1"><span className="material-icons me-0">filter_alt</span></span>
                            <input type="text" title="Bot condition - action will only execute if this condition matches. Use prefix (-) for negation. Examples: banned_user OR -banned_user" onChange={(e) => this.onchangeAttr({'path' : ['trigger_condition'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','trigger_condition'])} className="form-control form-control-sm" placeholder="vip_1 OR -vip_1" />
                        </div>
                    </div>
                    <div className="pe-2">
                        <div className="input-group input-group-sm">
                            <span className="input-group-text" id="basic-addon1"><span className="material-icons me-0">vpn_key</span></span>
                            <input type="text" className="form-control" readOnly={true} value={this.props.action.getIn(['_id'])} title="Action ID"/>
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
                    <div className="col-12">
                        <div className="btn-group float-end" role="group">
                            <button type="button" onClick={this.addCondition} className="btn btn-sm btn-secondary"><i className="material-icons me-0">add</i> Add condition</button>
                        </div>
                    </div>
                </div>

                {button_list}
                <div className="row">
                    <div className="col-6">
                        <div className="form-group">
                            <label>If conditions <b>are</b> met execute this trigger</label>
                            <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['attr_options','callback_match'], 'value' : e})} payload={this.props.action.getIn(['content','attr_options','callback_match'])} />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Schedule this trigger execution</label>
                            <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['attr_options','callback_reschedule'], 'value' : e})} payload={this.props.action.getIn(['content','attr_options','callback_reschedule'])} />
                        </div>
                    </div>
                </div>

                <div className="row">
                    <div className="col-6">
                        <div className="form-group">
                            <label>If conditions <b>are NOT</b> met execute this trigger</label>
                            <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['attr_options','callback_unmatch'], 'value' : e})} payload={this.props.action.getIn(['content','attr_options','callback_unmatch'])} />
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Schedule this trigger execution</label>
                            <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['attr_options','callback_unreschedule'], 'value' : e})} payload={this.props.action.getIn(['content','attr_options','callback_unreschedule'])} />
                        </div>
                    </div>
                    <div className="col-12">
                        <div className="form-group">
                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','continue_all'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','continue_all'])} /> If conditions are matched continue executing responses.</label>
                            <p><small>By default if conditions are met we execute trigger and stop any futher responses execution.</small></p>
                        </div>
                        <div className="form-group">
                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','compare_live'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','compare_live'])} /> Take lhc.* attributes value directly from database.</label>
                            <p><small>Usefull in case some other process might have updated comparable variable already. E.g Chat status was update to pending while Rest APi call was going.</small></p>
                        </div>
                        <div className="form-group">
                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','log_matched'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','log_matched'])} /> Log detailed information in the log if conditions match</label>
                        </div>
                        <div className="form-group">
                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['attr_options','log_un_matched'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','attr_options','log_un_matched'])} /> Log detailed information in the log if conditions does not match</label>
                        </div>
                    </div>
                    <div className="col-12">
                        <div></div>
                    </div>
                </div>
                {continueNoticeMessage && (
                    <div className="alert alert-warning mt-2 mb-2">
                        <strong>Notice:</strong>
                        <ul className="mb-0 mt-1">
                            {continueNoticeMessage.map((notice, index) => (
                                <li key={index}><small>{notice}</small></li>
                            ))}
                        </ul>
                    </div>
                )}
                <hr className="hr-big" />
            </div>
        );
    }
}

export default NodeTriggerActionConditions;
