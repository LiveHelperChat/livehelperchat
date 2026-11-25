import React, { Component } from 'react';
import { connect } from "react-redux";

@connect((store) => {
    return {
       nodegroups: store.nodegroups,
       nodegrouptriggers: store.nodegrouptriggers
    };
})

class NodeTriggerList extends Component {

    constructor(props) {
        super(props);
    }

    onChange(e) {
        this.props.onSetPayload(e.target.value);

    }

    onChangeActionId(e) {
        this.props.onSetPayloadActionId(e.target.value);
    }

    render() {
        const mappedNodeGroups = this.props.nodegroups.get('nodegroups').map(nodegroup => <optgroup label={nodegroup.get('name')} key={nodegroup.get('id')}>
            {this.props.nodegrouptriggers.get('nodegrouptriggers').has(nodegroup.get('id')) &&
                this.props.nodegrouptriggers.get('nodegrouptriggers').get(nodegroup.get('id')).map(nodegrouptrigger =><option key={nodegrouptrigger.get('id')} value={nodegrouptrigger.get('id')} >{nodegrouptrigger.get('name')}</option>)
            }
        </optgroup>);

        return (
            <React.Fragment>
                <div className="row">
                    <div className={"col-"+(this.props.enableAction ? 6 : 12)}>
                        <select className="form-control form-control-sm" onChange={this.onChange.bind(this)} value={this.props.payload}>
                            <option value="">Choose a trigger</option>
                            {mappedNodeGroups}
                        </select>
                    </div>
                    {this.props.enableAction && <div className="col-6">
                        <input type="text" defaultValue={this.props.payload_action_id} onChange={this.onChangeActionId.bind(this)} className="form-control form-control-sm" title="Execute only one action from selected trigger from selected" placeholder="Action ID" />
                    </div>}
                </div>
            </React.Fragment>
        );
    }
}

export default NodeTriggerList;
