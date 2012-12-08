<?php
/**
 * File containing the ezcTreeVisitorXHTML class.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 */

/**
 * An implementation of the ezcTreeVisitor interface that generates
 * an XHTML representatation of a tree structure.
 *
 * <code>
 * <?php
 *     $options = new ezcTreeVisitorXHTMLOptions;
 *     $options->xmlId = 'menu_tree';
 *     $visitor = new ezcTreeVisitorXHTML( $options );
 *     $tree->accept( $visitor );
 *     echo (string) $visitor; // print the plot
 * ?>
 * </code>
 *
 * Shows (something like):
 * <code>
 * </code>
 *
 * @package Tree
 * @version 1.1.4
 */
class ezcTreeVisitorXHTML implements ezcTreeVisitor
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
     * Whether the XML ID has been set.
     *
     * @var bool
     */
    private $treeIdSet;

    /**
     * Holds the options for this class
     *
     * @var ezcTreeVisitorXHTMLOptions
     */
    public $options;

    /**
     * Constructs a new ezcTreeVisitorXHTML visualizer.
     *
     * @param ezcTreeVisitorXHTMLOptions $options
     */
    public function __construct( ezcTreeVisitorXHTMLOptions $options = null )
    {
        if ( $options === null )
        {
            $this->options = new ezcTreeVisitorXHTMLOptions;
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
        return $highlight ? "<div class=\"highlight\">$data</div>" : $data;
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
        if ( $visitable instanceof ezcTree )
        {
        }

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
     * Formats the path to the node
     *
     * @param array $child
     */
    protected function formatPath( $child )
    {
        $path = $child[2]->nodes;
        if ( !$this->options->displayRootNode )
        {
            array_shift( $path );
        }
        if ( $this->options->selectedNodeLink )
        {
            $slice = array_slice( $path, -1 );
            $path = htmlspecialchars( $this->options->basePath . '/' . array_pop( $slice ) );
        }
        else
        {
            $path = htmlspecialchars( $this->options->basePath . '/' . join( '/', $path ) );
        }
        return $path;
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
            if ( !$this->treeIdSet )
            {
                $idPart = $this->options->xmlId ? " id=\"{$this->options->xmlId}\"" : '';
                $this->treeIdSet = true;
            }
            $text .= "<ul{$idPart}>\n";
            foreach ( $children as $child )
            {
                $text .= str_repeat( '  ', $level + 2 );

                $path = $this->formatPath( $child );
                $data = $this->formatData( $child[1], in_array( $child[0], $this->options->highlightNodeIds ) );

                $linkStart = $linkEnd = '';
                if ( $this->options->addLinks )
                {
                    $linkStart = "<a href=\"{$path}\">";
                    $linkEnd   = "</a>";
                }

                $highlightPart = '';
                if ( in_array( $child[0], $this->options->subtreeHighlightNodeIds ) )
                {
                    $highlightPart = ' class="highlight"';
                }

                if ( isset( $this->edges[$child[0]] ) )
                {
                    $text .= "<li{$highlightPart}>{$linkStart}{$data}{$linkEnd}\n";
                    $text .= $this->doChildren( $child[0], $level + 2, $levelLast );
                    $text .= str_repeat( '  ', $level + 2 );
                    $text .= "</li>\n";
                }
                else
                {
                    $text .= "<li{$highlightPart}>{$linkStart}{$data}{$linkEnd}</li>\n";
                }
            }
            $text .= str_repeat( '  ', $level + 1 );
            $text .= "</ul>\n";
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

        if ( $this->options->displayRootNode )
        {
            $idPart = $this->options->xmlId ? " id=\"{$this->options->xmlId}\"" : '';
            $tree .= "<ul{$idPart}>\n";
            $tree .= "<li>{$this->root}</li>\n";
            $this->treeIdSet = true;
        }
        $tree .= $this->doChildren( $this->root );
        if ( $this->options->displayRootNode )
        {
            $tree .= "</ul>\n";
        }
        return $tree;
    }
}
?>
