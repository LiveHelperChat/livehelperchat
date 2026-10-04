<?php

/**
 * Call recordings maintenance. Run once a day.
 * php cron.php -s site_admin -c cron/voicevideo_recordings
 *
 * - Removes recordings (database records and files) older than retention period
 * - Marks recordings without final status from egress as failed
 */

echo "Voice & Video recordings maintenance\n";

try {
    $removed = erLhcoreClassVoiceVideoRecording::applyRetention();
    echo "Removed recordings: " . $removed . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
