import React, { useState, useEffect, useRef } from 'react';
import {useTranslation} from 'react-i18next';

const MailChatAttachment = ({ 
    id,
    name,
    description,
    download_url,
    is_image = false,
    download_policy = 0,
    restricted_file = false,
    restricted_reason = 0,
    ...props 
}) => {
    const [verificationStatus, setVerificationStatus] = useState('idle'); // idle, verifying, verified, failed, denied
    const [verificationMessage, setVerificationMessage] = useState('');
    const [verificationAttempts, setVerificationAttempts] = useState(0);
    const [countdownSeconds, setCountdownSeconds] = useState(0);
    const [extractedFileId, setExtractedFileId] = useState(null);
    const [extractedConvId, setExtractedConvId] = useState(null);
    const [canDownload, setCanDownload] = useState(false);
    const [finalDownloadUrl, setFinalDownloadUrl] = useState('');
    const [isSensitiveImage, setIsSensitiveImage] = useState(false);
    const [buttonTitle, setButtonTitle] = useState('');

    const { t, i18n } = useTranslation('mail_chat');

    const countdownInterval = useRef(null);
    const maxVerificationAttempts = 4;

    // Convert numeric restriction reason to user-friendly message
    const getRestrictionMessage = (reason) => {
        switch (parseInt(reason, 10)) {
            case 1:
                return t('file.no_permission_download');
            case 2:
                return t('file.extension_not_allowed');
            default:
                return t('file.access_restricted');
        }
    };

    // Extract file_id and conversation_id from URL structure like mailconv/inlinedownload/<file_id>/<conversation_id>
    const extractIdsFromUrl = (url) => {
        if (!url) return { fileId: null, convId: null };
        
        // Match pattern: any_domain/any_path/mailconv/inlinedownload/file_id/conversation_id
        const match = url.match(/\/mailconv\/inlinedownload\/(\d+)\/(\d+)/);
        if (match) {
            return {
                fileId: parseInt(match[1], 10),
                convId: parseInt(match[2], 10)
            };
        }
        
        return { fileId: null, convId: null };
    };

    useEffect(() => {
        if (download_url) {
            // Check if file is restricted first
            if (restricted_file) {
                setCanDownload(false);
                setVerificationStatus('denied');
                setVerificationMessage(getRestrictionMessage(restricted_reason));
                return;
            }

            // Extract file_id and conversation_id from download_url
            const { fileId, convId } = extractIdsFromUrl(download_url);

            if (fileId && convId) {
                setExtractedFileId(fileId);
                setExtractedConvId(convId);

                // Check download policy (convert to number for comparison)
                const policyValue = parseInt(download_policy, 10);
                if (policyValue === 0 || !is_image) {
                    // No verification needed - allow download immediately
                    setCanDownload(true);
                    setFinalDownloadUrl(download_url);
                    setVerificationStatus('verified');
                } else {
                    // Verification needed for policy 1 or 2
                    setCanDownload(false);
                    setVerificationStatus('idle');
                }
            } else {
                // Fallback to regular download_url if no pattern match
                setCanDownload(true);
                setFinalDownloadUrl(download_url);
                setVerificationStatus('verified');
            }
        }

        return () => {
            clearCountdown();
        };
    }, [download_policy, download_url, restricted_file, restricted_reason]);

    const clearCountdown = () => {
        if (countdownInterval.current) {
            clearInterval(countdownInterval.current);
            countdownInterval.current = null;
        }
        setCountdownSeconds(0);
    };

    const handleButtonClick = (e) => {
        e.preventDefault();
        
        // Check if file is restricted
        if (restricted_file) {
            return; // Do nothing for restricted files
        }
        
        // If it's not an image or verification is not needed, proceed with download
        if (!is_image || verificationStatus === 'verified') {
            if (canDownload && finalDownloadUrl) {
                window.open(finalDownloadUrl, '_blank');
            }
            return;
        }

        // For images that need verification, start the verification process
        if (verificationStatus === 'idle' || verificationStatus === 'failed') {
            startVerificationProcess(extractedFileId, extractedConvId);
        }
    };

    const startVerificationProcess = (fileId, convId) => {
        setVerificationAttempts(0);
        setVerificationStatus('verifying');
        setVerificationMessage(t('image.verifying_access'));

        requestVerification(fileId, convId);
    };

    const requestVerification = (fileId, convId, currentAttempts = null) => {
        const attempts = currentAttempts !== null ? currentAttempts : verificationAttempts;
        
        if (attempts >= maxVerificationAttempts) {
            setVerificationStatus('failed');
            setVerificationMessage(t('image.verification_failed'));
            return;
        }

        const newAttempts = attempts + 1;
        setVerificationAttempts(newAttempts);
        
        // Build verification URL using conversation ID
        const verifyUrl = window.WWW_DIR_JAVASCRIPT + 'mailconv/verifyaccess/' + fileId + '/' + convId;
        
        // Make verification request
        fetch(verifyUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            const policyValue = parseInt(download_policy, 10);
            
            if (data.verified === true) {
                // Check if file has error message
                if (data.error_msg) {
                    setVerificationStatus('failed');
                    setVerificationMessage(data.error_msg);
                    clearCountdown();
                } else {
                    if (policyValue === 2 && (data.protection_image || data.protection_html)) {
                        // Policy 2 with sensitive image - show access denied
                        setVerificationStatus('denied');
                        setVerificationMessage(t('image.access_denied'));
                        clearCountdown();
                    } else {
                        // Verification successful - allow download
                        setVerificationStatus('verified');
                        setCanDownload(true);
                        setFinalDownloadUrl(window.WWW_DIR_JAVASCRIPT + 'mailconv/inlinedownload/' + fileId + '/' + convId);
                        setVerificationMessage('');
                        clearCountdown();
                        
                        // Check if it's a sensitive image
                        if (data.protection_image || data.protection_html) {
                            setIsSensitiveImage(true);
                            setButtonTitle(data.btn_title || '');
                        } else {
                            setIsSensitiveImage(false);
                            setButtonTitle('');
                        }
                    }
                }
            } else {
                // Verification not ready yet - schedule next attempt
                if (newAttempts < maxVerificationAttempts) {
                    scheduleNextVerification(newAttempts, fileId, convId);
                } else {
                    setVerificationStatus('failed');
                    setVerificationMessage(t('image.verification_failed'));
                }
            }
        })
        .catch(error => {
            console.error('Verification request failed:', error);
            if (newAttempts < maxVerificationAttempts) {
                scheduleNextVerification(newAttempts, fileId, convId);
            } else {
                setVerificationStatus('failed');
                setVerificationMessage(t('image.verification_failed'));
            }
        });
    };

    const scheduleNextVerification = (attempts, fileId, convId) => {
        const delaySeconds = 4;
        setCountdownSeconds(delaySeconds);
        
        setVerificationMessage(`${t('image.verifying_access')} (${attempts}/${maxVerificationAttempts})`);
        
        // Start countdown
        countdownInterval.current = setInterval(() => {
            setCountdownSeconds(prev => {
                if (prev <= 1) {
                    clearInterval(countdownInterval.current);
                    requestVerification(fileId, convId, attempts);
                    return 0;
                }
                return prev - 1;
            });
        }, 1000);
    };

    const getButtonIcon = () => {
        if (restricted_file) {
            return 'lock';
        }
        
        if (!is_image) {
            return 'attach_file';
        }

        switch (verificationStatus) {
            case 'verifying':
                return 'hourglass_empty';
            case 'verified':
                return 'verified';
            case 'failed':
                return 'warning';
            case 'denied':
                return 'block';
            default:
                return 'image';
        }
    };

    const getButtonClass = () => {
        let baseClass = 'btn btn-sm me-1';
        
        if (restricted_file) {
            return baseClass + ' btn-secondary';
        }
        
        if (!is_image || verificationStatus === 'verified') {
            return baseClass + ' btn-outline-info';
        }

        switch (verificationStatus) {
            case 'verifying':
                return baseClass + ' btn-warning';
            case 'failed':
                return baseClass + ' btn-danger';
            case 'denied':
                return baseClass + ' btn-danger';
            default:
                return baseClass + ' btn-outline-info';
        }
    };

    const getButtonTitle = () => {
        if (restricted_file) {
            return getRestrictionMessage(restricted_reason);
        }
        
        if (!is_image) {
            return description || name;
        }

        switch (verificationStatus) {
            case 'verifying':
                return verificationMessage + (countdownSeconds > 0 ? ` (${countdownSeconds}s)` : '');
            case 'verified':
                return description || name;
            case 'failed':
                return verificationMessage;
            case 'denied':
                return verificationMessage;
            default:
                return description || name;
        }
    };

    const getButtonText = () => {
        if (restricted_file) {
            return `${name} - 🔒 ` + getRestrictionMessage(restricted_reason);
        }
        
        if (verificationStatus === 'verifying' && countdownSeconds > 0) {
            return `${name} (${countdownSeconds}s)`;
        }
        if (is_image && verificationStatus === 'verified') {
            if (isSensitiveImage && buttonTitle) {
                return `${name} - 🔒 ${buttonTitle}`;
            } else {
                return `${name} - ✔️ ${t('image.click_to_download')}`;
            }
        }

        if (is_image && verificationStatus === 'failed') {
            return `${name} - ⚠️ ${verificationMessage}`;
        }


        return name;
    };

    const isDisabled = () => {
        return restricted_file ||
               (is_image && verificationStatus === 'verifying') || 
               (is_image && verificationStatus === 'denied') ||
               (is_image && verificationStatus === 'failed');
    };

    return (
        <button
            className={getButtonClass()}
            onClick={handleButtonClick}
            title={getButtonTitle()}
            disabled={isDisabled()}
            {...props}
        >
            <i className="material-icons me-1 fs14" >
                {getButtonIcon()}
            </i>
            {getButtonText()}
        </button>
    );
};

export default React.memo(MailChatAttachment);
