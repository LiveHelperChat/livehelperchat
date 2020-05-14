import React, { Component } from 'react';

class ChatField extends Component {

    state = {
        hiddenIfPrefilled: false
    };

    constructor(props) {
        super(props);
        this.onchangeAttr = this.onchangeAttr.bind(this);
    }

    onchangeAttr(e) {
        this.props.onChangeContent({id : this.props.field.get('name'), value : e.value, field : this.props.field});
    }
 
    componentDidMount() {
        if (this.props.field.get('type') == 'checkbox' && this.props.field.get('default') == true) {
            this.props.onChangeContent({id : this.props.field.get('name'), value : true});
        } else if (this.props.field.get('type') == 'dropdown') {
            this.props.onChangeContent({id : this.props.field.get('name'), value : this.props.defaultValueField});
        }

        if (this.props.attrPrefill && this.props.attrPrefill.attr_prefill_admin) {
            this.props.attrPrefill.attr_prefill_admin.forEach((item) => {
                if (item.index == this.props.field.get('identifier') || (this.props.field.has('identifier_prefill') && item.index == this.props.field.get('identifier_prefill'))) {
                    this.props.onChangeContent({id : this.props.field.get('name'), value : item.value});
                    // Hide only valid prefilled fields
                    if (this.props.field.has('hide_prefilled') && this.props.field.get('hide_prefilled') == true && this.props.isInvalid === false) {
                        this.setState({'hiddenIfPrefilled':true});
                    }
                }
            });
        }
    }

    render() {

        if (this.state.hiddenIfPrefilled === true && this.props.isInvalid !== true) {
            return null;
        }

        var className = 'col-' + this.props.field.get('width');
        var required = this.props.field.get('required') === true;

        var classNameInput = [];

        if (this.props.field.get('class') != '') {
            classNameInput.push(this.props.field.get('class'));
        }

        if (this.props.isInvalid === true) {
            classNameInput.push('is-invalid');
        }

        if (this.props.field.get('type') == 'text') {
            return (
                <div className={className}>
                    <div className="form-group">
                        <label className="control-label">{this.props.field.get('label')}{required === true ? '*' : ''}</label>
                        <input type="text" className={classNameInput.join(' ')} required={required} onChange={(e) => this.onchangeAttr({'value' : e.target.value})} name={this.props.field.get('name')} defaultValue={this.props.defaultValueField} placeholder={this.props.field.get('placeholder')} />
                    </div>
                </div>
            )
        } else if (this.props.field.get('type') == 'password') {
            return (
                <div className={className}>
                    <div className="form-group">
                        <label className="control-label">{this.props.field.get('label')}{required === true ? '*' : ''}</label>
                        <input type="password" autocomplete="new-password" className={classNameInput.join(' ')} required={required} onChange={(e) => this.onchangeAttr({'value' : e.target.value})} name={this.props.field.get('name')} defaultValue={this.props.defaultValueField} placeholder={this.props.field.get('placeholder')} />
                    </div>
                </div>
            )
        } else if (this.props.field.get('type') == 'textarea') {
            return (
                <div className={className}>
                    <div className="form-group">
                        {!this.props.chatUI.has('hide_message_label') && this.props.field.get('identifier') == 'question' && <label className="control-label">{this.props.field.get('label')}{required === true ? '*' : ''}</label>}
                        <textarea className={classNameInput.join(' ')} required={required} onChange={(e) => this.onchangeAttr({'value' : e.target.value})} name={this.props.field.get('name')} defaultValue={this.props.defaultValueField} placeholder={this.props.field.get('placeholder')} />
                    </div>
                </div>
            )
        } else if (this.props.field.get('type') == 'file') {
            return (
                <div className={className}>
                    <div className="form-group">
                        <label className="control-label">{this.props.field.get('label')}{required === true ? '*' : ''}</label>
                        <input type="file" className={this.props.field.get('class')} required={required} name={this.props.field.get('name')}  />
                    </div>
                </div>
            )
        } else if (this.props.field.get('type') == 'checkbox') {
            return (
                <div className={className}>
                    <div className="form-group">
                        <div className="form-check">
                            <input className={classNameInput.join(' ')} defaultChecked={this.props.field.get('default')} type="checkbox" value="on" onChange={(e) => this.onchangeAttr({'value' : e.target.checked})} required={required} name={this.props.field.get('name')} />
                            <label className="form-check-label form-control-sm" dangerouslySetInnerHTML={{ __html:this.props.field.get('label')}}></label>
                        </div>
                    </div>
                </div>
            )
        } else if (this.props.field.get('type') == 'hidden') {
            return <input type="hidden" className={classNameInput.join(' ')} required={required} onChange={(e) => this.onchangeAttr({'value' : e.target.value})} name={this.props.field.get('name')} defaultValue={this.props.defaultValueField} placeholder={this.props.field.get('placeholder')} />
        } else if (this.props.field.get('type') == 'dropdown') {
           var options = this.props.field.get('options').map(dep => <option key={'opt-drop-'+dep.get('value')} value={dep.get('value')}>{dep.get('name')}</option>);
           return (<div className={className}>
                <div className="form-group">
                    <label className="control-label">{this.props.field.get('label')}{required === true ? '*' : ''}</label>
                    <select className={classNameInput.join(' ')} required={required} onChange={(e) => this.onchangeAttr({'value' : e.target.value})} name={this.props.field.get('name')} defaultValue={this.props.defaultValueField}>
                        {options}
                    </select>
                </div>
            </div>);
        } else {
            console.log('Unknown field');
            return null;
        }
    }
}

export default ChatField;
