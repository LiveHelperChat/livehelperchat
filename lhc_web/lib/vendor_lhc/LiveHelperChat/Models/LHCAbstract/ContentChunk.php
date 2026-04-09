<?php

namespace LiveHelperChat\Models\LHCAbstract;
#[\AllowDynamicProperties]
class ContentChunk
{
    use \erLhcoreClassDBTrait;

    public static $dbTable = 'lh_abstract_content_chunk';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = '`name` ASC';

    public $has_filter = true;

    public $filter_name = 'contentchunk';

    public function getFilter($inputData = null)
    {
        $filter = [];
        $depIds = isset($inputData->dep_id) && is_array($inputData->dep_id) ? array_map('intval', $inputData->dep_id) : [];
        $depIds = array_filter($depIds, function($v) { return $v > 0; });
        if (!empty($depIds)) {
            $filter['innerjoin'] = ['lh_abstract_content_chunk_dep' => ['lh_abstract_content_chunk.id', 'lh_abstract_content_chunk_dep.chunk_id']];
            $filter['filterin'] = ['lh_abstract_content_chunk_dep.dep_id' => array_values($depIds)];
        }
        return $filter;
    }

    public function getState()
    {
        return array(
            'id'         => $this->id,
            'name'       => $this->name,
            'identifier' => $this->identifier,
            'content'    => $this->content,
            'in_active'  => $this->in_active,
        );
    }

    public function __toString()
    {
        return $this->name;
    }

    public function afterSave()
    {
        $db = \ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM `lh_abstract_content_chunk_dep` WHERE `chunk_id` = :chunk_id');
        $stmt->bindValue(':chunk_id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();

        if (isset($this->department_ids) && !empty($this->department_ids)) {
            $values = [];
            foreach ($this->department_ids as $department_id) {
                $values[] = "(" . (int)$this->id . "," . (int)$department_id . ")";
            }
            if (!empty($values)) {
                $db->query('INSERT INTO `lh_abstract_content_chunk_dep` (`chunk_id`,`dep_id`) VALUES ' . implode(',', $values));
            }
        }
    }

    public function updateThis()
    {
        $this->saveThis();
    }

    public function afterRemove()
    {
        $db = \ezcDbInstance::get();
        $stmt = $db->prepare('DELETE FROM `lh_abstract_content_chunk_dep` WHERE `chunk_id` = :chunk_id');
        $stmt->bindValue(':chunk_id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function customForm()
    {
        return 'contentchunk.tpl.php';
    }

    public function getFields()
    {
        return include('lib/core/lhabstract/fields/erlhabstractmodelcontentchunk.php');
    }

    public function getModuleTranslations()
    {
        return array(
            'permission_delete' => array('module' => 'lhabstract', 'function' => 'use'),
            'permission'        => array('module' => 'lhcannedmsg', 'function' => 'use_replace'),
            'name'              => \erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/contentchunk', 'Content Chunk'),
        );
    }

    public function __get($var)
    {
        switch ($var) {

            case 'department_ids_front':
                $this->department_ids_front = [];
                if ($this->id > 0) {
                    $db = \ezcDbInstance::get();
                    $stmt = $db->prepare("SELECT `dep_id` FROM `lh_abstract_content_chunk_dep` WHERE `chunk_id` = " . (int)$this->id);
                    $stmt->execute();
                    $this->department_ids_front = $stmt->fetchAll(\PDO::FETCH_COLUMN);
                }
                return $this->department_ids_front;

            default:
                break;
        }
    }

    public function validateInput($params)
    {
        $definition = array(
            'DepartmentID' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1), FILTER_REQUIRE_ARRAY
            ),
        );
        $form = new \ezcInputForm(INPUT_POST, $definition);
        if (!$form->hasValidData('DepartmentID')) {
            $this->department_ids = $this->department_ids_front = [];
        } else {
            $this->department_ids_front = $this->department_ids = $form->DepartmentID;
        }
    }

    public function dependJs()
    {
		return '<script type="text/javascript" src="'.\erLhcoreClassDesign::designJS('js/colorpicker.js;js/ace/ace.js').'"></script>';
	}


    public $id         = null;
    public $name       = '';
    public $identifier = '';
    public $content    = '';
    public $in_active  = 0;

    public $department_ids = [];
}
