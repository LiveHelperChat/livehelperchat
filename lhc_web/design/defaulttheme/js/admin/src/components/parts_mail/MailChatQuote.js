import parse, { domToReact } from 'html-react-parser';
import React, { useEffect, useState, useReducer, useRef } from "react";

const MailChatQuote = ({children}) => {

    const [expandBody, setExpandBody] = useState(false);

    return <React.Fragment>
        <div className="pb-2"><button onClick={() => setExpandBody(!expandBody)} className="btn btn-sm btn-outline-secondary">...</button></div>
        {expandBody && children}
    </React.Fragment>

}

export default React.memo(MailChatQuote);