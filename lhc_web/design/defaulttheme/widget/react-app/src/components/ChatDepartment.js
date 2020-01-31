import React, { Component } from 'react';
import { withTranslation } from 'react-i18next';

class ChatDepartment extends Component {

    constructor(props) {
        super(props);
        this.onchangeAttr = this.onchangeAttr.bind(this);
    }

    onchangeAttr(e) {
        this.props.onChangeContent({id : 'DepartamentID', value : e.value});
    }

    componentDidMount() {
        if (this.props.departments.get('departments').size == 1) {
            this.props.onChangeContent({id : 'DepartamentID', value : this.props.departments.getIn(['departments',0]).get('value')});
        } else if (this.props.departments.get('departments').size > 1) {
            this.props.onChangeContent({id : 'DepartamentID', value : -1});
        }
    }

    render() {

        const { t } = this.props;

        var classNameInput = [];

        classNameInput.push('form-control form-control-sm');
        
        if (this.props.isInvalid === true) {
            classNameInput.push('is-invalid');
        }
        
        if (this.props.departments.get('departments').size > 1) {
            var options = this.props.departments.get('departments').map(dep => <option key={'dep-'+dep.get('value')} value={dep.get('value')}>{dep.get('name')} {!dep.get('online') ? t('department.offline') : ''}</option>);
            return <div className="col-12">
                <div className="form-group">
                    <label className="control-label">{this.props.departments.getIn(['settings','label'])}</label>
                    <select defaultValue={this.props.defaultValueField} className={classNameInput.join(' ')} onChange={(e) => this.onchangeAttr({'value' : e.target.value})}>
                        {this.props.departments.hasIn(['settings','optional']) && <option value="-1">{this.props.departments.getIn(['settings','optional'])}</option>}
                        {options}
                    </select>
                </div>
            </div>
        } else {
            return null;
        }
    }
}

export default withTranslation()(ChatDepartment);
