import React, { Component } from 'react';

class NodeCollectableField extends Component {

    constructor(props) {
        super(props);
        this.changeType = this.changeType.bind(this);
    }

    changeType(e) {
        this.props.onChangeType({id : this.props.id, 'type' : e.target.value});
    }

    onChangeFieldStoreName(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','field'], value : e.target.value});
    }

    onChangeFieldName(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','name'], value : e.target.value});
    }

    onChangeFieldType(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['type'], value : e.target.value});
    }

    onChangeValidation(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','validation'], value : e.target.value});
    }

    onChangeRenderFunction(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','render_function'], value : e.target.value});
    }

    onChangePrecheckRenderFunction(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','render_precheck_function'], value : e.target.value});
    }

    onChangeRenderArgs(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','render_args'], value : e.target.value});
    }

    onChangeMessage(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','message'], value : e.target.value});
    }

    onChangePrecheckMessage(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','message_precheck'], value : e.target.value});
    }

    onChangeAdditionalMessage(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','message_explain'], value : e.target.value});
    }

    onChangeProvider(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','provider_dropdown'], value : e.target.value});
    }

    onChangeProviderName(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','provider_name'], value : e.target.value});
    }

    onChangeProviderId(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','provider_id'], value : e.target.value});
    }

    onChangeProviderDefault(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','provider_default'], value : e.target.value});
    }

    onChangeProviderArgument(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','provider_argument'], value : e.target.value});
    }

    onChangeRenderValidateFunction(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','render_validate'], value : e.target.value});
    }

    onChangeValidationCallback(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','validation_callback'], value : e.target.value});
    }

    onChangeValidationError(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','validation_error'], value : e.target.value});
    }

    onChangeValidationArgument(e) {
        this.props.onChangeFieldAttr({id : this.props.id, 'path' : ['content','validation_argument'], value : e.target.value});
    }

    deleteField() {
        this.props.onDeleteField(this.props.id);
    }

    upField() {
        this.props.onMoveUpField(this.props.id);
    }

    downField() {
        this.props.onMoveDownField(this.props.id);
    }

    render() {

        return (
            <div className="row">
                <div className="col-6">
                    <div className="form-group">
                        <label>{this.props.id + 1}. Field name*</label>
                        <input className="form-control" onChange={this.onChangeFieldName.bind(this)} type="text" defaultValue={this.props.field.getIn(['content','name'])}/>
                    </div>
                </div>
                <div className="col-6">
                    <div className="form-group">
                        <label>Field store name*</label>
                        <input className="form-control" onChange={this.onChangeFieldStoreName.bind(this)} type="text" defaultValue={this.props.field.getIn(['content','field'])}/>
                    </div>
                </div>
                <div className="col-6">
                    <div className="form-group">
                        <label>Field type*</label>
                        <select className="form-control" onChange={this.onChangeFieldType.bind(this)} defaultValue={this.props.field.get('type')}>
                            <option value="">Select field type</option>
                            <option value="text">Text</option>
                            <option value="email">E-mail</option>
                            <option value="phone">Phone</option>
                            <option value="buttons">Buttons</option>
                            <option value="dropdown">Dropdown</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                </div>

                {this.props.field.get('type') == 'text' &&
                <div className="col-6">
                    <div className="form-group">
                        <label>Validation preg match rule</label>
                        <input className="form-control" type="text" onChange={this.onChangeValidation.bind(this)} defaultValue={this.props.field.getIn(['content','validation'])}/>
                    </div>
                </div>}

                {this.props.field.get('type') == 'text' &&
                <div className="col-12">
                    <div className="row">
                        <div className="col-6">
                            <div className="form-group">
                                <label>Custom event validation</label>
                                <input className="form-control" type="text" onChange={this.onChangeValidationCallback.bind(this)} defaultValue={this.props.field.getIn(['content','validation_callback'])}/>
                            </div>
                        </div>
                        <div className="col-6">
                            <div className="form-group">
                                <label>Validation error message</label>
                                <input className="form-control" type="text" onChange={this.onChangeValidationError.bind(this)} defaultValue={this.props.field.getIn(['content','validation_error'])}/>
                            </div>
                        </div>
                        <div className="col-12">
                            <div className="form-group">
                                <label>Argument</label>
                                <input className="form-control" type="text" onChange={this.onChangeValidationArgument.bind(this)} defaultValue={this.props.field.getIn(['content','validation_argument'])}/>
                            </div>
                        </div>
                    </div>
                </div>}

                {this.props.field.get('type') == 'dropdown' &&
                <div className="col-6">

                    <div className="row">
                        <div className="col-4">
                            <div className="form-group">
                                <label>Provider event</label>
                                <input className="form-control" type="text" onChange={this.onChangeProvider.bind(this)} defaultValue={this.props.field.getIn(['content','provider_dropdown'])}/>
                            </div>
                        </div>
                        <div className="col-4">
                            <div className="form-group">
                                <label>Name attribute</label>
                                <input className="form-control" type="text" onChange={this.onChangeProviderName.bind(this)} defaultValue={this.props.field.getIn(['content','provider_name'])}/>
                            </div>
                        </div>
                        <div className="col-4">
                            <div className="form-group">
                                <label>Id attribute</label>
                                <input className="form-control" type="text" onChange={this.onChangeProviderId.bind(this)} defaultValue={this.props.field.getIn(['content','provider_id'])}/>
                            </div>
                        </div>
                        <div className="col-6">
                            <div className="form-group">
                                <label>Default value</label>
                                <input className="form-control" type="text" onChange={this.onChangeProviderDefault.bind(this)} defaultValue={this.props.field.getIn(['content','provider_default'])}/>
                            </div>
                        </div>
                        <div className="col-6">
                            <div className="form-group">
                                <label>Argument</label>
                                <input className="form-control" type="text" onChange={this.onChangeProviderArgument.bind(this)} defaultValue={this.props.field.getIn(['content','provider_argument'])}/>
                            </div>
                        </div>
                    </div>

                </div>}

                {(this.props.field.get('type') == 'buttons' || this.props.field.get('type') == 'custom') &&
                <div className="col-6">
                    <div className="row">
                        <div className="col-6">
                            <div className="form-group">
                                <label>Precheck event</label>
                                <input className="form-control" type="text" onChange={this.onChangePrecheckRenderFunction.bind(this)} defaultValue={this.props.field.getIn(['content','render_precheck_function'])}/>
                            </div>
                        </div>
                        <div className="col-6">
                            <div className="form-group">
                                <label>Render event</label>
                                <input className="form-control" type="text" onChange={this.onChangeRenderFunction.bind(this)} defaultValue={this.props.field.getIn(['content','render_function'])}/>
                            </div>
                        </div>
                        <div className="col-12">
                            <div className="form-group">
                                <label>Validate event</label>
                                <input className="form-control" type="text" onChange={this.onChangeRenderValidateFunction.bind(this)} defaultValue={this.props.field.getIn(['content','render_validate'])}/>
                            </div>
                        </div>
                        <div className="col-6">
                            <div className="form-group">
                                <label>Arguments</label>
                                <input className="form-control" type="text" onChange={this.onChangeRenderArgs.bind(this)} defaultValue={this.props.field.getIn(['content','render_args'])}/>
                            </div>
                        </div>
                    </div>
                </div>}

                <div className="col-12">

                    <div className="row">
                        <div className="col-6">
                            <div className="form-group">
                                <label>Message to user</label>
                                <textarea onChange={this.onChangeMessage.bind(this)} defaultValue={this.props.field.getIn(['content','message'])} className="form-control"></textarea>
                            </div>
                        </div>
                        <div className="col-6">
                            <div className="form-group">
                                <label>If something wrong in precheck send this</label>
                                <textarea onChange={this.onChangePrecheckMessage.bind(this)} defaultValue={this.props.field.getIn(['content','message_precheck'])} className="form-control"></textarea>
                            </div>
                        </div>
                        <div className="col-6">
                            <div className="form-group">
                                <label>Additional message to user</label>
                                <textarea onChange={this.onChangeAdditionalMessage.bind(this)} defaultValue={this.props.field.getIn(['content','message_explain'])} className="form-control"></textarea>
                            </div>
                        </div>
                    </div>

                    <div className="row">
                        <div className="col-12">
                            <div className="btn-group float-left" role="group" aria-label="Trigger actions">
                                {this.props.isFirst == false && <a className="btn btn-secondary btn-sm" onClick={this.upField.bind(this)}><i className="material-icons mr-0">keyboard_arrow_up</i></a>}
                                {this.props.isLast == false && <a className="btn btn-secondary btn-sm" onClick={this.downField.bind(this)}><i className="material-icons mr-0">keyboard_arrow_down</i></a>}
                            </div>

                            <div className="btn-group float-right" role="group" aria-label="Trigger actions">
                                <a className="btn btn-warning btn-sm" onClick={this.deleteField.bind(this)}>Delete</a>
                            </div>
                        </div>
                    </div>

                    <hr/>
                </div>
            </div>
        );
    }
}

export default NodeCollectableField;
