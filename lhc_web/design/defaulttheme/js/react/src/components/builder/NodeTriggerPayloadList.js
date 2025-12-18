import React, { Component } from 'react';
import { connect } from "react-redux";

@connect((store) => {
    return {
        payloads: store.currenttrigger
    };
})

class NodeTriggerPayloadList extends Component {

    constructor(props) {
        super(props);
    }

    onChange(e) {
        this.props.onSetPayload(e.target.value);
    }

    render() {

        var list = this.props.payloads.get('payloads').map((option, index) => <option key={option.get('id')} value={option.get('payload')}>{option.get('name')+' [' + option.get('payload') + ']'}</option>);

        return (
            <div className="row">
                <div className="col-6">
                    <label>Choose predefined or enter payload</label>
                    <select className="form-control form-control-sm" onChange={this.onChange.bind(this)} value={this.props.payload}>
                        {this.props.showOptional == true &&
                            <option value="">Choose payload</option>
                        }
                        {list}
                    </select>
                </div>
                <div className="col-6">
                    <div className="form-group">
                        <label>Manual payload message</label>
                        <input className="form-control form-control-sm" onChange={(e) => {this.props.onSetPayload(e.target.value)} } defaultValue={this.props.payload} type="text" placeholder="Manual payload message" />
                    </div>
                </div>
            </div>
        );
    }
}

export default NodeTriggerPayloadList;
