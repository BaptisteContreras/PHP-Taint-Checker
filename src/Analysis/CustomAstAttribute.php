<?php


namespace BaptisteContreras\TaintChecker\Analysis;


enum CustomAstAttribute: string
{
    case CLASS_METHOD = 'classMethod';
    case HTTP_FOUNDATION_REQUEST_PARAM_NAME = 'httpFoundationRequestParamName';
    case IS_CLASS_METHOD_USING_HTTP_FOUNDATION_REQUEST = 'isClassMethodUsingHttpFoundationRequest';
    case IGNORED_MARKERS = 'ignoredMarkers';
    case GLOBAL_CONTEXT = 'globalContext';
    case IS_TAINTED = 'isTainted';
}