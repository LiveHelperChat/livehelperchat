<?php

namespace LiveHelperChat\Helpers\Bot;

class IndividualizationValidator
{
    /**
     * Validates individualization (translation) item from submitted form data
     *
     * @param \erLhcoreClassModelGenericBotTrItem $trItem
     *
     * @return array Errors
     */
    public static function validate(& $trItem)
    {
        $definition = array(
            'identifier' => new \ezcInputFormDefinitionElement(\ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'),
            'translation' => new \ezcInputFormDefinitionElement(\ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',null,FILTER_REQUIRE_ARRAY ),
            'default_message' => new \ezcInputFormDefinitionElement(\ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' ),
            'message_item' => new \ezcInputFormDefinitionElement(\ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',null,FILTER_REQUIRE_ARRAY ),
            'languages' => new \ezcInputFormDefinitionElement(\ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw',null,FILTER_REQUIRE_ARRAY),
            'group_id' => new \ezcInputFormDefinitionElement(\ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)),
            'auto_translate' => new \ezcInputFormDefinitionElement(\ezcInputFormDefinitionElement::OPTIONAL, 'boolean')
        );

        $form = new \ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( !$form->hasValidData( 'identifier' ) || $form->identifier == '' ) {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter translation group name!');
        } else {
            $trItem->identifier = $form->identifier;
        }

        $data = array('default' => '', 'items' => array());

        $languagesData = array();
        if ( $form->hasValidData( 'languages' ) && !empty($form->languages) )
        {
            foreach ($form->languages as $index => $languages) {
                $languagesData[] = array(
                    'languages' => $form->languages[$index],
                    'message' => $form->message_item[$index]
                );
            }
        }

        $data['items'] = $languagesData;

        if ( $form->hasValidData( 'default_message' ) ) {
            $data['default'] = $form->default_message;
        }

        $trItem->translation_array = $data;
        $trItem->translation = json_encode($data);

        if ( $form->hasValidData( 'group_id' ) ) {
            $trItem->group_id = $form->group_id;
        } else {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please choose a group!');
        }

        if ( $form->hasValidData( 'auto_translate' ) ) {
            $trItem->auto_translate = 1;
        } else {
            $trItem->auto_translate = 0;
        }

        return $Errors;
    }

    /**
     * Copies individualization item to other individualization groups based on submitted form data
     *
     * @param \erLhcoreClassModelGenericBotTrItem $trItem
     */
    public static function copyToGroupsFromRequest(& $trItem)
    {
        $definition = array(
            'copy_mode' => new \ezcInputFormDefinitionElement(\ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'),
            'copy_group_ids' => new \ezcInputFormDefinitionElement(\ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1), FILTER_REQUIRE_ARRAY)
        );

        $form = new \ezcInputForm( INPUT_POST, $definition );

        if ( $form->hasValidData( 'copy_mode' ) && in_array($form->copy_mode, array('create','update'))
            && $form->hasValidData( 'copy_group_ids' ) && !empty($form->copy_group_ids) ) {
            self::copyToGroups($trItem, $form->copy_group_ids, $form->copy_mode == 'update');
        }
    }

    /**
     * Copies individualization item to other individualization groups
     *
     * @param \erLhcoreClassModelGenericBotTrItem $trItem Item which should be copied
     * @param array $groupIds Individualization groups ID's
     * @param bool $updateExisting true - update existing item, false - create only if it does not exist
     */
    public static function copyToGroups($trItem, $groupIds, $updateExisting = false)
    {
        foreach (array_unique(array_map('intval', (array)$groupIds)) as $groupId) {

            if ($groupId <= 0 || $groupId == $trItem->group_id) {
                continue;
            }

            $trItemExisting = \erLhcoreClassModelGenericBotTrItem::findOne(array(
                'filter' => array(
                    'group_id' => $groupId,
                    'identifier' => $trItem->identifier,
                )
            ));

            if ($trItemExisting !== false) {
                if ($updateExisting == true) {
                    $trItemExisting->translation = $trItem->translation;
                    $trItemExisting->auto_translate = $trItem->auto_translate;
                    $trItemExisting->updateThis();
                }
                continue;
            }

            $trItemCopy = new \erLhcoreClassModelGenericBotTrItem();
            $trItemCopy->group_id = $groupId;
            $trItemCopy->identifier = $trItem->identifier;
            $trItemCopy->translation = $trItem->translation;
            $trItemCopy->auto_translate = $trItem->auto_translate;
            $trItemCopy->saveThis();
        }
    }
}