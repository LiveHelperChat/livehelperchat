import React, { Component } from 'react';
import NodeTriggerPayloadList from './NodeTriggerPayloadList';

class NodeTriggerCallbackItem extends Component {

    constructor(props) {
        super(props);
        this.onChangeMainAttr = this.onChangeMainAttr.bind(this);
    }

    deleteField() {
        this.props.onDeleteField(this.props.id);
    }

    onChangeType(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','type'], value : e.target.value});
    }

    onChangeField(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','field'], value : e.target.value});
    }

    onChangeEvent(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','event'], value : e.target.value});
    }

    onChangeSuccessCallback(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','success_callback'], value : e.target.value});
    }

    onChangeMainAttr(field, e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content',field], value : e});
    }

    render() {

        return (
            <div className="row">

                <div className="col-3">
                    <div className="form-group">
                        <label>Type</label>
                        <select defaultValue={this.props.callback.getIn(['content','type'])} className="form-control" onChange={this.onChangeType.bind(this)}>
                            <option value="">Choose what should be updated</option>
                            <option value="chat">Set chat attribute</option>
                        </select>
                    </div>
                </div>

                <div className="col-3">
                    <div className="form-group">
                        <label>Attribute</label>
                        <select defaultValue={this.props.callback.getIn(['content','field'])} className="form-control" onChange={this.onChangeField.bind(this)}>
                            <option value="">Choose what attribute should be set</option>
                            <option value="email">E-mail</option>
                            <option value="phone">Phone</option>
                        </select>
                    </div>
                </div>

                <div className="col-3">
                    <div className="form-group">
                        <label>Custom event to validate</label>
                        <input type="text" defaultValue={this.props.callback.getIn(['content','event'])} onChange={this.onChangeEvent.bind(this)} className="form-control"/>
                    </div>
                </div>

                <div className="col-3">
                    <div className="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <a onClick={this.deleteField.bind(this)}><i className="material-icons mr-0">delete</i></a>
                        </div>
                    </div>
                </div>

                <div className="col-6">
                    <div className="form-group">
                        <label>Confirmation message</label>
                        <textarea className="form-control" defaultValue={this.props.callback.getIn(['content','success_message'])} onChange={(e) => this.onChangeMainAttr('success_message',e.target.value)}></textarea>
                    </div>
                </div>

                <div className="col-6">
                    <div className="form-group">
                        <label>Choose payload to initialise after success</label>
                        <NodeTriggerPayloadList showOptional={true} onSetPayload={(e) => this.onChangeMainAttr('success_callback',e)} payload={this.props.callback.getIn(['content','success_callback'])} />
                    </div>
                    <div className="form-group">
                        <label>Enter text pattern to find a trigger. This can be next trigger activation text.</label>
                        <input type="text" defaultValue={this.props.callback.getIn(['content','success_text_pattern'])} onChange={(e) => this.onChangeMainAttr('success_text_pattern',e.target.value)} className="form-control"/>
                    </div>
                </div>

            </div>
        );
    }
}

export default NodeTriggerCallbackItem;
