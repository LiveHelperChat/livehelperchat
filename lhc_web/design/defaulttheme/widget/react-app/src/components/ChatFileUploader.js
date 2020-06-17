import React, { PureComponent } from 'react';
import { withTranslation } from 'react-i18next';
import { helperFunctions } from "../lib/helperFunctions";

/**
 * https://github.com/LukasMarx/react-file-upload
 * */
class ChatFileUploader extends PureComponent {

    state = {
        hightlight: false,
        files: [],
        uploading: false,
        uploadProgress: {},
        successfullUploaded: false
    };

    constructor(props) {
        super(props);

        this.fileInputRef = React.createRef();

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
    }

    onFilesAdded(files) {
        const { t } = this.props;

        const ruleTest = new RegExp("(\.|\/)(" + this.props.fileOptions.get('ft_us') + ")$","i");

        let uploadErrors = [];
        files.forEach(file => {

            if (!(ruleTest.test(file.type) || ruleTest.test(file.name))) {
                uploadErrors.push(file.name + ': ' + t('file.incorrect_type'));
            }

            if (file.size > this.props.fileOptions.get('fs')) {
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
                    this.props.progress(t('file.uploading') + Math.round((event.loaded / event.total) * 100) + '%');
                }
            });

            req.upload.addEventListener("load", event => {
                const copy = { ...this.state.uploadProgress };
                copy[file.name] = { state: "done", percentage: 100 };
                //this.setState({ uploadProgress: copy });
                this.props.progress('100%');
                this.props.onCompletion();
                resolve(req.response);
            });

            req.upload.addEventListener("error", event => {
                const copy = { ...this.state.uploadProgress };
                copy[file.name] = { state: "error", percentage: 0 };
                this.setState({ uploadProgress: copy });
                reject(req.response);
            });

            const formData = new FormData();
            formData.append("files", file, file.name);

            req.open("POST", this.props.base_url + '/file/uploadfile/' + this.props.chat_id + '/' + this.props.hash);
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

        if (this.props.onDrag) {
            this.props.onDrag(true);
        }
    }

    componentDidMount() {
        setTimeout(() => {
            if (this.props.dropArea.current) {
                this.props.dropArea.current.ondragover = this.onDragOver;
                this.props.dropArea.current.ondragleave = this.onDragLeave;
                this.props.dropArea.current.ondrop = this.onDrop;
                document.addEventListener("paste", this.onPaste);

                helperFunctions.eventEmitter.addListener('fileupload', (e) => {
                    this.openFileDialog()
                });
            }
        }, 1000);
    }

    componentWillUnmount() {
        document.removeEventListener("paste", this.onPaste);
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
        if (this.props.onDrag) {
            this.props.onDrag(false);
        }
    }

    onDrop(event) {
        event.preventDefault();
        if (this.state.uploading) return;
        const files = event.dataTransfer.files;
        const array = this.fileListToArray(files);

        this.onFilesAdded(array);

        if (this.props.onDrag) {
            this.props.onDrag(false);
        }
    }

    fileListToArray(list) {
        const array = [];
        for (var i = 0; i < list.length; i++) {
            array.push(list.item(i));
        }
        return array;
    }

    render() {
        if (this.props.link) {
            return (
                <React.Fragment>
                    <input onChange={this.onFilesAddedUI} ref={this.fileInputRef} id="fileupload" type="file" name="files[]" multiple className="d-none" />
                    <a className="file-uploader" onClick={this.openFileDialog}><i className="material-icons chat-setting-item text-muted">&#xf10e;</i></a>
                </React.Fragment>
            );
        }
    }
}

export default withTranslation()(ChatFileUploader);