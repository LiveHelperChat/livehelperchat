<?php
/**
 * Autoloader definition for the Tree component.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 */

return array(
    'ezcTreeException'                          => 'Tree/exceptions/exception.php',
    'ezcTreeDataStoreMissingDataException'      => 'Tree/exceptions/missing_data.php',
    'ezcTreeIdsDoNotMatchException'             => 'Tree/exceptions/ids_do_not_match.php',
    'ezcTreeInvalidClassException'              => 'Tree/exceptions/invalid_class.php',
    'ezcTreeInvalidIdException'                 => 'Tree/exceptions/invalid_id.php',
    'ezcTreeInvalidXmlException'                => 'Tree/exceptions/invalid_xml.php',
    'ezcTreeInvalidXmlFormatException'          => 'Tree/exceptions/invalid_xml_format.php',
    'ezcTreeTransactionAlreadyStartedException' => 'Tree/exceptions/transaction_already_started.php',
    'ezcTreeTransactionNotStartedException'     => 'Tree/exceptions/transaction_not_started.php',
    'ezcTreeUnknownIdException'                 => 'Tree/exceptions/unknown_id.php',
    'ezcTreeDataStore'                          => 'Tree/interfaces/data_store.php',
    'ezcTreeVisitable'                          => 'Tree/interfaces/visitable.php',
    'ezcTree'                                   => 'Tree/tree.php',
    'ezcTreeVisitor'                            => 'Tree/interfaces/visitor.php',
    'ezcTreeXmlDataStore'                       => 'Tree/stores/xml.php',
    'ezcTreeMemory'                             => 'Tree/backends/memory.php',
    'ezcTreeMemoryDataStore'                    => 'Tree/stores/memory.php',
    'ezcTreeMemoryNode'                         => 'Tree/structs/memory_node.php',
    'ezcTreeNode'                               => 'Tree/tree_node.php',
    'ezcTreeNodeList'                           => 'Tree/tree_node_list.php',
    'ezcTreeNodeListIterator'                   => 'Tree/tree_node_list_iterator.php',
    'ezcTreeTransactionItem'                    => 'Tree/structs/transaction_item.php',
    'ezcTreeVisitorGraphViz'                    => 'Tree/visitors/graphviz.php',
    'ezcTreeVisitorPlainText'                   => 'Tree/visitors/plain_text.php',
    'ezcTreeVisitorXHTML'                       => 'Tree/visitors/xhtml.php',
    'ezcTreeVisitorXHTMLOptions'                => 'Tree/options/visitor_xhtml.php',
    'ezcTreeVisitorYUI'                         => 'Tree/visitors/yui.php',
    'ezcTreeVisitorYUIOptions'                  => 'Tree/options/visitor_yui.php',
    'ezcTreeXml'                                => 'Tree/backends/xml.php',
    'ezcTreeXmlInternalDataStore'               => 'Tree/stores/xml_internal.php',
);
?>
