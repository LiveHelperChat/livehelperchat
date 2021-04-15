import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeEventActionTypeItem from './events/NodeEventActionTypeItem';
import shortid from 'shortid';

class NodeTriggerActionEventType extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.removeAction = this.removeAction.bind(this);
        this.onchangeAttr = this.onchangeAttr.bind(this);
        this.onchangeFieldAttr = this.onchangeFieldAttr.bind(this);
        this.onDeleteField = this.onDeleteField.bind(this);
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

    addField() {
        this.props.addSubelement({id : this.props.id, 'path' : ['content','events'], 'default' : {'_id': shortid.generate(), 'type' : 'button', content : {'identifier' : '', 'trigger_id' : ''}}});
    }

    onDeleteField(fieldIndex) {
        this.props.deleteSubelement({id : this.props.id, 'path' : ['content','events',fieldIndex]});
    }

    onchangeFieldAttr(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','events',e.id].concat(e.path), value : e.value});
    }

    render() {

        var event_list = [];

        if (this.props.action.hasIn(['content','events'])) {
            event_list = this.props.action.getIn(['content','events']).map((field, index) => {
                return <NodeEventActionTypeItem id={index} isFirst={index == 0} isLast={index +1 == this.props.action.getIn(['content','events']).size} key={field.get('_id')} button={field} onMoveDownField={this.onMoveDownField} onMoveUpField={this.onMoveUpField} onDeleteField={this.onDeleteField} onChangeFieldAttr={this.onchangeFieldAttr}/>
            });
        }

        return (
            <div>
                <div className="d-flex flex-row">
                    <div>
                        <div className="btn-group float-left" role="group" aria-label="Trigger actions">
                            <button disabled="disabled" className="btn btn-sm btn-info">{this.props.id + 1}</button>
                            {this.props.isFirst == false && <button className="btn btn-secondary btn-sm" onClick={(e) => this.props.upField(this.props.id)}><i className="material-icons mr-0">keyboard_arrow_up</i></button>}
                            {this.props.isLast == false && <button className="btn btn-secondary btn-sm" onClick={(e) => this.props.downField(this.props.id)}><i className="material-icons mr-0">keyboard_arrow_down</i></button>}
                        </div>
                    </div>
                    <div className="flex-grow-1 px-2">
                        <NodeTriggerActionType onChange={this.changeType} type={this.props.action.get('type')} />
                    </div>
                    <div className="pr-2 pt-1">
                        <label className="form-check-label" title="Response will not be executed. Usefull for a quick testing."><input onChange={(e) => this.props.onChangeContent({id : this.props.id, 'path' : ['skip_resp'], value : e.target.checked})} defaultChecked={this.props.action.getIn(['skip_resp'])} type="checkbox"/> Skip</label>
                    </div>
                    <div>
                        <button onClick={this.removeAction} type="button" className="btn btn-danger btn-sm float-right">
                            <i className="material-icons mr-0">delete</i>
                        </button>
                    </div>
                </div>

                <hr/>
                <div className="bot-buttons">
                    {event_list}
                </div>

                <button className="btn btn-info btn-sm" onClick={this.addField.bind(this)}>Add event</button>

                <hr className="hr-big" />

            </div>
        );
    }
}

export default NodeTriggerActionEventType;
