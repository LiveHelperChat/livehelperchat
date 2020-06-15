import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeActionListItem from './list/NodeActionListItem';
import NodeTriggerPayloadList from './NodeTriggerPayloadList';

import shortid from 'shortid';

class NodeTriggerActionGeneric extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.removeAction = this.removeAction.bind(this);
        this.onchangeFieldAttr = this.onchangeFieldAttr.bind(this);
        this.onDeleteField = this.onDeleteField.bind(this);
        this.onMoveUpField = this.onMoveUpField.bind(this);
        this.onMoveDownField = this.onMoveDownField.bind(this);
        this.onChangeMainAttr = this.onChangeMainAttr.bind(this);
        this.addSubelement = this.addSubelement.bind(this);
        this.onDeleteSubField = this.onDeleteSubField.bind(this);
    }

    changeType(e) {
        this.props.onChangeType({id : this.props.id, 'type' : e.target.value});
    }

    removeAction() {
        this.props.removeAction({id : this.props.id});
    }

    addField() {
        this.props.addSubelement({id : this.props.id, 'path' : ['content','list'], 'default' : {'_id': shortid.generate(), 'type' : 'none', content : {'img' : '', 'title' : '', 'subtitle' : '', 'payload' : '','subbuttons' : []}}});
    }

    addSubelement(e){
        this.props.addSubelement({id : this.props.id, 'path' : ['content','list'].concat(e.path), 'default' : e.default});
    }

    onchangeFieldAttr(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','list',e.id].concat(e.path), value : e.value});
    }

    onDeleteField(fieldIndex) {
        this.props.deleteSubelement({id : this.props.id, 'path' : ['content','list',fieldIndex]});
    }

    onDeleteSubField(e) {
        this.props.deleteSubelement({id : this.props.id, 'path' : ['content','list'].concat(e.path)});
    }

    onMoveUpField(fieldIndex) {
        this.props.moveUpSubelement({id : this.props.id, 'index' : fieldIndex, 'path' : ['content','list']});
    }

    onMoveDownField(fieldIndex) {
        this.props.moveDownSubelement({id : this.props.id, 'index' : fieldIndex, 'path' : ['content','list']});
    }

    onChangeMainAttr(field, e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','list_options',field], value : e});
    }

    render() {

        var element_list = [];

        if (this.props.action.hasIn(['content','list'])) {
            element_list = this.props.action.getIn(['content','list']).map((field, index) => {
                return <NodeActionListItem id={index} isFirst={index == 0} isLast={index +1 == this.props.action.getIn(['content','list']).size} onDeleteSubField={this.onDeleteSubField} addSubelement={this.addSubelement} key={field.get('_id')} item={field} onMoveDownField={this.onMoveDownField} onMoveUpField={this.onMoveUpField} onDeleteField={this.onDeleteField} onChangeFieldAttr={this.onchangeFieldAttr}/>
            });
        }

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
                        <button onClick={this.removeAction} type="button" className="btn btn-danger btn-sm float-right"><i className="material-icons mr-0">delete</i></button>
                    </div>
                </div>

                <div role="group">
                    <label><input type="checkbox" onChange={(e) => this.onChangeMainAttr('hide_text_area',e.target.checked)} defaultChecked={this.props.action.getIn(['content','list_options','hide_text_area'])} /> Hide text area on response.</label> <i className="material-icons" title="Textarea to enter user message will be disabled. Make sure you include buttons for user to click.">info</i>
                </div>

                <div className="bot-elements">
                    {element_list}
                    <div>
                        <a className="btn btn-info btn-sm" onClick={this.addField.bind(this)}>Add Element</a>
                    </div>
                </div>
                <hr className="hr-big" />
            </div>
        );
    }
}

export default NodeTriggerActionGeneric;