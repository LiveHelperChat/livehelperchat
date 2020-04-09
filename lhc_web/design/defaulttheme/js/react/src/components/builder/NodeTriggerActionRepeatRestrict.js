import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeTriggerList from './NodeTriggerList';

class NodeTriggerActionRepeatRestrict extends Component {

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
                    <div className="col-6">
                        <div className="form-group">
                            <label>How many times allow to execute this trigger</label>
                            <input type="text" className="form-control form-control-sm" defaultValue={this.props.action.getIn(['content','repeat_count'])} onChange={(e) => this.onchangeAttr({'path' : ['repeat_count'], 'value' : e.target.value})}/>
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="form-group">
                            <label>Repeat identifier</label>
                            <input type="text" maxLength="20" placeholder="E.g global_counter" title="If you define identifier counter will be using it for repeatable identification so you can use same identifier in multiple places." className="form-control form-control-sm" defaultValue={this.props.action.getIn(['content','identifier'])} onChange={(e) => this.onchangeAttr({'path' : ['identifier'], 'value' : e.target.value})}/>
                        </div>
                    </div>
                    <div className="col-12">
                        <div className="form-group">
                            <label>If limit is reached execute this trigger</label>
                            <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['alternative_callback'], 'value' : e})} payload={this.props.action.getIn(['content','alternative_callback'])} />
                        </div>
                    </div>
                </div>
                <hr className="hr-big" />
            </div>
        );
    }
}

export default NodeTriggerActionRepeatRestrict;
