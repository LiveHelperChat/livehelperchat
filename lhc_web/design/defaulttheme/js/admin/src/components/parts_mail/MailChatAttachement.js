import React, { PureComponent } from 'react';
import { withTranslation } from 'react-i18next';

/**
 * https://github.com/LukasMarx/react-file-upload
 * */
class MailChatAttatchement extends PureComponent {

    state = {
        hightlight: false,
        files: [],
        uploading: false,
        uploadProgress: {},
        successfullUploaded: false,
        progress: ''
    };

    constructor(props) {
        super(props);

        this.fileInputRef = React.createRef();
        this.dropAreaRef = React.createRef();

        // UI Actions
        this.openFileDialog = this.openFileDialog.bind(this);
        this.onFilesAddedUI = this.onFilesAddedUI.bind(this);
        this.onDragOver = this.onDragOver.bind(this);
        this.onDragLeave = this.onDragLeave.bind(this);
        this.onDrop = this.onDrop.bind(this);
        this.onPaste = this.onPaste.bind(this);

        // Backend actions
        this.onFilesAdded = this.onFilesAdded.bind(this);
        this.uploadFiles = this.uploadFiles.bind(this);
        this.sendRequest = this.sendRequest.bind(this);
        this.chooseFromUploaded = this.chooseFromUploaded.bind(this);
        this.fileUploaded = this.fileUploaded.bind(this);
    }

    onFilesAdded(files) {
        const { t } = this.props;

        const ruleTest = new RegExp("(\.|\/)(" + 'zip|doc|pdf' /*this.props.fileOptions.get('ft_us')*/ + ")$","i");

        let uploadErrors = [];
        files.forEach(file => {

            if (!(ruleTest.test(file.type) || ruleTest.test(file.name))) {
                uploadErrors.push(file.name + ': ' + t('file.incorrect_type'));
            }

            if (file.size > 1000000/*this.props.fileOptions.get('fs')*/) {
                uploadErrors.push(file.name + ': '+ t('file.to_big_file'));
            }
        });

        if (uploadErrors.length > 0) {
            alert(uploadErrors.join("\n"));
        } else {
            this.setState({
                'files': files
            })
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (this.state.files.length > 0 && this.state.uploading == false) {
            this.uploadFiles();
        }
    }

    async uploadFiles() {
        this.setState({ uploadProgress: {}, uploading: true });
        const promises = [];
        this.state.files.forEach(file => {
            promises.push(this.sendRequest(file));
        });
        try {
            await Promise.all(promises);
            this.setState({ successfullUploaded: true, uploading: false,  files : []});
        } catch (e) {
            // Not Production ready! Do some error handling here instead...
            this.setState({ successfullUploaded: true, uploading: false,  files : [] });
        }
    }

    fileUploaded(file) {
        this.props.fileAttached(file);
    }

    sendRequest(file) {
        const { t } = this.props;

        return new Promise((resolve, reject) => {
            const req = new XMLHttpRequest();

            req.upload.addEventListener("progress", event => {
                if (event.lengthComputable) {
                    const copy = { ...this.state.uploadProgress };
                    copy[file.name] = {
                        state: "pending",
                        percentage: (event.loaded / event.total) * 100
                    };
                    this.setState({ progress: t('file.uploading') + ' ' + Math.round((event.loaded / event.total) * 100) + '%' });
                }
            });

            req.upload.addEventListener("load", event => {
                const copy = { ...this.state.uploadProgress };
                copy[file.name] = { state: "done", percentage: 100 };
                this.setState({ progress: '' });
                resolve(req.response);
            });

            var _inst = this;

            req.onreadystatechange = function() {
                if (req.readyState === 4) {
                    _inst.fileUploaded(JSON.parse(req.response));
                }
            }

            req.upload.addEventListener("error", event => {
                const copy = { ...this.state.uploadProgress };
                copy[file.name] = { state: "error", percentage: 0 };
                this.setState({ progress: copy });
                reject(req.response);
            });

            const formData = new FormData();
            formData.append("files", file, file.name);

            req.open("POST", WWW_DIR_JAVASCRIPT + 'mailconv/uploadfile');
            req.send(formData);
        });
    }

    openFileDialog() {
        if (this.state.uploading) return;
        this.fileInputRef.current.click();
    }

    onFilesAddedUI(evt) {
        const files = evt.target.files;
        const array = this.fileListToArray(files);
        this.onFilesAdded(array);
    }

    onDragOver(event) {
        event.preventDefault();
        if (this.state.uploading) return;
        this.setState({hightlight: true})
    }

    componentDidMount() {
        if (this.dropAreaRef.current) {
            this.dropAreaRef.current.ondragover = this.onDragOver;
            this.dropAreaRef.current.ondragleave = this.onDragLeave;
            this.dropAreaRef.current.ondrop = this.onDrop;
        }
    }

    componentWillUnmount() {
        if (this.dropAreaRef.current) {
            this.dropAreaRef.current.ondragover = null;
            this.dropAreaRef.current.ondragleave = null;
            this.dropAreaRef.current.ondrop = null;
        }

        window.attatchReplyCurrent = null;
    }

    onPaste(e) {
        var items = e && e.clipboardData &&
            e.clipboardData.items,
            data = {files: []};

        if (items && items.length) {
            const array = [];
            for (var i = 0; i < items.length; i++) {
                var file = items[i].getAsFile && items[i].getAsFile();
                if (file){
                    array.push(file);
                }
            }

            if (array.length > 0) {
                this.onFilesAdded(array);
            }
        }
    }

    onDragLeave(event) {
        this.setState({hightlight: false})
    }

    onDrop(event) {
        event.preventDefault();
        if (this.state.uploading) return;
        const files = event.dataTransfer.files;
        const array = this.fileListToArray(files);
        this.onFilesAdded(array);
        this.setState({hightlight: false})
    }

    fileListToArray(list) {
        const array = [];
        for (var i = 0; i < list.length; i++) {
            array.push(list.item(i));
        }
        return array;
    }

    chooseFromUploaded() {
        lhc.revealModal({
            'title' : 'Attatch an already uploaded file',
            'iframe':true,
            'height':500,
            'url':WWW_DIR_JAVASCRIPT +'mailconv/attatchfile/(attachment)/1'
        });

        var _inst = this;

        window.attatchReplyCurrent = function(file) {
             _inst.props.fileAttached(file);
        }
    }

    render() {
            return (
                <React.Fragment>
                    <button className="btn btn-sm btn-outline-secondary" onClick={this.chooseFromUploaded} ><i className="material-icons">list</i> Choose file from uploaded files</button>
                    <button ref={this.dropAreaRef} onClick={this.openFileDialog} className={"btn btn-sm " + (this.state.hightlight == true ? 'btn-outline-primary' : 'btn-outline-secondary')}><i className="material-icons">attach_file</i> {this.state.progress || 'Drop your files here or choose a new file'}</button>
                    <input onChange={this.onFilesAddedUI} ref={this.fileInputRef} id="fileupload" type="file" name="files[]" multiple className="d-none" />
                </React.Fragment>
            );
     }
}

export default withTranslation()(MailChatAttatchement);