<?php
/**
 * Autoloader definition for the MvcTools component.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

return array(
    'ezcMvcToolsException'                   => 'MvcTools/exceptions/exception.php',
    'ezcMvcActionNotFoundException'          => 'MvcTools/exceptions/action_not_found.php',
    'ezcMvcControllerException'              => 'MvcTools/exceptions/controller.php',
    'ezcMvcFatalErrorLoopException'          => 'MvcTools/exceptions/fatal_error_loop.php',
    'ezcMvcFilterHasNoOptionsException'      => 'MvcTools/exceptions/filter_has_no_options.php',
    'ezcMvcInfiniteLoopException'            => 'MvcTools/exceptions/infinite_loop.php',
    'ezcMvcInvalidConfiguration'             => 'MvcTools/exceptions/invalid_configuration.php',
    'ezcMvcInvalidEncodingException'         => 'MvcTools/exceptions/invalid_encoding.php',
    'ezcMvcMissingRouteArgumentException'    => 'MvcTools/exceptions/missing_route_argument.php',
    'ezcMvcNamedRouteNotFoundException'      => 'MvcTools/exceptions/named_route_not_found.php',
    'ezcMvcNamedRouteNotReversableException' => 'MvcTools/exceptions/named_route_not_reversable.php',
    'ezcMvcNoRoutesException'                => 'MvcTools/exceptions/no_routes.php',
    'ezcMvcNoZonesException'                 => 'MvcTools/exceptions/no_zones.php',
    'ezcMvcRegexpRouteException'             => 'MvcTools/exceptions/regexp_route.php',
    'ezcMvcRouteNotFoundException'           => 'MvcTools/exceptions/route_not_found.php',
    'ezcMvcDispatcher'                       => 'MvcTools/interfaces/dispatcher.php',
    'ezcMvcRequestParser'                    => 'MvcTools/interfaces/request_parser.php',
    'ezcMvcResponseFilter'                   => 'MvcTools/interfaces/response_filter.php',
    'ezcMvcResponseWriter'                   => 'MvcTools/interfaces/response_writer.php',
    'ezcMvcResultStatusObject'               => 'MvcTools/interfaces/result_status_object.php',
    'ezcMvcReversibleRoute'                  => 'MvcTools/interfaces/reversed_route.php',
    'ezcMvcRoute'                            => 'MvcTools/interfaces/route.php',
    'ezcMvcViewHandler'                      => 'MvcTools/interfaces/view_handler.php',
    'ezcMvcCatchAllRoute'                    => 'MvcTools/routes/catchall.php',
    'ezcMvcConfigurableDispatcher'           => 'MvcTools/dispatchers/configurable.php',
    'ezcMvcController'                       => 'MvcTools/interfaces/controller.php',
    'ezcMvcDispatcherConfiguration'          => 'MvcTools/interfaces/dispatcher_configuration.php',
    'ezcMvcExternalRedirect'                 => 'MvcTools/result_types/external_redirect.php',
    'ezcMvcFilterDefinition'                 => 'MvcTools/structs/filter_definition.php',
    'ezcMvcGzDeflateResponseFilter'          => 'MvcTools/response_filters/gzdeflate.php',
    'ezcMvcGzipResponseFilter'               => 'MvcTools/response_filters/gzip.php',
    'ezcMvcHttpRawRequest'                   => 'MvcTools/structs/request_raw_http.php',
    'ezcMvcHttpRequestParser'                => 'MvcTools/request_parsers/http.php',
    'ezcMvcHttpResponseWriter'               => 'MvcTools/response_writers/http.php',
    'ezcMvcInternalRedirect'                 => 'MvcTools/structs/internal_redirect.php',
    'ezcMvcJsonViewHandler'                  => 'MvcTools/view_handlers/json.php',
    'ezcMvcPhpViewHandler'                   => 'MvcTools/view_handlers/php.php',
    'ezcMvcRailsRoute'                       => 'MvcTools/routes/rails.php',
    'ezcMvcRecodeResponseFilter'             => 'MvcTools/response_filters/recode.php',
    'ezcMvcRegexpRoute'                      => 'MvcTools/routes/regexp.php',
    'ezcMvcRequest'                          => 'MvcTools/structs/request.php',
    'ezcMvcRequestAccept'                    => 'MvcTools/structs/request_accept.php',
    'ezcMvcRequestAuthentication'            => 'MvcTools/structs/request_authentication.php',
    'ezcMvcRequestCookie'                    => 'MvcTools/structs/request_cookie.php',
    'ezcMvcRequestFile'                      => 'MvcTools/structs/request_file.php',
    'ezcMvcRequestFilter'                    => 'MvcTools/interfaces/request_filter.php',
    'ezcMvcRequestUserAgent'                 => 'MvcTools/structs/request_user_agent.php',
    'ezcMvcResponse'                         => 'MvcTools/structs/response.php',
    'ezcMvcResult'                           => 'MvcTools/structs/result.php',
    'ezcMvcResultCache'                      => 'MvcTools/structs/result_cache.php',
    'ezcMvcResultContent'                    => 'MvcTools/structs/result_content.php',
    'ezcMvcResultContentDisposition'         => 'MvcTools/structs/result_content_disposition.php',
    'ezcMvcResultCookie'                     => 'MvcTools/structs/result_cookie.php',
    'ezcMvcResultFilter'                     => 'MvcTools/interfaces/result_filter.php',
    'ezcMvcResultUnauthorized'               => 'MvcTools/result_types/unauthorized.php',
    'ezcMvcRouter'                           => 'MvcTools/router.php',
    'ezcMvcRoutingInformation'               => 'MvcTools/structs/routing_information.php',
    'ezcMvcView'                             => 'MvcTools/view.php',
);
?>
