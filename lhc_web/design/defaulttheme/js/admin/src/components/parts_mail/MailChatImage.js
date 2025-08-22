import React, { useState, useEffect, useRef } from 'react';
import {useTranslation} from 'react-i18next';

const MailChatImage = ({ 
    src, 
    alt, 
    title, 
    className, 
    style, 
    download_policy = 0,
    width,
    height,
    ...props 
}) => {
    const [imageError, setImageError] = useState(false);
    const [errorMode, setErrorMode] = useState(false);
    const [imageLoading, setImageLoading] = useState(true);
    const [canShowImage, setCanShowImage] = useState(false);
    const [imageSrc, setImageSrc] = useState('');
    const [verificationMessage, setVerificationMessage] = useState('');
    const [verificationAttempts, setVerificationAttempts] = useState(0);
    const [countdownSeconds, setCountdownSeconds] = useState(0);
    const [protectionType, setProtectionType] = useState('');
    const [protectionHtml, setProtectionHtml] = useState('');
    const [isProtected, setIsProtected] = useState(false);
    const [imageRevealed, setImageRevealed] = useState(false);
    const [imageTitle, setImageTitle] = useState('');
    const [buttonTitle, setButtonTitle] = useState('');
    const [extractedFileId, setExtractedFileId] = useState(null);
    const [extractedConvId, setExtractedConvId] = useState(null);

    const { t, i18n } = useTranslation('mail_chat');

    const countdownInterval = useRef(null);
    const maxVerificationAttempts = 4;

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
        if (src) {
            // Extract file_id and conversation_id from src URL
            const { fileId, convId } = extractIdsFromUrl(src);

            if (fileId && convId) {
                setExtractedFileId(fileId);
                setExtractedConvId(convId);
                setImageTitle(title || '');

                // Check download policy (convert to number for comparison)
                const policyValue = parseInt(download_policy, 10);
                if (policyValue === 0) {
                    // No verification needed - show image immediately
                    setCanShowImage(true);
                    setImageSrc(window.WWW_DIR_JAVASCRIPT + 'mailconv/inlinedownload/' + fileId + '/' + convId);
                } else if (policyValue === 1) {
                    // Image needs verification - start verification process
                    setCanShowImage(false);
                    startVerificationProcess(fileId, convId);
                } else if (policyValue === 2) {
                    // No permission to view - check if image is sensitive first
                    setCanShowImage(false);
                    checkImageSensitivity(fileId, convId);
                }
            } else {
                // Fallback to regular src if no pattern match
                setCanShowImage(true);
                setImageSrc(src);
            }
        }

        return () => {
            clearCountdown();
        };
    }, [download_policy, src, title]);

    const clearCountdown = () => {
        if (countdownInterval.current) {
            clearInterval(countdownInterval.current);
            countdownInterval.current = null;
        }
        setCountdownSeconds(0);
    };

    const revealImage = () => {
        if (isProtected && !imageRevealed) {
            setImageRevealed(true);
            setImageSrc(window.WWW_DIR_JAVASCRIPT + 'mailconv/inlinedownload/' + extractedFileId + '/' + extractedConvId);
        }
    };

    const handleImageClick = () => {
        if (isProtected && !imageRevealed) {
            revealImage();
        }
    };

    const startVerificationProcess = (fileId, convId) => {
        setVerificationAttempts(0);
        setVerificationMessage(t('image.verifying_access'));

        requestVerification(fileId, convId);
    };

    const requestVerification = (fileId, convId, currentAttempts = null) => {
        const attempts = currentAttempts !== null ? currentAttempts : verificationAttempts;
        
        if (attempts >= maxVerificationAttempts) {
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
                    setVerificationMessage(data.error_msg);
                    clearCountdown();
                    setErrorMode(true);
                } else {
                    if (policyValue === 2 && (data.protection_image || data.protection_html)) {
                        // Policy 2 with sensitive image - show access denied
                        setVerificationMessage(t('image.access_denied'));
                        clearCountdown();
                        setErrorMode(true);
                    } else {
                        // Verification successful - check protection status
                        setCanShowImage(true);
                        setVerificationMessage('');
                        clearCountdown();
                        
                        if (data.protection_image) {
                            setProtectionType(data.protection_image);
                            setIsProtected(true);
                            setImageRevealed(false);
                            setImageSrc(data.protection_image);
                            setButtonTitle(data.btn_title);
                        } else if (data.protection_html) {
                            setProtectionHtml(data.protection_html);
                            setIsProtected(true);
                            setImageRevealed(false);
                            setImageSrc(''); // No image source for HTML protection
                        } else {
                            setImageSrc(window.WWW_DIR_JAVASCRIPT + 'mailconv/inlinedownload/' + fileId + '/' + convId);
                        }
                    }
                }
            } else {
                // Verification not ready yet - schedule next attempt
                if (newAttempts < maxVerificationAttempts) {
                    scheduleNextVerification(newAttempts, fileId, convId);
                } else {
                    setVerificationMessage(t('image.verification_failed'));
                }
            }
        })
        .catch(error => {
            console.error('Verification request failed:', error);
            if (newAttempts < maxVerificationAttempts) {
                scheduleNextVerification(newAttempts, fileId, convId);
            } else {
                setVerificationMessage(t('image.verification_failed'));
                setErrorMode(true);
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

    const checkImageSensitivity = (fileId, convId) => {
        setVerificationMessage(t('image.checking_access'));
        requestVerification(fileId, convId);
    };

    const handleImageLoad = () => {
        setImageLoading(false);
    };

    const handleImageError = () => {
        setImageError(true);
        setImageLoading(false);
    };

    

    // Show verification message if image is being verified
    if (!canShowImage && verificationMessage && extractedFileId) {
        return (
            <div className="mail-image-verification text-muted p-2 fs14 border rounded d-inline-block bg-light">
                <i className="material-icons text-warning">info</i>

                <span className="ms-1">{countdownSeconds == 0 && verificationAttempts <= 1 && errorMode === false ? t('image.downloading') : verificationMessage}</span>

                {countdownSeconds > 0 && (
                    <span className="text-secondary ms-1">
                        ({t('image.next_attempt_in')} {countdownSeconds} {t('image.seconds')})
                    </span>
                )}
            </div>
        );
    }

    if (imageError) {
        return (
            <div className="mail-image-error d-inline-block border rounded p-2 text-muted">
                <i className="material-icons">broken_image</i>
                <span className="ms-1">{t('image.image_not_available')}</span>
                {alt && <div className="small">{alt}</div>}
            </div>
        );
    }

    // Render protected image content
    if (canShowImage && (imageSrc || protectionHtml)) {        
        if (isProtected && !imageRevealed) {
            if (protectionHtml) {
                return (
                    <div
                        id={`mail-img-reveal-holder-${extractedFileId}`}
                        className="mail-image-protected-html action-image protected-html"
                        onClick={handleImageClick}
                        tabIndex="0"
                        role="button"
                        aria-label={t('image.click_to_reveal')}
                        style={{ cursor: 'pointer' }}
                        dangerouslySetInnerHTML={{ __html: protectionHtml }}
                    />
                );
            } else {
                return (
                    <div
                        id={`mail-img-reveal-holder-${extractedFileId}`}
                        className="mail-image-protected action-image protected-image clickable-reveal position-relative d-inline-block"
                        onClick={handleImageClick}
                        tabIndex="0"
                        role="button"
                        aria-label-title={buttonTitle}
                        aria-label={t('image.click_to_reveal')}
                        style={{ cursor: 'pointer' }}
                    >
                        <img 
                            id={`mail-img-file-${extractedFileId}`}
                            src={imageSrc} 
                            alt={imageTitle} 
                            title={imageTitle}
                            className={`action-image img-fluid ${className || ''}`}
                            width={width}
                            height={height}
                            onLoad={handleImageLoad}
                            onError={handleImageError}
                        />
                    </div>
                );
            }
        } else {
            return (
                <React.Fragment>
                    {imageLoading && (
                        <div className="mail-image-loading position-absolute d-flex align-items-center justify-content-center w-100 h-100">
                            <div className="spinner-border spinner-border-sm text-muted" role="status">
                                <span className="visually-hidden">{t('image.loading')}</span>
                            </div>
                        </div>
                    )}
                    
                    <img
                        id={`mail-img-file-${extractedFileId}`}
                        src={imageSrc}
                        alt={imageTitle || alt || ''}
                        title={imageTitle || title || alt || ''}
                        className={` ${className || ''}`}
                        style={{
                            maxWidth: '100%',
                            height: 'auto',
                            ...style
                        }}
                        width={width}
                        height={height}
                        onLoad={handleImageLoad}
                        onError={handleImageError}
                        {...props}
                    />
                </React.Fragment> 
            );
        }
    }

/*<img
                src={src}
                alt={alt || ''}
                title={title || alt || ''}
                className={`mail-image ${className || ''}`}
                style={{
                    maxWidth: '100%',
                    height: 'auto',
                    ...style
                }}
                onLoad={handleImageLoad}
                onError={handleImageError}
                {...props}
            />*/

    // Fallback to regular image rendering
    return (
        <div className="mail-image-container d-inline-block position-relative">
            {imageLoading && (
                <div className="mail-image-loading position-absolute d-flex align-items-center justify-content-center w-100 h-100">
                    <div className="spinner-border spinner-border-sm text-muted" role="status">
                        <span className="visually-hidden">{t('image.loading')}</span>
                    </div>
                </div>
            )}
        </div>
    );
};

export default React.memo(MailChatImage);
