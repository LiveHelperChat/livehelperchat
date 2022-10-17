<?php

namespace LiveHelperChat\Models\Statistic;

class SavedReport {

    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_saved_report';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = 'position DESC, id DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'name' => $this->name,
            'params' => $this->params,
            'position' => $this->position,
            'user_id' => $this->user_id,
            'days' => $this->days,
            'days_end' => $this->days_end,
            'date_type' => $this->date_type,
            'updated_at' => $this->updated_at,
            'description' => $this->description,
        );

        return $stateArray;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getParamsURL()
    {
        $params = $this->params_array;

        if ($this->date_type == 'ndays') {
            $params['input_form']['timefrom'] = date('Y-m-d',time() - $this->days * 24 * 3600);
        } elseif ($this->date_type == 'lweek') {
            $params['input_form']['timefrom'] = date('Y-m-d',
                strtotime("this week") - (7 * $this->days * 24 * 3600)
            );
        } elseif ($this->date_type == 'lmonth') {
            $params['input_form']['timefrom'] = date('Y-m-d',mktime(0,0,0,date('m') - $this->days,1));
        }

        if ($this->days_end > 0) {
            if ($this->date_type == 'ndays') {
                $params['input_form']['timeto'] = date('Y-m-d',time() - $this->days_end * 24 * 3600);
            } elseif ($this->date_type == 'lweek') {
                $params['input_form']['timeto'] = date('Y-m-d',strtotime("this week") + (6 * 24 * 3600) - (7 * $this->days_end * 24 * 3600));
            } elseif ($this->date_type == 'lmonth') {
                $params['input_form']['timeto'] = date('Y-m-d',
                    mktime(0,0,0,date('m') - $this->days_end,
                        cal_days_in_month(CAL_GREGORIAN,
                            date('m',mktime(0,0,0,date('m') - $this->days_end,1)),
                            date('Y',mktime(0,0,0,date('m') - $this->days_end,1))
                        ))
                );
            }
        } elseif ($params['input_form']['timeto_hours'] != '' && is_numeric($params['input_form']['timeto_hours']) && $params['input_form']['timeto_hours'] >= 0) {
            $params['input_form']['timeto'] = date('Y-m-d');
        } else {
            $params['input_form']['timeto'] = null;
        }

        return $params;
    }

    public function generateURL()
    {
        $params = $this->getParamsURL();
        if (isset($params['input_form']['report'])) {
            unset($params['input_form']['report']);
        }

        //return \erLhcoreClassDesign::baseurl('statistic/statistic'). '/(report)/'. $this->id . (isset($this->params_array['tab']) ? '/(tab)/'.$this->params_array['tab'] : '') . \erLhcoreClassSearchHandler::getURLAppendFromInput($params['input_form']);
        return \erLhcoreClassDesign::baseurl('statistic/statistic'). '/(report)/'. $this->id . (isset($this->params_array['tab']) ? '/(tab)/'.$this->params_array['tab'] : '') .'?'.  http_build_query($params['input_form']).'&doSearch=on';
    }

    public function __get($var)
    {
        switch ($var) {

            case 'params_array':
                $jsonData = json_decode($this->params, true);
                if ($jsonData !== null) {
                    $this->params_array = $jsonData;
                } else {
                    $this->params_array = $this->params;
                }

                if (!is_array($this->params_array)) {
                    $this->params_array = array();
                }

                return $this->params_array;

            case 'user':
                $this->user = $this->user_id;
                return $this->user;

            case 'updated_ago':
                $this->updated_ago = \erLhcoreClassChat::formatSeconds(time() - $this->updated_at);
                return $this->updated_ago;

            default:
                ;
                break;
        }
    }

    public $id = null;
    public $name = '';
    public $params = '';
    public $date_type = 'ndays';
    public $days = 0;
    public $days_end = 0;
    public $user_id = 0;
    public $position = 0;
    public $updated_at = 0;
    public $description = '';
}