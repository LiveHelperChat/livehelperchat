import React, { Component } from 'react';

class NodeGroupTrigger extends Component {
    render() {
        return (
        <div>
            <p>{this.props.trigger.name}</p>
        </div>
    );
    }
}

export default NodeGroupTrigger;
