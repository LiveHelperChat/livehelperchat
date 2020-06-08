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

    render() {

        var button_list = [];

        if (this.props.action.hasIn(['content','conditions'])) {
            button_list = this.props.action.getIn(['content','conditions']).map((field, index) => {
                return <NodeActionConditionItem id={index} isFirst={index == 0} isLast={index +1 == this.props.action.getIn(['content','conditions']).size} key={field.get('_id')} action={field} onMoveDownField={this.onMoveDownField} onMoveUpField={this.onMoveUpField} onDeleteField={this.onDeleteField} onChangeFieldAttr={this.onchangeFieldAttr}/>
            });
        }

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
                        <div className="btn-group float-right" role="group">
                            <button type="button" onClick={this.addCondition} className="btn btn-sm btn-secondary"><i className="material-icons mr-0">add</i> Add condition</button>
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
                </div>

                <hr className="hr-big" />
            </div>
        );
    }
}

export default NodeTriggerActionConditions;
