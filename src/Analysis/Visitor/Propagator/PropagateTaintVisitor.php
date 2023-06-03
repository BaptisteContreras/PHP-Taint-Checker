<?php


namespace BaptisteContreras\TaintChecker\Analysis\Visitor\Propagator;


use BaptisteContreras\TaintChecker\Analysis\GlobalContextManipulator;
use BaptisteContreras\TaintChecker\Analysis\NodeInformationExtractor;
use BaptisteContreras\TaintChecker\Analysis\TaintVariableManipulator;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class PropagateTaintVisitor extends NodeVisitorAbstract
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
            if ($assignExpr->expr instanceof Node\Expr\Variable) {
                $variableExpr = $assignExpr->expr;

                if ($this->isVariableTaintedAtLine($variableExpr->name, $variableExpr->getLine(), $this->currentClassMethod, $this->getGlobalContext($assignExpr))) {
                    $this->markNodeTainted($assignExpr);
                    $this->addTaintedVariable($assignExpr, $this->currentClassMethod, $this->getGlobalContext($assignExpr));
                }

                return;
            }

            if ($this->isVariableTaintedAtLine(
                $assignExpr->var->name,
                $assignExpr->getLine(),
                $this->currentClassMethod,
                $this->getGlobalContext($assignExpr)
            )) {
                if (!$assignExpr->expr instanceof Node\Expr\Variable
                    || !$this->isVariableTaintedAtLine(
                        $assignExpr->expr->name,
                        $assignExpr->getLine(),
                        $this->currentClassMethod,
                        $this->getGlobalContext($assignExpr))
                ) {
                    $this->stopVariableTaintAtLine(
                        $assignExpr->var->name,
                        $assignExpr->getLine(),
                        $this->currentClassMethod,
                        $this->getGlobalContext($assignExpr)
                    );
                }
            }
        }


    }
}