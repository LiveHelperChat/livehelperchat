import React, { Component } from 'react';

class NodeTriggerActionPredefinedPreview extends Component {

    constructor(props) {
        super(props);
    }

    render() {

        return (
            <div>
                <button className="btn btn-secondary"><i className="material-icons">play_arrow</i>Execute trigger - {this.props.action.getIn(['content','payload'])}</button>
            </div>
        );
    }
}

export default NodeTriggerActionPredefinedPreview;
