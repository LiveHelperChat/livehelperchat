import React, { Component } from 'react';
import { withTranslation } from 'react-i18next';

class ChatField extends Component {

    state = {
        hiddenIfPrefilled: false
    };

    constructor(props) {
        super(props);
        this.onchangeAttr = this.onchangeAttr.bind(this);
        this.onFileAdded = this.onFileAdded.bind(this);
    }

    onchangeAttr(e) {
        this.props.onChangeContent({id : this.props.field.get('name'), value : e.value, field : this.props.field});
    }

    onFileAdded(e) {
        const list = e.target.files;
        const files = [];
        for (var i = 0; i < list.length; i++) {
            files.push(list.item(i));
        }

        if (list.length == 0) {
            return null;
        }

        const ruleTest = new RegExp("(\.|\/)(" + this.props.field.get('ft_us') + ")$","i");

        const { t } = this.props;

        let uploadErrors = [];
        files.forEach(file => {
            if (!(ruleTest.test(file.type) || ruleTest.test(file.name))) {
                uploadErrors.push(file.name + ': ' + t('file.incorrect_type'));
            }
            if (file.size > this.props.field.get('fs')) {
                uploadErrors.push(file.name + ': '+ t('file.to_big_file'));
            }
        });

        if (uploadErrors.length > 0) {
            alert(uploadErrors.join("\n"));
        } else {
            this.props.onChangeContent({id : this.props.field.get('name'), value : files[0], field : this.props.field});
        }
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
                    <div className="form-group overflow-hidden">
                        <label className="control-label">{this.props.field.get('label')}{required === true ? '*' : ''}</label>
                        <input type="file" onChange={(e) => this.onFileAdded(e)} className={this.props.field.get('class')} required={required} name={this.props.field.get('name')}  />
                    </div>
                </div>
            )
        } else if (this.props.field.get('type') == 'checkbox') {
            return (
                <div className={className}>
                    <div className="form-group">
                        <div className="form-check">
                            <input className={classNameInput.join(' ')} id={"check-for-"+this.props.field.get('name')} defaultChecked={this.props.field.get('default')} type="checkbox" value="on" onChange={(e) => this.onchangeAttr({'value' : e.target.checked})} required={required} name={this.props.field.get('name')} />
                            <label className="form-check-label" for={'check-for-'+this.props.field.get('name')} dangerouslySetInnerHTML={{ __html:this.props.field.get('label')}}></label>
                        </div>
                    </div>
                </div>
            )
        } else if (this.props.field.get('type') == 'hidden') {
            return <input type="hidden" className={classNameInput.join(' ')} required={required} onChange={(e) => this.onchangeAttr({'value' : e.target.value})} name={this.props.field.get('name')} defaultValue={this.props.defaultValueField} placeholder={this.props.field.get('placeholder')} />
        } else if (this.props.field.get('type') == 'dropdown') {
           var options = this.props.field.get('options').map(dep => <option key={'opt-drop-'+dep.get('value')} selected={this.props.defaultValueField == dep.get('value')} value={dep.get('value')}>{dep.get('name')}</option>);
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

export default withTranslation()(ChatField);
