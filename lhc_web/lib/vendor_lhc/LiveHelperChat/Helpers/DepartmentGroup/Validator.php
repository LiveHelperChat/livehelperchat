<?php

namespace LiveHelperChat\Helpers\DepartmentGroup;

class Validator
{
    /**
     * Validates department group submit
     *
     * @param \erLhcoreClassModelDepartamentGroup $departamentGroup
     * @return array
     */
    public static function validateGroup(\erLhcoreClassModelDepartamentGroup $departamentGroup)
    {
        $definition = array(
            'Name' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            )
        );

        $form = new \ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( !$form->hasValidData( 'Name' ) || $form->Name == '' ) {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('departament/editgroup','Please enter a department group name');
        } else {
            $departamentGroup->name = $form->Name;
        }

        return $Errors;
    }

    /**
     * Validates department limit group submit
     *
     * @param \erLhcoreClassModelDepartamentLimitGroup $departamentGroup
     * @return array
     */
    public static function validateLimitGroup(\erLhcoreClassModelDepartamentLimitGroup $departamentGroup)
    {
        $definition = array(
            'Name' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'PendingMax' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int'
            )
        );

        $form = new \ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( !$form->hasValidData( 'Name' ) || $form->Name == '' ) {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('departament/editgroup','Please enter a department group name');
        } else {
            $departamentGroup->name = $form->Name;
        }

        if ( $form->hasValidData( 'PendingMax' )) {
            $departamentGroup->pending_max = $form->PendingMax;
        } else {
            $departamentGroup->pending_max = 0;
        }

        return $Errors;
    }

    /**
     * Validates and stores departments assigned to a department group
     *
     * @param \erLhcoreClassModelDepartamentGroup $departamentGroup
     * @return array
     */
    public static function validateGroupDepartments(\erLhcoreClassModelDepartamentGroup $departamentGroup)
    {
        $definition = array(
            'departaments' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
            ));

        $form = new \ezcInputForm( INPUT_POST, $definition );

        if ( $form->hasValidData( 'departaments' ) && !empty($form->departaments)) {
            self::assignDepartmentsToGroup($departamentGroup, $form->departaments);
        } else {
            self::assignDepartmentsToGroup($departamentGroup, array());
        }

        return array();
    }

    /**
     * Validates and stores departments assigned to a pending chats limit group
     *
     * @param \erLhcoreClassModelDepartamentLimitGroup $departamentGroup
     * @return array
     */
    public static function validateGroupLimitDepartments(\erLhcoreClassModelDepartamentLimitGroup $departamentGroup)
    {
        $definition = array(
            'departaments' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null, FILTER_REQUIRE_ARRAY
            ));

        $form = new \ezcInputForm( INPUT_POST, $definition );

        if ( $form->hasValidData( 'departaments' ) && !empty($form->departaments)) {
            self::assignDepartmentsToLimitGroup($departamentGroup, $form->departaments);
        } else {
            self::assignDepartmentsToLimitGroup($departamentGroup, array());
        }

        return array();
    }

    /**
     * Assigns departments to a department group
     *
     * @param \erLhcoreClassModelDepartamentGroup $departamentGroup
     * @param array $ids
     */
    public static function assignDepartmentsToGroup(\erLhcoreClassModelDepartamentGroup $departamentGroup, $ids)
    {
        $members = \erLhcoreClassModelDepartamentGroupMember::getList(array('limit' => false,'filter' => array('dep_group_id' => $departamentGroup->id)));

        $oldMembers = array();

        // Remove old members
        foreach ($members as $member) {
            if (!in_array($member->dep_id, $ids)) {
                $member->removeThis();
            } else {
                $oldMembers[] = $member->dep_id;
            }
        }

        // Store new members
        foreach ($ids as $id) {
            if (!in_array($id, $oldMembers)) {
                $member = new \erLhcoreClassModelDepartamentGroupMember();
                $member->dep_id = $id;
                $member->dep_group_id = $departamentGroup->id;
                $member->saveThis();
            }
        }
    }

    /**
     * Assigns departments to a pending chats limit group
     *
     * @param \erLhcoreClassModelDepartamentLimitGroup $departamentGroup
     * @param array $ids
     */
    public static function assignDepartmentsToLimitGroup(\erLhcoreClassModelDepartamentLimitGroup $departamentGroup, $ids)
    {
        $members = \erLhcoreClassModelDepartamentLimitGroupMember::getList(array('limit' => false,'filter' => array('dep_limit_group_id' => $departamentGroup->id)));

        $oldMembers = array();

        // Remove old members
        foreach ($members as $member) {
            if (!in_array($member->dep_id, $ids)) {
                $member->removeThis();
            } else {
                $oldMembers[] = $member->dep_id;
            }
        }

        // Store new members
        foreach ($ids as $id) {
            if (!in_array($id, $oldMembers)) {
                $member = new \erLhcoreClassModelDepartamentLimitGroupMember();
                $member->dep_id = $id;
                $member->dep_limit_group_id = $departamentGroup->id;
                $member->saveThis();
            }
        }
    }
}