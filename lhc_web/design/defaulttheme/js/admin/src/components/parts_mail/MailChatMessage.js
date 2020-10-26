import parse, { domToReact } from 'html-react-parser';
import React, { useState } from "react";
import MailChatQuote from "./MailChatQuote";
import MailChatReply from "./MailChatReply";
import {useTranslation} from 'react-i18next';

const MailChatMessage = ({message, index, totalMessages, noReplyRequired, mode, addLabel, moptions, fetchMessages, fetchingMessages}) => {

    const [expandHeader, setExpandHeader] = useState(false);
    const [expandBody, setExpandBody] = useState(index + 1 == totalMessages);
    const [replyMode, setReplyMode] = useState(false);
    const [forwardMode, setForwardMode] = useState(false);

    const formatStringToCamelCase = str => {
        const splitted = str.split("-");
        if (splitted.length === 1) return splitted[0];
        return (
            splitted[0] +
            splitted
                .slice(1)
                .map(word => word[0].toUpperCase() + word.slice(1))
                .join("")
        );
    };

    const getStyleObjectFromString = str => {
        const style = {};
        str.split(";").forEach(el => {
            const [property, value] = el.split(":");
            if (!property) return;

            const formattedProperty = formatStringToCamelCase(property.trim());
            style[formattedProperty] = value.trim();
        });

        return style;
    };

    if (fetchingMessages == true && (replyMode == true || forwardMode == true)) {
        setReplyMode(false);
        setForwardMode(false);
    }

    const { t, i18n } = useTranslation('mail_chat');

    return <div className={"row pb-2 mb-2 border-secondary" + (mode !== 'preview' ? ' border-top pt-2' : ' border-bottom')}>
        <div className="col-7 action-image" onClick={() => setExpandBody(!expandBody)}>
            <span title={"Expand message " + message.id} ><i className="material-icons">{expandBody ? 'expand_less' : 'expand_more'}</i></span>
            <b>{message.from_name}</b>
            <small>&nbsp;&lt;{message.from_address}&gt;&nbsp;</small>
            <small className={!message.status || message.status == 1 ? 'chat-pending' : (message.cls_time ? 'chat-closed' : 'chat-active')}>
                <i className="material-icons">mail_outline</i>
                {!message.status || message.status == 1 ? 'Pending response' : 'Responded'}
            </small>
        </div>
        <div className="col-5 text-right text-muted">
            <small className="pr-1">
                {message.subjects && message.subjects.map((label, index) => (
                        <span className="badge badge-info mr-1">{label.name}</span>
                    ))}
                {mode !== 'preview' && <React.Fragment><i title={t('msg.ar_label')} onClick={() => addLabel(message)} className="material-icons action-image text-muted">label</i> |</React.Fragment>}
            </small>

            <small className="pr-2">{message.udate_front} | {message.udate_ago} {t('msg.ago')}.</small>
            {mode !== 'preview' && <i onClick={(e) => {e.stopPropagation();setForwardMode(false);setReplyMode(true)}} className="material-icons settings text-muted">reply</i>}

            <i onClick={(e) => {e.stopPropagation(); setExpandHeader(!expandHeader)}} className="material-icons settings text-muted">{expandHeader ? 'expand_less' : 'expand_more'}</i>

            {mode !== 'preview' && <div className="dropdown float-right">
                <i className="material-icons settings text-muted" id={"message-id-"+message.id} data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">more_vert</i>
                <div className="dropdown-menu" aria-labelledby={"message-id-"+message.id}>
                    <a className="dropdown-item" href="#" onClick={(e) => {e.stopPropagation();setForwardMode(false);setReplyMode(true)}}><i className="material-icons text-muted" >reply</i>{t('msg.reply')}</a>
                    <a className="dropdown-item" href="#" onClick={(e) => {e.stopPropagation();setReplyMode(false);setForwardMode(true)}}><i className="material-icons text-muted">forward</i>{t('msg.forward')}</a>
                    <a className="dropdown-item" target="_blank" href={WWW_DIR_JAVASCRIPT  + "mailconv/mailprint/" + message.id} ><i className="material-icons text-muted">print</i>{t('mail.print')}</a>
                    <a className="dropdown-item" href={WWW_DIR_JAVASCRIPT  + "mailconv/apimaildownload/" + message.id} ><i className="material-icons text-muted">cloud_download</i>{t('msg.download')}</a>
                    <a className="dropdown-item" href="#" onClick={() => noReplyRequired(message)}><i className="material-icons text-muted">done</i>{t('msg.no_reply')}</a>
                </div>
            </div>}
        </div>

        {expandHeader && <div className="col-12">

            <div className="card">
                <div className="card-body">
                    <h6 className="card-subtitle mb-2 text-muted">{t('msg.info')}</h6>

                    <div className="row">
                        <div className="col-6">
                            <ul className="fs13 mb-0 list-unstyled">
                                <li>
                                    <span className="text-muted">{t('msg.from')}:</span> <b>{message.from_name}</b> &lt;{message.from_address}&gt;
                                </li>
                                <li>
                                    <span className="text-muted">{t('msg.to')}:</span> {message.to_data_front}
                                </li>
                                {message.cc_data_front && <li>
                                    <span className="text-muted">cc:</span> {message.cc_data_front}
                                </li>}
                                {message.bcc_data_front && <li>
                                    <span className="text-muted">bcc:</span> {message.bcc_data_front}
                                </li>}
                                <li>
                                    <span className="text-muted">{t('msg.reply_to')}:</span> {message.reply_to_data_front}
                                </li>
                                <li>
                                    <span className="text-muted">{t('msg.mailed_by')}:</span> {message.from_host}
                                </li>
                            </ul>
                        </div>
                        <div className="col-6">
                            <ul className="fs13 mb-0 list-unstyled">
                                {message.accept_time_front && <li>{t('mail.accepted_at')}: {message.accept_time_front}</li>}
                                {message.plain_user_name && <li>{t('mail.accepted_by')}: <b>{message.plain_user_name}</b></li>}
                                {message.wait_time && <li>{t('mail.accept_wait_time')}: {message.wait_time_pending}</li>}
                                {message.lr_time && message.response_time && <li>{t('mail.response_wait_time')}: {message.wait_time_response}</li>}
                                {message.lr_time && <li>Response type: {message.response_type == 1 ? t('msg.nrr') : (message.response_type == 2 ? t('msg.orm') : t('msg.rbe'))}</li>}
                                {message.interaction_time && <li>{t('mail.interaction_time')}: {message.interaction_time_duration}</li>}
                                {message.cls_time && <li>{t('mail.closed_at')}: {message.cls_time_front}</li>}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>


        </div>}

        {expandBody && <div className="col-12 mail-message-body pt-2 pb-2">

         {parse(message.body_front, {
        replace: domNode => {
            if (domNode.attribs) {

                var cloneAttr = Object.assign({}, domNode.attribs);

                if (domNode.attribs.class) {
                    domNode.attribs.className = domNode.attribs.class;
                    delete domNode.attribs.class;
                }

                if (domNode.name && domNode.name === 'blockquote') {
                    if (domNode.attribs.style) {
                        domNode.attribs.style = getStyleObjectFromString(domNode.attribs.style);
                    }

                    return <blockquote {...domNode.attribs}><MailChatQuote>{domToReact(domNode.children)}</MailChatQuote></blockquote>
                }
            }
        }
    })}

        {message.attachments && message.attachments.length > 0 &&
            <div className="pt-2">{message.attachments.map((file) => (
                <a className="btn btn-sm btn-outline-info mr-1" href={file.download_url} title={file.description}>{file.name}</a>
            ))}</div>
        }


    </div>}

        {mode !== 'preview' && !fetchingMessages && ((index + 1 == totalMessages) || replyMode || forwardMode) && <MailChatReply fetchingMessages={fetchingMessages} fetchMessages={(e) => fetchMessages()} moptions={moptions} forwardMode={forwardMode} cancelForward={(e) => setForwardMode(false)} cancelReply={(e) => setReplyMode(false)} replyMode={replyMode} lastMessage={index + 1 == totalMessages} message={message} noReplyRequired={() => noReplyRequired(message)} />}

    </div>
}

export default React.memo(MailChatMessage);