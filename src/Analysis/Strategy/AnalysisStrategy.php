<?php


namespace BaptisteContreras\TaintChecker\Analysis\Strategy;


use PhpParser\NodeTraverser;

abstract class AnalysisStrategy implements AnalysisStrategyInterface
{
    public function doPrepare(array $ast, NodeTraverser $globalIteration): void
    {
    }

    public function doMark(array $ast, NodeTraverser $globalIteration): void
    {
    }

    public function doClassify(array $ast, NodeTraverser $globalIteration): void
    {
    }

    public function doPropagate(array $ast, NodeTraverser $globalIteration): void
    {
    }
}