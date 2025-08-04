<?php

namespace LiveHelperChat\Helpers\Bot;

class ActionConditions
{
    /**
     * Check if current server time matches the custom schedule
     * @param string $scheduleValue Schedule string like "1-5,11:00-20:00; 6-7,11:00-19:00"
     * @return bool True if current time matches any schedule segment
     */
    public static function checkCustomSchedule($scheduleValue)
    {
        if (empty($scheduleValue)) {
            return false;
        }

        $currentTime = time();
        $currentDayOfWeek = (int)date('N', $currentTime); // 1=Monday, 7=Sunday
        $currentHour = (int)date('G', $currentTime);
        $currentMinute = (int)date('i', $currentTime);
        $currentTimeInMinutes = $currentHour * 60 + $currentMinute;

        // Parse schedule segments separated by semicolon
        $scheduleSegments = explode(';', $scheduleValue);

        foreach ($scheduleSegments as $segment) {
            $segment = trim($segment);
            if (empty($segment)) continue;

            // Parse format: "1-5,11:00-20:00" or "6-7,11:00-19:00"
            $parts = explode(',', $segment);
            if (count($parts) !== 2) continue;

            $dayRange = trim($parts[0]);
            $timeRange = trim($parts[1]);

            // Parse day range (e.g., "1-5", "6-7", or single day "1")
            $dayParts = explode('-', $dayRange);
            $dayMatches = false;

            if (count($dayParts) === 1) {
                // Single day (e.g., "1")
                $singleDay = (int)$dayParts[0];
                $dayMatches = ($currentDayOfWeek === $singleDay);
            } elseif (count($dayParts) === 2) {
                // Day range (e.g., "1-5")
                $startDay = (int)$dayParts[0];
                $endDay = (int)$dayParts[1];

                // Check if current day is in range
                if ($startDay <= $endDay) {
                    $dayMatches = ($currentDayOfWeek >= $startDay && $currentDayOfWeek <= $endDay);
                } else {
                    // Handle wrap-around (e.g., 6-1 for Sat-Mon)
                    $dayMatches = ($currentDayOfWeek >= $startDay || $currentDayOfWeek <= $endDay);
                }
            }

            if ($dayMatches) {
                // Parse time range (e.g., "11:00-20:00")
                $timeParts = explode('-', $timeRange);
                if (count($timeParts) !== 2) continue;

                $startTime = trim($timeParts[0]);
                $endTime = trim($timeParts[1]);

                // Convert times to minutes
                $startTimeParts = explode(':', $startTime);
                $endTimeParts = explode(':', $endTime);

                if (count($startTimeParts) !== 2 || count($endTimeParts) !== 2) continue;

                $startTimeInMinutes = (int)$startTimeParts[0] * 60 + (int)$startTimeParts[1];
                $endTimeInMinutes = (int)$endTimeParts[0] * 60 + (int)$endTimeParts[1];

                // Check if current time is in range
                if ($startTimeInMinutes <= $endTimeInMinutes) {
                    if ($currentTimeInMinutes >= $startTimeInMinutes && $currentTimeInMinutes <= $endTimeInMinutes) {
                        return true;
                    }
                } else {
                    // Handle wrap-around (e.g., 22:00-06:00)
                    if ($currentTimeInMinutes >= $startTimeInMinutes || $currentTimeInMinutes <= $endTimeInMinutes) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}