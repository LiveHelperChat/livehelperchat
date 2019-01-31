import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeActionButton from './buttons/NodeActionButton';
import NodeTriggerPayloadList from './NodeTriggerPayloadList';
import shortid from 'shortid';

class NodeTriggerActionButtons extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.removeAction = this.removeAction.bind(this);
        this.onchangeFieldAttr = this.onchangeFieldAttr.bind(this);
        this.onDeleteField = this.onDeleteField.bind(this);
        this.onMoveUpField = this.onMoveUpField.bind(this);
        this.onMoveDownField = this.onMoveDownField.bind(this);
        this.onChangeMainAttr = this.onChangeMainAttr.bind(this);
    }

    changeType(e) {
        this.props.onChangeType({id : this.props.id, 'type' : e.target.value});
    }

    removeAction() {
        this.props.removeAction({id : this.props.id});
    }

    addField() {
        this.props.addSubelement({id : this.props.id, 'path' : ['content','buttons'], 'default' : {'_id': shortid.generate(), 'type' : 'button', content : {'name' : '', 'payload' : ''}}});
    }

    onchangeFieldAttr(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','buttons',e.id].concat(e.path), value : e.value});
    }

    onDeleteField(fieldIndex) {
        this.props.deleteSubelement({id : this.props.id, 'path' : ['content','buttons',fieldIndex]});
    }

    onMoveUpField(fieldIndex) {
        this.props.moveUpSubelement({id : this.props.id, 'index' : fieldIndex, 'path' : ['content','buttons']});
    }

    onMoveDownField(fieldIndex) {
        this.props.moveDownSubelement({id : this.props.id, 'index' : fieldIndex, 'path' : ['content','buttons']});
    }

    onChangeMainAttr(field, e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','buttons_options',field], value : e});
    }

    render() {

        var button_list = [];

        if (this.props.action.hasIn(['content','buttons'])) {
            button_list = this.props.action.getIn(['content','buttons']).map((field, index) => {
                return <NodeActionButton id={index} isFirst={index == 0} isLast={index +1 == this.props.action.getIn(['content','buttons']).size} key={field.get('_id')} button={field} onMoveDownField={this.onMoveDownField} onMoveUpField={this.onMoveUpField} onDeleteField={this.onDeleteField} onChangeFieldAttr={this.onchangeFieldAttr}/>
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

                <div>
                    <label><input type="checkbox" onChange={(e) => this.onChangeMainAttr('hide_text_area',e.target.checked)} defaultChecked={this.props.action.getIn(['content','buttons_options','hide_text_area'])} /> Hide text area on response.</label> <i className="material-icons" title="Textarea to enter user message will be disabled. Make sure you include buttons for user to click.">info</i>
                </div>

                <div className="row">
                    <div className="col-12">
                        <div className="form-group">
                            <label>Default message</label>
                            <input type="text" className="form-control" onChange={(e) => this.onChangeMainAttr('message',e.target.value)} defaultValue={this.props.action.getIn(['content','buttons_options','message'])} />
                        </div>
                    </div>
                </div>

                <hr/>
                <div className="bot-buttons">
                    {button_list}
                </div>

                <button className="btn btn-info btn-sm" onClick={this.addField.bind(this)}>Add button</button>
                <hr className="hr-big" />
            </div>
        );
    }
}

export default NodeTriggerActionButtons;
