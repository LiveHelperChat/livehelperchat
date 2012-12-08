<?php
/**
 * File containing the ezcDocumentPcssStyleInferencer class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Style inferencer
 *
 * Inferences the style of a element, basing on the default styles for the
 * element and the given list of user defined style directives.
 *
 * This class is meant to return a list of styles for any element in the
 * Docbook document tree. To speed up the inferencing process styles for
 * elements with the same path are cached.
 *
 * The inferencing algorithm basically works like:
 *
 * 1) Apply the default styles to the element
 * 2) Inherit styles from the parent element
 * 3) Apply styles from all given style directives in their given order, so
 *    that rules defined later overwrite rules defined earlier.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPcssStyleInferencer
{
    /**
     * Style cache
     *
     * Caches styles for defined paths. This speeds up resolving of styles for
     * similar or same elements multiple times.
     *
     * @var array
     */
    protected $styleCache = array();

    /**
     * Ordered list of style directives
     *
     * Ordered list of style directvies, which each include the pattern, and a
     * list of formatting rules. Matching directives are applied in the given
     * order and may overwrite each other.
     *
     * @var array
     */
    protected $styleDirectives = array();

    /**
     * Special classes for style directive values
     *
     * If no class is given it will fall back to a generic string value.
     *
     * @var array
     */
    protected $valueParserClasses = array(
        'font-size'            => 'ezcDocumentPcssStyleMeasureValue',
        'line-height'          => 'ezcDocumentPcssStyleMeasureValue',
        'margin'               => 'ezcDocumentPcssStyleMeasureBoxValue',
        'margin-top'           => 'ezcDocumentPcssStyleMeasureValue',
        'margin-right'         => 'ezcDocumentPcssStyleMeasureValue',
        'margin-bottom'        => 'ezcDocumentPcssStyleMeasureValue',
        'margin-left'          => 'ezcDocumentPcssStyleMeasureValue',
        'padding'              => 'ezcDocumentPcssStyleMeasureBoxValue',
        'padding-top'          => 'ezcDocumentPcssStyleMeasureValue',
        'padding-right'        => 'ezcDocumentPcssStyleMeasureValue',
        'padding-bottom'       => 'ezcDocumentPcssStyleMeasureValue',
        'padding-left'         => 'ezcDocumentPcssStyleMeasureValue',
        'text-columns'         => 'ezcDocumentPcssStyleIntValue',
        'text-columns-spacing' => 'ezcDocumentPcssStyleMeasureValue',
        'text-decoration'      => 'ezcDocumentPcssStyleListValue',
        'color'                => 'ezcDocumentPcssStyleColorValue',
        'background-color'     => 'ezcDocumentPcssStyleColorValue',
        'border'               => 'ezcDocumentPcssStyleBorderBoxValue',
        'border-top'           => 'ezcDocumentPcssStyleBorderValue',
        'border-right'         => 'ezcDocumentPcssStyleBorderValue',
        'border-bottom'        => 'ezcDocumentPcssStyleBorderValue',
        'border-left'          => 'ezcDocumentPcssStyleBorderValue',
        'border-style'         => 'ezcDocumentPcssStyleLineBoxValue',
        'border-style-top'     => 'ezcDocumentPcssStyleLineValue',
        'border-style-right'   => 'ezcDocumentPcssStyleLineValue',
        'border-style-bottom'  => 'ezcDocumentPcssStyleLineValue',
        'border-style-left'    => 'ezcDocumentPcssStyleLineValue',
        'border-color'         => 'ezcDocumentPcssStyleColorBoxValue',
        'border-color-top'     => 'ezcDocumentPcssStyleColorValue',
        'border-color-right'   => 'ezcDocumentPcssStyleColorValue',
        'border-color-bottom'  => 'ezcDocumentPcssStyleColorValue',
        'border-color-left'    => 'ezcDocumentPcssStyleColorValue',
        'border-width'         => 'ezcDocumentPcssStyleMeasureBoxValue',
        'border-width-top'     => 'ezcDocumentPcssStyleMeasureValue',
        'border-width-right'   => 'ezcDocumentPcssStyleMeasureValue',
        'border-width-bottom'  => 'ezcDocumentPcssStyleMeasureValue',
        'border-width-left'    => 'ezcDocumentPcssStyleMeasureValue',
        'src'                  => 'ezcDocumentPcssStyleSrcValue',
    );

    /**
     * Text category of style directives
     */
    const TEXT   = 1;

    /**
     * Layout category of style directives
     */
    const LAYOUT = 2;

    /**
     * Page category of style directives
     */
    const PAGE   = 4;

    /**
     * CSS property categories, used to minimize the amount of returned
     * properties
     *
     * @var array
     */
    protected $categories = array(
        self::TEXT => array(
            'direction',
            'text-decoration',
            'text-align',
            'font-size',
            'font-family',
            'font-weight',
            'font-style',
            'background-color',
            'line-height',
            'color',
        ),
        self::LAYOUT => array(
            'line-height',
            'text-columns',
            'text-column-spacing',
            'margin',
            'padding',
            'orphans',
            'widows',
        ),
        self::PAGE => array(
            'page-size',
            'page-orientation',
            'margin',
            'padding',
        ),
    );

    /**
     * Construct style inference with default styles
     *
     * @param bool $loadDefault
     * @return void
     */
    public function __construct( $loadDefault = true )
    {
        if ( $loadDefault )
        {
            $this->loadDefaultStyles();
        }
    }

    /**
     * Set the default styles
     *
     * Creates a list of default styles for very common elements.
     *
     * @return void
     */
    protected function loadDefaultStyles()
    {
        if ( file_exists( $file = dirname( __FILE__ ) . '/style/default.php' ) )
        {
            $this->appendStyleDirectives( include $file );
            return;
        }

        // If the file does not exist parse the PCSS style file
        $parser     = new ezcDocumentPcssParser();
        $directives = $parser->parseFile( dirname( __FILE__ ) . '/style/default.css' );

        // Write parsed object tree back to file
        file_put_contents( $file, "<?php\n\nreturn " . str_replace( dirname( __FILE__ ) . '/', '', var_export( $directives, true ) ) . ";\n\n?>" ); // */

        $this->appendStyleDirectives( $directives );
    }

    /**
     * Append list of style directives
     *
     * Append another set of style directives. Since style directives are
     * applied in the given order and may overwrite each other, all given
     * directives might overwrite existing formatting rules.
     *
     * @param array $styleDirectives
     * @return void
     */
    public function appendStyleDirectives( array $styleDirectives )
    {
        // Convert values, depending on assigned value handler classes
        foreach ( $styleDirectives as $nr => $directive )
        {
            foreach ( $directive->formats as $name => $value )
            {
                try
                {
                    $valueHandler = isset( $this->valueParserClasses[$name] ) ? $this->valueParserClasses[$name] : 'ezcDocumentPcssStyleStringValue';
                    $styleDirectives[$nr]->formats[$name] = new $valueHandler();
                    $styleDirectives[$nr]->formats[$name]->parse( $value );
                }
                catch ( ezcDocumentParserException $e )
                {
                    // Annotate parser exceptions with additional information
                    // from directive context
                    throw new ezcDocumentParserException(
                        E_PARSE, $e->parseError,
                        $directive->file, $directive->line, $directive->position,
                        $e
                    );
                }
            }
        }

        $this->styleDirectives = array_merge(
            $this->styleDirectives,
            $styleDirectives
        );

        // Clear styl cache, since new styles may leed to different results
        $this->styleCache = array();
    }

    /**
     * Merges box values into one single definition
     *
     * Merges partial box definitions, like "margin-top", into a single
     * "margin" definition, so it can be access easier.
     * 
     * @param array $values 
     * @return array
     */
    protected function mergeBoxValues( array $values )
    {
        $merged = array();

        foreach ( $values as $name => $value )
        {
            if ( ( strpos( $name, '-' . ( $position = 'top' ) ) !== false ) ||
                 ( strpos( $name, '-' . ( $position = 'right' ) ) !== false ) ||
                 ( strpos( $name, '-' . ( $position = 'bottom' ) ) !== false ) ||
                 ( strpos( $name, '-' . ( $position = 'left' ) ) !== false ) )
            {
                $baseProperty = substr( $name, 0, -( strlen( $position ) + 1 ) );

                if ( isset( $merged[$baseProperty] ) )
                {
                    $merged[$baseProperty]->value[$position] = $value->value;
                }
                elseif ( isset( $this->valueParserClasses[$baseProperty] ) )
                {
                    $class = $this->valueParserClasses[$baseProperty];
                    $merged[$baseProperty] = new $class();
                    $merged[$baseProperty]->value[$position] = $value->value;
                }
                else
                {
                    throw new ezcDocumentParserException( "Unknown property to merge: $baseProperty." );
                }
            }
            else
            {
                $merged[$name] = clone $value;
            }
        }

        return $merged;
    }

    /**
     * Merges border values into one single definition
     *
     * Merges partial border definitions like "border-style" into a single
     * border definition, which then includes all border properties.
     * 
     * @param array $values 
     * @return array
     */
    protected function mergeBorderValues( array $values )
    {
        $merged = array();

        foreach ( $values as $name => $value )
        {
            if ( ( $name === 'border-' . ( $type = 'width' ) ) ||
                 ( $name === 'border-' . ( $type = 'style' ) ) ||
                 ( $name === 'border-' . ( $type = 'color' ) ) )
            {
                if ( !isset( $merged['border'] ) )
                {
                    $merged['border'] = new ezcDocumentPcssStyleBorderBoxValue();
                }

                foreach ( $merged['border']->value as $position => $dummy )
                {
                    if ( $value->value[$position] !== null )
                    {
                        $merged['border']->value[$position][$type] = $value->value[$position];
                    }
                }
            }
            else
            {
                $merged[$name] = clone $value;
            }
        }

        return $merged;
    }

    /**
     * Merge formatting rules
     *
     * Merges two sets of formatting rules, while rules set in the second rule
     * set will always overwrite existing rules of the same name in the first.
     * Rules in the first set, not existing in the second will left untouched.
     *
     * @param array $base
     * @param array $new
     * @return array
     */
    protected function mergeFormattingRules( array $base, array $new )
    {
        foreach ( $new as $k => $v )
        {
            // Unset key first, to keep the array order intact.
            if ( isset( $base[$k] ) )
            {
                unset( $base[$k] );
            }

            $base[$k] = $v;
        }

        $base = $this->mergeBoxValues( $base );
        $base = $this->mergeBorderValues( $base );
        return $base;
    }

    /**
     * Filter the styles
     *
     * Filter the styles so that only styles of the specified classes are
     * returned.
     * 
     * @param array $formats 
     * @param int $types 
     * @return array void
     */
    protected function filterStyles( array $formats, $types )
    {
        if ( $types === -1 )
        {
            return $formats;
        }

        // Filter formats to only include formats matching the given category
        $filtered = array();
        foreach ( $this->categories as $type => $properties )
        {
            if ( !( $type & $types ) )
            {
                continue;
            }

            foreach ( $formats as $name => $value )
            {
                if ( in_array( $name, $properties, true ) )
                {
                    $filtered[$name] = $value;
                    unset( $formats[$name] );
                }
            }
        }

        return $filtered;
    }

    /**
     * Inference formatting rules for element
     *
     * Inference the formatting rules for the passed DOMElement or location id.
     * First the cache will be checked for already inferenced formatting rules
     * defined for this element type, using its generated location identifier.
     *
     * Of not cached, the formatting rules will be inferenced using the
     * algorithm described in the class header.
     *
     * @param ezcDocumentLocateable $element
     * @param int $types
     * @return array
     */
    public function inferenceFormattingRules( ezcDocumentLocateable $element, $types = -1 )
    {
        // Check style cache early, to speed things up.
        $locationId = $element->getLocationId();
        if ( isset( $this->styleCache[$locationId] ) )
        {
            return $this->filterStyles( $this->styleCache[$locationId], $types );
        }

        // Check if we are at the root node, otherwise inherit style directives
        $formats = array();
        if ( ( $element instanceof DOMElement ) &&
             !$element->parentNode instanceof DOMDocument )
        {
            $formats = $this->inferenceFormattingRules( $element->parentNode );

            // Some styles do not make sense to be inherited like background
            // properties.
            $formats = array_diff_key( $formats, array(
                'background-color' => true,
            ) );
        }


        // Apply all style directives, which match the location ID
        foreach ( $this->styleDirectives as $directive )
        {
            if ( ! $directive instanceof ezcDocumentPcssLayoutDirective )
            {
                continue;
            }

            if ( preg_match( $directive->getRegularExpression(), $locationId ) )
            {
                $formats = $this->mergeFormattingRules(
                    $formats,
                    $directive->formats
                );
            }
        }

        $this->styleCache[$locationId] = $formats;
        return $this->filterStyles( $formats, $types );
    }

    /**
     * Get definition directives of given type
     *
     * Returns an array of definition directives, which matches the type passed 
     * as a parameter.
     * 
     * @param string $type 
     * @return array
     */
    public function getDefinitions( $type )
    {
        $directives = array();
        foreach ( $this->styleDirectives as $directive )
        {
            if ( ( $directive instanceof ezcDocumentPcssDeclarationDirective ) &&
                  ( $directive->getType() === $type ) )
            {
                $directives[] = $directive;
            }
        }

        return $directives;
    }
}
?>
