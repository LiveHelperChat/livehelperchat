<?php
/**
 * File containing the ezcFeedCategoryElement class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class defining a category.
 *
 * @property string $term
 *                  The readable value of the category.
 * @property string $scheme
 *                  The scheme (domain) value of the category.
 * @property string $label
 *                  The label value of the category.
 * @property ezcFeedCategoryElement $category
 *                                  A subcategory of the category.
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedCategoryElement extends ezcFeedElement
{
    /**
     * The term (name) of the category.
     *
     * @var string
     */
    public $term;

    /**
     * The scheme (domain) for the category.
     *
     * @var string
     */
    public $scheme;

    /**
     * The label for the category.
     *
     * @var string
     */
    public $label;

    /**
     * Subcategory for the category.
     *
     * @var ezcFeedCategoryElement
     */
    public $category;

    /**
     * Adds a new element with name $name to the feed item and returns it.
     *
     * The subcategory is only used by the iTunes module (ezcFeedITunesModule).
     *
     * Example:
     * <code>
     * // $feed is an ezcFeed object
     * $category = $feed->add( 'category' );
     * $category->term = 'Technology';
     * $subCategory = $category->add( 'category' );
     * $subCategory->term = 'Gadgets';
     * </code>
     *
     * @param string $name The name of the element to add
     * @return ezcFeedCategoryElement
     */
    public function add( $name )
    {
        if ( $name === 'category' )
        {
            $this->category = new ezcFeedCategoryElement();
            return $this->category;
        }
        else
        {
            return parent::add( $name );
        }
    }
}
?>
