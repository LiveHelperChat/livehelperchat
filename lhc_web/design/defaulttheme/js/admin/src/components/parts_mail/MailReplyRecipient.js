import React, { useEffect, useState, useReducer, useRef } from "react";

const MailReplyRecipient = props => {

    const [recipients, dispatch] = useReducer((recipients, { type, value }) => {
        switch (type) {
            case "add":
                return [...recipients, value];
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

    return <div>
        {recipients.to && recipients.to.map((mail, index) => (
            <div className="form-row pb-1">
                <div className="col-auto">To:</div>
                <div className="col-auto">

                    <div className="input-group input-group-sm">
                        <div className="input-group-prepend">
                            <span className="input-group-text" >@</span>
                        </div>
                        <input type="text" className="form-control form-control-sm" defaultValue={mail.email} placeholder="Username" aria-describedby="validationTooltipUsernamePrepend" />
                    </div>

                </div>
                <div className="col-auto"><input type="text" defaultValue={mail.name} className="form-control form-control-sm" /></div>
            </div>
        ))}
    </div>
}

export default React.memo(MailReplyRecipient);