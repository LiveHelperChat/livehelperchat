import React, { useEffect, useState, useReducer, useRef } from "react";

const MailSendStatus = props => {

    const [expandBody, setExpandBody] = useState(false);

    return <React.Fragment>
        {props.status.send && <div className="alert alert-success p-1 pl-2" role="alert">Success!</div>}
        {!props.status.send && <div className="alert alert-danger p-1 pl-2" role="alert">
            <ul className="mb-0">
                {props.status.errors.general && <li>{props.status.errors.general}</li>}
                {props.status.errors.reply && <li>{props.status.errors.reply}</li>}
                {props.status.errors.content && <li>{props.status.errors.content}</li>}
            </ul>
        </div>}
        
    </React.Fragment>

}

export default React.memo(MailSendStatus);