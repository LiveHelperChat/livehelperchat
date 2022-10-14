<?php

namespace LiveHelperChat\Validators;

use LiveHelperChat\Models\Statistic\SavedReport;

class ReportValidator {

    public static function validateReport(SavedReport & $search, $params) {
        $definition = array(
            'name' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'description' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
            ),
            'days' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
            ),
            'days_end' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0)
            ),
            'position' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int'
            ),
            'timefrom_hours' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0,'max_range' => 23)
            ),
            'timefrom_minutes' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0,'max_range' => 59)
            ),
            'timefrom_seconds' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0,'max_range' => 59)
            ),
            'timeto_hours' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 23)
            ),
            'timeto_minutes' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 59)
            ),
            'timeto_seconds' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 0, 'max_range' => 59)
            ),
            'date_type' => new \ezcInputFormDefinitionElement(
                \ezcInputFormDefinitionElement::OPTIONAL, 'string'
            )
        );

        $form = new \ezcInputForm( INPUT_POST, $definition );
        $Errors = array();

        if ( !$form->hasValidData( 'name' ) || $form->name == '' ) {
            $Errors[] = \erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Please enter a name');
        } else {
            $search->name = $form->name;
        }

        if ( $form->hasValidData( 'description' ) ) {
            $search->description = $form->description;
        }

        if ($form->hasValidData( 'position' )) {
            $search->position = $form->position;
        } else {
            $search->position = 0;
        }

        if ($form->hasValidData( 'days' )) {
            $search->days = $form->days;
        } else {
            $search->days = 0;
        }

        if ($form->hasValidData( 'days_end' )) {
            $search->days_end = $form->days_end;
        } else {
            $search->days_end = 0;
        }

        if ($form->hasValidData( 'date_type' )) {
            $search->date_type = $form->date_type;
        } else {
            $search->date_type = 'ndays';
        }

        if ($form->hasValidData( 'timefrom_hours' )) {
            $params['input_form']->timefrom_hours = $form->timefrom_hours;
        } else {
            $params['input_form']->timefrom_hours = '';
        }

        if ($form->hasValidData( 'timefrom_minutes' )) {
            $params['input_form']->timefrom_minutes = $form->timefrom_minutes;
        } else {
            $params['input_form']->timefrom_minutes = '';
        }

        if ($form->hasValidData( 'timefrom_seconds' )) {
            $params['input_form']->timefrom_seconds = $form->timefrom_seconds;
        } else {
            $params['input_form']->timefrom_seconds = '';
        }

        if ($form->hasValidData( 'timeto_hours' )) {
            $params['input_form']->timeto_hours = $form->timeto_hours;
        } else {
            $params['input_form']->timeto_hours = '';
        }

        if ($form->hasValidData( 'timeto_minutes' )) {
            $params['input_form']->timeto_minutes = $form->timeto_minutes;
        } else {
            $params['input_form']->timeto_minutes = '';
        }

        if ($form->hasValidData( 'timeto_seconds' )) {
            $params['input_form']->timeto_seconds = $form->timeto_seconds;
        } else {
            $params['input_form']->timeto_seconds = '';
        }

        $search->params = json_encode($params);

        return $Errors;
    }

}