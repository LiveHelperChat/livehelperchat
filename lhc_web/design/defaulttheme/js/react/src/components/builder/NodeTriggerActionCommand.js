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
                            {this.props.isFirst == false && <a className="btn btn-secondary btn-sm" onClick={(e) => this.props.upField(this.props.id)}><i className="material-icons mr-0">keyboard_arrow_up</i></a>}
                            {this.props.isLast == false && <a className="btn btn-secondary btn-sm" onClick={(e) => this.props.downField(this.props.id)}><i className="material-icons mr-0">keyboard_arrow_down</i></a>}
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
                            <select className="form-control" onChange={(e) => this.onchangeAttr({'path' : ['command'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','command'])}>
                                <option value="">Select action</option>
                                <option value="stopchat">Stop chat and transfer to human</option>
                                <option value="transfertobot">Transfer chat to bot</option>
                                <option value="closechat">Close chat</option>
                                <option value="chatvariable">Set chat variable</option>
                            </select>
                        </div>
                    </div>
                </div>

                {this.props.action.getIn(['content','command']) == 'stopchat' &&
                <div>
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
                        <label>Set chat variables in json format.</label>
                        <input className="form-control" type="text" placeholder="{&quot;bot_touched&quot;:true}" onChange={(e) => this.onchangeAttr({'path':['payload'],'value':e.target.value})} defaultValue={this.props.action.getIn(['content','payload'])} />
                    </div>
                </div>}

                <hr/>
            </div>
        );
    }
}

export default NodeTriggerActionCommand;
