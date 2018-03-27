import React, { Component } from 'react';

class NodeGroup extends Component {

    handleChange(e) {
        const name = e.target.value;
        this.props.changeTitle({id : this.props.group.id, name:name});
    }

    shouldComponentUpdate(nextProps, nextState) {

        if (this.props.group.name !== nextProps.group.name) {
            return true;
        }

        return false;
    }

    render() {
        return (
            <div>
                <input value={this.props.group.name} onChange={this.handleChange.bind(this)} />
            </div>
        );
    }
}

export default NodeGroup;
