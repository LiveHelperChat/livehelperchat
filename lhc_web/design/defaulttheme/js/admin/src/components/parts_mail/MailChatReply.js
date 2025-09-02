import React, { useEffect, useState, useReducer, useRef } from "react";
import { Editor } from '@tinymce/tinymce-react';
import axios from "axios";
import MailChatAttachement from "./MailChatAttachement";
import MailReplyRecipient from "./MailReplyRecipient";
import MailSendStatus from "./MailSendStatus";
import {useTranslation} from 'react-i18next';

const MailChatReply = props => {

    const [replyMode, setReplyMode] = useState(false);
    const [forwardMode, setForwardMode] = useState(false);
    const [replyContent, setReplyContent] = useState(null);
    const [replyIntro, setReplyIntro] = useState(null);
    const [replySignature, setReplySignature] = useState(null);
    const [loadedReplyData, setLoadedReplyData] = useState(false);
    const [recipients, setRecipients] = useState([]);
    const [recipientsModified, setModifiedRecipients] = useState([]);
    const [replySendStatus, setReplySendStatus] = useState([]);
    const [sendInProgress, setSendInProgress] = useState(false);
    const [underReplySignature, setUnderReplySignature] = useState(false);
    const [isOwner, setIsOwner] = useState(true);
    const [isSelfReply, setIsSelfReply] = useState(true);

    const [attachedFiles, dispatch] = useReducer((attachedFiles, { type, value }) => {
        switch (type) {
            case "add":
                return [...attachedFiles, value];
            case "cleanup":
                return [];
            case "remove":
                return attachedFiles.filter((_, index) => index !== value);
            default:
                return attachedFiles;
        }
    }, []);

    const currentAttatchedFiles = useRef();
    currentAttatchedFiles.current = attachedFiles;

    let replyContentDirect = null;

    const handleEditorChange = (content, editor) => {
        replyContentDirect = content;
    }

    const sendReply = (status) => {
        let replyPayload = {
            'recipients' : recipientsModified,
            'content' : tinyMCE.get("reply-to-mce-"+props.message.id).getContent(),
            'attatchements' : attachedFiles,
            'status': status,
            'mode' : (replyMode == true ? 'reply' : 'forward')
        };

        setSendInProgress(true);

        axios.post(WWW_DIR_JAVASCRIPT  + "mailconv/apisendreply/" + props.message.id, replyPayload).then(result => {

            // We always expect an object
            if (typeof result.data !== 'object') {
                setSendInProgress(false);
                setReplySendStatus({
                    'send' : false,
                    'send_tried': true,
                    'errors': {
                        'raw_error': result.data
                    }
                });
                return;
            }

            setReplySendStatus(result.data);
            setSendInProgress(false);

            if (result.data.send == true) {
                props.fetchMessages();
                props.setConversationStatus(result.data.conv_status);
            }

        }).catch(error => {
            setSendInProgress(false);
            // Error 😨
            if (error.response) {
                /*
                 * The request was made and the server responded with a
                 * status code that falls out of the range of 2xx
                 */
                if (error.response.status === 400) {
                    setReplySendStatus(error.response.data);
                } else {
                    setReplySendStatus({
                        'send' : false,
                        'send_tried': true,
                        'errors': {
                            'raw_error': error.response.data
                        }
                    });
                }

            } else if (error.request) {
                /*
                 * The request was made but no response was received, `error.request`
                 * is an instance of XMLHttpRequest in the browser and an instance
                 * of http.ClientRequest in Node.js
                 */
                console.log(error.request);
            } else {
                // Something happened in setting up the request and triggered an Error
                console.log('Error', error.message);
            }

            console.log(error.config);

        });
    }

    const removeAttatchedFile = (file, index) => {
        dispatch({ type: "remove", value: index });
        if (file.new === true) {
            axios.get(WWW_DIR_JAVASCRIPT  + "file/delete/" + file.id + '/(csfr)/' + confLH.csrf_token + '?react=1');
        }
    }

    useEffect(() => {
        return () => {
            currentAttatchedFiles.current.map((file, index) => {
                if (file.new === true) {
                    axios.get(WWW_DIR_JAVASCRIPT  + "file/delete/" + file.id + '/(csfr)/' + confLH.csrf_token + '?react=1');
                }
            })
        }
    },[]);

    useEffect(() => {
        if ((replyMode == true || forwardMode == true) && loadedReplyData == false) {
            axios.post(WWW_DIR_JAVASCRIPT  + "mailconv/getreplydata/" + props.message.id + '/' + (replyMode == true ? 'reply' : 'forward')).then(result => {
                setLoadedReplyData(true);
                setReplyIntro(result.data.intro);
                setReplySignature(result.data.signature);
                setRecipients(result.data.recipients);
                setUnderReplySignature(result.data.signature_under);
                setIsSelfReply(result.data.is_self_reply);

                if (result.data.user_id > 0) {
                    props.verifyOwner(result.data.user_id);
                    setIsOwner(result.data.is_owner);
                }
            });
        } else if (replyMode == false && forwardMode == false && loadedReplyData == true) {
            setLoadedReplyData(false);
            if (currentAttatchedFiles.current.length > 0) {
                currentAttatchedFiles.current.map((file, index) => {
                    if (file.new === true) {
                        axios.get(WWW_DIR_JAVASCRIPT  + "file/delete/" + file.id + '/(csfr)/' + confLH.csrf_token + '?react=1');
                    }
                });
                dispatch({ type: "cleanup"})
            }
        }
    },[replyMode,forwardMode]);

    if (props.replyMode == true && replyMode == false) {
        if (forwardMode == true) {
            setLoadedReplyData(false);
            setForwardMode(false);
        }
        setReplyMode(true);
    }

    if (props.forwardMode == true && forwardMode == false) {
        if (replyMode == true) {
            setLoadedReplyData(false);
            setReplyMode(false);
        }
        setForwardMode(true);
    }

    const { t, i18n } = useTranslation('mail_chat');

    return <React.Fragment>
        <div className="col-12 mt-2 pt-3 pb-2">

            {!isOwner && <div className="alert alert-warning" role="alert"><span className="material-icons">warning</span>
                {t('msg.not_an_owner')}
            </div>}

            {replyMode && isSelfReply && <div className="alert alert-warning" role="alert"><span className="material-icons">warning</span>
                {t('msg.self_reply')}
            </div>}

            {!replyMode && !forwardMode && !props.fetchingMessages && <div className="btn-group" role="group" aria-label="Mail actions">
                <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => {setForwardMode(false);setReplyMode(true);}}><i className="material-icons">reply</i>{t('msg.reply')}</button>
                <button disabled={props.message.response_type == 1} type="button" className="btn btn-sm btn-outline-secondary" onClick={() => props.noReplyRequired()}><i className="material-icons">done</i>{t('msg.nrr')}</button>
                {!props.moptions.hide_recipients && <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => {setReplyMode(false);setForwardMode(true)}}><i className="material-icons">forward</i>{t('msg.forward')}</button>}
            </div>}

            {!props.fetchingMessages && (replyMode || forwardMode) && loadedReplyData && <div className="shadow p-2">

                {replySendStatus.send_tried && <MailSendStatus status={replySendStatus} />}

                <MailReplyRecipient readOnly={props.moptions.hide_recipients} setRecipients={(recipients) => setModifiedRecipients(recipients)} mode={replyMode == true ? 'reply' : 'forward'} message={props.message} recipients={recipients} />

                <Editor
                    tinymceScriptSrc={props.moptions.tiny_mce_path}
                    initialValue={"<p></p>" + replyIntro + (!props.moptions.no_quote_mail && props.message.body_front ? ("<blockquote>" + (props.moptions.skip_images == true ? props.message.body_front.replace(/\<img([^>]*)\ssrc=('|")([^>]*)\2\s([^>]*)\/\>/gi, props.moptions.image_skipped_text ) : props.message.body_front) + "</blockquote>") : "") + (underReplySignature == false ? replySignature : "")}
                    onInit={() => {
                        tinyMCE.get("reply-to-mce-"+props.message.id).focus();
                    }}
                    id={"reply-to-mce-"+props.message.id}
                    init={{
                        cache_suffix: "?v="+props.moptions.cache_version_plugin,
                        height: 320,
                        automatic_uploads: props.moptions.files_enabled,
                        file_picker_types: 'image',
                        images_upload_url: WWW_DIR_JAVASCRIPT + 'mailconv/uploadimage/(csrf)/' + confLH.csrf_token,
                        templates: WWW_DIR_JAVASCRIPT + 'mailconv/apiresponsetemplates/'+props.message.id,
                        paste_data_images: props.moptions.files_enabled,
                        relative_urls : false,
                        browser_spellcheck: true,
                        paste_as_text: true,
                        contextmenu: false,
                        menubar: false,
                        plugins: props.moptions.mce_plugins,
                        toolbar_mode: 'wrap',
                        toolbar: props.moptions.mce_toolbar
                    }}
                />

                {replyMode && <div className="float-end">
                    <a className="action-image" onClick={() => {setReplyMode(false); props.cancelReply()}}><i className="material-icons">delete</i></a>
                </div>}

                {forwardMode && <div className="float-end">
                    <a className="action-image" onClick={() => {setForwardMode(false); props.cancelForward()}}><i className="material-icons">delete</i></a>
                </div>}

                <div className="btn-group mt-2" role="group" aria-label="Mail actions">
                    <button type="button" disabled={sendInProgress} className="btn btn-sm btn-primary" onClick={() => sendReply(2)}><i className="material-icons">send</i>{sendInProgress == true ? t('msg.sending') : t('msg.send_and_close')}</button>
                    {props.moptions.send_as_new && <button type="button" disabled={sendInProgress} className="btn btn-sm btn-outline-secondary" onClick={() => sendReply(0)}><i className="material-icons text-warning">send</i>{sendInProgress == true ? t('msg.sending') : t('msg.send_as_pending')}</button>}
                    <button type="button" disabled={sendInProgress} className="btn btn-sm btn-outline-secondary" onClick={() => sendReply(1)}><i className="material-icons text-success">send</i>{sendInProgress == true ? t('msg.sending') : t('msg.send_as_active')}</button>
                </div>

                {props.moptions.files_enabled && <div className="btn-group d-block mt-2" role="group" aria-label="Mail actions">
                     <MailChatAttachement moptions={props.moptions} fileAttached={(file) => dispatch({ type: "add", value: file})} message={props.message}></MailChatAttachement>
                </div>}

                {attachedFiles && attachedFiles.length > 0 &&
                <div className="pt-2">{attachedFiles.map((file, index) => (
                    <button title={t('msg.click_to_remove')} onClick={() => removeAttatchedFile(file, index)} className="btn btn-sm btn-outline-info me-1 mb-1" title={file.id}>{file.name}</button>
                ))}</div>}

            </div>}

        </div>
    </React.Fragment>

}

export default React.memo(MailChatReply);