<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "lh_abstract_browse_offer_invitation";
$def->class = "erLhAbstractModelBrowseOfferInvitation";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

// time_on_site - It's iddle time on some page. Not total time on site.
foreach (['siteaccess','time_on_site','content','callback_content','custom_iframe_url','name','identifier','url','referrer','hash','unit'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;
}

foreach (['lhc_iframe_content','executed_times','active','has_url','is_wildcard','priority','width','height','created_at','updated_at','muser_id'] as $posAttr) {
    $def->properties[$posAttr] = new ezcPersistentObjectProperty();
    $def->properties[$posAttr]->columnName   = $posAttr;
    $def->properties[$posAttr]->propertyName = $posAttr;
    $def->properties[$posAttr]->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;
}

return $def;

?>