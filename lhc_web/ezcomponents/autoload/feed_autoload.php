<?php
/**
 * Autoloader definition for the Feed component.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.3
 * @filesource
 * @package Feed
 */

return array(
    'ezcFeedException'                           => 'Feed/exceptions/exception.php',
    'ezcFeedAtLeastOneItemDataRequiredException' => 'Feed/exceptions/one_item_data_required.php',
    'ezcFeedOnlyOneValueAllowedException'        => 'Feed/exceptions/only_one_value_allowed.php',
    'ezcFeedParseErrorException'                 => 'Feed/exceptions/parse_error.php',
    'ezcFeedRequiredMetaDataMissingException'    => 'Feed/exceptions/meta_data_missing.php',
    'ezcFeedUndefinedModuleException'            => 'Feed/exceptions/undefined_module.php',
    'ezcFeedUnsupportedElementException'         => 'Feed/exceptions/unsupported_element.php',
    'ezcFeedUnsupportedModuleException'          => 'Feed/exceptions/unsupported_module.php',
    'ezcFeedUnsupportedTypeException'            => 'Feed/exceptions/unsupported_type.php',
    'ezcFeedElement'                             => 'Feed/interfaces/element.php',
    'ezcFeedModule'                              => 'Feed/interfaces/module.php',
    'ezcFeedParser'                              => 'Feed/interfaces/parser.php',
    'ezcFeedProcessor'                           => 'Feed/interfaces/processor.php',
    'ezcFeedTextElement'                         => 'Feed/structs/text.php',
    'ezcFeed'                                    => 'Feed/feed.php',
    'ezcFeedAtom'                                => 'Feed/processors/atom.php',
    'ezcFeedCategoryElement'                     => 'Feed/structs/category.php',
    'ezcFeedCloudElement'                        => 'Feed/structs/cloud.php',
    'ezcFeedContentElement'                      => 'Feed/structs/content.php',
    'ezcFeedContentModule'                       => 'Feed/modules/content_module.php',
    'ezcFeedCreativeCommonsModule'               => 'Feed/modules/creativecommons_module.php',
    'ezcFeedDateElement'                         => 'Feed/structs/date.php',
    'ezcFeedDublinCoreModule'                    => 'Feed/modules/dublincore_module.php',
    'ezcFeedEnclosureElement'                    => 'Feed/structs/enclosure.php',
    'ezcFeedEntryElement'                        => 'Feed/structs/entry.php',
    'ezcFeedGeneratorElement'                    => 'Feed/structs/generator.php',
    'ezcFeedGeoModule'                           => 'Feed/modules/geo_module.php',
    'ezcFeedITunesModule'                        => 'Feed/modules/itunes_module.php',
    'ezcFeedIdElement'                           => 'Feed/structs/id.php',
    'ezcFeedImageElement'                        => 'Feed/structs/image.php',
    'ezcFeedLinkElement'                         => 'Feed/structs/link.php',
    'ezcFeedPersonElement'                       => 'Feed/structs/person.php',
    'ezcFeedRss1'                                => 'Feed/processors/rss1.php',
    'ezcFeedRss2'                                => 'Feed/processors/rss2.php',
    'ezcFeedSkipDaysElement'                     => 'Feed/structs/skip_days.php',
    'ezcFeedSkipHoursElement'                    => 'Feed/structs/skip_hours.php',
    'ezcFeedSourceElement'                       => 'Feed/structs/source.php',
    'ezcFeedTextInputElement'                    => 'Feed/structs/text_input.php',
);
?>
