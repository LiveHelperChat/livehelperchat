import React, { Component } from 'react';

class NodeCollectableField extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
    }

    changeType(e) {
        this.props.onChangeType({id : this.props.id, 'type' : e.target.value});
    }

    render() {
        return (
            <div className="row">
                <div className="col-xs-12">
                    <div className="form-group">
                        <label>Field name</label>
                        <input className="form-control" type="text" />
                    </div>
                </div>
                <div className="col-xs-12">
                    <div className="form-group">
                        <label>Message to user</label>
                        <textarea className="form-control"></textarea>
                    </div>
                </div>
            </div>
        );
    }
}

export default NodeCollectableField;
