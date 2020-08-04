import parse, { domToReact } from 'html-react-parser';
import React, { useEffect, useState, useReducer, useRef } from "react";
import { Editor } from '@tinymce/tinymce-react';
import axios from "axios";
import MailChatAttachement from "./MailChatAttachement";
import MailReplyRecipient from "./MailReplyRecipient";
import MailSendStatus from "./MailSendStatus";

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

    const sendReply = () => {
        let replyPayload = {
            'recipients' : recipientsModified,
            'content' : tinyMCE.get("reply-to-mce-"+props.message.id).getContent(),
            'attatchements' : attachedFiles,
            'mode' : (replyMode == true ? 'reply' : 'forward')
        };

        setSendInProgress(true);

        axios.post(WWW_DIR_JAVASCRIPT  + "mailconv/apisendreply/" + props.message.id, replyPayload).then(result => {
            setReplySendStatus(result.data);
            setSendInProgress(false);

            if (result.data.send == true) {
                props.fetchMessages();
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
                    alert('Unhandled error.' + error.response.data);
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

    return <React.Fragment>
        <div className="col-12 mt-2 pt-3 pb-2">

            {!replyMode && !forwardMode && !props.fetchingMessages && <div className="btn-group" role="group" aria-label="Mail actions">
                <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => {setForwardMode(false);setReplyMode(true);}}><i className="material-icons">reply</i>Reply</button>
                <button disabled={props.message.response_type == 1} type="button" className="btn btn-sm btn-outline-secondary" onClick={() => props.noReplyRequired()}><i className="material-icons">done</i>No reply required</button>
                <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => {setReplyMode(false);setForwardMode(true)}}><i className="material-icons">forward</i>Forward</button>
            </div>}

            {!props.fetchingMessages && (replyMode || forwardMode) && loadedReplyData && <div className="shadow p-2">

                {replySendStatus.send_tried && <MailSendStatus status={replySendStatus} />}
                
                <MailReplyRecipient setRecipients={(recipients) => setModifiedRecipients(recipients)} mode={replyMode == true ? 'reply' : 'forward'} message={props.message} recipients={recipients} />

                <Editor
                    tinymceScriptSrc="/design/defaulttheme/js/tinymce/js/tinymce/tinymce.min.js"
                    initialValue={"<p></p>" + replyIntro + "<blockquote>" + props.message.body_front + "</blockquote>" + replySignature}
                    onInit={() => {
                        tinyMCE.get("reply-to-mce-"+props.message.id).focus();
                    }}
                    id={"reply-to-mce-"+props.message.id}
                    init={{
                        height: 320,
                        automatic_uploads: true,
                        file_picker_types: 'image',
                        images_upload_url: WWW_DIR_JAVASCRIPT + 'mailconv/uploadimage',
                        templates: WWW_DIR_JAVASCRIPT + 'mailconv/apiresponsetemplates/'+props.message.id,
                        paste_data_images: true,
                        relative_urls : false,
                        browser_spellcheck: true,
                        paste_as_text: true,
                        contextmenu: false,
                        menubar: false,
                        plugins: [
                            'advlist autolink lists link image charmap print preview anchor image lhfiles',
                            'searchreplace visualblocks code fullscreen',
                            'media table paste help',
                            'print preview importcss searchreplace autolink save autosave directionality visualblocks visualchars fullscreen media template codesample charmap pagebreak nonbreaking anchor toc advlist lists wordcount textpattern noneditable help charmap emoticons'
                        ],
                        toolbar_mode: 'wrap',
                        toolbar:
                            'undo redo | fontselect formatselect fontsizeselect | table | paste pastetext | subscript superscript | bold italic underline strikethrough | forecolor backcolor | \
                            alignleft aligncenter alignright alignjustify | lhfiles insertfile image pageembed template link anchor codesample | \
                            bullist numlist outdent indent | removeformat permanentpen | charmap emoticons | fullscreen print preview paste code | help'
                    }}
                />

                {replyMode && <div className="float-right">
                    <a className="action-image" onClick={() => {setReplyMode(false); props.cancelReply()}}><i className="material-icons">delete</i></a>
                </div>}

                {forwardMode && <div className="float-right">
                    <a className="action-image" onClick={() => {setForwardMode(false); props.cancelForward()}}><i className="material-icons">delete</i></a>
                </div>}

                <div className="btn-group mt-1" role="group" aria-label="Mail actions">
                    <button type="button" disabled={sendInProgress} className="btn btn-sm btn-outline-primary" onClick={() => sendReply()}><i className="material-icons">send</i>{sendInProgress == true ? 'Sending...' : 'Send'}</button>
                    <MailChatAttachement moptions={props.moptions} fileAttached={(file) => dispatch({ type: "add", value: file})} message={props.message}></MailChatAttachement>
                </div>

                {attachedFiles && attachedFiles.length > 0 &&
                <div className="pt-2">{attachedFiles.map((file, index) => (
                    <button title="Click to remove" onClick={() => removeAttatchedFile(file, index)} className="btn btn-sm btn-outline-info mr-1 mb-1" title={file.id}>{file.name}</button>
                ))}</div>}

            </div>}

        </div>
    </React.Fragment>

}

export default React.memo(MailChatReply);