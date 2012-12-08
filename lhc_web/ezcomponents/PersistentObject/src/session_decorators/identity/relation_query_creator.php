<?php
/**
 * File containing the ezcPersistentIdentityRelationQueryCreator class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Creates JOIN queries to fetch related objects for a given object.
 *
 * An instance of this class is used in {@link ezcPersistentSessionIdentityDecorator} to
 * generate a {@link ezcQuerySelect} object to fetch related objects for a
 * certain object. The results of the generated query are then extracted using
 * {@link ezcPersistentIdentityRelationObjectExtractor}.
 * 
 * @package PersistentObject
 * @version 1.7.1
 * @access private
 */
class ezcPersistentIdentityRelationQueryCreator
{
    /**
     * Definition Manager.
     * 
     * @var ezcPersistentDefinitionManager
     */
    protected $defManager;

    /**
     * Database handler. 
     * 
     * @var ezcDbHandler
     */
    protected $db;

    /**
     * Aliases to be registered in query objects. 
     * 
     * @var array(string=>string)
     */
    protected $aliases = array();

    /**
     * Creates a new query generator. 
     *
     * Creates a new query generator that will receive needed persistentence
     * definitions from $defManager.
     * 
     * @param ezcPersistentDefinitionManager $defManager 
     * @param ezcDbHandler $database
     */
    public function __construct( ezcPersistentDefinitionManager $defManager, ezcDbHandler $database )
    {
        $this->defManager = $defManager;
        $this->db         = $database;
    }

    /**
     * Creates a load object query with relation pre-fetching.
     *
     * This method generates a query that loads the object of $class with $id
     * and its related objects as specified by $relations.
     * 
     * @param string $class 
     * @param mixed $id 
     * @param array(string=>ezcPersistentRelationFindDefinition) $relations 
     * @return ezcDbQuerySelect
     */
    public function createLoadQuery( $class, $id, array $relations )
    {
        $srcDef = $this->defManager->fetchDefinition( $class );

        $q = $this->createBasicFindQuery( $srcDef, $relations );

        $q->where(
            $q->expr->eq(
                $this->getColumnName(
                    $srcDef->table,
                    $srcDef->idProperty->columnName
                ),
                $q->bindValue( $id )
            )
        );

        $this->aliases = array();

        return $q;
    }

    /**
     * Creates a find object query for $class with relation pre-fetching for $relations.
     *
     * This method generates a query that finds objects of $class together with
     * their related objects as defined in $relations.
     * 
     * @param mixed $class 
     * @param array(string=>ezcPersistentRelationFindDefinition) $relations
     * @return ezcPersistentFindWithRelationsQuery
     */
    public function createFindQuery( $class, array $relations )
    {
        $srcDef = $this->defManager->fetchDefinition( $class );

        $q = $this->createBasicFindQuery( $srcDef, $relations );
        $q->setAliases( $this->aliases );

        $this->aliases = array();

        return new ezcPersistentFindWithRelationsQuery(
            $q,
            $class,
            $relations
        );
    }

    /**
     * Generates a basic find query with relation pre-fetching.
     *
     * This method generates a basic find query for the table/class defined in
     * $srcDef, with relation pre-fetching for $relations.
     * 
     * @param ezcPersistentObjectDefinition $srcDef 
     * @param array(string=>ezcPersistentRelationFindDefinition) $relations
     * @return ezcQuerySelect
     */
    protected function createBasicFindQuery( ezcPersistentObjectDefinition $srcDef, array $relations )
    {
        $this->fetchDefinitions( $srcDef, $relations );

        $q = $this->db->createSelectQuery();

        // Select the desired object columns as main
        $q->select(
            $q->alias(
                $this->getColumnName( $srcDef->table, $srcDef->idProperty->columnName ),
                $this->db->quoteIdentifier( $srcDef->idProperty->propertyName )
            )
        );
        $this->registerAlias(
            $this->getColumnName( $srcDef->table, $srcDef->idProperty->columnName ),
            $srcDef->idProperty->propertyName
        );
        foreach ( $srcDef->properties as $property )
        {
            $q->select(
                $q->alias(
                    $this->getColumnName( $srcDef->table, $property->columnName ),
                    $this->db->quoteIdentifier( $property->propertyName )
                )
            );
            $this->registerAlias(
                $this->getColumnName( $srcDef->table, $property->columnName ),
                $property->propertyName
            );
        }
        
        $this->createSelects( $q, $relations );

        $q->from( $this->db->quoteIdentifier( $srcDef->table ) );

        $this->createJoins( $q, $srcDef->table, $relations );

        return $q;
    }

    /**
     * Fetches the needed definitions for $relations and stores them.
     *
     * Fetches {@link ezcPersistentObjectDefinition} and relation definitions
     * for all relations defined in $relations. $srcDef is the the object
     * definition, where $relations should be fetched for. The definitions are
     * stored inside the {@link ezcPersistentRelationFindDefinition} objects
     * the correspond to.
     *
     * @param ezcPersistentObjectDefinition $srcDef 
     * @param array(string=>ezcPersistentRelationFindDefinition) $relations 
     */
    protected function fetchDefinitions( ezcPersistentObjectDefinition $srcDef, array $relations )
    {
        foreach ( $relations as $relation )
        {
            if ( !isset( $srcDef->relations[$relation->relatedClass] ) )
            {
                throw new ezcPersistentRelationNotFoundException(
                    $srcDef->class, $relation->relatedClass
                );
            }
            $srcRelDef = $srcDef->relations[$relation->relatedClass];
            if ( $relation->relationName !== null )
            {
                $srcRelDef = $srcRelDef[$relation->relationName];
            }
            $relation->relationDefinition = $srcRelDef;

            $relation->definition = $this->defManager->fetchDefinition( $relation->relatedClass );
            
            if ( $relation->furtherRelations !== array() )
            {
                $this->fetchDefinitions( $relation->definition, $relation->furtherRelations );
            }
        }
    }

    /**
     * Adds all columns to be selected to $q.
     *
     * Adds the columns from all tables defined in $relations to the select
     * query $q. Columns are selected using an alias to identify which relation
     * they belong too, created using the tables alias. In addition, aliases
     * are created in the query $q, for usage ease.
     * 
     * @param ezcQuerySelect $q 
     * @param array(ezcPersistentRelationFindDefinition) $relations 
     */
    protected function createSelects( ezcQuerySelect $q, array $relations )
    {
        foreach ( $relations as $tableAlias => $relation )
        {
            $q->select(
                $q->alias(
                    $this->getColumnName(
                        $tableAlias,
                        $relation->definition->idProperty->columnName
                    ),
                    $this->getColumnAlias(
                        $tableAlias,
                        $relation->definition->idProperty->propertyName
                    )
                )
            );

            $this->registerAlias(
                $this->getColumnName( $tableAlias, $relation->definition->idProperty->columnName ),
                // Register unquoted alias in query object!
                $this->getColumnAlias( $tableAlias, $relation->definition->idProperty->propertyName, false )
            );

            foreach ( $relation->definition->properties as $property )
            {
                $q->select(
                    $q->alias(
                        $this->getColumnName( $tableAlias, $property->columnName ),
                        $this->getColumnAlias( $tableAlias, $property->propertyName )
                    )
                );

                $this->registerAlias(
                    $this->getColumnName( $tableAlias, $property->columnName ),
                    // Register unquoted alias in query object!
                    $this->getColumnAlias( $tableAlias, $property->propertyName, false )
                );
            }

            if ( $relation->furtherRelations !== array() )
            {
                $this->createSelects( $q, $relation->furtherRelations );
            }
        }
    }

    /**
     * Returns an alias for $column from $table.
     *
     * Returns the alias for $column from $table to be used in the select
     * query. If $quote is set to false, the alias name will not be quoted as
     * an identifier.
     * 
     * @param string $table 
     * @param string $property
     * @param bool $quote
     * @return string
     */
    protected function getColumnAlias( $table, $property, $quote = true )
    {
        $alias = sprintf(
            '%s_%s',
            $table,
            $property
        );

        if ( $quote )
        {
            $alias = $this->db->quoteIdentifier( $alias );
        }

        return $alias;
    }

    /**
     * Returns the full qualified column name for the $column in $table.
     *
     * The returned qualified name already contains identifier quotings.
     * 
     * @param string $table 
     * @param string $column 
     * @return string
     */
    protected function getColumnName( $table, $column )
    {
        return sprintf(
            '%s.%s',
            $this->db->quoteIdentifier( $table ),
            $this->db->quoteIdentifier( $column )
        );
    }
    
    /**
     * Creates the joins to select $relations from $srcTableAlias.
     *
     * Creates the necessary JOIN statements in $q to select all related
     * objects defined by $relations, seen from $srcTableAlias point of view.
     * $srcTableAlias must already be the alias table name of the source table.
     * 
     * @param ezcQuerySelect $q 
     * @param string $srcTableAlias 
     * @param array(string=>ezcPersistentRelationFindDefinition) $relations 
     */
    protected function createJoins( ezcQuerySelect $q, $srcTableAlias, array $relations )
    {
        foreach ( $relations as $dstTableAlias => $relation )
        {
            $this->createJoin( $q, $srcTableAlias, $dstTableAlias, $relation );
            $this->createJoins( $q, $dstTableAlias, $relation->furtherRelations );
        }
    }

    /**
     * Creates the JOIN necessary to fetch related objects of $relation.
     *
     * Detects if $relation needs a n:m-relation JOIN or just a simple join and
     * dispatches to the correct methods.
     * 
     * @param ezcQuerySelect $q 
     * @param string $srcTableAlias 
     * @param string $dstTableAlias
     * @param ezcPersistentRelationFindDefinition $relation 
     */
    protected function createJoin( ezcQuerySelect $q, $srcTableAlias, $dstTableAlias, ezcPersistentRelationFindDefinition $relation )
    {
        if ( $relation->relationDefinition instanceof ezcPersistentManyToManyRelation )
        {
            $this->createComplexJoin( $q, $srcTableAlias, $dstTableAlias, $relation );
        }
        else
        {
            $this->createSimpleJoin( $q, $srcTableAlias, $dstTableAlias, $relation );
        }
    }

    /**
     * Creates an n:m relation JOIN to fetch $relation.
     *
     * Uses the aliases defined in $relation and the relations definition to
     * create 2 LEFT JOIN statements in $q. These 2 JOINs are used to fetch the
     * objects defined in $relation.
     * 
     * @param ezcQuerySelect $q 
     * @param string $srcTableAlias 
     * @param string $dstTableAlias 
     * @param ezcPersistentRelationFindDefinition $relation 
     */
    protected function createComplexJoin( ezcQuerySelect $q, $srcTableAlias, $dstTableAlias, ezcPersistentRelationFindDefinition $relation )
    {
        $relationDefinition = $relation->relationDefinition;
        $relTableAlias = sprintf( '%s__%s', $srcTableAlias, $dstTableAlias );

        $first        = true;
        $srcJoinCond  = null;
        $destJoinCond = null;

        // Build join conditions in paralell
        foreach ( $relationDefinition->columnMap as $mapping )
        {
            $srcColumn = $this->getColumnName(
                $srcTableAlias,
                $mapping->sourceColumn
            );
            $relSrcColumn = $this->getColumnName(
                $relTableAlias,
                $mapping->relationSourceColumn
            );
            $relDestColumn = $this->getColumnName(
                $relTableAlias,
                $mapping->relationDestinationColumn
            );
            $destColumn = $this->getColumnName(
                $dstTableAlias,
                $mapping->destinationColumn
            );

            if ( $first )
            {
                $srcJoinCond  = $q->expr->eq( $srcColumn, $relSrcColumn );
                $destJoinCond = $q->expr->eq( $relDestColumn, $destColumn );
                $first        = false;
            }
            else
            {
                $srcJoinCond = $q->expr->and(
                    $srcJoinCond,
                    $q->expr->eq( $srcColumn, $relSrcColumn )
                );
                $destJoinCond = $q->expr->and(
                    $destJoinCond,
                    $q->expr->eq( $relDestColumn, $destColumn )
                );
            }
        }

        // Add 2 joins
        $q->leftJoin(
            $q->alias(
                $this->db->quoteIdentifier( $relationDefinition->relationTable ),
                $this->db->quoteIdentifier( $relTableAlias )
            ),
            $srcJoinCond
        );
        $q->leftJoin(
            $q->alias(
                $this->db->quoteIdentifier( $relationDefinition->destinationTable ),
                $this->db->quoteIdentifier( $dstTableAlias )
            ),
            $destJoinCond
        );
    }

    /**
     * Creates a simple JOIN to fetch the objects defined by $relation.
     *
     * Creates a simple LEFT JOIN using the aliases defined in $relation and
     * the $srcTableAlias, to fetch all objects defined by $relation, which are
     * related to the source object, fetched by $srcTableAlias.
     * 
     * @param ezcQuerySelect $q 
     * @param string $srcTableAlias 
     * @param string $dstTableAlias 
     * @param ezcPersistentRelationFindDefinition $relation 
     */
    protected function createSimpleJoin( ezcQuerySelect $q, $srcTableAlias, $dstTableAlias, ezcPersistentRelationFindDefinition $relation )
    {
        $relationDefinition = $relation->relationDefinition;

        $first    = true;
        $joinCond = null;
        foreach ( $relationDefinition->columnMap as $mapping )
        {
            $srcColumn  = $this->getColumnName( $srcTableAlias, $mapping->sourceColumn );
            $destColumn = $this->getColumnName( $dstTableAlias, $mapping->destinationColumn );

            if ( $first )
            {
                $joinCond = $q->expr->eq( $srcColumn, $destColumn );
                $first    = false;
            }
            else
            {
                $joinCond = $q->expr->and(
                    $joinCond,
                    $q->expr->eq( $srcColumn, $destColumn )
                );
            }
        }
        $q->leftJoin(
            $q->alias(
                $this->db->quoteIdentifier( $relationDefinition->destinationTable ),
                $this->db->quoteIdentifier( $dstTableAlias )
            ),
            $joinCond
        );
    }

    /**
     * Register an alias to be used in {@link ezcPersistentFindQuery}.
     * 
     * @param string $identifier 
     * @param string $alias 
     * @return void
     */
    protected function registerAlias( $identifier, $alias )
    {
        $this->aliases[$alias] = $identifier;
    }
}

?>
