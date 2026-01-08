import parse, { domToReact } from 'html-react-parser';
import React, { useState } from "react";
import MailChatQuote from "./MailChatQuote";
import MailChatReply from "./MailChatReply";
import MailChatImage from "./MailChatImage";
import MailChatAttachment from "./MailChatAttachment";
import {useTranslation} from 'react-i18next';
import axios from "axios";

const MailChatMessage = ({message, index, totalMessages, noReplyRequired, mode, addLabel, moptions, fetchMessages, fetchingMessages, verifyOwner, setConversationStatus, updateMessages, loadMessageBody, keyword}) => {

    const [expandingBody, setExpandingBody] = useState(false);
    const [expandHeader, setExpandHeader] = useState(false);
    const [expandBody, setExpandBody] = useState(false);
    const [plainBody, setPlainBody] = useState(!!message.undelivered);
    const [replyMode, setReplyMode] = useState(false);
    const [forwardMode, setForwardMode] = useState(false);
    const [expandDeliveryInformation, setExpandDeliveryInformation] = useState(false);

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

    const processRestAPIError = (err) => {
        if (!!err.isAxiosError && !err.response) {
            alert(t('system.error'));
        } else {
            if (err.response.data.error) {
                alert(err.response.data.error);
            } else {
                alert(JSON.stringify(err.response.data));
            }
        }
    }

    const unMerge = message => {
        if (confirm(t('status.are_you_sure'))) {
            axios.post(WWW_DIR_JAVASCRIPT  + "mailconv/apiunmerge/" + message.id + "/" + message.conversation_id).then(result => {
                updateMessages();
            }).catch((error) => processRestAPIError(error));
        }
    }

    if (fetchingMessages == true && (replyMode == true || forwardMode == true)) {
        setReplyMode(false);
        setForwardMode(false);
    }

    const loadAndExpand = expandAction => {
        setExpandBody(expandAction);
        if (expandAction == true && typeof message.body_front === 'undefined' && typeof message.alt_body === 'undefined') {
            setExpandingBody(true);
            axios.post(WWW_DIR_JAVASCRIPT  + "mailconv/loadmessagebody/" + message.id + "/" + message.conversation_id, {keyword: keyword}).then(result => {
                loadMessageBody(result.data);
                setExpandingBody(false);
            }).catch((error) => {

            });
        }
    }

    if (index + 1 == totalMessages && expandBody === false && expandingBody === false || (expandingBody === false && expandBody === true && typeof message.body_front === 'undefined' && typeof message.alt_body === 'undefined')) {
        loadAndExpand(true);
    }

    const { t, i18n } = useTranslation('mail_chat');

    return <div className={"row pb-2 mb-2 border-secondary" + (mode !== 'preview' ? ' border-top pt-2' : ' border-bottom')}>
        <div className="col-7 action-image" onClick={() => loadAndExpand(!expandBody)}>
            <span title={"Expand message " + message.id} ><i className="material-icons">{expandBody ? 'expand_less' : 'expand_more'}</i></span>
            <b>{message.from_name}</b>
            <small>&nbsp;&lt;{message.from_address}&gt;&nbsp;</small>
            {message.opened_at && <span className="material-icons text-success" title={message.opened_at_front}>visibility</span>}
            <span className={"material-icons " + (message.is_external ? 'chat-pending' : 'chat-active')} title={message.is_external ? t('msg.external_email') : t('msg.internal_email')} >{message.is_external ? 'location_away' : 'location_home'}</span>
                <small className={!message.status || message.status == 1 ? 'chat-pending' : (message.cls_time ? 'chat-closed' : 'chat-active')}>
                <i className="material-icons">mail_outline</i>
                {!message.status || message.status == 1 ?  t('msg.pnd_rsp') : t('msg.rsp')}
            </small>
            {message.conversation_id_old && <small className="text-muted" title={t('msg.merged_message')} ><span className="material-icons me-0">merge_type</span>{message.conversation_id_old}</small>}
        </div>
        <div className="col-5 text-end text-muted">
            <small className="pe-1">
                {message.subjects && message.subjects.map((label, index) => (
                        <span className="badge me-1" style={{'background-color': label.color ? '#'+label.color : '#0dcaf0'}}>{label.name}</span>
                    ))}
                {mode !== 'preview' && moptions.can_write && <React.Fragment><i title={t('msg.ar_label')} onClick={() => addLabel(message)} className="material-icons action-image text-muted">label</i> |</React.Fragment>}
            </small>
            <small className="pe-2">{message.opened_at && <span className="material-icons" title={t('msg.opened_at_message') + message.opened_at_front}>visibility</span>}{message.udate_front} | {message.udate_ago} {t('msg.ago')}.</small>
            {mode !== 'preview' && moptions.can_write && <i onClick={(e) => {e.stopPropagation();setForwardMode(false);setReplyMode(true)}} className="material-icons settings text-muted">reply</i>}

            <i onClick={(e) => {e.stopPropagation(); setExpandHeader(!expandHeader)}} className="material-icons settings text-muted">{expandHeader ? 'expand_less' : 'expand_more'}</i>

            {mode !== 'preview' && <div className="dropdown float-end">
                <i className="material-icons settings text-muted" id={"message-id-"+message.id} data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">more_vert</i>
                <div className="dropdown-menu" aria-labelledby={"message-id-"+message.id}>
                    {moptions.can_write && <a className="dropdown-item" href="#" onClick={(e) => {e.stopPropagation();setForwardMode(false);setReplyMode(true)}}><i className="material-icons text-muted" >reply</i>{t('msg.reply')}</a>}
                    {moptions.can_write && moptions.can_forward && <a className="dropdown-item" href="#" onClick={(e) => {e.stopPropagation();setReplyMode(false);setForwardMode(true)}}><i className="material-icons text-muted">forward</i>{t('msg.forward')}</a>}
                    {message.conversation_id_old && <a className="dropdown-item" href="#" onClick={(e) => {e.stopPropagation();unMerge(message);}} ><i className="material-icons">alt_route</i>{t('msg.unmerge')}</a>}
                    <a className="dropdown-item" target="_blank" href={WWW_DIR_JAVASCRIPT  + "mailconv/mailprint/" + message.id + "/" + message.conversation_id} ><i className="material-icons text-muted">print</i>{t('mail.print')}</a>
                    {moptions.can_download && <a className="dropdown-item" href={WWW_DIR_JAVASCRIPT  + "mailconv/apimaildownload/" + message.id + "/" + message.conversation_id} ><i className="material-icons text-muted">cloud_download</i>{t('msg.download')}</a>}
                    {moptions.mail_links && moptions.mail_links.map((link, index) => <a className="dropdown-item" target="_blank" href={link.link.replace('{msg_id}',message.id)}>{link.icon && <i className="material-icons text-muted">{link.icon}</i>}{link.title}</a>)}
                    {moptions.can_write && <a className="dropdown-item" href="#" onClick={() => noReplyRequired(message)}><i className="material-icons text-muted">done</i>{t('msg.no_reply')}</a>}
                    {message.alt_body && <a className="dropdown-item" href="#" onClick={(e) => setPlainBody(!plainBody)}><i className="material-icons text-muted">visibility</i>{t('msg.plain_html')}</a>}
                </div>
            </div>}
        </div>

        {expandHeader && <div className="col-12">

            <div className="card">
                <div className="card-body">
                    <h6 className="card-subtitle mb-2 text-muted">{t('msg.info')}</h6>

                    <div className="row">
                        <div className="col-12 fs13">
                            <strong>{t('mail.subject')}:</strong> <span className="text-muted">{message.subject}</span>
                        </div>
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
                                <li>
                                    <span className="text-muted">id:</span> {message.message_id}
                                </li>
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
                                {message.plain_user_name && <li data-id={message.user_id} >{t('mail.accepted_by')}: <b>{message.plain_user_name}</b></li>}
                                {message.wait_time && <li>{t('mail.accept_wait_time')}: {message.wait_time_pending}</li>}
                                {message.lr_time && message.response_time && <li>{t('mail.response_wait_time')}: {message.wait_time_response}, {t('mail.exc_pending_time')}</li>}
                                <li data-id={message.response_type}>{t('mail.rsp_type')}: {message.response_type == 1 ? t('msg.nrr') : (message.response_type == 2 ? t('msg.orm') : (message.response_type == 3 ? t('msg.rbe') : t('msg.unr')))}</li>
                                {message.interaction_time && <li>{t('mail.interaction_time')}: {message.interaction_time_duration}</li>}
                                {message.cls_time && <li>{t('mail.closed_at')}: {message.cls_time_front}</li>}
                                {message.conv_duration_front && <li>{t('mail.response_wait_time')}: {message.conv_duration_front}</li>}
                                <li>{t('mail.message_id')}: {message.id}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>}

        {expandBody && message.undelivered && <div className="col-12 alert alert-warning mt-2">
            This message was undelivered. <a href={WWW_DIR_JAVASCRIPT  + "mailconv/downloadrfc822/" + message.id + "/" + message.conversation_id}>Download sent message.</a>

            {message.delivery_status_keyed && <div className="text-danger border-bottom my-2 py-2 fs13">
                <ul className="m-0 ps-3">
                {message.delivery_status_keyed.Diagnostic_Code && <li>{message.delivery_status_keyed.Diagnostic_Code}</li>}
                {message.delivery_status_keyed.taken && <li>{message.delivery_status_keyed.taken}</li>}
                </ul>
            </div>}

            {message.delivery_status_keyed && <button onClick={(e) => setExpandDeliveryInformation(!expandDeliveryInformation)} className="btn fs12 btn-link">Show technical information.</button>}

            {expandDeliveryInformation && message.delivery_status_keyed && <div>

                <pre>{JSON.stringify(message.delivery_status_keyed, null, 2)}</pre>
            </div>}
        </div>}

        {expandBody && plainBody && message.alt_body && <div className="col-12 mail-message-body pt-2 pb-2">
            <pre className="fs12">{message.alt_body}</pre>
        </div>}

        {expandBody && message.body_front && !plainBody && <div className="col-12 mail-message-body pt-2 pb-2">
         {parse("<div>"+message.body_front+"</div>", {
            replace: domNode => {
                if (domNode.attribs) {

                    var cloneAttr = Object.assign({}, domNode.attribs);

                    if (domNode.attribs.class) {
                        domNode.attribs.className = domNode.attribs.class;
                        delete domNode.attribs.class;
                    }

                    if (domNode.name && domNode.name === 'img') {
                        if (domNode.attribs.style) {
                            domNode.attribs.style = getStyleObjectFromString(domNode.attribs.style);
                        }
                        
                        return <MailChatImage 
                            download_policy={moptions.download_policy}
                            src={domNode.attribs.src}
                            alt={domNode.attribs.alt}
                            title={domNode.attribs.title}
                            className={domNode.attribs.className}
                            style={domNode.attribs.style}
                            {...domNode.attribs}
                        />;
                    }

                    if (domNode.name && domNode.name === 'blockquote') {
                        if (domNode.attribs.style) {
                            domNode.attribs.style = getStyleObjectFromString(domNode.attribs.style);
                        }

                        return <blockquote {...domNode.attribs}><MailChatQuote>{domToReact(domNode.children, {
                            replace: domNode => {
                                if (domNode.attribs) {
                                    if (domNode.attribs.class) {
                                        domNode.attribs.className = domNode.attribs.class;
                                        delete domNode.attribs.class;
                                    }

                                    if (domNode.name && domNode.name === 'img') {
                                        if (domNode.attribs.style) {
                                            domNode.attribs.style = getStyleObjectFromString(domNode.attribs.style);
                                        }
                                        
                                        return <MailChatImage 
                                            download_policy={moptions.download_policy}
                                            src={domNode.attribs.src}
                                            alt={domNode.attribs.alt}
                                            title={domNode.attribs.title}
                                            className={domNode.attribs.className}
                                            style={domNode.attribs.style}
                                            {...domNode.attribs}
                                        />;
                                    }
                                }
                            }
                        })}</MailChatQuote></blockquote>
                    }
                }
            }
    })}
    </div>}

        {expandBody && message.attachments && message.attachments.length > 0 &&
            <div className="pb-2 col-12">
                {message.attachments.map((file) => (
                
                <MailChatAttachment
                    key={file.id}
                    id={file.id}
                    name={file.name}
                    description={file.description}
                    download_url={file.download_url}
                    is_image={file.is_image}
                    download_policy={moptions.download_policy}
                    restricted_file={file.restricted_file}
                    restricted_reason={file.restricted_reason}
                    download_modal={moptions.download_modal}
                />

            ))}</div>
        }


        {mode !== 'preview' && moptions.can_write && !fetchingMessages && ((index + 1 == totalMessages) || replyMode || forwardMode) && <MailChatReply setConversationStatus={setConversationStatus} verifyOwner={verifyOwner} fetchingMessages={fetchingMessages} fetchMessages={(e) => fetchMessages()} moptions={moptions} forwardMode={forwardMode} cancelForward={(e) => setForwardMode(false)} cancelReply={(e) => setReplyMode(false)} replyMode={replyMode} lastMessage={index + 1 == totalMessages} message={message} noReplyRequired={() => noReplyRequired(message)} />}

    </div>
}

export default React.memo(MailChatMessage);