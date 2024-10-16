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
                                                    onButtonNoName={(e) => this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'content','no_name'], value : e.value})}
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
                <div className="d-flex flex-row">
                    <div>
                        <div className="btn-group float-start" role="group" aria-label="Trigger actions">
                            <button disabled="disabled" className="btn btn-sm btn-info">{this.props.id + 1}</button>
                            {this.props.isFirst == false && <button className="btn btn-secondary btn-sm" onClick={(e) => this.props.upField(this.props.id)}><i className="material-icons me-0">keyboard_arrow_up</i></button>}
                            {this.props.isLast == false && <button className="btn btn-secondary btn-sm" onClick={(e) => this.props.downField(this.props.id)}><i className="material-icons me-0">keyboard_arrow_down</i></button>}
                        </div>
                    </div>
                    <div className="flex-grow-1 px-2">
                        <NodeTriggerActionType onChange={this.changeType} type={this.props.action.get('type')} />
                    </div>
                    <div className="pe-2">
                        <div className="input-group input-group-sm">
                            
                                <span className="input-group-text" id="basic-addon1"><span className="material-icons">vpn_key</span></span>

                            <input type="text" className="form-control" readOnly={true} value={this.props.action.getIn(['_id'])} title="Action ID"/>
                        </div>
                    </div>
                    <div className="pe-2 pt-1 text-nowrap">
                        <label className="form-check-label" title="Response will not be executed. Usefull for a quick testing."><input onChange={(e) => this.props.onChangeContent({id : this.props.id, 'path' : ['skip_resp'], value : e.target.checked})} defaultChecked={this.props.action.getIn(['skip_resp'])} type="checkbox"/> Skip</label>
                    </div>
                    <div>
                        <button onClick={this.removeAction} type="button" className="btn btn-danger btn-sm float-end">
                            <i className="material-icons me-0">delete</i>
                        </button>
                    </div>
                </div>

                <div className="row">
                    <div className="col-6">
                        <div>
                            <label><input type="checkbox" onChange={(e) => this.onChangeMainAttr('no_highlight', e.target.checked)} defaultChecked={this.props.action.getIn(['content','list_options','no_highlight'])} /> No Highlight Top Element</label>
                        </div>
                        <div role="group">
                            <label><input type="checkbox" onChange={(e) => this.onChangeMainAttr('auto_translate', e.target.checked)} defaultChecked={this.props.action.getIn(['content','list_options','auto_translate'])} /> Automatic translations.</label> <i className="material-icons" title="If you have enabled automatic translations for translation group we will translate this message. You can't mix manual and automatic translations in the same message. Before final save we will translate all response including buttons to visitor language.">info</i>
                        </div>
                    </div>
                    <div className="col-6">
                          <label><input type="checkbox" onChange={(e) => this.onChangeMainAttr('hide_text_area', e.target.checked)} defaultChecked={this.props.action.getIn(['content','list_options','hide_text_area'])} /> Hide text area on response.</label> <i className="material-icons" title="Textarea to enter user message will be disabled. Make sure you include buttons for user to click.">info</i>
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
