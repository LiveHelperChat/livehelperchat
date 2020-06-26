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
                            <label>Command</label>
                            <select className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['command'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','command'])}>
                                <option value="">Select action</option>
                                <option value="stopchat">Stop chat and transfer to human</option>
                                <option value="transfertobot">Transfer chat to bot</option>
                                <option value="closechat">Close chat</option>
                                <option value="chatvariable">Set chat variable [not visible by operator]</option>
                                <option value="chatattribute">Set chat additional attribute [visible by operator]</option>
                                <option value="dispatchevent">Dispatch Event</option>
                                <option value="setchatattribute">Update main chat attribute</option>
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

                {this.props.action.getIn(['content','command']) == 'chatvariable' &&
                <div>
                    <div className="form-group">
                        <label>Set chat variables in JSON format.</label>
                        <input className="form-control form-control-sm" type="text" placeholder="{&quot;bot_touched&quot;:true}" onChange={(e) => this.onchangeAttr({'path':['payload'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload'])} />
                    </div>
                </div>}

                {this.props.action.getIn(['content','command']) == 'chatattribute' &&
                <div>
                    <div className="form-group">
                        <label>Set chat attribute in JSON format.</label>
                        <input className="form-control form-control-sm" type="text" placeholder="[{&quot;value&quot;:&quot;Attribute value or {content_1}&quot;,&quot;identifier&quot;:&quot;attribute_name&quot;,&quot;key&quot;:&quot;Attribute Name&quot;}]" onChange={(e) => this.onchangeAttr({'path':['payload'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload'])} />
                    </div>
                </div>}

                {this.props.action.getIn(['content','command']) == 'setchatattribute' &&
                <div>
                    <div className="form-group">
                        <label>Chat attribute name (nick,remarks,email,dep_id).</label>
                        <input className="form-control form-control-sm" type="text" placeholder="remarks" onChange={(e) => this.onchangeAttr({'path':['payload'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload'])} />
                    </div>
                    <div className="form-group">
                        <label>Chat attribute value.</label>
                        <textarea className="form-control form-control-sm" type="text" placeholder="" onChange={(e) => this.onchangeAttr({'path':['payload_arg'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload_arg'])} ></textarea>
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
                        <input className="form-control form-control-sm" type="text" onChange={(e) => this.onchangeAttr({'path':['payload_arg'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload_arg'])} />
                    </div>
                </div>}

                <hr className="hr-big" />
            </div>
        );
    }
}

export default NodeTriggerActionCommand;
