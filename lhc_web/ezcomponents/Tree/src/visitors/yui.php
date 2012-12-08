<?php
/**
 * File containing the ezcTreeVisitorYUI class.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 */

/**
 * An implementation of the ezcTreeVisitor interface that generates
 * an XHTML representatation of a tree structure, as YUI wants.
 * See {@link http://developer.yahoo.com/yui/menu}.
 *
 * <code>
 * <?php
 *     $visitor = new ezcTreeVisitorYUI( 'menu' );
 *     $tree->accept( $visitor );
 *     echo (string) $visitor; // print the plot
 * ?>
 * </code>
 *
 * @package Tree
 * @version 1.1.4
 */
class ezcTreeVisitorYUI implements ezcTreeVisitor
{
    /**
     * Holds all the edges of the graph.
     *
     * @var array(string=>array(string))
     */
    protected $edges = array();

    /**
     * Holds the root ID.
     *
     * @var string
     */
    protected $root = null;

    /**
     * Holds the XML ID.
     *
     * @var string
     */
    protected $xmlId;

    /**
     * Holds the XHTML class.
     *
     * @var string
     */
    protected $class;

    /**
     * Whether the XML ID has been set.
     *
     * @var bool
     */
    private $treeIdSet;

    /**
     * Holds the options for this class
     *
     * @var ezcTreeVisitorYUIOptions
     */
    public $options;

    /**
     * Constructs a new ezcTreeVisitorYUI visualizer.
     *
     * @param string $xmlId
     * @param ezcTreeVisitorYUIOptions $options
     */
    public function __construct( $xmlId, ezcTreeVisitorYUIOptions $options = null )
    {
        if ( !is_string( $xmlId ) || strlen( $xmlId ) === 0 )
        {
            throw new ezcBaseValueException( 'xmlId', $xmlId, 'non-empty string' );
        }
        $this->xmlId = $xmlId;
        if ( $options === null )
        {
            $this->options = new ezcTreeVisitorYUIOptions;
        }
        else
        {
            $this->options = $options;
        }
    }

    /**
     * Formats a node's data.
     *
     * It is just a simple method, that provide an easy way to change the way
     * on how data is formatted when this class is extended. The data is passed
     * in the $data argument, and whether the node should be highlighted is
     * passed in the $highlight argument.
     *
     * @param mixed $data
     * @param bool  $highlight
     * @return string
     */
    protected function formatData( $data, $highlight )
    {
        $data = htmlspecialchars( $data );
        return $data;
    }

    /**
     * Visits the node and sets the the member variables according to the node
     * type and contents.
     *
     * @param ezcTreeVisitable $visitable
     * @return bool
     */
    public function visit( ezcTreeVisitable $visitable )
    {
        if ( $visitable instanceof ezcTreeNode )
        {
            if ( $this->root === null )
            {
                $this->root = $visitable->id;
            }

            $parent = $visitable->fetchParent();
            if ( $parent )
            {
                $this->edges[$parent->id][] = array( $visitable->id, $visitable->data, $visitable->fetchPath() );
            }
        }

        return true;
    }

    /**
     * Loops over the children of the node with ID $id.
     *
     * This methods loops over all the node's children and adds the correct
     * layout for each node depending on the state that is collected in the
     * $level and $levelLast variables.
     *
     * @param string $id
     * @param int    $level
     * @param array(int=>bool) $levelLast
     *
     * @return string
     */
    protected function doChildren( $id, $level = 0, $levelLast = array() )
    {
        $text = '';

        $children = $this->edges[$id];
        $numChildren = count( $children );

        if ( $numChildren > 0 )
        {
            $text .= str_repeat( '  ', $level + 1 );

            $idPart = '';
            if ( $level !== 0 )
            {
                $text .= "<div id='{$id}' class='yuimenu'>\n";
            }
            $text .= str_repeat( '  ', $level + 2 );
            $text .= "<div class='bd'>\n";
            $text .= str_repeat( '  ', $level + 3 );
            $text .= "<ul>\n";
            foreach ( $children as $child )
            {
                $path = $child[2]->nodes;
                if ( !$this->options->displayRootNode )
                {
                    array_shift( $path );
                }
                if ( $this->options->selectedNodeLink )
                {
                    $slice = array_slice( $path, -1 );
                    $path = htmlspecialchars( $this->options->basePath . '/' . array_pop( $slice ), ENT_QUOTES );
                }
                else
                {
                    $path = htmlspecialchars( $this->options->basePath . '/' . join( '/', $path ), ENT_QUOTES );
                }
                $text .= str_repeat( '  ', $level + 4 );

                $data = $this->formatData( $child[1], in_array( $child[0], $this->options->highlightNodeIds ) );

                $yuiItemClass =      $level == 0 ? 'yuimenubaritem' : 'yuimenuitem';
                $yuiItemLabelClass = $level == 0 ? 'yuimenubaritemlabel' : 'yuimenuitemlabel';

                $highlightPart = '';
                if ( in_array( $child[0], $this->options->highlightNodeIds ) )
                {
                    $highlightPart = ' highlight';
                }

                $linkStart = "<a class='{$yuiItemLabelClass}{$highlightPart}' href='{$path}'>";
                $linkEnd   = "</a>";

                if ( isset( $this->edges[$child[0]] ) )
                {
                    $text .= "<li class='{$yuiItemClass}'>{$linkStart}{$data}{$linkEnd}\n";
                    $text .= $this->doChildren( $child[0], $level + 4, $levelLast );
                    $text .= str_repeat( '  ', $level + 4 );
                    $text .= "</li>\n";
                }
                else
                {
                    $text .= "<li class='{$yuiItemClass}'>{$linkStart}{$data}{$linkEnd}</li>\n";
                }
            }
            $text .= str_repeat( '  ', $level + 3 );
            $text .= "</ul>\n";
            $text .= str_repeat( '  ', $level + 2 );
            if ( $level !== 0 )
            {
                $text .= "</div>\n";
                $text .= str_repeat( '  ', $level + 1 );
            }
            $text .= "</div>\n";
        }

        return $text;
    }

    /**
     * Returns the XHTML representation of a tree.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        $tree = '';
        $this->treeIdSet = false;

        $idPart = " id=\"{$this->xmlId}\"";
        $tree .= "<div{$idPart} class='yuimenubar yuimenubarnav'>\n";
        if ( $this->options->displayRootNode )
        {
            $tree .= <<<END
  <div class='bd'>
    <ul>
      <li class='yuimenubaritem'><a class='yuimenubaritemlabel' href='/Hominoidea/Hylobatidae'>{$this->root}</a>

END;
        }
        $tree .= $this->doChildren( $this->root, 4 * (bool) $this->options->displayRootNode );
        if ( $this->options->displayRootNode )
        {
            $tree .= <<<END
      </li>
    </ul>
  </div>

END;
        }
        $tree .= "</div>\n";
        return $tree;
    }
}
?>
