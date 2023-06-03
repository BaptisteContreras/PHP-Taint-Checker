<?php


namespace BaptisteContreras\TaintChecker\Analysis;


use PhpParser\Node;

class GlobalContext
{
    /**
     * @var array<string, array<TaintedVariable>>
     */
    private array $taintedVariables = [];

    /**
     * @param array<string, mixed> $internalState
     */
    public function __construct(private array $internalState = [])
    {
    }


    public function setKey(string $key, $value): self
    {
        $this->internalState[$key] = $value;

        return $this;
    }

    public function getKey(string $key, $default = null)
    {
        if (array_key_exists($key, $this->internalState)) {
            return $this->internalState[$key];
        }

        return $default;
    }

    public function addTaintedVariable(string $name, string $methodName, Node $node): self
    {
        if (!isset($this->taintedVariables[$methodName][$name]) || empty($this->taintedVariables[$methodName][$name])) {
            $this->taintedVariables[$methodName][$name] = [new TaintedVariable($name, $node)];

            return $this;
        }

        /** @var TaintedVariable $lastNamesake */
        $lastNamesake = end($this->taintedVariables[$methodName][$name]);

        if ($lastNamesake->getUntilLine() === null) {
            $lastNamesake->setUntilLine($node->getLine() - 1);
        }

        $this->taintedVariables[$methodName][$name][] = new TaintedVariable($name, $node);

        return $this;
    }

    public function getTaintedVariables(): array
    {
        return $this->taintedVariables;
    }


}