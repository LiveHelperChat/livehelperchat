<svelte:options customElement={{tag: 'audio-checker',shadow: 'none'}}/>
<script>
    import { onMount } from 'svelte';
    import { t } from "../i18n/i18n.js";

    // State variables
    let soundEnabled = false;
    let statusMessage = $t("homepage.interact_sound");
    let audioContext = null;
    let initialInteractionDone = false;

    // Function to check if AudioContext is suspended/running
    const checkAudioState = async () => {
        if (!audioContext) {
            try {
                // Create new AudioContext
                audioContext = new (window.AudioContext || window.webkitAudioContext)();

                // Initial state is usually suspended due to browser policies
                soundEnabled = audioContext.state === 'running';
                updateStatusMessage();
            } catch (error) {
                statusMessage = $t("homepage.browser_no_sound");
                console.error("Audio Context Error:", error);
            }
        } else {
            // Update state based on current audio context state
            soundEnabled = audioContext.state === 'running';
            updateStatusMessage();
        }
    };

    // Function to update status message
    const updateStatusMessage = () => {
        if (soundEnabled) {
            statusMessage = "Sound is enabled";
        } else if (initialInteractionDone) {
            statusMessage = $t("homepage.sound_disabled");
        } else {
            statusMessage = $t("homepage.interact_sound");
        }
    };

    // Function to try enabling sound after user interaction
    const enableSound = async () => {
        if (!audioContext) return;

        try {
            // Always try to resume audio context on interaction
            if (audioContext.state === 'suspended') {
                await audioContext.resume();
                // Check if it worked
                soundEnabled = audioContext.state === 'running';
            }

            // Update status message
            updateStatusMessage();
        } catch (error) {
            statusMessage = "Failed to enable audio";
            console.error("Enable Sound Error:", error);
        }
    };

    // Add interaction listeners to detect user interaction with the page
    const setupInteractionListeners = () => {
        const interactionEvents = ['click', 'keydown', 'touchstart'];

        const handleInteraction = async () => {
            initialInteractionDone = true;

            // Try to enable sound on interaction
            await enableSound();

            // Check current state and update UI
            await checkAudioState();

            // If sound is successfully enabled, remove all event listeners
            if (soundEnabled) {
                interactionEvents.forEach(event => {
                    document.removeEventListener(event, handleInteraction);
                });
            }
        };

        // Add event listeners that will trigger only once
        interactionEvents.forEach(event => {
            // Only use { once: true } for the first listener registration
            // This ensures we get at least one chance to enable sound
            document.addEventListener(event, handleInteraction, { once: true });
        });

        // No need for cleanup function since { once: true } automatically removes listeners
        // or they'll be manually removed when sound is enabled
    };

    // Set up our component when it mounts
    onMount(() => {
        checkAudioState();
        setupInteractionListeners();

        // Clean up function
        return () => {
            if (audioContext) {
                audioContext.close();
            }
        };
    });
</script>

{#if !soundEnabled}
<li class="list-inline-item nav-item">
    <a class="nav-link" title={statusMessage}><i class="material-icons text-danger">volume_off</i></a>
</li>
{/if}