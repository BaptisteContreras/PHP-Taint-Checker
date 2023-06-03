<?php


namespace BaptisteContreras\TaintChecker\Analysis;

use PhpParser\Node;

trait TaintVariableManipulator
{
    protected function addTaintedVariable(Node\Expr\Assign $assignExpr, Node\Stmt\ClassMethod $currentMethod, GlobalContext $globalContext): void
    {
        $globalContext->addTaintedVariable($assignExpr->var->name, $currentMethod->name, $assignExpr->var);
    }

    protected function isVariableTaintedAtLine(string $variable, int $line, Node\Stmt\ClassMethod $currentMethod, GlobalContext $globalContext): bool
    {
        $taintedMethodVariables = $globalContext->getTaintedVariables()[(string) $currentMethod->name][$variable] ?? [];

        $taintedVariableForLine = array_filter($taintedMethodVariables, function (TaintedVariable $taintedVariable) use ($line) {
           return null === $taintedVariable->getUntilLine() || $line <= $taintedVariable->getUntilLine() && $line >= $taintedVariable->getStartLine();
        });

        return !empty($taintedVariableForLine);
    }

    protected function stopVariableTaintAtLine(string $variable, int $line, Node\Stmt\ClassMethod $currentMethod, GlobalContext $globalContext): void
    {
        $taintedMethodVariables = $globalContext->getTaintedVariables()[(string) $currentMethod->name][$variable];

        /** @var TaintedVariable $taintedVariableToStop */
        $taintedVariableToStop = end($taintedMethodVariables);

        $taintedVariableToStop->setUntilLine($line - 1);
    }
}