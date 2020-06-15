import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeActionListItem from './list/NodeActionListItem';
import NodeTriggerPayloadList from './NodeTriggerPayloadList';
import NodeTriggerActionQuickReply from './NodeTriggerActionQuickReply';

import shortid from 'shortid';

class NodeTriggerActionList extends Component {

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

        // General buttons as last elements
        this.addQuickReply = this.addQuickReply.bind(this);
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

    onMoveUpField(fieldIndex, path) {
        this.props.moveUpSubelement({id : this.props.id, 'index' : fieldIndex, 'path' : path});
    }

    onMoveDownField(fieldIndex, path) {
        this.props.moveDownSubelement({id : this.props.id, 'index' : fieldIndex, 'path' : path});
    }

    onChangeMainAttr(field, e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','list_options',field], value : e});
    }

    addQuickReply(e) {
        this.props.addQuickReply({id : this.props.id});
    }

    render() {

        var element_list = [];

        if (this.props.action.hasIn(['content','list'])) {
            element_list = this.props.action.getIn(['content','list']).map((field, index) => {
                return <NodeActionListItem id={index} isFirst={index == 0} isLast={index +1 == this.props.action.getIn(['content','list']).size} onDeleteSubField={this.onDeleteSubField} addSubelement={this.addSubelement} key={field.get('_id')} item={field} onMoveDownField={(e) => this.onMoveDownField(index,['content','list'])} onMoveUpField={(e) => this.onMoveUpField(index,['content','list'])} onDeleteField={this.onDeleteField} onChangeFieldAttr={this.onchangeFieldAttr}/>
            });
        }

        var button_list = [];

        if (this.props.action.hasIn(['content','quick_replies'])) {
            var totalButtons = this.props.action.getIn(['content','quick_replies']).size;
            button_list = this.props.action.getIn(['content','quick_replies']).map((reply, index) => {
                return <NodeTriggerActionQuickReply onPayloadAttrChange={(e) => this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'content', e.payload.attr], value : e.payload.value})}
                                                    onPayloadTypeChange={(e) => this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'type'], value : e.value})}
                                                    deleteReply={(e) => this.props.removeQuickReply({id : this.props.id, 'path' : ['content','quick_replies',e.id]})}
                                                    onNameChange={(e) => this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'content','name'], value : e.value})}
                                                    onPayloadChange={(e) => this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'content','payload'], value : e.value})}
                                                    upField={(e) => this.onMoveUpField(index, ['content','quick_replies'])}
                                                    downField={(e) => this.onMoveDownField(index, ['content','quick_replies'])}
                                                    isFirst={index == 0}
                                                    isLast={index + 1 == totalButtons}
                                                    id={index}
                                                    key={reply.get('_id') || index}
                                                    reply={reply} />
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
                        <button onClick={this.removeAction} type="button" className="btn btn-danger btn-sm float-right">
                            <i className="material-icons mr-0">delete</i>
                        </button>
                    </div>
                </div>

                <div className="row">
                    <div className="col-12">
            
                    <div className="float-right">
                          <label><input type="checkbox" onChange={(e) => this.onChangeMainAttr('no_highlight',e.target.checked)} defaultChecked={this.props.action.getIn(['content','list_options','no_highlight'])} /> No Highlight Top Element</label>
                    </div>
                          <label><input type="checkbox" onChange={(e) => this.onChangeMainAttr('hide_text_area',e.target.checked)} defaultChecked={this.props.action.getIn(['content','list_options','hide_text_area'])} /> Hide text area on response.</label> <i className="material-icons" title="Textarea to enter user message will be disabled. Make sure you include buttons for user to click.">info</i>

                    </div>
                </div>

                <div className="bot-elements">
                    {element_list}
                    <div>
                        <a className="btn btn-info btn-sm" onClick={this.addField.bind(this)}>Add Element</a>
                    </div>
                </div>

                <hr/>

                <div className="bot-buttons">
                    {button_list}
                    <div>
                        <a className="btn btn-info btn-sm" onClick={this.addQuickReply.bind(this)}>Add buttom</a>
                    </div>
                </div>

                <hr className="hr-big" />
            </div>
        );
    }
}

export default NodeTriggerActionList;
