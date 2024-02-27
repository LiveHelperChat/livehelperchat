<?php
#[\AllowDynamicProperties]
class erLhcoreClassModelMailconvResponseTemplate
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lhc_mailconv_response_template';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassMailconv::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'dep_id' => $this->dep_id,
            'template' => $this->template,
            'template_plain' => $this->template_plain,
            'unique_id' => $this->unique_id,
            'disabled' => $this->disabled
        );
    }

    public function __toString()
    {
        return $this->name;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'ctime_front':
                return date('Ymd') == date('Ymd', $this->ctime) ? date(erLhcoreClassModule::$dateHourFormat, $this->ctime) : date(erLhcoreClassModule::$dateDateHourFormat, $this->ctime);
                break;

            case 'template_html':
                return $this->template != '' ? $this->template : nl2br($this->template_plain);
                break;

            case 'subjects':
                $this->subjects = erLhcoreClassModelMailconvResponseTemplateSubject::getList(array('filter' => array('template_id' => $this->id)));
                return $this->subjects;

            case 'subject_name_front':
                $this->subject_name_front = [];
                foreach ($this->subjects as $subject) {
                    $this->subject_name_front[] = (string)$subject->subject;
                }
                return $this->subject_name_front;

            case 'department_ids_front':
                $this->department_ids_front = [];
                if ($this->id > 0) {
                    $db = ezcDbInstance::get();
                    $stmt = $db->prepare("SELECT `dep_id` FROM `lhc_mailconv_response_template_dep` WHERE `template_id` = " . $this->id);
                    $stmt->execute();
                    $this->department_ids_front = $stmt->fetchAll(PDO::FETCH_COLUMN);
                }
                return $this->department_ids_front;

            default:
                ;
                break;
        }
    }

    public function afterSave()
    {
        if ($this->unique_id == 0) {
            $this->unique_id = $this->id;
            $this->updateThis(array('update' => array('unique_id')));
        }

        $db = ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM `lhc_mailconv_response_template_dep` WHERE `template_id` = :template_id');
        $stmt->bindValue(':template_id', $this->id,PDO::PARAM_INT);
        $stmt->execute();

        if (isset($this->department_ids) && !empty($this->department_ids)) {
            $values = [];
            foreach ($this->department_ids as $department_id) {
                $values[] = "(" . $this->id . "," . $department_id . ")";
            }
            $db->query('INSERT INTO `lhc_mailconv_response_template_dep` (`template_id`,`dep_id`) VALUES ' . implode(',',$values));
        }
    }

    public function afterRemove()
    {
        $db = ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM `lhc_mailconv_response_template_dep` WHERE `template_id` = :template_id');
        $stmt->bindValue(':template_id', $this->id,PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $db->prepare('DELETE FROM `lhc_mailconv_response_template_subject` WHERE `template_id` = :template_id');
        $stmt->bindValue(':template_id', $this->id,PDO::PARAM_INT);
        $stmt->execute();
    }

    public $id = NULL;
    public $name = '';
    public $dep_id = 0;
    public $template = '';
    public $template_plain = '';
    public $department_ids = [];
    public $unique_id = '';
    public $disabled = 0;
}

?>