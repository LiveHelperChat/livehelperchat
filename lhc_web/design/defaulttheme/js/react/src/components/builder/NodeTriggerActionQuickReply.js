import React, { Component } from 'react';

class NodeTriggerActionQuickReply extends Component {

    constructor(props) {
        super(props);
        this.onNameChange = this.onNameChange.bind(this);
        this.onPayloadChange = this.onPayloadChange.bind(this);
        this.deleteReply = this.deleteReply.bind(this);
    }

    onNameChange(e) {
        this.props.onNameChange({id : this.props.id, value : e.target.value});
    }

    onPayloadChange(e) {
        this.props.onPayloadChange({id : this.props.id, value : e.target.value});
    }

    deleteReply() {
        this.props.deleteReply({id : this.props.id});
    }

    render() {
        return (
            <div className="row">
                <div className="col-xs-5">
                    <div className="form-group">
                        <label>Name</label>
                        <input type="text" onChange={this.onNameChange} defaultValue={this.props.reply.getIn(['content','name'])} className="form-control" />
                    </div>
                </div>
                <div className="col-xs-5">
                    <div className="form-group">
                        <label>Payload</label>
                        <input type="text" onChange={this.onPayloadChange} defaultValue={this.props.reply.getIn(['content','payload'])} className="form-control" />
                    </div>
                </div>
                <div className="col-xs-2">
                    <div className="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <a onClick={this.deleteReply}><i className="material-icons mr-0">delete</i></a>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default NodeTriggerActionQuickReply;
