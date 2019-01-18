import React, { Component } from 'react';

class NodeTriggerActionPredefinedPreview extends Component {

    constructor(props) {
        super(props);
    }

    render() {

        return (
            <div>
                <a className="btn btn-default"><i className="material-icons">play_arrow</i>Execute trigger - {this.props.action.getIn(['content','payload'])}</a>
            </div>
        );
    }
}

export default NodeTriggerActionPredefinedPreview;
