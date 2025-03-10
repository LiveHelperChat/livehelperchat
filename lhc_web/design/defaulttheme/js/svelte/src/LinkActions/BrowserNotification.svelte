<svelte:options customElement={{tag: 'browser-notification',shadow: 'none'}}/>
<script>
    import { onMount } from 'svelte';

    // State variables
    let notificationEnabled = false;
    let notificationState = "default"; // "default", "granted", "denied"
    let statusMessage = $t("homepage.enable_notifications");
    import { t } from "../i18n/i18n.js";


    // Check if browser supports notifications
    const isNotificationSupported = () => {
        return 'Notification' in window;
    };

    // Check current notification permission status
    const checkNotificationStatus = () => {
        if (!isNotificationSupported()) {
            notificationState = "denied";
            statusMessage = $t("homepage.notifications_not_supported");
            return;
        }

        notificationState = Notification.permission;

        if (notificationState === "granted") {
            notificationEnabled = true;
            statusMessage = "Notifications enabled";
        } else if (notificationState === "denied") {
            notificationEnabled = false;
            statusMessage = $t("homepage.notifications_blocked");
        } else {
            notificationEnabled = false;
            statusMessage = $t("homepage.enable_notifications");
        }
    };

    // Request notification permission
    const requestNotificationPermission = async () => {
        if (!isNotificationSupported()) {
            statusMessage = $t("homepage.notifications_not_supported");
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
                statusMessage = "Notifications enabled";

                // Show a test notification
                const notification = new Notification("Notifications Enabled", {
                    body: $t("homepage.notifications_like"),
                    icon: WWW_DIR_JAVASCRIPT_FILES_NOTIFICATION + '/notification.png' // Use your site's favicon or other icon
                });

                // Close the notification after 3 seconds
                setTimeout(() => notification.close(), 3000);
            } else if (permission === "denied") {
                notificationEnabled = false;
                statusMessage = $t("homepage.notifications_blocked");
            }
        } catch (error) {
            console.error("Error requesting notification permission:", error);
            statusMessage = "Error enabling notifications";
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
        title="{statusMessage}"
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
