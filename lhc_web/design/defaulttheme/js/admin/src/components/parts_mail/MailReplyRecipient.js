import React, { useEffect, useState, useReducer, useRef } from "react";

const MailReplyRecipient = props => {

    const [recipients, dispatch] = useReducer((recipients, { type, value }) => {
        switch (type) {
            case "add":
                return [...recipients, value];

            case "add_recipient":
                recipients = { ... recipients};
                recipients[value].push({"name" : "", "email" : ""});
                return recipients;

            case "remove_recipient":
                recipients = { ... recipients};
                recipients[value.recipient] = recipients[value.recipient].filter((_, index) => index !== value.index);
                return recipients;

            case "set":
                console.log(value);
                return value;
            case "cleanup":
                return [];
            case "remove":
                return recipients.filter((_, index) => index !== value);
            default:
                return recipients;
        }
    }, []);

    useEffect(() => {
        dispatch({"type" : "set", "value" : props.recipients});
    },[props.recipients]);

    return <div className="row">

            <div className="col-12 text-secondary font-weight-bold fs13 pb-1">Recipients <i className="material-icons settings text-muted" onClick={(e) => dispatch({'type' : "add_recipient","value" : "to"})} style={{fontSize: "20px"}}>add</i> Cc <i className="material-icons settings text-muted" onClick={(e) => dispatch({'type' : "add_recipient","value" : "cc"})} style={{fontSize: "20px"}}>add</i> Bcc <i onClick={(e) => dispatch({'type' : "add_recipient","value" : "bcc"})} className="material-icons settings text-muted" style={{fontSize: "20px"}}>add</i></div>

        <div className="col-6">
            {recipients.to && recipients.to.map((mail, index) => (
                <div className="form-row pb-1">
                    <div className="col-1 text-secondary fs13 pt-1">To:</div>
                    <div className="col-5">
                        <div className="input-group input-group-sm">
                            <div className="input-group-prepend">
                                <span className="input-group-text" ><i className="material-icons mr-0">mail_outline</i></span>
                            </div>
                            <input type="text" className="form-control form-control-sm" placeholder="E-mail" defaultValue={mail.email} placeholder="Username" aria-describedby="validationTooltipUsernamePrepend" />
                        </div>
                    </div>
                    <div className="col-5"><input type="text" placeholder="Recipient name" defaultValue={mail.name} className="form-control form-control-sm" /></div>
                    {index > 0 && <div className="col"><i className="material-icons settings text-muted" onClick={(e) => dispatch({'type' : "remove_recipient","value" : {"recipient":"to", "index" : index}})}>remove</i></div>}
                </div>
            ))}
        </div>

        <div className="col-6">
        {recipients.cc && recipients.cc.map((mail, index) => (
            <div className="form-row pb-1">
                <div className="col-1 text-secondary fs13 pt-1">Cc:</div>
                <div className="col-5">
                    <div className="input-group input-group-sm">
                        <div className="input-group-prepend">
                            <span className="input-group-text" ><i className="material-icons mr-0">mail_outline</i></span>
                        </div>
                        <input type="text" className="form-control form-control-sm" placeholder="E-mail" defaultValue={mail.email} placeholder="Username" aria-describedby="validationTooltipUsernamePrepend" />
                    </div>
                </div>
                <div className="col-5"><input type="text" placeholder="Recipient name" defaultValue={mail.name} className="form-control form-control-sm" /></div>
                <div className="col"><i className="material-icons settings text-muted" onClick={(e) => dispatch({'type' : "remove_recipient","value" : {"recipient":"cc", "index" : index}})}>remove</i></div>
            </div>
        ))}
        </div>

        <div className="col-6">
        {recipients.bcc && recipients.bcc.map((mail, index) => (
            <div className="form-row pb-1">
                <div className="col-1 text-secondary fs13 pt-1">Bcc:</div>
                <div className="col-5">
                    <div className="input-group input-group-sm">
                        <div className="input-group-prepend">
                            <span className="input-group-text" ><i className="material-icons mr-0">mail_outline</i></span>
                        </div>
                        <input type="text" className="form-control form-control-sm" placeholder="E-mail" defaultValue={mail.email} placeholder="Username" aria-describedby="validationTooltipUsernamePrepend" />
                    </div>
                </div>
                <div className="col-5"><input type="text" placeholder="Recipient name" defaultValue={mail.name} className="form-control form-control-sm" /></div>
                <div className="col"><i className="material-icons settings text-muted" onClick={(e) => dispatch({'type' : "remove_recipient","value" : {"recipient":"bcc", "index" : index}})}>remove</i></div>
            </div>
        ))}
        </div>


    </div>
}

export default React.memo(MailReplyRecipient);