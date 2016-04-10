<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_admin_theme";
$def->class = "erLhAbstractModelAdminTheme";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Header custom html
$def->properties['header_content'] = new ezcPersistentObjectProperty();
$def->properties['header_content']->columnName   = 'header_content';
$def->properties['header_content']->propertyName = 'header_content';
$def->properties['header_content']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Custom header CSS entered manually, usefull during testing
$def->properties['header_css'] = new ezcPersistentObjectProperty();
$def->properties['header_css']->columnName   = 'header_css';
$def->properties['header_css']->propertyName = 'header_css';
$def->properties['header_css']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Images fonts etc, static content
$def->properties['static_content'] = new ezcPersistentObjectProperty();
$def->properties['static_content']->columnName   = 'static_content';
$def->properties['static_content']->propertyName = 'static_content';
$def->properties['static_content']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// JS
$def->properties['static_js_content'] = new ezcPersistentObjectProperty();
$def->properties['static_js_content']->columnName   = 'static_js_content';
$def->properties['static_js_content']->propertyName = 'static_js_content';
$def->properties['static_js_content']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// CSS
$def->properties['static_css_content'] = new ezcPersistentObjectProperty();
$def->properties['static_css_content']->columnName   = 'static_css_content';
$def->properties['static_css_content']->propertyName = 'static_css_content';
$def->properties['static_css_content']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

return $def;