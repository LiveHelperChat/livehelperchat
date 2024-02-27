import React, { useEffect, useState, useReducer, useRef } from "react";

const MailSendStatus = props => {

    const [expandBody, setExpandBody] = useState(false);

    return <React.Fragment>
        {props.status.send && <div className="alert alert-success p-1 ps-2" role="alert">Success!</div>}
        {!props.status.send && <div className="alert alert-danger p-1 ps-2" role="alert">
            <ul className="mb-0">
                {props.status.errors.general && <li>{props.status.errors.general}</li>}
                {props.status.errors.reply && <li>{props.status.errors.reply}</li>}
                {props.status.errors.content && <li>{props.status.errors.content}</li>}
            </ul>
            {props.status.errors.raw_error &&
                <div>
                    <h5>An unknown error happened while sending an email. Please reload a window!</h5>
                    <div><button onClick={() => {document.location.reload()}} className="btn btn-danger btn-sm">Reload this window</button></div>
                    <p>Please copy error text and send to your manager!</p>
                    <textarea className="form-control form-control-sm" rows="5">{props.status.errors.raw_error}</textarea>
                </div>
            }
        </div>}
        
    </React.Fragment>

}

export default React.memo(MailSendStatus);