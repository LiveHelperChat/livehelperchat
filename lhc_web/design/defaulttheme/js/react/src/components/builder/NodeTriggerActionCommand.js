import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeTriggerList from './NodeTriggerList';

class NodeTriggerActionCommand extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.removeAction = this.removeAction.bind(this);
        this.onchangeAttr = this.onchangeAttr.bind(this);
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
                    <div className="col-12">
                        <div className="form-group">
                            <label>Command</label>
                            <select className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['command'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','command'])}>
                                <option value="">Select action</option>
                                <optgroup label="Chat related">
                                    <option value="stopchat">Stop chat and transfer to human</option>
                                    <option value="transfertobot">Transfer chat to bot</option>
                                    <option value="closechat">Close chat</option>
                                    <option value="chatvariable">Set chat variable [not visible by operator]</option>
                                    <option value="chatattribute">Set chat additional attribute [visible by operator]</option>
                                    <option value="dispatchevent">Dispatch Event</option>
                                    <option value="setchatattribute">Update main chat attribute</option>
                                    <option value="setdepartment">Change department</option>
                                    <option value="setsubject">Set subject</option>
                                    <option value="setliveattr">Set widget live attribute</option>
                                    <option value="removeprocess">Remove any previous process</option>
                                </optgroup>
                                <optgroup label="Chat messages aggregation">
                                    <option value="messageaggregation">Messages aggregation</option>
                                </optgroup>
                                <optgroup label="Visitor message related">
                                    <option value="messageattribute">Update main message attribute</option>
                                    <option value="metamsg">Set meta_msg attribute</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                </div>

                {this.props.action.getIn(['content','command']) == 'closechat' && <div>
                    <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['close_widget'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','close_widget'])} /> Close widget also.</label>
                </div>}

                {this.props.action.getIn(['content','command']) == 'stopchat' &&
                <div>
                    <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['payload_ignore_status'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','payload_ignore_status'])} /> Ignore department status and always transfer to operator.</label>
                    <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['payload_ignore_dep_hours'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','payload_ignore_dep_hours'])} /> Ignore department online hours. This will force chat to be transfered to operator only if there is logged operators.</label>

                    <div className="row">
                        <div className="col-6">
                            <div className="form-group">
                                <label>Search only for operators with this field</label>
                                <input className="form-control form-control-sm" type="text" placeholder="email" onChange={(e) => this.onchangeAttr({'path':['payload_attr'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload_attr'])} />
                            </div>
                        </div>
                        <div className="col-6">
                            <div className="form-group">
                                <label>equal to</label>
                                <input className="form-control form-control-sm" type="text" placeholder="" onChange={(e) => this.onchangeAttr({'path':['payload_val'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload_val'])} />
                            </div>
                        </div>
                    </div>

                    <div className="form-group">
                        <label>If there is no online operators send this trigger to user</label>
                        <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path':['payload'],'value':e})} payload={this.props.action.getIn(['content','payload'])} />
                    </div>
                    <div className="form-group">
                        <label>If there is online operators send this trigger to user</label>
                        <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path':['payload_online'],'value':e})} payload={this.props.action.getIn(['content','payload_online'])} />
                    </div>
                </div>}

                {this.props.action.getIn(['content','command']) == 'transfertobot' &&
                <div>
                    <div className="form-group">
                        <label>What trigger to execute once chat is transfered to bot</label>
                        <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path':['payload'],'value':e})} payload={this.props.action.getIn(['content','payload'])} />
                    </div>
                </div>}

                {this.props.action.getIn(['content','command']) == 'metamsg' &&
                <div>
                    <div className="form-group">
                        <label>Set meta_msg variables in JSON format. It will merge with existing one.</label>
                        <input className="form-control form-control-sm" type="text" placeholder="{&quot;bot_touched&quot;:true}" onChange={(e) => this.onchangeAttr({'path':['payload'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload'])} />
                    </div>
                </div>}

                {this.props.action.getIn(['content','command']) == 'chatvariable' &&
                <div>

                    <div className="form-group">
                        <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['update_if_empty'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','update_if_empty'])} /> Update only if empty</label>
                    </div>

                    <div className="form-group">
                        <label>Set chat variables in JSON format.</label>
                        <input className="form-control form-control-sm" type="text" placeholder="{&quot;bot_touched&quot;:true}" onChange={(e) => this.onchangeAttr({'path':['payload'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload'])} />
                    </div>
                </div>}

                {this.props.action.getIn(['content','command']) == 'setliveattr' && <div>
                    <div className="form-group">
                        <label>Live attribute path</label>
                        <input className="form-control form-control-sm" type="text" placeholder="['chat_ui','survey_id']" onChange={(e) => this.onchangeAttr({'path':['payload'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload'])} />
                    </div>

                    <div className="form-group">
                        <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['remove_subject'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','remove_subject'])} /> Remove attribute if it exists.</label>
                    </div>

                    <div className="form-group">
                        <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['remove_if_empty'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','remove_if_empty'])} /> Remove attribute if it's value is empty (0,"",null).</label>
                    </div>

                    {!this.props.action.getIn(['content','remove_subject']) && <div className="form-group">
                        <label>Live attribute value in JSON format.</label>
                        <textarea className="form-control form-control-sm" type="text" placeholder="" onChange={(e) => this.onchangeAttr({'path':['payload_arg'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload_arg'])} ></textarea>
                    </div>}
                </div>}

                {this.props.action.getIn(['content','command']) == 'chatattribute' &&
                <div>

                    <div className="form-group">
                        <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['update_if_empty'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','update_if_empty'])} /> Update only if empty</label>
                    </div>

                    <div className="form-group">
                        <label>Set chat attribute in JSON format.</label>
                        <input className="form-control form-control-sm" type="text" placeholder="[{&quot;value&quot;:&quot;Attribute value or {content_1}&quot;,&quot;identifier&quot;:&quot;attribute_name&quot;,&quot;key&quot;:&quot;Attribute Name&quot;}]" onChange={(e) => this.onchangeAttr({'path':['payload'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload'])} />
                    </div>
                </div>}

                {this.props.action.getIn(['content','command']) == 'setsubject' &&
                <div>
                    <div className="form-group">
                        <label>Set subject for a chat. Enter subject ID from the subject list.</label>
                        <input className="form-control form-control-sm" type="text" placeholder="Please enter Subject ID." onChange={(e) => this.onchangeAttr({'path':['payload'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload'])} />
                    </div>
                    <div className="form-group">
                        <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['remove_subject'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','remove_subject'])} /> Remove subject. Instead of adding we will remove subject.</label>
                    </div>
                </div>}

                {this.props.action.getIn(['content','command']) == 'messageattribute' &&
                    <div>
                        <div className="form-group">
                            <label>Main message attribute (msg,name_support,user_id,chat_id,time)</label>
                            <input className="form-control form-control-sm" type="text" placeholder="remarks" onChange={(e) => this.onchangeAttr({'path':['payload'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload'])} />
                        </div>
                        <div className="form-group">
                            <label>Message attribute value.</label>
                            <textarea className="form-control form-control-sm" type="text" placeholder="" onChange={(e) => this.onchangeAttr({'path':['payload_arg'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload_arg'])} ></textarea>
                        </div>
                    </div>}

                {this.props.action.getIn(['content','command']) == 'messageaggregation' &&
                    <div>
                        <div className="row">
                            <div className="col-6">
                                <div className="form-group">
                                    <label>Chat variable as Group field*</label>
                                    <input className="form-control form-control-sm" type="text" onChange={(e) => this.onchangeAttr({'path':['payload'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload'])} />
                                </div>
                            </div>

                            {this.props.action.getIn(['content','payload_arg_type']) != 'count_filter' && this.props.action.getIn(['content','payload_arg_type']) != 'count' && this.props.action.getIn(['content','payload_arg_type']) != 'ratio' && <div className="col-6">
                                <div className="form-group">
                                    <label>Calculated value from group method*</label>
                                    <input className="form-control form-control-sm" type="text" onChange={(e) => this.onchangeAttr({'path':['payload_cond_field'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload_cond_field'])} />
                                </div>
                            </div>}

                            <div className="col-12">
                                <div className="form-group">
                                    <label>Group method*</label>
                                    <select className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['payload_arg_type'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','payload_arg_type'])}>
                                        <option value="">Select group logic</option>
                                        <optgroup label="Grouping">
                                            <option value="avg">AVG</option>
                                            <option value="sum">SUM</option>
                                            <option value="sum_avg">SUM as comparator and AVG as value</option>
                                            <option value="max">MAX</option>
                                            <option value="min">MIN</option>
                                            <option value="count_max">COUNT MAX (maximum number of grouped record)</option>
                                        </optgroup>
                                        <optgroup label="Counting">
                                            <option value="count">COUNT (total number of messages)</option>
                                            <option value="count_filter">COUNT FILTER (filtered by group value field)</option>
                                            <option value="ratio">RATIO in comparison with all messages</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>

                            {this.props.action.getIn(['content', 'payload_arg_type']) != 'count' &&
                                <div className="col-6">
                                    <div className="form-group">
                                        <label>Group field (sentiment)*</label>
                                        <input className="form-control form-control-sm" type="text"
                                               onChange={(e) => this.onchangeAttr({
                                                   'path': ['payload_arg_field'],
                                                   'value': e.target.value
                                               })}
                                               defaultValue={this.props.action.getIn(['content', 'payload_arg_field'])}/>
                                    </div>
                                </div>
                            }

                            {this.props.action.getIn(['content','payload_arg_type']) != 'count' &&
                            <div className="col-6">
                                {this.props.action.getIn(['content','payload_arg_type']) == 'ratio' &&
                                 <div className="form-group">
                                    <label>Group value field. Eg (score field of the sentiment)</label>
                                    <input className="form-control form-control-sm" type="text" onChange={(e) => this.onchangeAttr({'path':['payload_arg_val_field'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload_arg_val_field'])} />
                                </div>}
                                <div className="form-group">
                                    {(this.props.action.getIn(['content','payload_arg_type']) == 'count_filter' || this.props.action.getIn(['content','payload_arg_type']) == 'ratio') && <label>Filter value*</label>}
                                    {this.props.action.getIn(['content','payload_arg_type']) != 'count_filter' && this.props.action.getIn(['content','payload_arg_type']) != 'ratio' && <label>Group value field. Eg (score field of the sentiment)*</label>}
                                    <input className="form-control form-control-sm" type="text" onChange={(e) => this.onchangeAttr({'path':['payload_arg_val'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload_arg_val'])} />
                                </div>
                            </div>}

                            {['ratio','avg','sum_avg','max','min','count_max'].indexOf(this.props.action.getIn(['content','payload_arg_type'])) !== -1  &&
                                <div className="col-6">
                                    <div className="form-group">
                                        <label>Allowed values. Separated by comma.</label>
                                        <input className="form-control form-control-sm" placeholder="negative,positive" type="text" onChange={(e) => this.onchangeAttr({'path':['payload_arg_val_sum'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload_arg_val_sum'])} />
                                    </div>
                                </div>
                            }

                            {['ratio','avg','sum_avg','max','min','count_max'].indexOf(this.props.action.getIn(['content','payload_arg_type'])) !== -1  &&
                                <div className="col-6">
                                    <div className="form-group">
                                        <label>Threshold, minimum value. Optional.</label>
                                        <input className="form-control form-control-sm" placeholder="0.8" type="text" onChange={(e) => this.onchangeAttr({'path':['payload_arg_val_trshl'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload_arg_val_trshl'])} />
                                    </div>
                                </div>
                            }

                            <div className="col-12">
                                <label>Messages to include</label>
                                <div className="form-group">
                                    <div className="row">
                                        <div className="col-6">
                                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['msg_type_all'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','msg_type_all'])} /> All messages</label>
                                        </div>
                                        <div className="col-6">
                                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['msg_type_vis'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','msg_type_vis'])} /> All visitor messages</label>
                                        </div>
                                        <div className="col-6">
                                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['msg_type_vis_bot'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','msg_type_vis_bot'])} /> Visitor messages with a bot</label>
                                        </div>
                                        <div className="col-6">
                                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['msg_type_vis_op'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','msg_type_vis_op'])} /> Visitor messages with an operator</label>
                                        </div>
                                        <div className="col-6">
                                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['msg_type_op'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','msg_type_op'])} /> Operator messages</label>
                                        </div>
                                        <div className="col-6">
                                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['msg_type_bot'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','msg_type_bot'])} /> Bot messages</label>
                                        </div>
                                    </div>
                                </div>

                                <label>Number of messages to include</label>
                                <select className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['payload_include_number'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','payload_include_number'])}>
                                    <option value="">All messages</option>
                                    <option value="f10">First 10% of the messages</option>
                                    <option value="f20">First 20% of the messages</option>
                                    <option value="f30">First 30% of the messages</option>
                                    <option value="f40">First 40% of the messages</option>
                                    <option value="f50">First 50% of the messages</option>
                                    <option value="f60">First 60% of the messages</option>
                                    <option value="f70">First 70% of the messages</option>
                                    <option value="f80">First 80% of the messages</option>
                                    <option value="f90">First 90% of the messages</option>
                                    <option value="l10">Last 10% of the messages</option>
                                    <option value="l20">Last 20% of the messages</option>
                                    <option value="l30">Last 30% of the messages</option>
                                    <option value="l40">Last 40% of the messages</option>
                                    <option value="l50">Last 50% of the messages</option>
                                    <option value="l60">Last 60% of the messages</option>
                                    <option value="l70">Last 70% of the messages</option>
                                    <option value="l80">Last 80% of the messages</option>
                                    <option value="l90">Last 90% of the messages</option>
                                </select>


                            </div>
                        </div>
                    </div>}

                {this.props.action.getIn(['content','command']) == 'setchatattribute' &&
                <div>
                    <div className="form-group">
                        <label>Chat attribute name (nick,remarks,email,dep_id).</label>
                        <input className="form-control form-control-sm" type="text" placeholder="remarks" onChange={(e) => this.onchangeAttr({'path':['payload'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload'])} />
                    </div>

                    <div className="form-group">
                        <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['update_if_empty'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','update_if_empty'])} /> Update only if empty</label>
                    </div>

                    <div className="form-group">
                        <label>Chat attribute value.</label>
                        <textarea className="form-control form-control-sm" type="text" placeholder="" onChange={(e) => this.onchangeAttr({'path':['payload_arg'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload_arg'])} ></textarea>
                    </div>
                </div>}

                {this.props.action.getIn(['content','command']) == 'setdepartment' &&
                <div>
                    <div className="form-group">
                        <label>Enter department ID or Brand Role</label>
                        <input className="form-control form-control-sm" type="text" placeholder="Department ID or Brand Role" onChange={(e) => this.onchangeAttr({'path':['payload'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload'])} />
                    </div>
                </div>}

                {this.props.action.getIn(['content','command']) == 'dispatchevent' &&
                <div>
                    <div className="form-group">
                        <label>Event Name</label>
                        <input className="form-control form-control-sm" type="text" placeholder="SetSubjectExtension" onChange={(e) => this.onchangeAttr({'path':['payload'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload'])} />
                    </div>
                    <div className="form-group">
                        <label>Event argument</label>
                        <textarea className="form-control form-control-sm" type="text" onChange={(e) => this.onchangeAttr({'path':['payload_arg'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload_arg'])} ></textarea>
                    </div>
                </div>}

                <hr className="hr-big" />
            </div>
        );
    }
}

export default NodeTriggerActionCommand;
