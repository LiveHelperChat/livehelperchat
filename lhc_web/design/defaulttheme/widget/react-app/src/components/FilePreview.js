import React from 'react';

const FilePreview = ({ previewFiles, onRemoveFile, t }) => {
    if (!previewFiles || previewFiles.length === 0) {
        return null;
    }

    return (
        <div className="file-preview-container border-top p-2 w-100" style={{bottom: '100%', left: 0, zIndex: 10}}>
            <div className="d-flex flex-wrap gap-2">
                {previewFiles.map((file, index) => (
                    <div key={file.id || index} className="file-preview-item position-relative">
                        {file.previewUrl && file.type && file.type.startsWith('image/') ? (
                            <div className="position-relative">
                                <img src={file.previewUrl} alt={file.name} className="file-preview-image border rounded" style={{width: '80px', height: '80px', objectFit: 'cover'}} />
                                <button
                                    type="button"
                                    className="position-absolute top-0 end-0 btn btn-danger btn-sm rounded-circle d-flex align-items-center justify-content-center"
                                    onClick={() => onRemoveFile(file.id)}
                                    title={t('chat.remove_file')}
                                    style={{width: '20px', height: '20px', fontSize: '0.7rem', transform: 'translate(50%, -50%)'}}
                                >×</button>
                            </div>
                        ) : (
                            <div className="position-relative d-flex align-items-center bg-light border rounded p-2">
                                <i className="material-icons me-2 text-muted">&#xf10e;</i>
                                <span className="small text-truncate" style={{maxWidth: '80px'}}>{file.name}</span>
                                <button
                                    type="button"
                                    className="position-absolute top-0 end-0 btn btn-danger btn-sm rounded-circle d-flex align-items-center justify-content-center"
                                    onClick={() => onRemoveFile(file.id)}
                                    title={t('chat.remove_file')}
                                    style={{width: '20px', height: '20px', fontSize: '0.7rem', transform: 'translate(50%, -50%)'}}
                                >×</button>
                            </div>
                        )}
                    </div>
                ))}
            </div>
        </div>
    );
};

export default FilePreview;
