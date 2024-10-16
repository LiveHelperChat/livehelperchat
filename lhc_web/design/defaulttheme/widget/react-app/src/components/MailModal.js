import React, { PureComponent } from 'react';
import axios from "axios";
import { withTranslation } from 'react-i18next';

class MailModal extends PureComponent {

    state = {
        mail: null,
        success: '',
        errors: null,
        sending: false
    };

    constructor(props) {
        super(props);
        this.sendMail = this.sendMail.bind(this);
        this.emailRef = React.createRef();
    }

    sendMail(event) {

        this.setState({'sending' : true});

        axios.post(window.lhcChat['base_url'] + "widgetrestapi/sendmailsettings/" + this.props.chatId + '/' + this.props.chatHash + '/(action)/send', JSON.stringify({email:this.state.mail}), {headers : {'Content-Type': 'application/x-www-form-urlencoded'}}).then((response) => {
            if (response.data.error == false) {
                this.props.toggle();
            } else {
                this.setState({'sending' : false});
                this.setState({'errors' : response.data.result});
            }
        });

        if (event)
            event.preventDefault();
    }

    componentDidMount() {
        axios.get(window.lhcChat['base_url'] + 'widgetrestapi/sendmailsettings/' + this.props.chatId + '/' + this.props.chatHash)
        .then((response) => {
            this.setState({'mail' : response.data});
            if (this.emailRef.current) {
                this.emailRef.current.focus();
            }
        })
        .catch((err) => {
            console.log(err);
        });
    }

    dismissModal = () => {
        this.props.toggle()
    }

    render() {
        const { t } = this.props;

        return (
            <React.Fragment>
                {this.state.mail !== null && <React.Fragment>
                <div className="fade modal-backdrop show"></div>
                <div role="dialog" id="dialog-content" aria-modal="true" className="fade modal show d-block mail-send-modal" tabIndex="-1">
                    <div className="modal-dialog modal-lg">
                        <div className="modal-content">
                            <div className="modal-header"> <h4 className="modal-title" id="myModalLabel"><span className="material-icons">&#xf11a;</span>{t('button.mail')}</h4>
                                <button type="button" className="btn-close float-end" data-bs-dismiss="modal" onClick={this.dismissModal} aria-label="Close"></button>
                            </div>
                            <div className="modal-body">
                                <div className="row">
                                    <div className="col-12">
                                        {this.state.errors && <div className="mb-0" dangerouslySetInnerHTML={{__html:this.state.errors}}></div>}
                                        <div className="mb-0">
                                            <form onSubmit={this.sendMail}>
                                                <p className="mail-explain">
                                                    {t('button.email_explain')}
                                                </p>
                                                <div className="form-group">
                                                    <label className="text-muted">{t('button.email')}</label>
                                                    <input className="form-control form-group form-control-sm" ref={this.emailRef} required="required" type="email" defaultValue={this.state.mail} onChange={(e) => this.setState({'mail' : e.target.value})} placeholder={t('chat.enter_email')} title={t('chat.enter_email')} />
                                                </div>
                                                <div className="row">
                                                    <div className="col-5">
                                                        <button type="submit" disabled={this.state.sending || this.state.mail == ''} className="btn btn-primary w-100 btn-sm">{t('button.send')}</button>
                                                    </div>
                                                    <div className="col-2"></div>
                                                    <div className="col-5">
                                                        <button type="button" className="btn text-muted btn-link btn-sm w-100" onClick={this.dismissModal}>{t('button.cancel')}</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
               </div>
               </React.Fragment>}
            </React.Fragment>
        )
    }
}

export default withTranslation()(MailModal);
