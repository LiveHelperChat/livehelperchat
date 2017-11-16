<?php

class erLhcoreClassModelChatOnlineUser
{

    public function getState()
    {
        return array(
            'id' => $this->id,
            'ip' => $this->ip,
            'vid' => $this->vid,
            'current_page' => $this->current_page,
            'invitation_seen_count' => $this->invitation_seen_count,
            'page_title' => $this->page_title,
            'chat_id' => $this->chat_id, // For future
            'last_visit' => $this->last_visit,
            'first_visit' => $this->first_visit,
            'user_agent' => $this->user_agent,
            'user_country_name' => $this->user_country_name,
            'user_country_code' => $this->user_country_code,
            'operator_message' => $this->operator_message,
            'operator_user_id' => $this->operator_user_id,
            'operator_user_proactive' => $this->operator_user_proactive,
            'message_seen' => $this->message_seen,
            'message_seen_ts' => $this->message_seen_ts,
            'pages_count' => $this->pages_count,
            'tt_pages_count' => $this->tt_pages_count,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'city' => $this->city,
            'identifier' => $this->identifier,
            'time_on_site' => $this->time_on_site,
            'tt_time_on_site' => $this->tt_time_on_site,
            'referrer' => $this->referrer,
            'invitation_id' => $this->invitation_id,
            'total_visits' => $this->total_visits,
            'invitation_count' => $this->invitation_count,
            'requires_email' => $this->requires_email,
            'requires_username' => $this->requires_username,
            'requires_phone' => $this->requires_phone,
            'dep_id' => $this->dep_id,
            'reopen_chat' => $this->reopen_chat,
            'operation' => $this->operation,
            'operation_chat' => $this->operation_chat,
            'screenshot_id' => $this->screenshot_id,
            'online_attr' => $this->online_attr,
            'online_attr_system' => $this->online_attr_system,
            'visitor_tz' => $this->visitor_tz,
            'last_check_time' => $this->last_check_time,
            'user_active' => $this->user_active,
            'notes' => $this->notes,
            'show_on_mobile' => $this->show_on_mobile
        );
    }

    public function setState(array $properties)
    {
        foreach ($properties as $key => $val) {
            $this->$key = $val;
        }
    }

    public static function fetch($chat_id)
    {
        $chat = erLhcoreClassChat::getSession()->load('erLhcoreClassModelChatOnlineUser', (int)$chat_id);
        return $chat;
    }

    public function removeThis()
    {

        $q = ezcDbInstance::get()->createDeleteQuery();

        // Delete user footprint
        $q->deleteFrom('lh_chat_online_user_footprint')->where($q->expr->eq('chat_id', 0), $q->expr->eq('online_user_id', $this->id));
        $stmt = $q->prepare();
        $stmt->execute();
        
        
        $q = ezcDbInstance::get()->createDeleteQuery();
        
        // Delete realted events
        $q->deleteFrom('lh_abstract_proactive_chat_event')->where( $q->expr->eq('vid_id', $this->id));
        $stmt = $q->prepare();
        $stmt->execute();

        erLhcoreClassChat::getSession()->delete($this);
    }

    public function __get($var)
    {
        switch ($var) {
            case 'last_visit_front':
                return $this->last_visit_front = date(erLhcoreClassModule::$dateDateHourFormat, $this->last_visit);
                break;

            case 'first_visit_front':
                return $this->first_visit_front = date(erLhcoreClassModule::$dateDateHourFormat, $this->first_visit);
                break;

            case 'invitation':
                $this->invitation = false;

                if ($this->invitation_id > 0) {
                    try {
                        $this->invitation = erLhAbstractModelProactiveChatInvitation::fetch($this->invitation_id);
                    } catch (Exception $e) {
                        $this->invitation = false;
                    }
                }

                return $this->invitation;
                break;

            case 'has_message_from_operator':
                return ($this->message_seen == 0 && $this->operator_message != '');
                break;

            case 'notes_intro':
                return $this->notes_intro = $this->notes != '' ? '[ ' . mb_substr($this->notes, 0, 50) . ' ]' . '<br/>' : '';
                break;

            case 'chat':
                $this->chat = false;
                if ($this->chat_id > 0) {
                    try {
                        $this->chat = erLhcoreClassModelChat::fetch($this->chat_id);
                    } catch (Exception $e) {
                        //
                    }
                }
                return $this->chat;
                break;

            case 'can_view_chat':
                $this->can_view_chat = false;
                $currentUser = erLhcoreClassUser::instance();

                if ($this->operator_user_id == $currentUser->getUserID()) {
                    $this->can_view_chat = true; // Faster way
                } else if ($this->chat instanceof erLhcoreClassModelChat) {
                    $this->can_view_chat = erLhcoreClassChat::hasAccessToRead($this->chat);
                }

                return $this->can_view_chat;
                break;

            case 'operator_user':
                $this->operator_user = false;
                if ($this->operator_user_id > 0) {
                    try {
                        $this->operator_user = erLhcoreClassModelUser::fetch($this->operator_user_id);
                    } catch (Exception $e) {

                    }
                }
                return $this->operator_user;
                break;

            case 'operator_user_send':
                $this->operator_user_send = $this->operator_user !== false;
                return $this->operator_user_send;
                break;

            case 'operator_user_string':
                $this->operator_user_string = (string)$this->operator_user;
                return $this->operator_user_string;
                break;

            case 'time_on_site_front':
                $this->time_on_site_front = gmdate(erLhcoreClassModule::$dateHourFormat, $this->time_on_site);
                return $this->time_on_site_front;
                break;

            case 'tt_time_on_site_front':

                $this->tt_time_on_site_front = null;

                $diff = $this->tt_time_on_site;
                $days = floor($diff / (3600 * 24));
                $hours = floor(($diff - ($days * 3600 * 24)) / 3600);
                $minits = floor(($diff - ($hours * 3600) - ($days * 3600 * 24)) / 60);
                $seconds = ($diff - ($hours * 3600) - ($minits * 60) - ($days * 3600 * 24));

                if ($days > 0) {
                    $this->tt_time_on_site_front = $days . ' d.';
                } elseif ($hours > 0) {
                    $this->tt_time_on_site_front = $hours . ' h.';
                } elseif ($minits > 0) {
                    $this->tt_time_on_site_front = $minits . ' m.';
                } elseif ($seconds >= 0) {
                    $this->tt_time_on_site_front = $seconds . ' s.';
                }

                return $this->tt_time_on_site_front;
                break;

            case 'last_visit_seconds_ago':
                $this->last_visit_seconds_ago = time() - $this->last_visit;
                return $this->last_visit_seconds_ago;
                break;

            case 'last_check_time_ago':
                $this->last_check_time_ago = time() - $this->last_check_time;
                return $this->last_check_time_ago;
                break;

            case 'visitor_tz_time':
                $this->visitor_tz_time = '-';
                if ($this->visitor_tz != '') {
                    $date = new DateTime(null, new DateTimeZone($this->visitor_tz));
                    $this->visitor_tz_time = $date->format(erLhcoreClassModule::$dateHourFormat);
                }
                return $this->visitor_tz_time;
                break;

            case 'lastactivity_ago':
                $this->lastactivity_ago = '';

                if ($this->last_visit > 0) {

                    $periods = array("s.", "m.", "h.", "d.", "w.", "m.", "y.", "dec.");
                    $lengths = array("60", "60", "24", "7", "4.35", "12", "10");

                    $difference = time() - $this->last_visit;

                    for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
                        $difference /= $lengths[$j];
                    }

                    $difference = round($difference);

                    $this->lastactivity_ago = "$difference $periods[$j]";
                };

                return $this->lastactivity_ago;
                break;

            case 'screenshot':
                $this->screenshot = false;
                if ($this->screenshot_id > 0) {
                    try {
                        $this->screenshot = erLhcoreClassModelChatFile::fetch($this->screenshot_id);
                    } catch (Exception $e) {

                    }
                }

                return $this->screenshot;
                break;

            case 'online_attr_system_array':
                $this->online_attr_system_array = array();
                if ($this->online_attr_system != '') {
                    $this->online_attr_system_array = json_decode($this->online_attr_system, true);
                }
                return $this->online_attr_system_array;
                break;

            case 'online_status':
                $this->online_status = 2; // Offline

                if (erLhcoreClassChat::$trackTimeout == 0) {
                    erLhcoreClassChat::$trackTimeout = 15;
                }

                if (erLhcoreClassChat::$trackActivity == true) {
                    if ($this->last_check_time_ago < (erLhcoreClassChat::$trackTimeout + 10) && $this->user_active == 1) { //User still on site, it does not matter that he have closed widget.
                        $this->online_status = 0; // Online
                    } elseif ($this->last_check_time_ago < (erLhcoreClassChat::$trackTimeout + 10) && $this->user_active == 0) {
                        $this->online_status = 1; // Away
                    }
                } else {
                    if ($this->last_check_time_ago < (erLhcoreClassChat::$trackTimeout + 10)) { //User still on site, it does not matter that he have closed widget.
                        $this->online_status = 0; // Online
                    } elseif ($this->last_check_time_ago < (erLhcoreClassChat::$trackTimeout + 300)) {
                        $this->online_status = 1; // Away
                    }
                }

                return $this->online_status;
                break;

            default:
                break;
        }
    }

    public static function getCount($params = array())
    {
        return erLhcoreClassChat::getCount($params, "lh_chat_online_user");
    }

    public static function getList($paramsSearch = array(), $ignoreColumns = array())
    {
        $paramsDefault = array('limit' => 32, 'offset' => 0);

        $params = array_merge($paramsDefault, $paramsSearch);

        $session = erLhcoreClassChat::getSession();
        $q = $session->createFindQuery('erLhcoreClassModelChatOnlineUser', $ignoreColumns);

        $conditions = array();

        if (isset($params['filter']) && count($params['filter']) > 0) {
            foreach ($params['filter'] as $field => $fieldValue) {
                $conditions[] = $q->expr->eq($field, $q->bindValue($fieldValue));
            }
        }

        if (isset($params['filterin']) && count($params['filterin']) > 0) {
            foreach ($params['filterin'] as $field => $fieldValue) {
                $conditions[] = $q->expr->in($field, $fieldValue);
            }
        }

        if (isset($params['filterlt']) && count($params['filterlt']) > 0) {
            foreach ($params['filterlt'] as $field => $fieldValue) {
                $conditions[] = $q->expr->lt($field, $q->bindValue($fieldValue));
            }
        }

        if (isset($params['filtergt']) && count($params['filtergt']) > 0) {
            foreach ($params['filtergt'] as $field => $fieldValue) {
                $conditions[] = $q->expr->gt($field, $q->bindValue($fieldValue));
            }
        }

        if (count($conditions) > 0) {
            $q->where(
                $conditions
            );
        }

        $q->limit($params['limit'], $params['offset']);

        if (!isset($params['sort']) || (isset($params['sort']) && $params['sort'] !== false)) {
            $q->orderBy(isset($params['sort']) ? $params['sort'] : 'id DESC');
        }

        $objects = $session->find($q);

        return $objects;
    }

    public static function executeRequest($url)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Some hostings produces wargning...
        $content = curl_exec($ch);

        return $content;
    }

    public static function getUserData($service, $ip, $params = array())
    {

        if ($service == 'mod_geoip2') {

            if (isset($_SERVER[$params['country_code']]) && isset($_SERVER[$params['country_name']])) {
                $normalizedObject = new stdClass();
                $normalizedObject->country_code = strtolower($_SERVER[$params['country_code']]);
                $normalizedObject->country_name = strtolower($_SERVER[$params['country_name']]);
                $normalizedObject->city = isset($_SERVER[$params['mod_geo_ip_city_name']]) ? $_SERVER[$params['mod_geo_ip_city_name']] : '';
                $normalizedObject->city .= isset($params['mod_geo_ip_region_name']) && isset($_SERVER[$params['mod_geo_ip_region_name']]) ? ', ' . $_SERVER[$params['mod_geo_ip_region_name']] : '';
                $normalizedObject->lat = isset($_SERVER[$params['mod_geo_ip_latitude']]) ? $_SERVER[$params['mod_geo_ip_latitude']] : '0';
                $normalizedObject->lon = isset($_SERVER[$params['mod_geo_ip_longitude']]) ? $_SERVER[$params['mod_geo_ip_longitude']] : '0';

                return $normalizedObject;
            } else {
                return false;
            }
        } elseif ($service == 'php_geoip') {

            if (function_exists('geoip_record_by_name')) {
                $data = @geoip_record_by_name($ip);

                if ($data !== null) {
                    $normalizedObject = new stdClass();
                    $normalizedObject->country_code = isset($data['country_code']) ? strtolower($data['country_code']) : '';
                    $normalizedObject->country_name = isset($data['country_name']) ? strtolower($data['country_name']) : '';
                    $normalizedObject->city = isset($data['city']) ? strtolower($data['city']) : '';
                    $normalizedObject->city .= isset($data['region']) ? ', ' . strtolower($data['region']) : '';
                    $normalizedObject->lat = isset($data['latitude']) ? strtolower($data['latitude']) : '';
                    $normalizedObject->lon = isset($data['longitude']) ? strtolower($data['longitude']) : '';
                    return $normalizedObject;
                } else {
                    return false;
                }

            } else {
                return false;
            }

        } elseif ($service == 'max_mind') {

            if ($params['detection_type'] == 'country') {
                try {
                    $reader = new GeoIp2\Database\Reader('var/external/geoip/GeoLite2-Country.mmdb');
                    $countryData = $reader->country($ip);
                    $normalizedObject = new stdClass();
                    $normalizedObject->country_code = strtolower($countryData->raw['country']['iso_code']);
                    $normalizedObject->country_name = $countryData->raw['country']['names']['en'];
                    $normalizedObject->city = '';
                    $normalizedObject->lat = '';
                    $normalizedObject->lon = '';
                    return $normalizedObject;
                } catch (Exception $e) {
                    return false;
                }
            } elseif ($params['detection_type'] == 'city') {
                try {
                    $reader = new GeoIp2\Database\Reader((isset($params['city_file']) && $params['city_file'] != '') ? $params['city_file'] : 'var/external/geoip/GeoLite2-City.mmdb');
                    $countryData = $reader->city($ip);
                    $normalizedObject = new stdClass();
                    $normalizedObject->country_code = strtolower($countryData->raw['country']['iso_code']);
                    $normalizedObject->country_name = $countryData->raw['country']['names']['en'];
                    $normalizedObject->lat = isset($countryData->raw['location']['latitude']) ? $countryData->raw['location']['latitude'] : '0';
                    $normalizedObject->lon = isset($countryData->raw['location']['longitude']) ? $countryData->raw['location']['longitude'] : '0';

                    try {
                        $normalizedObject->city = $countryData->city->name != '' ? $countryData->city->name : (isset($countryData->raw['location']['time_zone']) ? $countryData->raw['location']['time_zone'] : '');
                        $regionName = isset($countryData->raw['mostSpecificSubdivision']['name']) ? ', ' . $countryData->raw['mostSpecificSubdivision']['name'] : '';
                        $normalizedObject->city .= isset($countryData->mostSpecificSubdivision->isoCode) ? ', ' . $countryData->mostSpecificSubdivision->isoCode : '';
                        $normalizedObject->city .= $regionName;
                    } catch (Exception $e) {
                        // Just in case of city error
                    }

                    return $normalizedObject;
                } catch (Exception $e) {
                    return false;
                }
            }

            return false;

        } elseif ($service == 'freegeoip') {
            $response = self::executeRequest('http://freegeoip.net/json/' . $ip);
            if (!empty($response)) {
                $responseData = json_decode($response);
                if (is_object($responseData)) {

                    $normalizedObject = new stdClass();
                    $normalizedObject->country_code = strtolower($responseData->country_code);
                    $normalizedObject->country_name = $responseData->country_name;
                    $normalizedObject->lat = $responseData->latitude;
                    $normalizedObject->lon = $responseData->longitude;
                    $normalizedObject->city = $responseData->city . ($responseData->region_name != '' ? ', ' . $responseData->region_name : '');

                    return $normalizedObject;
                }
            }

            return false;

        } elseif ($service == 'ipinfodbcom') {
            $response = self::executeRequest("http://api.ipinfodb.com/v3/ip-city/?key={$params['api_key']}&ip={$ip}&format=json");

            if (!empty($response)) {
                $responseData = json_decode($response);
                if (is_object($responseData)) {
                    if ($responseData->statusCode == 'OK') {
                        $normalizedObject = new stdClass();
                        $normalizedObject->country_code = strtolower($responseData->countryCode);
                        $normalizedObject->country_name = $responseData->countryName;
                        $normalizedObject->lat = $responseData->latitude;
                        $normalizedObject->lon = $responseData->longitude;
                        $normalizedObject->city = $responseData->cityName . ($responseData->regionName != '' ? ', ' . $responseData->regionName : '');
                        return $normalizedObject;
                    }
                }
            }

            return false;
        } elseif ($service == 'locatorhq') {

            $ip = (isset($params['ip']) && !empty($params['ip'])) ? $params['ip'] : $ip;

            $response = self::executeRequest("http://api.locatorhq.com/?user={$params['username']}&key={$params['api_key']}&ip={$ip}&format=json");

            if (!empty($response)) {
                $responseData = json_decode($response);
                if (is_object($responseData)) {

                    $normalizedObject = new stdClass();
                    $normalizedObject->country_code = strtolower($responseData->countryCode);
                    $normalizedObject->country_name = $responseData->countryName;
                    $normalizedObject->lat = $responseData->latitude;
                    $normalizedObject->lon = $responseData->longitude;
                    $normalizedObject->city = $responseData->city . ($responseData->region != '' ? ', ' . $responseData->region : '');

                    return $normalizedObject;
                }
                return false;
            }
        }

        return false;
    }

    public static function detectLocation(erLhcoreClassModelChatOnlineUser & $instance)
    {
        $geoData = erLhcoreClassModelChatConfig::fetch('geo_data');
        $geo_data = (array)$geoData->data;

        if (isset($geo_data['geo_detection_enabled']) && $geo_data['geo_detection_enabled'] == 1) {

            $params = array();

            if ($geo_data['geo_service_identifier'] == 'mod_geoip2') {
                $params['country_code'] = $geo_data['mod_geo_ip_country_code'];
                $params['country_name'] = $geo_data['mod_geo_ip_country_name'];
                $params['mod_geo_ip_city_name'] = $geo_data['mod_geo_ip_city_name'];
                $params['mod_geo_ip_latitude'] = $geo_data['mod_geo_ip_latitude'];
                $params['mod_geo_ip_longitude'] = $geo_data['mod_geo_ip_longitude'];
            } elseif ($geo_data['geo_service_identifier'] == 'locatorhq') {
                $params['username'] = $geo_data['locatorhqusername'];
                $params['api_key'] = $geo_data['locatorhq_api_key'];
            } elseif ($geo_data['geo_service_identifier'] == 'ipinfodbcom') {
                $params['api_key'] = $geo_data['ipinfodbcom_api_key'];
            } elseif ($geo_data['geo_service_identifier'] == 'max_mind') {
                $params['detection_type'] = $geo_data['max_mind_detection_type'];
                $params['city_file'] = isset($geo_data['max_mind_city_location']) ? $geo_data['max_mind_city_location'] : '';
            }

            $location = self::getUserData($geo_data['geo_service_identifier'], $instance->ip, $params);

            if ($location !== false) {
                $instance->user_country_code = $location->country_code;
                $instance->user_country_name = $location->country_name;
                $instance->lat = $location->lat;
                $instance->lon = $location->lon;
                $instance->city = $location->city;
            }
        }
    }

    public static function cleanupOnlineUsers()
    {
        $db = ezcDbInstance::get();

        $timeoutCleanup = erLhcoreClassModelChatConfig::fetch('tracked_users_cleanup')->current_value;

        $lastCleanup = erLhcoreClassModelChatConfig::fetch('tracked_users_cleanup_last');

        // Do not clean more often that once per hour
        if ((int)$lastCleanup->current_value < time()-3600) {

            $lastCleanup->identifier = 'tracked_users_cleanup_last';
            $lastCleanup->type = 0;
            $lastCleanup->explain = 'Track last cleanup';
            $lastCleanup->hidden = 1;
            $lastCleanup->value = time();
            $lastCleanup->saveThis();

            $stmt = $db->prepare('DELETE T2 FROM lh_abstract_proactive_chat_event as T2 INNER JOIN lh_chat_online_user as T1 ON T1.id = T2.vid_id WHERE last_visit < :last_activity');
            $stmt->bindValue(':last_activity', (int)(time() - $timeoutCleanup * 24 * 3600), PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $db->prepare('DELETE FROM lh_chat_online_user WHERE last_visit < :last_activity');
            $stmt->bindValue(':last_activity', (int)(time() - $timeoutCleanup * 24 * 3600), PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $db->prepare('DELETE FROM lh_chat_online_user_footprint WHERE chat_id = 0 AND vtime < :last_activity');
            $stmt->bindValue(':last_activity', (int)(time() - $timeoutCleanup * 24 * 3600), PDO::PARAM_INT);
            $stmt->execute();


        }
    }

    public static function cleanAllRecords()
    {
        $db = ezcDbInstance::get();

        $stmt = $db->prepare('DELETE FROM lh_chat_online_user');
        $stmt->execute();

        $stmt = $db->prepare('DELETE FROM lh_abstract_proactive_chat_event');
        $stmt->execute();

        $stmt = $db->prepare('DELETE FROM lh_chat_online_user_footprint WHERE chat_id = 0');
        $stmt->execute();
    }

    public static function isBot($userAgent)
    {
        $crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|' .
            'AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|' .
            'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby';
        $isCrawler = (preg_match("/$crawlers/", $userAgent) > 0);

        return $isCrawler;
    }

    public static function fetchByVid($vid)
    {
        $items = erLhcoreClassModelChatOnlineUser::getList(array('filter' => array('vid' => $vid)));
        if (!empty($items)) {
            $item = array_shift($items);
            return $item;
        }

        return false;
    }
    
    public static function getDynamicInvitation($paramsHandle = array())
    {    
        return erLhAbstractModelProactiveChatInvitation::processProActiveInvitationDynamic($paramsHandle['online_user'], array('tag' => isset($paramsHandle['tag']) ? $paramsHandle['tag'] : ''));
    }
    
    public static function handleRequest($paramsHandle = array())
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && !self::isBot($_SERVER['HTTP_USER_AGENT'])) {
            $newVisitor = false;

            if (isset($paramsHandle['vid']) && !empty($paramsHandle['vid'])) {
                $items = erLhcoreClassModelChatOnlineUser::getList(array('filter' => array('vid' => $paramsHandle['vid'])));
                if (!empty($items)) {
                    $item = array_shift($items);

                    // Visit duration les than 30m. Same as google analytics
                    // See: https://support.google.com/analytics/answer/2731565?hl=en
                    if ((time() - $item->last_visit) <= 30 * 60) {
                        $item->time_on_site += time() - $item->last_visit;
                        $item->tt_time_on_site += time() - $item->last_visit;
                    } else {
                        $item->time_on_site = 0;
                        $item->total_visits++;
                        $item->last_visit = time();
                        $item->pages_count = 0;

                        // Reset chat_id only if chat is not active or pending
                        if ($item->chat_id > 0) {
                            if ($item->chat === false || !in_array($item->chat->status, array(erLhcoreClassModelChat::STATUS_ACTIVE_CHAT, erLhcoreClassModelChat::STATUS_PENDING_CHAT))) {
                                $item->chat_id = 0;
                            }
                        }

                        if ($item->message_seen == 1 && $item->message_seen_ts < (time() - ((int)$paramsHandle['message_seen_timeout'] * 3600))) {
                            $item->message_seen = 0;
                            $item->message_seen_ts = 0;
                            $item->operator_message = '';
                        }
                    }

                    $item->identifier = (isset($paramsHandle['identifier']) && !empty($paramsHandle['identifier'])) ? $paramsHandle['identifier'] : $item->identifier;

                    if (isset($paramsHandle['department']) && is_array($paramsHandle['department']) && count($paramsHandle['department']) == 1) {
                        $item->dep_id = array_shift($paramsHandle['department']);
                    } elseif (isset($paramsHandle['department']) && is_numeric($paramsHandle['department'])) {
                        $item->dep_id = (int)$paramsHandle['department'];
                    }

                } else {
                    $item = new erLhcoreClassModelChatOnlineUser();
                    $item->ip = isset($paramsHandle['ip']) ? $paramsHandle['ip'] : erLhcoreClassIPDetect::getIP();
                    $item->vid = $paramsHandle['vid'];
                    $item->identifier = (isset($paramsHandle['identifier']) && !empty($paramsHandle['identifier'])) ? $paramsHandle['identifier'] : '';
                    $item->referrer = isset($_GET['r']) ? rawurldecode($_GET['r']) : '';
                    $item->total_visits = 1;

                    if (isset($paramsHandle['department']) && is_array($paramsHandle['department']) && count($paramsHandle['department']) == 1) {
                        $item->dep_id = array_shift($paramsHandle['department']);
                    } elseif (isset($paramsHandle['department']) && is_numeric($paramsHandle['department'])) {
                        $item->dep_id = (int)$paramsHandle['department'];
                    }

                    if (isset($paramsHandle['tz']) && is_numeric($paramsHandle['tz'])) {
                        $timezone_name = timezone_name_from_abbr(null, (int)$paramsHandle['tz'] * 3600, true);
                        if ($timezone_name !== false) {
                            $item->visitor_tz = $timezone_name;
                        }
                    }

                    self::detectLocation($item);

                    // Cleanup database then new user comes
                    self::cleanupOnlineUsers();

                    $item->store_chat = true;

                    $newVisitor = true;
                }
            } else {
                self::cleanupOnlineUsers();
                return false;
            }
            
            $ip = isset($paramsHandle['ip']) ? $paramsHandle['ip'] : erLhcoreClassIPDetect::getIP();
            
            if ($item->ip != $ip) {
                $item->ip = $ip;
                self::detectLocation($item);
                $item->store_chat = true;
            }
            
            if (isset($_POST['onattr']) && !empty($_POST['onattr']) && $item->online_attr != $_POST['onattr']) {
            	$item->online_attr = $_POST['onattr'];
            	$item->store_chat = true;
            }
            	
            if (isset($paramsHandle['pages_count']) && $paramsHandle['pages_count'] == true) {
                $item->pages_count++;
                $item->tt_pages_count++;
                $item->store_chat = true;

                if (isset($_GET['onattr']) && is_array($_GET['onattr']) && !(empty($_GET['onattr']))) {
                    $item->online_attr = json_encode($_GET['onattr']);
                }

                if ($item->has_message_from_operator == true) {
                    $item->invitation_seen_count++;
                }

                if (isset($paramsHandle['tz']) && is_numeric($paramsHandle['tz']) && $item->visitor_tz == '') {
                    $timezone_name = timezone_name_from_abbr(null, (int)$paramsHandle['tz'] * 3600, true);
                    if ($timezone_name !== false) {
                        $item->visitor_tz = $timezone_name;
                    }
                }

                // Hide invitation message after n times if required
                if ($item->has_message_from_operator == true && $item->invitation !== false && $item->invitation->hide_after_ntimes > 0 && $item->invitation_seen_count > $item->invitation->hide_after_ntimes) {
                    $item->message_seen = 1;
                    $item->message_seen_ts = time();
                }
            }

            $logPageView = false;

            // Update variables only if it's not JS to check for operator message
            if (!isset($paramsHandle['check_message_operator']) || (isset($paramsHandle['pages_count']) && $paramsHandle['pages_count'] == true)) {
                $item->user_agent = isset($_POST['ua']) ? $_POST['ua'] : (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
                $item->current_page = isset($_POST['l']) ? $_POST['l'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
                $item->page_title = isset($_POST['dt']) ? $_POST['dt'] : (isset($_GET['dt']) ? (string)rawurldecode($_GET['dt']) : '');
                $item->last_visit = time();
                $item->store_chat = true;
                $logPageView = true;
            }



            if ((!isset($paramsHandle['wopen']) || $paramsHandle['wopen'] == 0) && $item->operator_message == '' && isset($paramsHandle['pro_active_invite']) && $paramsHandle['pro_active_invite'] == 1 && isset($paramsHandle['pro_active_limitation']) && ($paramsHandle['pro_active_limitation'] == -1 || erLhcoreClassChat::getPendingChatsCountPublic($item->dep_id > 0 ? $item->dep_id : false) <= $paramsHandle['pro_active_limitation'])) {
                $errors = array();
                erLhcoreClassChatEventDispatcher::getInstance()->dispatch('onlineuser.before_proactive_triggered', array('ou' => & $item, 'errors' => & $errors));

                if (empty($errors)) {
                    //Process pro active chat invitation if this visitor matches any rules
                    erLhAbstractModelProactiveChatInvitation::processProActiveInvitation($item, array('tag' => isset($paramsHandle['tag']) ? $paramsHandle['tag'] : ''));
                }
            }

            $activityChanged = false;
            if (isset($paramsHandle['uactiv'])) {
                $activityChanged = $item->user_active != (int)$paramsHandle['uactiv'] && $newVisitor == false;
                $item->user_active = (int)$paramsHandle['uactiv'];
            }

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('onlineuser.before_store_chat',
                array('new_visitor' => $newVisitor, 'log_page_view' => $logPageView, 'activity_changed' => $activityChanged, 'online_user' => $item, 'errors' => array()));

            // Save only then we have to, in general only then page view appears
            if ($item->store_chat == true) {
                $item->last_check_time = time();
                $item->saveThis();

                if ($newVisitor == true) {
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('onlineuser.created', array('tpl' => (isset($paramsHandle['tpl']) ? $paramsHandle['tpl'] : false), 'ou' => & $item));
                } elseif ($logPageView == true) {
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('onlineuser.pageview_logged', array('tpl' => (isset($paramsHandle['tpl']) ? $paramsHandle['tpl'] : false), 'ou' => & $item));
                }

                if ($activityChanged == true && $item->chat_id > 0) {
                    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.data_changed_chat', array('chat_id' => $item->chat_id));
                }
            }

            erLhcoreClassChatEventDispatcher::getInstance()->dispatch('onlineuser.handle_request', array('online_user' => $item, 'params' => $paramsHandle));

            return $item;
        } else {
            // throw new Exception('Invalid HTTP_USER_AGENT!');
            // Stop execution on google bot
            exit;
        }

    }

    public function saveThis()
    {

        if ($this->first_visit == 0) {
            $this->first_visit = time();
        }

        if ($this->last_visit == 0) {
            $this->last_visit = time();
        }

        erLhcoreClassChat::getSession()->saveOrUpdate($this);
    }

    public $id = null;
    public $ip = '';
    public $vid = '';
    public $current_page = '';
    public $user_agent = '';
    public $chat_id = 0;
    public $last_visit = 0;
    public $first_visit = 0;
    public $user_country_name = '';
    public $user_country_code = '';
    public $operator_message = '';
    public $identifier = '';
    public $operator_user_id = 0;
    public $operator_user_proactive = '';
    public $message_seen = 0;
    public $message_seen_ts = 0;
    public $pages_count = 0;
    public $tt_pages_count = 0;
    public $lat = 0;
    public $lon = 0;
    public $invitation_id = 0;
    public $city = '';
    public $time_on_site = 0;
    public $tt_time_on_site = 0;
    public $referrer = '';
    public $page_title = '';
    public $total_visits = 0;
    public $invitation_count = 0;
    public $requires_email = 0;
    public $requires_username = 0;
    public $dep_id = 0;
    public $invitation_seen_count = 0;
    public $screenshot_id = 0;
    public $operation = '';
    public $operation_chat = '';
    public $online_attr = '';
    public $online_attr_system = '';
    public $visitor_tz = '';
    public $notes = '';
    public $requires_phone = 0;
    public $last_check_time = 0;
    public $user_active = 0;
    public $show_on_mobile = 0;

    // 0 - do not reopen
    // 1 - reopen chat
    public $reopen_chat = 0;

    // Logical attributes
    public $store_chat = false;

    // Invitation assigned
    public $invitation_assigned = false;

}

?>