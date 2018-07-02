<?php /*
*
*  Push Notifications codelab
*  Copyright 2015 Google Inc. All rights reserved.
*
*  Licensed under the Apache License, Version 2.0 (the "License");
*  you may not use this file except in compliance with the License.
*  You may obtain a copy of the License at
*
*      https://www.apache.org/licenses/LICENSE-2.0
*
*  Unless required by applicable law or agreed to in writing, software
*  distributed under the License is distributed on an "AS IS" BASIS,
*  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
*  See the License for the specific language governing permissions and
*  limitations under the License
*
*/ ?>

/* eslint-env browser, serviceworker, es6 */

'use strict';

/* eslint-disable max-len */

const applicationServerPublicKey = '<?php echo htmlspecialchars($nsettings['public_key'])?>';

/* eslint-enable max-len */

function urlB64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

function isClientFocused() {
    return clients.matchAll({
        type: 'window',
        includeUncontrolled: true
    })
        .then((windowClients) => {
            let clientIsFocused = false;

            for (let i = 0; i < windowClients.length; i++) {
                const windowClient = windowClients[i];
                if (windowClient.focused) {
                    clientIsFocused = true;
                    break;
                }
            }

            return clientIsFocused;
        });
}

self.addEventListener('push', function(event) {
    <?php /* console.log('[Service Worker] Push Received.'); */ ?>
    <?php /* console.log(`[Service Worker] Push had this data: "${event.data.text()}"`); */ ?>

    const dataNotification = event.data.json();

    var options = {
        body: dataNotification.msg,
        tag: dataNotification.tag
    };

    if (typeof dataNotification.icon !== 'undefined' && dataNotification.icon != '') {
        options.icon = dataNotification.icon;
    }

    if (typeof dataNotification.badge !== 'undefined' && dataNotification.badge != '') {
        options.badge = dataNotification.badge;
    }

    if (typeof dataNotification.renotify !== 'undefined' && dataNotification.renotify == 1) {
        options.renotify = true;
    }

    if (typeof dataNotification.rinteraction !== 'undefined' && dataNotification.rinteraction == 1) {
        options.requireInteraction = true;
    }

    if (typeof dataNotification.vibrate !== 'undefined' && dataNotification.vibrate.length > 0) {
        options.vibrate = dataNotification.vibrate;
    }

    if (typeof dataNotification.data !== 'undefined') {
        options.data = dataNotification.data;
    }

    // Do not show notification if window is focused
    const promiseChain = isClientFocused().then((clientIsFocused) => {
        if (clientIsFocused) {
            <?php /* console.log('Don\'t need to show a notification.'); */ ?>
            return;
        }
        <?php /* Client isn't focused, we need to show a notification. */ ?>
        return self.registration.showNotification(dataNotification.title, options);
    });

    event.waitUntil(promiseChain);
});

self.addEventListener('notificationclick', function(event) {
    <?php /* console.log('[Service Worker] Notification click Received.'); */ ?>

    const notificationData = event.notification.data;

    event.notification.close();

    const promiseChain = clients.matchAll({
        type: 'window',
        includeUncontrolled: true
    }).then((windowClients) => {
        let matchingClient = null;

        for (let i = 0; i < windowClients.length; i++) {
            const windowClient = windowClients[i];
            if (windowClient.focused === false) {
                matchingClient = windowClient;
                windowClient.postMessage({
                    lhc_cid: notificationData.cid,
                    lhc_ch: notificationData.ch
                });
                break;
            }
        }

        if (matchingClient) {
            if (matchingClient.focused == false) {
                return matchingClient.focus();
            }
        } else {
            return clients.openWindow(notificationData.url + '/(id)/' + notificationData.cid + '/(hashread)/' + notificationData.ch);
        }
    });

    event.waitUntil(promiseChain);
});

self.addEventListener('pushsubscriptionchange', function(event) {
    <?php /* console.log('[Service Worker]: \'pushsubscriptionchange\' event fired.'); */ ?>
    const applicationServerKey = urlB64ToUint8Array(applicationServerPublicKey);
    event.waitUntil(
        self.registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: applicationServerKey
        })
        .then(function(newSubscription) {
            <?php /* // TODO: Send to application server */ ?>
            <?php /* console.log('[Service Worker] New subscription: ', newSubscription); */ ?>
        })
    );
});