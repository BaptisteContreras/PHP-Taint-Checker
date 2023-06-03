<?php


namespace BaptisteContreras\TaintChecker\Analysis;


enum GlobalContextKey: string
{
    case HTTP_FOUNDATION_HAS_USE_FOR_REQUEST = 'httpFoundationHasUseForRequest';
}