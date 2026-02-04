<svelte:options customElement={{tag: 'browser-notification',shadow: 'none'}}/>
<script>
    import { onMount } from 'svelte';

    // State variables
    let notificationEnabled = false;
    let notificationState = "default"; // "default", "granted", "denied"
    import { t } from "../i18n/i18n.js";


    // Check if browser supports notifications
    const isNotificationSupported = () => {
        return 'Notification' in window;
    };

    // Check current notification permission status
    const checkNotificationStatus = () => {
        if (!isNotificationSupported()) {
            notificationState = "denied";
            return;
        }

        notificationState = Notification.permission;

        if (notificationState === "granted") {
            notificationEnabled = true;
        } else if (notificationState === "denied") {
            notificationEnabled = false;
        } else {
            notificationEnabled = false;
        }
    };

    // Request notification permission
    const requestNotificationPermission = async () => {
        if (!isNotificationSupported()) {
            return;
        }

        // If already granted or denied, don't request again
        if (notificationState === "granted" || notificationState === "denied") {
            return;
        }

        try {
            const permission = await Notification.requestPermission();
            notificationState = permission;

            if (permission === "granted") {
                notificationEnabled = true;

                // Show a test notification
                const notification = new Notification($t("homepage.notifications_enabled"), {
                    body: $t("homepage.notifications_like"),
                    icon: WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION + '/notification.png' // Use your site's favicon or other icon
                });

                // Close the notification after 3 seconds
                setTimeout(() => notification.close(), 3000);
            } else if (permission === "denied") {
                notificationEnabled = false;
            }
        } catch (error) {
            console.error("Error requesting notification permission:", error);
            // nothing to set — template uses translations directly
        }
    };

    // Handle click on the notification icon
    const handleIconClick = () => {
        requestNotificationPermission();
    };

    // Set up our component when it mounts
    onMount(() => {
        checkNotificationStatus();
    });
</script>

{#if notificationState !== 'granted'}
<li class="list-inline-item nav-item">
    <a class="nav-link status-indicator {notificationState === 'granted' ? 'text-success' : notificationState === 'denied' ? 'text-danger' : 'text-warning'}"
        on:click={handleIconClick}
        role="button"
        title={$t(notificationState === 'granted' ? "homepage.notifications_enabled" : notificationState === 'denied' ? "homepage.notifications_blocked" : "homepage.enable_notifications")}
        tabindex="0"
        aria-label="Toggle notifications">
        <span class="material-icons">
          {#if notificationState === 'granted'}
            notifications
          {:else if notificationState === 'denied'}
            notifications_off
          {:else}
            notifications
          {/if}
        </span>
    </a>
</li>
{/if}
