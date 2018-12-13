import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeTriggerList from './NodeTriggerList';
import NodeActionIntentItem from './intent/NodeActionIntentItem';
import shortid from 'shortid';

class NodeTriggerActionIntent extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.removeAction = this.removeAction.bind(this);
        this.onchangeAttr = this.onchangeAttr.bind(this);
        this.addIntent = this.addIntent.bind(this);
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
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','intents',e.id].concat(e.path), value : e.value});
    }

    addIntent() {
        this.props.addSubelement({id : this.props.id, 'path' : ['content','intents'], 'default' : {'_id': shortid.generate(), 'type' : 'intent', content : {'words' : ''}}});
    }

    onDeleteField(fieldIndex) {
        this.props.deleteSubelement({id : this.props.id, 'path' : ['content','intents',fieldIndex]});
    }

    render() {

        var button_list = [];

        if (this.props.action.hasIn(['content','intents'])) {
            button_list = this.props.action.getIn(['content','intents']).map((field, index) => {
                return <NodeActionIntentItem id={index} isFirst={index == 0} isLast={index +1 == this.props.action.getIn(['content','intents']).size} key={field.get('_id')} action={field} onMoveDownField={this.onMoveDownField} onMoveUpField={this.onMoveUpField} onDeleteField={this.onDeleteField} onChangeFieldAttr={this.onchangeFieldAttr}/>
            });
        }

        return (
            <div>
                <div className="row">
                    <div className="col-xs-11">
                        <NodeTriggerActionType onChange={this.changeType} type={this.props.action.get('type')} />
                    </div>
                    <div className="col-xs-1">
                        <button onClick={this.removeAction} type="button" className="btn btn-danger btn-sm pull-right">
                            <i className="material-icons mr-0">delete</i>
                        </button>
                    </div>
                </div>
                <div className="row">
                    <div className="col-xs-12">
                        <div className="btn-group pull-right" role="group">
                            <a onClick={this.addIntent} className="btn btn-xs btn-default"><i className="material-icons mr-0">add</i> Add intent detection</a>
                        </div>
                    </div>
                </div>

                {button_list}
                <hr/>

            </div>
        );
    }
}

export default NodeTriggerActionIntent;
