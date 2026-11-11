import { removeFilePreview as removeFilePreviewAction } from '../actions/chatActions';

export const MAX_PREVIEW_FILES = 4;

/**
 * Calculates new preview files list.
 * Returns files which have to be released/removed from server.
 */
export function addPreviewFile(previewFiles, fileData, oneFileUpload) {
    // If one_file_upload is enabled, replace existing files instead of adding
    if (oneFileUpload === true) {
        return {
            previewFiles: [fileData],
            removedFiles: previewFiles.slice()
        };
    }

    // Otherwise, add to existing files but limit to MAX_PREVIEW_FILES
    if (previewFiles.length >= MAX_PREVIEW_FILES) {
        return {
            previewFiles: [...previewFiles.slice(1), fileData],
            removedFiles: [previewFiles[0]]
        };
    }

    return {
        previewFiles: [...previewFiles, fileData],
        removedFiles: []
    };
}

/**
 * Releases local blob URL to prevent memory leaks.
 */
export function releasePreviewUrl(file) {
    if (file.previewUrl && file.previewUrl.startsWith('blob:')) {
        URL.revokeObjectURL(file.previewUrl);
    }
}

/**
 * Releases local blob URL and removes already uploaded file from the server.
 */
export function removePreviewFile(file, dispatch) {
    releasePreviewUrl(file);

    // If file has been uploaded to server (has security_hash), remove it from server
    if (file.security_hash && file.security_hash !== 'preview') {
        const request = dispatch(removeFilePreviewAction({
            'file_id': file.id,
            'security_hash': file.security_hash
        }));

        if (request && typeof request.catch === 'function') {
            request.catch((error) => {
                console.warn('Failed to remove file from server:', error);
                // Continue with local removal even if server removal fails
            });
        }
    }
}

/**
 * OnlineChat.onFilePreview handler (bound to the component instance).
 */
export function onFilePreview(fileData) {
    let removedFiles = [];

    this.setState(prevState => {
        const result = addPreviewFile(
            prevState.previewFiles,
            fileData,
            this.props.chatwidget.getIn(['chat_ui', 'file_options', 'one_file_upload']) === true
        );

        removedFiles = result.removedFiles;

        return {
            previewFiles: result.previewFiles
        };
    }, () => {
        removedFiles.forEach(file => {
            removePreviewFile(file, this.props.dispatch);
        });
    });
}

/**
 * OnlineChat.removeFilePreview handler (bound to the component instance).
 */
export function removeFilePreview(fileId) {
    const fileToRemove = this.state.previewFiles.find(file => file.id === fileId);

    this.setState(prevState => ({
        previewFiles: prevState.previewFiles.filter(file => file.id !== fileId)
    }));

    if (fileToRemove) {
        removePreviewFile(fileToRemove, this.props.dispatch);
    }
}

