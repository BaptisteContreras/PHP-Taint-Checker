<?php


namespace BaptisteContreras\TaintChecker\Analysis;


use PhpParser\Node;

trait NodeInformationExtractor
{
    protected function isNodeTainted(Node $node): bool
    {
        return $node->getAttribute(CustomAstAttribute::IS_TAINTED->value, false);
    }

    protected function markNodeTainted(Node $node): void
    {
        $node->setAttribute(CustomAstAttribute::IS_TAINTED->value, true);
    }
}