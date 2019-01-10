import React, { Component } from 'react';
import { connect } from "react-redux";
import { initArgumentTemplates } from "../../actions/nodeGroupTriggerActions"
import {fromJS} from 'immutable';

@connect((store) => {
    return {
        payloads: store.currenttrigger
    };
})

class NodeTriggerArgumentTemplate extends Component {

    constructor(props) {
        super(props);
        this.props.dispatch(initArgumentTemplates());
        this.onChangeMainAttr = this.onChangeMainAttr.bind(this);
    }

    onChange(e) {
        this.props.onChange({'path' : ['argument_template'],'value' : fromJS({'id' : e.target.value})});
    }

    onChangeMainAttr(path, e) {
        this.props.onChange({id : this.props.id, 'path' : ['argument_template'].concat(path), value : e});
    }

    render() {

        var list = [];
        if (this.props.payloads.has('arguments')) {
            var list = this.props.payloads.get('arguments').map((option, index) => <option key={index} value={index}>{option.get('name')}</option>);
        }

        var argumentId = typeof this.props.argument !== 'undefined' ? this.props.argument.get('id') : -1;

        var header = "";
        var elements = [];
        if (argumentId != -1 && this.props.payloads.hasIn(['arguments',argumentId])) {
            header = <div><hr/><h4>{this.props.payloads.getIn(['arguments',argumentId,'name'])}</h4></div>;
            var elements = this.props.payloads.getIn(['arguments',argumentId,'items']).map((option, index) => <div className="col-6"><div key={option.get('id')} className="form-group">
                <label>{option.get('name')}</label>
                <input type="text" defaultValue={this.props.argument.getIn(['args',option.get('id'),'value'])} onChange={(e) => this.onChangeMainAttr(['args',option.get('id'),'value'],e.target.value)} className="form-control form-control-sm" placeholder={option.get('placeholder')}/>
            </div></div>);
        }

        return (
            <div>
                <select className="form-control form-control-sm" onChange={this.onChange.bind(this)} value={argumentId}>
                    {this.props.showOptional == true &&
                        <option value="">Choose argument</option>
                    }
                    {list}
                </select>
                {header}
                <div className="row">
                    {elements}
                </div>
            </div>
        );
    }
}

export default NodeTriggerArgumentTemplate;
