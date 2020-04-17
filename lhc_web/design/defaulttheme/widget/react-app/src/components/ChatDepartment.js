import React, { Component } from 'react';
import { withTranslation } from 'react-i18next';
import { getProducts } from "../actions/chatActions"
import { connect } from "react-redux";

class ChatDepartment extends Component {

    constructor(props) {
        super(props);
        this.onchangeAttr = this.onchangeAttr.bind(this);
        this.onchangeAttrProduct = this.onchangeAttrProduct.bind(this);
    }

    onchangeAttr(e) {
        this.props.onChangeContent({id : 'DepartamentID', value : e.value});

        if (this.props.departments.getIn(['settings','product_by_department']) === true) {
            this.props.dispatch(getProducts({'dep_id' : e.value}));
        }
    }

    onchangeAttrProduct(e) {
        this.props.onChangeContent({id : 'ProductID', value : e.value});
    }

    componentDidMount() {

        // We have product functionality enabled
        if (this.props.departments.getIn(['settings','product']) === true && (this.props.departments.getIn(['settings','product_by_department']) === true || this.props.departments.has('products')))
        {
            this.props.onChangeContent({id : 'HasProductID', value : true});
        }

        if (this.props.departments.get('departments').size == 0)
        {
            return;
        }

        if (this.props.departments.get('departments').size == 1 || !this.props.departments.hasIn(['settings','optional'])) {
            this.onchangeAttr({'value': this.props.setDefaultValue || this.props.departments.getIn(['departments',0]).get('value')});
          } else if (this.props.departments.get('departments').size > 1) {
            this.onchangeAttr({'value': -1});
        }
    }

    render() {

        const { t } = this.props;

        var departmentOutput = null;
        if (this.props.departments.get('departments').size > 1 && !this.props.departments.hasIn(['settings','hide_department'])) {
            var classNameInput = ['form-control','form-control-sm'];

            if (this.props.isInvalid === true) {
                classNameInput.push('is-invalid');
            }

            var options = this.props.departments.get('departments').map(dep => <option key={'dep-'+dep.get('value')} value={dep.get('value')}>{dep.get('name')} {!dep.get('online') ? t('department.offline') : ''}</option>);
            departmentOutput = <div className="form-group">
                <label className="control-label">{this.props.departments.getIn(['settings','label'])}*</label>
                <select defaultValue={this.props.setDefaultValue || this.props.defaultValueField} className={classNameInput.join(' ')} onChange={(e) => this.onchangeAttr({'value' : e.target.value})}>
                    {this.props.departments.hasIn(['settings','optional']) && <option value="-1">{this.props.departments.getIn(['settings','optional'])}</option>}
                    {options}
                </select>
            </div>;
        }

        var productOutput = null;
        if (this.props.departments.getIn(['settings','product']) === true) {
            var classNameInputProduct = ['form-control','form-control-sm'];

            if (this.props.isInvalidProduct === true) {
                classNameInputProduct.push('is-invalid');
            }

            var prouducts = this.props.departments.has('products') ? this.props.departments.get('products').map(dep => <option key={'product-'+dep.get('value')} value={dep.get('value')}>{dep.get('name')}</option>) : "";
            if (this.props.departments.has('products') && this.props.departments.get('products').size > 0) {
                productOutput = <div className="form-group">
                    <label className="control-label">{t('department.product')}{this.props.departments.getIn(['settings','product_required']) === true ? '*' : ''}</label>
                    <select className={classNameInputProduct.join(' ')} onChange={(e) => this.onchangeAttrProduct({'value' : e.target.value})}>
                        <option value="">{t('department.choose_a_product')}</option>
                        {prouducts}
                    </select>
                </div>;
            };
        }

        if (productOutput !== null || departmentOutput !== null){
            return <div className="col-12">{departmentOutput}{productOutput}</div>
        } else {
            return null;
        }
    }
}

export default connect()(withTranslation()(ChatDepartment));
