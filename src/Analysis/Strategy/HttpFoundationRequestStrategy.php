<?php


namespace BaptisteContreras\TaintChecker\Analysis\Strategy;


use BaptisteContreras\TaintChecker\Analysis\CustomAstAttribute;
use BaptisteContreras\TaintChecker\Analysis\GlobalContextKey;
use BaptisteContreras\TaintChecker\Analysis\GlobalContextManipulator;
use BaptisteContreras\TaintChecker\Analysis\Visitor\Enhancer\AddHttpFoundationRequestHintVisitor;
use BaptisteContreras\TaintChecker\Analysis\Visitor\Marker\MarkHttpFoundationRequestVisitor;
use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;

class HttpFoundationRequestStrategy extends AnalysisStrategy
{
    use GlobalContextManipulator;

    public function doPrepare(array $ast, NodeTraverser $globalIteration): void
    {
        $globalIteration->addVisitor(new AddHttpFoundationRequestHintVisitor());
    }

    public function doMark(array $ast, NodeTraverser $globalIteration): void
    {
        $finder = new NodeFinder();

        $methodsToAnalyze = $finder->find($ast, function (Node $node): bool {
            return $node instanceof Node\Stmt\ClassMethod && $this->isMethodEligible($node);
        });

        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new MarkHttpFoundationRequestVisitor());

        $nodeTraverser->traverse($methodsToAnalyze);
    }

    private function isMethodEligible(Node\Stmt\ClassMethod $node): bool
    {
        return $node->getAttribute(CustomAstAttribute::IS_CLASS_METHOD_USING_HTTP_FOUNDATION_REQUEST->value, false);
    }
}