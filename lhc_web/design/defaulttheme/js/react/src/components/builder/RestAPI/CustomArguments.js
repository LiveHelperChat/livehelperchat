import React, { Component } from 'react';

class CustomArguments extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            activeTab: 1
        };
        this.setActiveTab = this.setActiveTab.bind(this);
    }

    setActiveTab(tab) {
        this.setState({activeTab: tab});
    }

    render() {
        return (
            <div className="col-12">
                <div className="form-group">
                    <div className="btn-group" role="group">
                        {[1, 2, 3, 4, 5].map(number => (
                            <button
                                key={`tab_btn_${number}`}
                                type="button"
                                className={`btn ${this.state.activeTab === number ? 'btn-primary' : 'btn-outline-secondary'} btn-sm`}
                                onClick={() => this.setActiveTab(number)}
                            >
                                Arg {number}
                            </button>
                        ))}
                    </div> use as <span className="text-muted">{`{{custom_args_${this.state.activeTab}}}`}</span> in Rest API definition

                    {[1, 2, 3, 4, 5].map(number => (
                        this.state.activeTab === number && (
                            <div key={`custom_args_${number}`} className="mt-2">
                                <label>Custom argument {number} for the Rest API Call</label>
                                <textarea
                                    onChange={(e) => this.props.onchangeAttr({'path' : ['attr_options', `custom_args_${number}`], 'value' : e.target.value})}
                                    defaultValue={this.props.action.getIn(['content', 'attr_options', `custom_args_${number}`])}
                                    placeholder={`You will be able to access this argument in your Rest API call via {{custom_args_${number}}}`}
                                    className="form-control form-control-sm">
                                </textarea>
                            </div>
                        )
                    ))}
                </div>
            </div>
        )
    }
}

export default CustomArguments;