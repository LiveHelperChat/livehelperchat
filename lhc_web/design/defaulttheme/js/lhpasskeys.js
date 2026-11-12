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
        var passwordInput = container.querySelector('[data-lh-passkey-password]');
        var addButton = container.querySelector('[data-lh-passkey-add]');

        if (addButton) {
            addButton.addEventListener('click', function () {
                if (addButton.disabled) {
                    return;
                }

                addButton.disabled = true;
                showError(errorBox, '');

                var password = passwordInput ? passwordInput.value : '';
                var reauth = Promise.resolve();
                if (!password && container.getAttribute('data-has-passkeys') === '1') {
                    reauth = postJson(container.getAttribute('data-url-reauth-options'), {}).then(function (result) {
                        if (result.error) {
                            throw new Error(result.message);
                        }
                        return navigator.credentials.get({ publicKey: prepareRequestOptions(result.options) });
                    }).then(function (credential) {
                        return postJson(container.getAttribute('data-url-reauth'), { credential: publicKeyCredentialToJSON(credential) });
                    }).then(function (result) {
                        if (result.error) {
                            throw new Error(result.message);
                        }
                    });
                }

                reauth.then(function () {
                    return postJson(container.getAttribute('data-url-register-options'), {
                        name: nameInput ? nameInput.value : '',
                        password: password
                    });
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

    var conditionalController = null;
    var conditionalSettled = null;

    function pause(milliseconds) {
        return new Promise(function (resolve) {
            window.setTimeout(resolve, milliseconds);
        });
    }

    /**
     * Modal (non autofill) assertion. Browser allows only one WebAuthn request at a time,
     * so if the autofill ceremony is still being cancelled we retry once.
     */
    function getAssertion(publicKey, attempt) {
        attempt = attempt || 0;

        return navigator.credentials.get({
            publicKey: publicKey
        }).catch(function (exception) {
            var isPending = exception && (exception.name === 'InvalidStateError' || /already pending/i.test(exception.message || ''));

            if (attempt === 0 && isPending) {
                return pause(300).then(function () {
                    return getAssertion(publicKey, attempt + 1);
                });
            }

            throw exception;
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

            // Autofill (conditional) request has to be cancelled and settled before a new request is allowed
            abortConditional().then(function () {
                return pause(50);
            }).then(function () {
                return postJson(button.getAttribute('data-options-url'), {
                    username: usernameInput ? usernameInput.value : ''
                });
            }).then(function (optionsResponse) {
                if (optionsResponse.error) {
                    throw new Error(optionsResponse.message);
                }

                return getAssertion(prepareRequestOptions(optionsResponse.options));
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

                // Offer passkey autofill again, it was cancelled by the button click
                initConditionalLogin(button);
            });
        });
    }

    function init() {
        document.querySelectorAll('[data-lh-passkey-container]').forEach(initRegistration);

        document.querySelectorAll('[data-lh-passkey-login]').forEach(initLogin);

        var loginButton = document.querySelector('[data-lh-passkey-login]');

        if (loginButton) {
            initConditionalLogin(loginButton);
        }
    }

    function abortConditional() {
        var controller = conditionalController;
        var settled = conditionalSettled;

        conditionalController = null;
        conditionalSettled = null;

        if (controller) {
            try {
                controller.abort();
            } catch (exception) {
                // Already aborted
            }
        }

        return settled || Promise.resolve();
    }

    /**
     * Passkey autofill (conditional mediation).
     *
     * Passkey is offered as one of the options in the username field autocomplete list,
     * nothing is shown until the user picks it. Modal flow stays on the login button.
     */
    function initConditionalLogin(button) {
        if (typeof window.PublicKeyCredential === 'undefined' || typeof PublicKeyCredential.isConditionalMediationAvailable !== 'function') {
            return;
        }

        var errorBox = document.getElementById('lh-passkey-login-error');
        var redirectInput = document.querySelector('input[name="redirect"]');
        var usernameInput = document.querySelector('input[name="Username"]');

        PublicKeyCredential.isConditionalMediationAvailable().then(function (available) {
            if (available !== true || !button.isConnected) {
                return;
            }

            abortConditional();

            var controller = new AbortController();
            conditionalController = controller;

            conditionalSettled = postJson(button.getAttribute('data-options-url'), {
                username: usernameInput ? usernameInput.value : ''
            }).then(function (optionsResponse) {
                if (optionsResponse.error || controller.signal.aborted) {
                    return null;
                }

                // `signal` must be next to `publicKey`, otherwise the request can not be cancelled
                return navigator.credentials.get({
                    publicKey: prepareRequestOptions(optionsResponse.options),
                    mediation: 'conditional',
                    signal: controller.signal
                });
            }).then(function (credential) {
                if (!credential) {
                    return null;
                }

                if (conditionalController === controller) {
                    conditionalController = null;
                }

                return postJson(button.getAttribute('data-verify-url'), {
                    credential: publicKeyCredentialToJSON(credential),
                    redirect: redirectInput ? redirectInput.value : ''
                });
            }).then(function (result) {
                if (!result) {
                    return;
                }

                if (result.error) {
                    throw new Error(result.message);
                }

                window.location.href = result.redirect;
            }).catch(function (exception) {
                if (exception && (exception.name === 'AbortError' || exception.name === 'NotAllowedError')) {
                    return;
                }

                showError(errorBox, errorMessage(exception));
            }).then(function () {
                if (conditionalController === controller) {
                    conditionalController = null;
                    conditionalSettled = null;
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
