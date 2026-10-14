<?php

return array(
    'id' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/product','ID'),
        'required' => false,
        'width' => 1,
        'hide_edit' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'name' => array(
        'type' => 'text',
        'maxlength' => 250,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/offlinereason', 'Name'),
        'required' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'description' => array(
        'type' => 'textarea',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/offlinereason', 'Description'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'icon' => array(
        'type' => 'text',
        'maxlength' => 250,
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/offlinereason', 'Icon'),
        'required' => false,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )),
    'pos' => array(
        'type' => 'text',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/offlinereason', 'Position'),
        'required' => false,
        'default' => 0,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'mode' => array(
        'type' => 'combobox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/offlinereason', 'Select reason mode'),
        'required' => false,
        'hide_optional' => true,
        'name_attr' => 'name',
        'frontend' => 'mode_frontend',
        'source' => function () {
            $items = [];

            $item = new StdClass();
            $item->id = \LiveHelperChat\Models\LHCAbstract\OfflineReason::MODE_OFFLINE;
            $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/offlinereason', 'Offline Mode');
            $items[] = $item;

            $item = new StdClass();
            $item->id = \LiveHelperChat\Models\LHCAbstract\OfflineReason::MODE_ONLINE;
            $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/offlinereason', 'Online mode');
            $items[] = $item;

            return $items;
        },
        'params_call' => array(),
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
        )),
    'disable_auto_assign' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/offlinereason', 'Disable auto assigned (Online mode reasons only)'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
    'disable_transfer' => array(
        'type' => 'checkbox',
        'trans' => erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/offlinereason', 'Disable transferring chats/mails to me manually if I am in this mode (Online mode reasons only)'),
        'required' => false,
        'hidden' => true,
        'validation_definition' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        )),
);
