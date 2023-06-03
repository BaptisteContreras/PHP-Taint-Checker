<?php


namespace BaptisteContreras\TaintChecker\Analysis;


use PhpParser\Node;

class TaintedVariable
{
    private int $startLine;
    public function __construct(
        private readonly string $name,
       Node $node,
        private ?int $untilLine = null
    ){
        $this->startLine = $node->getLine();
    }

    public function setUntilLine(int $untilLine): void
    {
        $this->untilLine = $untilLine;
    }

    public function getUntilLine(): ?int
    {
        return $this->untilLine;
    }

    public function getStartLine(): int
    {
        return $this->startLine;
    }





}