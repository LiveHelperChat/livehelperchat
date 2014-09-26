<?php
/**
 * Autoloader definition for the Search component.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.0.9
 * @filesource
 * @package Search
 */

return array(
    'ezcSearchException'                         => 'Search/exceptions/exception.php',
    'ezcSearchBuildQueryException'               => 'Search/exceptions/build_query.php',
    'ezcSearchCanNotConnectException'            => 'Search/exceptions/can_not_connect.php',
    'ezcSearchDefinitionInvalidException'        => 'Search/exceptions/definition_invalid.php',
    'ezcSearchDefinitionNotFoundException'       => 'Search/exceptions/definition_not_found.php',
    'ezcSearchDoesNotProvideDefinitionException' => 'Search/exceptions/does_not_provide_definition.php',
    'ezcSearchFieldNotDefinedException'          => 'Search/exceptions/field_not_defined.php',
    'ezcSearchIdNotFoundException'               => 'Search/exceptions/id_not_found.php',
    'ezcSearchIncompleteStateException'          => 'Search/exceptions/incomplete_state.php',
    'ezcSearchInvalidResultException'            => 'Search/exceptions/invalid_result.php',
    'ezcSearchNetworkException'                  => 'Search/exceptions/network.php',
    'ezcSearchQueryVariableParameterException'   => 'Search/exceptions/query_variable_parameter.php',
    'ezcSearchTransactionException'              => 'Search/exceptions/transaction.php',
    'ezcSearchQuery'                             => 'Search/interfaces/query.php',
    'ezcSearchDefinitionManager'                 => 'Search/interfaces/definition_manager.php',
    'ezcSearchDefinitionProvider'                => 'Search/interfaces/definition_provider.php',
    'ezcSearchDeleteQuery'                       => 'Search/interfaces/query_delete.php',
    'ezcSearchFindQuery'                         => 'Search/interfaces/query_find.php',
    'ezcSearchHandler'                           => 'Search/interfaces/handler.php',
    'ezcSearchIndexHandler'                      => 'Search/interfaces/index_handler.php',
    'ezcSearchDefinitionDocumentField'           => 'Search/structs/document_field_definition.php',
    'ezcSearchDeleteQuerySolr'                   => 'Search/abstraction/implementations/solr_delete.php',
    'ezcSearchDeleteQueryZendLucene'             => 'Search/abstraction/implementations/zend_lucene_delete.php',
    'ezcSearchDocumentDefinition'                => 'Search/document_definition.php',
    'ezcSearchEmbeddedManager'                   => 'Search/managers/embedded_manager.php',
    'ezcSearchQueryBuilder'                      => 'Search/query_builder.php',
    'ezcSearchQuerySolr'                         => 'Search/abstraction/implementations/solr.php',
    'ezcSearchQueryToken'                        => 'Search/structs/query_token.php',
    'ezcSearchQueryTools'                        => 'Search/abstraction/query_tools.php',
    'ezcSearchQueryZendLucene'                   => 'Search/abstraction/implementations/zend_lucene.php',
    'ezcSearchResult'                            => 'Search/structs/search_result.php',
    'ezcSearchResultDocument'                    => 'Search/structs/search_result_document.php',
    'ezcSearchRstXmlExtractor'                   => 'Search/extractors/rstxml.php',
    'ezcSearchSession'                           => 'Search/search_session.php',
    'ezcSearchSimpleArticle'                     => 'Search/extractors/helpers/simple.php',
    'ezcSearchSimpleImage'                       => 'Search/extractors/helpers/image.php',
    'ezcSearchSolrHandler'                       => 'Search/handlers/solr.php',
    'ezcSearchXmlManager'                        => 'Search/managers/xml_manager.php',
    'ezcSearchZendLuceneHandler'                 => 'Search/handlers/zend_lucene.php',
);
?>
