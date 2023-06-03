<?php


namespace BaptisteContreras\TaintChecker\Analysis;


use PhpParser\Node;

trait GlobalContextManipulator
{
    protected function getFromGlobalContext(Node $node, GlobalContextKey $key, $default = null)
    {
        return $node->getAttribute(CustomAstAttribute::GLOBAL_CONTEXT->value)->getKey($key->value, $default);
    }

    protected function setInGlobalContext(Node $node, GlobalContextKey $key, $value)
    {
        return $node->getAttribute(CustomAstAttribute::GLOBAL_CONTEXT->value)->setKey($key->value, $value);
    }

    /**
     * @param array<Node> $nodes
     * @throws CannotRetrieveGlobalContextException
     */
    protected function getGlobalContextFromNodes(array $nodes): GlobalContext
    {
       $globalContext = ($nodes[0] ?? null)?->getAttribute(CustomAstAttribute::GLOBAL_CONTEXT->value);

       if (!$globalContext) {
           throw new CannotRetrieveGlobalContextException();
       }

       return $globalContext;
    }


    protected function getGlobalContext(Node $node): GlobalContext
    {
        return $node->getAttribute(CustomAstAttribute::GLOBAL_CONTEXT->value);
    }
}