<?php
/**
 * File containing the ezcTemplateSymbolTable class.
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Symbol table
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateSymbolTable
{
    const VARIABLE = 1;
    const CYCLE = 2;
    const IMPORT = 3;  // USE is a keyword.

    // Messages.
    const SYMBOL_REDECLARATION = "The symbol <\$%s> is already declared.";
    const SYMBOL_TYPES_NOT_EQUAL = "The %s <\$%s> is already declared as '%s'.";
    const SYMBOL_NOT_DECLARED = "The symbol <\$%s> is not declared";
    const SYMBOL_INVALID_SCOPE = "The %s <\$%s> is cannot be declared in a subblock: while, foreach, if, etc";
    const SYMBOL_IMPORT_FIRST = "'Use' should be declared before other declarations";

    /**
     * Keeps track of the symbols with its type.
     *
     * @var array(string=>int)
     */
    protected $symbols;


    /**
     * Maps the variable name to the typehint integer.
     * The integer has one of the values: ezcTemplateAstNode::TYPE_ARRAY,
     * ezcTemplatesAstNode::TYPE_VALUE, or both (use bitwise OR).
     *  
     * @var array(string=>int)
     */
    protected $typehints;

    /**
     * Keeps track of the current scope. Inside a block the scope is higher
     * than outside the block.  
     *
     * Example:
     * <code>
     *
     * Scope 1
     *
     * {if true}
     *    Scope 2
     * {/if}
     *
     * Scope 1
     * </code>
     *
     * @var int
     */
    private $scope;

    /**
     * Keeps track of the type that is declared first. 
     * The first declared type should be a 'use' variable, if any.
     *
     * @var int
     */
    private $firstDeclaredType;

    /**
     * Stores the last error message.
     *
     * @var string
     */
    private $errorMessage = "";


    /**
     * This class is a singleton. Reference to itself.
     *
     * @var ezcTemplateSymbolTable
     */
    private static $instance = null;



    /**
     * Instantiate this class and reset() all the values.
     *
     * The constructor is private and getInstance() should be used
     * to instantiate the symbolTable.
     */
    private function __construct()
    {
        $this->reset();
    }

    /**
     * Return the existing instance of the symbol table if it exists. 
     * Otherwise a new instance is created. (Singleton)
     *
     * @return ezcTemplateSymbolTable
     */
    static public function getInstance()
    {
        if ( self::$instance === null)
        {
            self::$instance = new ezcTemplateSymbolTable();
        }

        return self::$instance;
    }

    /**
     * Reset the symbol table.
     * Set the scope to one. Remove all registered symbols and typehints.
     *
     * @return void
     */
    public function reset()
    {
        $this->typehints = array();
        $this->symbols = array();
        $this->scope = 1;

        $this->firstDeclaredType = false;
    }

    /**
     * Add a new or existing symbol $symbol with its type $type to the 
     * symbolTable.
     *
     * If $isAutoDeclared is set to false (default), the added symbol must 
     * apply to all following rules:
     *
     * - The symbol cannot be redeclared.
     * - The current scope must be one; thus only the top scope allows 
     *   variable declaration.
     * - The 'use' variables should all be declared before the other type of
     *   variables.
     *
     * When $isAutoDeclared is set to true:
     *
     * - Variables can be redeclared as long as the type does not change.
     * - It is allowed to declare and redeclare at any scope. 
     * - The 'use' variables should all be declared before the other type of
     *   variables.
     *
     * @param string $symbol
     * @param int $type             The type is usually one of the constants: 
     *                              ezcTemplateSymbolTable::VARIABLE, 
     *                              ezcTemplateSymbolTable::CYCLE, and 
     *                              ezcTemplateSymbolTable::IMPORT. 
     *
     * @param bool $isAutoDeclared  Usually this parameter is set to true when
     *                              a construct automatically declares a 
     *                              variable. For example the: {foreach}.
     *
     * @return bool                 Return true if the variable is added. If 
     *                              false is returned check the errorMessage.
     */
    public function enter( $symbol, $type, $isAutoDeclared = false )
    {
        // Check for redeclaration.
        if ( isset( $this->symbols[ $symbol ] ) )
        {
            if ( $isAutoDeclared )
            {
                $storedType = $this->symbols[ $symbol ];

                // Check whether the types are equal, when redeclaration is allowed.
                if ( $type != $storedType )
                {
                    $this->errorMessage = sprintf( self::SYMBOL_TYPES_NOT_EQUAL, self::symbolTypeToString( $type ), $symbol, self::symbolTypeToString( $storedType ) );
                    return false;
                }
            }
            else
            {
                $this->errorMessage = sprintf( self::SYMBOL_REDECLARATION, $symbol );
                return false;
            }
        }

        // Check whether the declaration is at the top scope.  Scope level 1.
        if ( !$isAutoDeclared && $this->scope != 1 )
        {
            $this->errorMessage = sprintf( self::SYMBOL_INVALID_SCOPE, self::symbolTypeToString( $type ), $symbol );
            return false;
        }

        if ( $this->firstDeclaredType === false )
        {
            $this->firstDeclaredType = $type;
        }
        else
        {
            if ( $type === self::IMPORT && $this->firstDeclaredType !== self::IMPORT )
            {
                $this->errorMessage = sprintf( self::SYMBOL_IMPORT_FIRST );
                return false;
            }
        }

        $this->symbols[ $symbol ] = $type;
        return true;
    }

    /**
     * Return the type of the given symbol $symbol.
     *
     * If the symbol is not registered in the symbol table the method returns
     * false and sets an error message.
     *
     * @param string $symbol
     * @return bool
     */
    public function retrieve( $symbol )
    {
        if ( !isset( $this->symbols[ $symbol ] ) )
        {
            $this->errorMessage = sprintf ( self::SYMBOL_NOT_DECLARED, $symbol );
            return false;
        }

        return $this->symbols[ $symbol ];
    }

    /**
     * Return an array with all symbols that have the given types $typeArray.
     *
     * @param array(int) $typeArray  The array can have one or more of the 
     *                               following values:
     *                               ezcTemplateSymbolTable::VARIABLE, 
     *                               ezcTemplateSymbolTable::CYCLE, and 
     *                               ezcTemplateSymbolTable::IMPORT. 
     *
     * @return array(string)
     */
    public function retrieveSymbolsWithType( $typeArray )
    {
        $total = array();

        foreach ( $typeArray as $type )
        {
            // Search for all the keys in the array, and merge it. 
            $total = array_merge($total, array_keys( $this->symbols, $type ) );
        }

        return $total;
    }

    /** 
     * Return the last error message.
     * 
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Translates a symbol type to a string.
     * It returns one of the following strings: 'variable', 'cycle', or 'use'.
     *
     * @param int $type
     * @return string
     */
    public static function symbolTypeToString( $type )
    {
        switch ( $type )
        {
            case self::VARIABLE: return "variable";
            case self::CYCLE: return "cycle";
            case self::IMPORT: return "use";
        }
    }

    /**
     * Increase the current scope with one.
     *
     * @return void
     */
    public function increaseScope()
    {
        ++$this->scope;
    }

    /**
     * Decreases the current scope with one.
     *
     * @return void
     */
    public function decreaseScope()
    {
        --$this->scope;
    }

    /**
     * Set the typehint with the symbol $name to the type hint value $hint.
     *
     * @param string $name   
     * @param int $hint      The integer has one of the values: ezcTemplateAstNode::TYPE_ARRAY,
     *                       ezcTemplatesAstNode::TYPE_VALUE, or both (use bitwise OR).
     *  
     * @return void
     */
    public function setTypeHint( $name, $hint )
    {
        $this->typehints[$name] = $hint;
    }

    /**
     * Return the typehint of the given symbol $name.
     *
     * @param string $name   
     *  
     * @return int   The integer has one of the values: ezcTemplateAstNode::TYPE_ARRAY,
     *               ezcTemplatesAstNode::TYPE_VALUE, or both (use bitwise OR).
     */
    public function getTypeHint( $name )
    {
        return isset( $this->typehints[$name] ) ? $this->typehints[$name] : false;

    }

}


?>
