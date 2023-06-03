<?php


namespace BaptisteContreras\TaintChecker\Analysis\Visitor\Enhancer;


use BaptisteContreras\TaintChecker\Analysis\CustomAstAttribute;
use BaptisteContreras\TaintChecker\Analysis\GlobalContext;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class AddGlobalContextVisitor extends NodeVisitorAbstract
{
    public function __construct(private readonly GlobalContext $globalContext)
    {
    }

    public function enterNode(Node $node)
    {
        $node->setAttribute(CustomAstAttribute::GLOBAL_CONTEXT->value, $this->globalContext);
    }
}