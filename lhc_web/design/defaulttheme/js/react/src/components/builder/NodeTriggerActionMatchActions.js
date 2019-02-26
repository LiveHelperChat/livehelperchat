import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeTriggerList from './NodeTriggerList';

class NodeTriggerActionMatchActions extends Component {

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
                        <label><input type="checkbox" onChange={(e) => this.onchangeAttr({'path' : ['event_background'],'value' : e.target.checked})} defaultChecked={this.props.action.getIn(['content','event_background'])} /> Event is processed on next visitor message.</label>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>For what start chat actions to search</label>
                            <select className="form-control form-control-sm" defaultValue={this.props.action.getIn(['content','on_start_type'])} onChange={(e) => this.onchangeAttr({'path' : ['on_start_type'], 'value' : e.target.value})}>
                                <option value="0">Do not check on chat start</option>
                                <option value="1">Instant execution (Executes and continues workflow)</option>
                                <option value="2">Instant execution and block (executes and blocks further triggers execution)</option>
                                <option value="3">Instant execution and continue if stop is returned from this trigger</option>
                                <option value="4">Schedule (schedules for further execution trigger)</option>
                                <option value="5">Any</option>
                            </select>
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>If no trigger was found execute this</label>
                            <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['alternative_callback'], 'value' : e})} payload={this.props.action.getIn(['content','alternative_callback'])} />
                        </div>
                    </div>
                </div>
                <hr className="hr-big" />
            </div>
        );
    }
}

export default NodeTriggerActionMatchActions;
