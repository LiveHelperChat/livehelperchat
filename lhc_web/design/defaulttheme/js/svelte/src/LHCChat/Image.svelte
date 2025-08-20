<svelte:options customElement={{
		tag: 'lhc-image',
		shadow: 'none',
		extend: (customElementConstructor) => {
          return class extends customElementConstructor {
            constructor() {
              super();
              this.host = this; // or this.shadowRoot, or whatever you need
            }
          };
    }
}}/>

<script>
    import { onMount } from 'svelte';
    import { t } from "../i18n/i18n.js";
    export let host;
    export let file_id;
    export let hash;
    export let title = '';
    export let width = '';
    export let height = '';
    export let download_policy = 0;
    export let disable_zoom = 'false';

    let canShowImage = false;
    let imageSrc = '';
    let verificationMessage = '';
    let verificationAttempts = 0;
    let nextAttemptIn = 0;
    let countdownInterval;
    let verificationInterval;
    let protectionType = '';
    let protectionHtml = '';
    let isProtected = false;
    let imageRevealed = false;
    let imageTitle = '';
    let buttonTitle = '';

    let countdownSeconds = 0;
    
    const maxVerificationAttempts = 4;

    onMount(() => {
        if (file_id && hash) {
            imageTitle = title || '';
            
            // Check download policy (convert to number for comparison)
            const policyValue = parseInt(download_policy, 10);
            if (policyValue === 0) {
                // No verification needed - show image immediately
                canShowImage = true;
                imageSrc = window.WWW_DIR_JAVASCRIPT + 'file/downloadfile/' + file_id + '/' + hash;
            } else if (policyValue === 1) {
                // Image needs verification - start verification process
                canShowImage = false;
                startVerificationProcess();
            } else if (policyValue === 2) {
                // No permission to view - check if image is sensitive first
                canShowImage = false;
                checkImageSensitivity();
            }
        }

        return () => {
            clearCountdown();
        };
    });

    function scrolltToImage(){
        // Scroll to the revealed image
        setTimeout(() => {
            const imgElement = document.querySelector(`#img-reveal-holder-${file_id}`);
            if (imgElement) {
                imgElement.scrollIntoView({ behavior: 'smooth' });
            }
        }, 100);
    }

    function revealImage() {
        if (isProtected && !imageRevealed) {
            imageRevealed = true;
            imageSrc = window.WWW_DIR_JAVASCRIPT + 'file/downloadfile/' + file_id + '/' + hash;
            scrolltToImage();
        }
    }

    function handleImageClick() {
        if (isProtected && !imageRevealed) {
            revealImage();
        } else if (disable_zoom !== 'true' && window.lhinst && window.lhinst.zoomImage) {
            // Use the global lhinst.zoomImage function when the image is not disabled for zoom
            const imgElement = document.querySelector(`#img-file-${file_id}`);
            if (imgElement) {
                window.lhinst.zoomImage(imgElement);
            }
        }
    }

    function startVerificationProcess() {
        verificationAttempts = 0;
        verificationMessage = $t('file.verifying_image_access');
        requestVerification();
    }

    function requestVerification() {
        if (verificationAttempts >= maxVerificationAttempts) {
            verificationMessage = $t('file.image_verification_failed');
            return;
        }

        verificationAttempts++;
        
        // Make verification request
        fetch(window.WWW_DIR_JAVASCRIPT + 'file/verifyaccess/' + file_id + '/' + hash, {
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
                    verificationMessage = data.error_msg;
                    clearCountdown();
                } else {
                    if (policyValue === 2 && (data.protection_image || data.protection_html)) {
                        // Policy 2 with sensitive image - show access denied
                        verificationMessage = $t('file.access_denied_view_image');
                        clearCountdown();
                    } else {
                        // Verification successful - check protection status
                        canShowImage = true;
                        verificationMessage = '';
                        clearCountdown();
                        
                        if (data.protection_image) {
                            protectionType = data.protection_image;
                            isProtected = true;
                            imageRevealed = false;
                            imageSrc = protectionType;
                            buttonTitle = data.btn_title;
                        } else if (data.protection_html) {
                            protectionHtml = data.protection_html;
                            isProtected = true;
                            imageRevealed = false;
                            imageSrc = ''; // No image source for HTML protection
                        } else {
                            imageSrc = window.WWW_DIR_JAVASCRIPT + 'file/downloadfile/' + file_id + '/' + hash;
                        }

                        // We do not want to scroll on first response as it means we already had this data.
                        if (verificationAttempts > 1) {
                            scrolltToImage();
                        }

                    }
                }
            } else {
                // Verification not ready yet - schedule next attempt
                if (verificationAttempts < maxVerificationAttempts) {
                    scheduleNextVerification();
                } else {
                    verificationMessage = $t('file.image_verification_failed');
                }
            }
        })
        .catch(error => {
            console.error('Verification request failed:', error);
            if (verificationAttempts < maxVerificationAttempts) {
                scheduleNextVerification();
            } else {
                verificationMessage = $t('file.image_verification_failed');
            }
        });
    }

    function scheduleNextVerification() {
        const delaySeconds = 4;
        countdownSeconds = delaySeconds;
        
        verificationMessage = `${$t('file.verifying_image_access')} (${verificationAttempts}/${maxVerificationAttempts})`;
        
        // Start countdown
        countdownInterval = setInterval(() => {
            countdownSeconds--;
            if (countdownSeconds <= 0) {
                clearInterval(countdownInterval);
                requestVerification();
            }
        }, 1000);
    }

    function clearCountdown() {
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }
        countdownSeconds = 0;
    }

    function checkImageSensitivity() {
        verificationMessage = $t('file.checking_image_access');
        requestVerification();
    }

    function requestReverification() {
        verificationMessage = $t('file.requesting_reverification');
        
        // Make reverification request
        fetch(window.WWW_DIR_JAVASCRIPT + 'file/verifyaccess/' + file_id + '/' + hash + '/(reverify)/true', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            // Reset verification attempts and start checking again
            verificationAttempts = 0;
            verificationMessage = $t('file.verifying_image_access');
            
            // Start verification process again
            setTimeout(() => {
                requestVerification();
            }, 1000); // Small delay before starting verification
        })
        .catch(error => {
            console.error('Reverification request failed:', error);
            verificationMessage = $t('file.reverification_failed');
        });
    }

</script>

{#if canShowImage && (imageSrc || protectionHtml)}
    {#if isProtected && !imageRevealed}
        {#if protectionHtml}
            <div
                class="action-image protected-html"
                on:click={handleImageClick}
                tabindex="0"
                role="button"
                aria-label={$t('file.click_to_reveal')}
            >                
                {@html protectionHtml}
            </div>
        {:else}
            <div
                class="action-image protected-image clickable-reveal"
                on:click={handleImageClick}
                tabindex="0"
                role="button"
                aria-label-title={buttonTitle}
                aria-label={$t('file.click_to_reveal')}
            >
                <img 
                    id="img-file-{file_id}"
                    src={imageSrc} 
                    alt={imageTitle} 
                    title={imageTitle}
                    class="action-image img-fluid"
                    width={width || undefined}
                    height={height || undefined}
                />
            </div>
        {/if}
    {:else}
        <div class="position-relative">
            <div
                class="action-image"
                on:click={handleImageClick}
                tabindex="0"
                role="button"
            >
                <img 
                    id="img-file-{file_id}"
                    src={imageSrc} 
                    alt={imageTitle} 
                    title={imageTitle}
                    class="action-image img-fluid"
                    width={width || undefined}
                    height={height || undefined}
                />
            </div>
            <a 
                class="hidden-download" 
                target="_blank" 
                rel="noreferrer" 
                href={window.WWW_DIR_JAVASCRIPT + 'file/downloadfile/' + file_id + '/' + hash + '/(inline)/true'}
            ></a>
        </div>
    {/if}
{:else if !canShowImage && verificationMessage}
    <div class="text-muted p-2 border rounded">
        <i class="material-icons">info</i>
        {verificationMessage}
        {#if countdownSeconds > 0}
            <span class="text-secondary">
                ({$t('file.next_attempt_in')} {countdownSeconds} s.)
            </span>
       {:else if verificationAttempts >= maxVerificationAttempts}
            <button 
                class="btn btn-secondary btn-xs"
                on:click={requestReverification}>
                {$t('file.reverify_image')}
            </button>
        {/if}
    </div>
{/if}
