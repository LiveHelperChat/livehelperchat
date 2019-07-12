import React, { Component } from 'react';
import NodeTriggerList from '../NodeTriggerList';

class NodeEventActionTypeItem extends Component {

    constructor(props) {
        super(props);
        this.onchangeAttr = this.onchangeAttr.bind(this);
    }

    deleteField() {
        this.props.onDeleteField(this.props.id);
    }

    upField() {
        this.props.onMoveUpField(this.props.id);
    }

    downField() {
        this.props.onMoveDownField(this.props.id);
    }

    onchangeAttr(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content'].concat(e.path), value : e.value});
    }

    render() {
        return (
            <div className="row">
                <div className="col-6">
                    <div className="form-group">
                        <label>Event identifier</label>
                        <input className="form-control form-control-sm" onChange={(e) => this.onchangeAttr({'path' : ['identifier'],'value' : e.target.value})} type="text" defaultValue={this.props.button.getIn(['content','identifier'])}/>
                    </div>
                </div>
                <div className="col-6">
                    <div className="form-group">
                        <label>Trigger to execute</label>
                        <NodeTriggerList onSetPayload={(e) => this.onchangeAttr({'path' : ['trigger_id'], 'value' : e})} payload={this.props.button.getIn(['content','trigger_id'])} />
                    </div>
                </div>

                <div className="col-12">
                    <div className="btn-group float-right" role="group" aria-label="Trigger actions">
                        <button type="button" className="btn btn-warning btn-sm" onClick={this.deleteField.bind(this)}>Delete</button>
                    </div>
                </div>

                <div className="col-12">
                    <hr/>
                </div>

            </div>
        );
    }
}

export default NodeEventActionTypeItem;
