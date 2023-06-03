<?php


namespace BaptisteContreras\TaintChecker\Analysis\Visitor\Marker;


use BaptisteContreras\TaintChecker\Analysis\CustomAstAttribute;
use BaptisteContreras\TaintChecker\Analysis\GlobalContextManipulator;
use BaptisteContreras\TaintChecker\Analysis\NodeInformationExtractor;
use BaptisteContreras\TaintChecker\Analysis\TaintVariableManipulator;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class MarkHttpFoundationRequestVisitor extends NodeVisitorAbstract
{
    use NodeInformationExtractor;
    use TaintVariableManipulator;
    use GlobalContextManipulator;

    private ?Node\Stmt\ClassMethod $currentClassMethod = null;

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Expr\Assign) {
            $this->handleAssignExpression($node);
        }

        if ($node instanceof Node\Stmt\ClassMethod) {
            $this->currentClassMethod = $node;
        }
    }

    private function handleAssignExpression(Node\Expr\Assign $assignExpr): void
    {
        if (!$this->isNodeTainted($assignExpr)) {
            if ($assignExpr->expr instanceof Node\Expr\MethodCall) {
                $methodCallExpr = $assignExpr->expr;

                if ($this->isCallingRequestMethod($methodCallExpr, 'request')) {
                    $this->markNodeTainted($methodCallExpr);
                    $this->markNodeTainted($assignExpr);
                    $this->addTaintedVariable($assignExpr, $this->currentClassMethod, $this->getGlobalContext($assignExpr));
                }
            }
        }
    }

    private function isCallingRequestMethod(Node\Expr\MethodCall $methodCall, string $requestVarName): bool
    {
        if (!$methodCall->var instanceof Node\Expr\Variable) {
            return false;
        }

        return $methodCall->var->name === $requestVarName;
    }
}