<?php


namespace BaptisteContreras\TaintChecker\Analysis\Visitor\Enhancer;


use BaptisteContreras\TaintChecker\Analysis\CustomAstAttribute;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ClassMethodConnectingVisitor extends NodeVisitorAbstract
{
    private ?Node\Stmt\ClassMethod $currentClassMethod = null;

    public function beforeTraverse(array $nodes)
    {
        $this->currentClassMethod = null;
    }

    public function enterNode(Node $node)
    {
       if ($node instanceof Node\Stmt\ClassMethod) {
           $this->currentClassMethod = $node;

       } else {
           $node->setAttribute(CustomAstAttribute::CLASS_METHOD->value, $this->currentClassMethod);
       }

    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\ClassMethod) {
            $this->currentClassMethod = null;
        }
    }
}