<?php


namespace BaptisteContreras\TaintChecker\Analysis;


use PhpParser\Node\Stmt\ClassMethod;

trait MarkerIgnoreNode
{
    protected function markMethodIgnoredForMarker(string $markerClass, ClassMethod $classMethodToIgnore): void
    {
        $currentList = $classMethodToIgnore->getAttribute(CustomAstAttribute::IGNORED_MARKERS->value, []);
        $currentList[$markerClass] = true;

        $classMethodToIgnore->setAttribute(CustomAstAttribute::IGNORED_MARKERS->value, $currentList);
    }

    protected function isMethodIgnoredForMarker(string $markerClass, ClassMethod $classMethodToIgnore): bool
    {
        return isset($classMethodToIgnore->getAttribute(CustomAstAttribute::IGNORED_MARKERS->value, [])[$markerClass]);
    }
}