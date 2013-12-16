<?php

class erLhcoreClassInputForm extends ezcInputForm {
    
    var $inputData = null;
    
    public function __construct( $inputSource, $definition, $characterEncoding = null, $inputData = null, $useOverride = false)
    {                
        if ( ( $returnValue = ezcInputForm::validateDefinition( $definition ) ) !== true )
        {
            throw new ezcInputFormInvalidDefinitionException( $returnValue[1] );
        }

        $this->definition = $definition;
        $this->inputSource = $inputSource;
        $this->inputData   = $inputData;
       
        if ( $inputData === null || count($inputData) == 0 )
            $this->parseInput();
        else 
            $this->parseInputFromData($useOverride);
    }
    
    private function parseInputFromData($useOverride)
    {   
        $this->allElementsValid = true;

        foreach ( $this->definition as $elementName => $inputElement )
        {
            $hasVariable = isset($this->inputData[$elementName]) || ($useOverride == true && isset($_GET[$elementName]));
            if ( ! $hasVariable )
            {
                if ( $inputElement->type === ezcInputFormDefinitionElement::REQUIRED )
                {
                    throw new ezcInputFormVariableMissingException( $elementName );
                }
                else
                {
                    $this->properties[$elementName] = ezcInputForm::INVALID;
                    $this->allElementsValid = false;
                    continue;
                }
            }

            if ( $useOverride == true && isset($_GET[$elementName])) {
                $flags = FILTER_NULL_ON_FAILURE | $inputElement->flags;
                $value = filter_var( isset($_GET[$elementName]) ? $_GET[$elementName] : null, filter_id( $inputElement->filterName ), array( 'options' => $inputElement->options, 'flags' => $flags ) );
                
            } else {
                $flags = FILTER_NULL_ON_FAILURE | $inputElement->flags;
                $value = filter_var(  $this->inputData[$elementName], filter_id( $inputElement->filterName ), array( 'options' => $inputElement->options, 'flags' => $flags ) );
            }

            if ( $value !== null )
            {
                $this->properties[$elementName] = ezcInputForm::VALID;
                $this->propertyValues[$elementName] = $value;
            } else {
                $this->properties[$elementName] = ezcInputForm::INVALID;
                $this->allElementsValid = false;
            }
        }
    }
    
}