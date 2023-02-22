import React, { Component } from 'react';
import { connect } from "react-redux";
import {loadTemplate, deleteTemplate} from "../../actions/nodeGroupTriggerActions"

@connect((store) => {
    return {
        payloads: store.currenttrigger
    };
})

class NodeTriggerListTemplate extends Component {

    constructor(props) {
        super(props);
        this.state = {template : ''};
    }

    onChange(e) {
        this.props.onSetPayload(e.target.value);
    }

    onChangeActionId(e) {
        this.props.onSetPayloadActionId(e.target.value);
    }

    loadTemplate(){
        this.props.dispatch(loadTemplate({id:this.state.template}));
        this.props.setTemplateName(this.state.templateName);
    }

    deleteTemplate(){
        this.props.dispatch(deleteTemplate({id:this.state.template}));
    }

    setTemplate(e) {
        this.setState({template: e.target.value, templateName: (e.target.value != '' ? e.target.options[e.target.selectedIndex].text : '')});
    }

    render() {
        const mappedTriggerTemplates = this.props.payloads.get('templates').map(template => <option key={template.get('id')} value={template.get('id')} >{template.get('name')}</option>)
        return (
            <React.Fragment>
                <div className="input-group mb-3">
                    <select className="form-control form-control-sm" onChange={(e) => this.setTemplate(e)} value={this.state.template}>
                        <option value="">Choose a template</option>
                        {mappedTriggerTemplates}
                    </select>
                    <button disabled={this.state.template == ''} className="btn btn-secondary btn-sm" onClick={(e) => this.loadTemplate()}>Load</button>
                    {this.state.template != '' && <button className="btn btn-danger btn-sm" onClick={(e) => this.deleteTemplate()}>Delete</button>}
                </div>
            </React.Fragment>
        );
    }
}

export default NodeTriggerListTemplate;
