import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeCollectableField from './collectable/NodeCollectableField';

class NodeTriggerActionCollectable extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.removeAction = this.removeAction.bind(this);
    }

    changeType(e) {
        this.props.onChangeType({id : this.props.id, 'type' : e.target.value});
    }

    removeAction() {
        this.props.removeAction({id : this.props.id});
    }

    addField() {
        this.props.addSubelement({id : this.props.id, 'path' : ['content','collectable_fields'], 'default' : [{'type' : 'text', content : {'message' : '', 'name' : '', 'field' : ''}}]});
    }

    render() {

        var collectable_fields = [];

        if (this.props.action.hasIn(['content','collectable_fields'])) {
            collectable_fields = this.props.action.getIn(['content','collectable_fields']).map((field, index) => {
                return <NodeCollectableField id={index} key={index} reply={field} />
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
                {collectable_fields}
                <a className="btn btn-info btn-sm" onClick={this.addField.bind(this)}>Add field</a>
                <hr/>
            </div>
        );
    }
}

export default NodeTriggerActionCollectable;
