import React, { Component } from 'react';

class NodeTriggerActionTextPreview extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        return (
            <div>
                <p>{this.props.action.getIn(['content','text'])}</p>
            </div>
        );
    }
}

export default NodeTriggerActionTextPreview;
