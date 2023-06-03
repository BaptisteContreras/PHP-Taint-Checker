<?php


namespace BaptisteContreras\TaintChecker\Analysis;


class CannotRetrieveGlobalContextException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Failed to retrieve the global context');
    }
}