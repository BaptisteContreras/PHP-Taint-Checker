<?php


namespace BaptisteContreras\TaintChecker\Analysis\Visitor\Enhancer;


use BaptisteContreras\TaintChecker\Analysis\CustomAstAttribute;
use BaptisteContreras\TaintChecker\Analysis\GlobalContextKey;
use BaptisteContreras\TaintChecker\Analysis\GlobalContextManipulator;
use BaptisteContreras\TaintChecker\Analysis\MarkerIgnoreNode;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class AddHttpFoundationRequestHintVisitor extends NodeVisitorAbstract
{
    use MarkerIgnoreNode;
    use GlobalContextManipulator;

    private const HTTP_FOUNDATION_REQUEST_NAMESPACE = 'Symfony|Component|HttpFoundation|Request';

    private bool $hasUseForRequest = false;

    public function beforeTraverse(array $nodes)
    {
        $this->hasUseForRequest = false;
    }

    public function enterNode(Node $node)
    {
        if (!$this->hasUseForRequest && $node instanceof Node\Stmt\Use_) {
            $this->checkUseOfRequest($node);
        }

        if ($node instanceof Node\Stmt\ClassMethod && !$this->shouldAnalyzeMethod($node)) {
            $this->markMethodIgnoredForMarker(self::class, $node);
        }
    }


    public function afterTraverse(array $nodes)
    {
        $this->getGlobalContextFromNodes($nodes)->setKey(
            GlobalContextKey::HTTP_FOUNDATION_HAS_USE_FOR_REQUEST->value,
            $this->hasUseForRequest
        );
    }

    private function checkUseOfRequest(Node\Stmt\Use_ $node): void
    {
        $useExtracted = $node->uses[0] ?? null;

        if (!$useExtracted) {
            return;
        }

        $this->hasUseForRequest = $this->isHttpFoundationRequestType($useExtracted->name);
    }

    private function shouldAnalyzeMethod(Node\Stmt\ClassMethod $classMethod): bool
    {
        foreach ($classMethod->getParams() as $param) {
            $firstTypeNamePart = $param->type->parts[0] ?? '';
            if ($this->hasUseForRequest && $firstTypeNamePart === 'Request' || $this->isHttpFoundationRequestType($param->type)) {
                $classMethod->setAttribute(CustomAstAttribute::HTTP_FOUNDATION_REQUEST_PARAM_NAME->value, $param->var->name);
                $classMethod->setAttribute(CustomAstAttribute::IS_CLASS_METHOD_USING_HTTP_FOUNDATION_REQUEST->value, true);

                return true;
            }
        }

        return false;
    }

    private function isHttpFoundationRequestType(Node\Name $name): bool
    {
        return self::HTTP_FOUNDATION_REQUEST_NAMESPACE === implode('|', $name->parts);
    }
}