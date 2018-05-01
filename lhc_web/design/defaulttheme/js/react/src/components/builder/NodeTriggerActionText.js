import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';
import NodeTriggerActionQuickReply from './NodeTriggerActionQuickReply';

class NodeTriggerActionText extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.setText = this.setText.bind(this);
        this.addQuickReply = this.addQuickReply.bind(this);
        this.removeAction = this.removeAction.bind(this);

        this.onQuickReplyNameChange = this.onQuickReplyNameChange.bind(this);
        this.onQuickReplyPayloadChange = this.onQuickReplyPayloadChange.bind(this);
        this.onDeleteQuickReply = this.onDeleteQuickReply.bind(this);
        this.onQuickReplyPayloadTypeChange = this.onQuickReplyPayloadTypeChange.bind(this);
        this.onPayloadAttrChange = this.onPayloadAttrChange.bind(this);
    }

    changeType(e) {
        this.props.onChangeType({id : this.props.id, 'type' : e.target.value});
    }

    setText(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','text'], value : e.target.value});
    }

    addQuickReply(e) {
        this.props.addQuickReply({id : this.props.id});
    }

    onQuickReplyNameChange(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'content','name'], value : e.value});
    }

    onQuickReplyPayloadChange(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'content','payload'], value : e.value});
    }

    onQuickReplyPayloadTypeChange(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'type'], value : e.value});
    }

    onPayloadAttrChange(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','quick_replies',e.id,'content', e.payload.attr], value : e.payload.value});
    }

    onDeleteQuickReply(e) {
        this.props.removeQuickReply({id : this.props.id, 'path' : ['content','quick_replies',e.id]});
    }

    removeAction() {
        this.props.removeAction({id : this.props.id});
    }

    render() {

        var quick_replies = [];

        if (this.props.action.hasIn(['content','quick_replies'])) {
             quick_replies = this.props.action.getIn(['content','quick_replies']).map((reply, index) => {
                return <NodeTriggerActionQuickReply onPayloadAttrChange={this.onPayloadAttrChange} onPayloadTypeChange={this.onQuickReplyPayloadTypeChange} deleteReply={this.onDeleteQuickReply} onNameChange={this.onQuickReplyNameChange} onPayloadChange={this.onQuickReplyPayloadChange} id={index} key={index} reply={reply} />
            });
        }

        return (
            <div className="row">
                <div className="col-xs-12">


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


                    <div className="form-group">
                        <label>Enter text</label>
                        <textarea placeholder="Write your response here!" onChange={this.setText} defaultValue={this.props.action.getIn(['content','text'])} className="form-control"></textarea>
                    </div>
                    <button onClick={this.addQuickReply} className="btn btn-xs btn-default pull-right"><i className="material-icons mr-0">add</i> Add quick reply</button>
                </div>
                <div className="col-xs-12">
                    {quick_replies}
                </div>
                <div className="col-xs-12">
                    <hr/>
                </div>
            </div>
        );
    }
}

export default NodeTriggerActionText;
