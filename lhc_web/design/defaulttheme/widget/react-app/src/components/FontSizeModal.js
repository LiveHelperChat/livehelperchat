import React, { PureComponent } from 'react';
import axios from "axios";
import { withTranslation } from 'react-i18next';

class FontSizeModal extends PureComponent {

    state = {
        mail: null,
        success: '',
        errors: null,
        sending: false
    };

    constructor(props) {
        super(props);
        this.changeFont = this.changeFont.bind(this);
        this.emailRef = React.createRef();
    }

    changeFont(increase) {
        this.props.changeFont(increase);
    }

    componentDidMount() {

    }

    dismissModal = () => {
        this.props.toggle()
    }


    render() {
        const { t } = this.props;

        const style = {
            top: 'auto',
            bottom: '0',
            height: 'auto'
        };

        return (

               <React.Fragment>
                    <div role="dialog" id="dialog-content" aria-modal="true" className="fade modal show d-block p-1 pt-0 pb-0" tabIndex="-1" style={style}>
                        <div className="modal-content radius-0 border-0">
                            <div className="modal-body ps-2 pe-2 pt-1 pb-1">
                                <div className="mb-0">
                                    <div className="row">
                                        <div className="col-5 me-0 pe-1 text-center">
                                            <span onClick={() => this.changeFont(false)} className="d-block fw-bold action-image font-button">
                                                -<i className="material-icons chat-setting-item">&#xf11d;</i>
                                            </span>
                                        </div>
                                        <div className="col-5 ms-0 ps-1 pe-1 text-center">
                                            <span onClick={() => this.changeFont(true)} className="d-block fw-bold action-image font-button">+<i className="material-icons chat-setting-item">&#xf11d;</i>
                                            </span>
                                        </div>
                                        <div className="col-2 ps-1">
                                            <button type="button" className="btn-close w-100 text-success" data-bs-dismiss="modal" onClick={this.dismissModal} aria-label="Close"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </React.Fragment>

        )
    }
}

export default withTranslation()(FontSizeModal);
