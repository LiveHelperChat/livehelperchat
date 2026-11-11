/**
 * Passkeys (WebAuthn) UI
 *
 * Registration is initialised for elements with `data-lh-passkey-container`,
 * login for buttons with `data-lh-passkey-login`.
 */
(function () {
    'use strict';

    if (!window.PublicKeyCredential || !navigator.credentials) {
        return;
    }

    function base64UrlToBuffer(value) {
        var padding = new Array((4 - (value.length % 4)) % 4 + 1).join('=');
        var base64 = (value + padding).replace(/-/g, '+').replace(/_/g, '/');
        var raw = window.atob(base64);
        var bytes = new Uint8Array(raw.length);

        for (var i = 0; i < raw.length; i++) {
            bytes[i] = raw.charCodeAt(i);
        }

        return bytes.buffer;
    }

    function bufferToBase64Url(buffer) {
        var bytes = new Uint8Array(buffer);
        var binary = '';

        for (var i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }

        return window.btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
    }

    function getCsrfToken() {
        var input = document.querySelector('input[name="csfr_token"]');
        return input ? input.value : '';
    }

    function postJson(url, data) {
        return fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRFTOKEN': getCsrfToken()
            },
            body: JSON.stringify(data || {})
        }).then(function (response) {
            return response.json().then(function (json) {
                return json;
            }, function () {
                return { error: true, message: 'Invalid server response' };
            });
        });
    }

    function publicKeyCredentialToJSON(credential) {
        var response = credential.response;
        var data = {
            id: credential.id,
            rawId: bufferToBase64Url(credential.rawId),
            type: credential.type,
            clientExtensionResults: credential.getClientExtensionResults ? credential.getClientExtensionResults() : {}
        };

        if (response.attestationObject) {
            data.response = {
                clientDataJSON: bufferToBase64Url(response.clientDataJSON),
                attestationObject: bufferToBase64Url(response.attestationObject),
                transports: response.getTransports ? response.getTransports() : []
            };

            if (credential.authenticatorAttachment) {
                data.authenticatorAttachment = credential.authenticatorAttachment;
            }
        } else {
            data.response = {
                clientDataJSON: bufferToBase64Url(response.clientDataJSON),
                authenticatorData: bufferToBase64Url(response.authenticatorData),
                signature: bufferToBase64Url(response.signature),
                userHandle: response.userHandle ? bufferToBase64Url(response.userHandle) : null
            };

            if (credential.authenticatorAttachment) {
                data.authenticatorAttachment = credential.authenticatorAttachment;
            }
        }

        return data;
    }

    function errorMessage(exception) {
        if (!exception) {
            return 'Passkey operation was cancelled';
        }

        if (exception.name === 'NotAllowedError') {
            return 'Passkey operation was cancelled or timed out';
        }

        return exception.message ? exception.message : 'Passkey operation failed';
    }

    function showError(element, message) {
        if (element) {
            element.textContent = message || '';
        }
    }

    function prepareCreationOptions(options) {
        options.challenge = base64UrlToBuffer(options.challenge);
        options.user.id = base64UrlToBuffer(options.user.id);

        (options.excludeCredentials || []).forEach(function (credential) {
            credential.id = base64UrlToBuffer(credential.id);
        });

        return options;
    }

    function prepareRequestOptions(options) {
        options.challenge = base64UrlToBuffer(options.challenge);

        (options.allowCredentials || []).forEach(function (credential) {
            credential.id = base64UrlToBuffer(credential.id);
        });

        return options;
    }

    function initRegistration(container) {
        var errorBox = container.querySelector('[data-lh-passkey-error]');
        var nameInput = container.querySelector('[data-lh-passkey-name]');
        var addButton = container.querySelector('[data-lh-passkey-add]');

        if (addButton) {
            addButton.addEventListener('click', function () {
                if (addButton.disabled) {
                    return;
                }

                addButton.disabled = true;
                showError(errorBox, '');

                postJson(container.getAttribute('data-url-register-options'), {
                    name: nameInput ? nameInput.value : ''
                }).then(function (optionsResponse) {
                    if (optionsResponse.error) {
                        throw new Error(optionsResponse.message);
                    }

                    return navigator.credentials.create({
                        publicKey: prepareCreationOptions(optionsResponse.options)
                    });
                }).then(function (credential) {
                    return postJson(container.getAttribute('data-url-register'), {
                        credential: publicKeyCredentialToJSON(credential)
                    });
                }).then(function (result) {
                    if (result.error) {
                        throw new Error(result.message);
                    }

                    window.location.reload();
                }).catch(function (exception) {
                    addButton.disabled = false;
                    showError(errorBox, errorMessage(exception));
                });
            });
        }

        container.querySelectorAll('[data-lh-passkey-delete]').forEach(function (button) {
            button.addEventListener('click', function () {
                if (button.disabled) {
                    return;
                }

                button.disabled = true;
                showError(errorBox, '');

                postJson(container.getAttribute('data-url-delete') + '/(id)/' + button.getAttribute('data-lh-passkey-delete'), {}).then(function (result) {
                    if (result.error) {
                        throw new Error(result.message);
                    }

                    window.location.reload();
                }).catch(function (exception) {
                    button.disabled = false;
                    showError(errorBox, errorMessage(exception));
                });
            });
        });
    }

    function initLogin(button) {
        var errorBox = document.getElementById('lh-passkey-login-error');
        var redirectInput = document.querySelector('input[name="redirect"]');
        var usernameInput = document.querySelector('input[name="Username"]');

        button.addEventListener('click', function () {
            if (button.disabled) {
                return;
            }

            button.disabled = true;
            showError(errorBox, '');

            postJson(button.getAttribute('data-options-url'), {
                username: usernameInput ? usernameInput.value : ''
            }).then(function (optionsResponse) {
                if (optionsResponse.error) {
                    throw new Error(optionsResponse.message);
                }

                return navigator.credentials.get({
                    publicKey: prepareRequestOptions(optionsResponse.options)
                });
            }).then(function (credential) {
                return postJson(button.getAttribute('data-verify-url'), {
                    credential: publicKeyCredentialToJSON(credential),
                    redirect: redirectInput ? redirectInput.value : ''
                });
            }).then(function (result) {
                if (result.error) {
                    throw new Error(result.message);
                }

                window.location.href = result.redirect;
            }).catch(function (exception) {
                button.disabled = false;
                showError(errorBox, errorMessage(exception));
            });
        });
    }

    function init() {
        document.querySelectorAll('[data-lh-passkey-container]').forEach(initRegistration);

        document.querySelectorAll('[data-lh-passkey-login]').forEach(initLogin);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
