import React, { Component } from 'react';
import NodeTriggerActionType from './NodeTriggerActionType';

class NodeTriggerActionText extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
        this.setText = this.setText.bind(this);
    }

    changeType(e) {
        this.props.onChangeType({id : this.props.id, 'type' : e.target.value});
    }

    setText(e) {
        this.props.onChangeContent({id : this.props.id, 'path' : ['content','text'], value : e.target.value});
    }

    render() {
        return (
            <div>
                <NodeTriggerActionType onChange={this.changeType} type={this.props.action.get('type')} />
                <div className="form-group">
                    <label>Enter text</label>
                    <textarea onChange={this.setText} defaultValue={this.props.action.getIn(['content','text'])} className="form-control"></textarea>
                </div>
                <hr/>
            </div>
        );
    }
}

export default NodeTriggerActionText;
