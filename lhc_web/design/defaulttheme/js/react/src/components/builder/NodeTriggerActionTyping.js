import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';


class NodeTriggerActionTyping extends Component {

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
                            <label>Show typing for N seconds</label>
                            <input type="text" placeholder="Value in seconds" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['duration'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','duration'])}/>
                        </div>
                        <div className="form-group">
                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['untill_message'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','untill_message'])} />OR untill next operator message</label> <i className="material-icons" title="Usefull if some background job posts a message.">info</i>
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Default typing text</label>
                            <input placeholder="Typing..." type="text" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['text'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','text'])}/>
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Delay typing appearance for n seconds</label>
                            <input placeholder="Value in seconds" type="text" className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['delay'], 'value' : e.target.value})} defaultValue={this.props.action.getIn(['content','delay'])}/>
                        </div>
                    </div>
                    <div className="col-12">
                        <div role="group">
                            <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['on_start_chat'], 'value' :e.target.checked})} defaultChecked={this.props.action.getIn(['content','on_start_chat'])} /> Send typing only at chat start.</label> <i className="material-icons" title="Typing will be send only on chat start event.">info</i>
                        </div>
                    </div>
                </div>
                <hr className="hr-big" />

            </div>
        );
    }
}

export default NodeTriggerActionTyping;
