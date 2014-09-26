<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_browse_offer_invitation";
$def->class = "erLhAbstractModelBrowseOfferInvitation";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['siteaccess'] = new ezcPersistentObjectProperty();
$def->properties['siteaccess']->columnName   = 'siteaccess';
$def->properties['siteaccess']->propertyName = 'siteaccess';
$def->properties['siteaccess']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

/**
 * It's iddle time on some page. Not total time on site.
 * */
$def->properties['time_on_site'] = new ezcPersistentObjectProperty();
$def->properties['time_on_site']->columnName   = 'time_on_site';
$def->properties['time_on_site']->propertyName = 'time_on_site';
$def->properties['time_on_site']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['content'] = new ezcPersistentObjectProperty();
$def->properties['content']->columnName   = 'content';
$def->properties['content']->propertyName = 'content';
$def->properties['content']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['callback_content'] = new ezcPersistentObjectProperty();
$def->properties['callback_content']->columnName   = 'callback_content';
$def->properties['callback_content']->propertyName = 'callback_content';
$def->properties['callback_content']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['lhc_iframe_content'] = new ezcPersistentObjectProperty();
$def->properties['lhc_iframe_content']->columnName   = 'lhc_iframe_content';
$def->properties['lhc_iframe_content']->propertyName = 'lhc_iframe_content';
$def->properties['lhc_iframe_content']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['custom_iframe_url'] = new ezcPersistentObjectProperty();
$def->properties['custom_iframe_url']->columnName   = 'custom_iframe_url';
$def->properties['custom_iframe_url']->propertyName = 'custom_iframe_url';
$def->properties['custom_iframe_url']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['identifier'] = new ezcPersistentObjectProperty();
$def->properties['identifier']->columnName   = 'identifier';
$def->properties['identifier']->propertyName = 'identifier';
$def->properties['identifier']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['executed_times'] = new ezcPersistentObjectProperty();
$def->properties['executed_times']->columnName   = 'executed_times';
$def->properties['executed_times']->propertyName = 'executed_times';
$def->properties['executed_times']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['url'] = new ezcPersistentObjectProperty();
$def->properties['url']->columnName   = 'url';
$def->properties['url']->propertyName = 'url';
$def->properties['url']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['active'] = new ezcPersistentObjectProperty();
$def->properties['active']->columnName   = 'active';
$def->properties['active']->propertyName = 'active';
$def->properties['active']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['has_url'] = new ezcPersistentObjectProperty();
$def->properties['has_url']->columnName   = 'has_url';
$def->properties['has_url']->propertyName = 'has_url';
$def->properties['has_url']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['is_wildcard'] = new ezcPersistentObjectProperty();
$def->properties['is_wildcard']->columnName   = 'is_wildcard';
$def->properties['is_wildcard']->propertyName = 'is_wildcard';
$def->properties['is_wildcard']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['priority'] = new ezcPersistentObjectProperty();
$def->properties['priority']->columnName   = 'priority';
$def->properties['priority']->propertyName = 'priority';
$def->properties['priority']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

// Referrer
$def->properties['referrer'] = new ezcPersistentObjectProperty();
$def->properties['referrer']->columnName   = 'referrer';
$def->properties['referrer']->propertyName = 'referrer';
$def->properties['referrer']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

// Hash
$def->properties['hash'] = new ezcPersistentObjectProperty();
$def->properties['hash']->columnName   = 'hash';
$def->properties['hash']->propertyName = 'hash';
$def->properties['hash']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['width'] = new ezcPersistentObjectProperty();
$def->properties['width']->columnName   = 'width';
$def->properties['width']->propertyName = 'width';
$def->properties['width']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['height'] = new ezcPersistentObjectProperty();
$def->properties['height']->columnName   = 'height';
$def->properties['height']->propertyName = 'height';
$def->properties['height']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['unit'] = new ezcPersistentObjectProperty();
$def->properties['unit']->columnName   = 'unit';
$def->properties['unit']->propertyName = 'unit';
$def->properties['unit']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;


return $def;

?>