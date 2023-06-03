<?php


namespace BaptisteContreras\TaintChecker\Analysis;


use BaptisteContreras\TaintChecker\Analysis\Phase\Classify\ClassifyStrategyInterface;
use BaptisteContreras\TaintChecker\Analysis\Phase\Mark\MarkStrategyInterface;
use BaptisteContreras\TaintChecker\Analysis\Phase\Mark\RequestMarkerVisitor;
use BaptisteContreras\TaintChecker\Analysis\Phase\Prepare\PrepareStrategyInterface;
use BaptisteContreras\TaintChecker\Analysis\Phase\Propagate\PropagateStrategyInterface;
use BaptisteContreras\TaintChecker\Analysis\Strategy\AnalysisStrategyInterface;
use BaptisteContreras\TaintChecker\Analysis\Visitor\Enhancer\AddGlobalContextVisitor;
use BaptisteContreras\TaintChecker\Analysis\Visitor\Propagator\PropagateTaintVisitor;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;

class ClassAnalysis
{
    private readonly GlobalContext $globalContext;
    private bool $hasRun = false;

    /**
     * @param array<Stmt> $ast
     * @param array<AnalysisStrategyInterface> $analysisStrategies
     */
    public function __construct(
        private readonly array $ast,
        private readonly array $analysisStrategies
    ){
        $this->globalContext = new GlobalContext();
    }


    public function start(): void
    {
        if (!$this->hasRun) {
            $this
                ->addGlobalContext()
                ->applyPreparePhase()
                ->applyMarkPhase()
                ->applyPropagatePhase()
                ->applyClassifyPhase();

            $this->hasRun = true;
        }
    }

    public function getTainted(): array
    {
        dd($this->globalContext->getTaintedVariables());
    }


    private function addGlobalContext(): self
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new AddGlobalContextVisitor($this->globalContext));
        $nodeTraverser->traverse($this->ast);

        return $this;
    }

    private function applyPreparePhase(): self
    {
        $nodeTraverser = new NodeTraverser();

        foreach ($this->analysisStrategies as $analysisStrategy) {
            $analysisStrategy->doPrepare($this->ast, $nodeTraverser);
        }

        $nodeTraverser->traverse($this->ast);

        return $this;
    }

    private function applyMarkPhase(): self
    {
        $nodeTraverser = new NodeTraverser();

        foreach ($this->analysisStrategies as $analysisStrategy) {
            $analysisStrategy->doMark($this->ast, $nodeTraverser);
        }

        $nodeTraverser->traverse($this->ast);

        return $this;
    }

    private function applyPropagatePhase(): self
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new PropagateTaintVisitor());

        foreach ($this->analysisStrategies as $analysisStrategy) {
            $analysisStrategy->doPropagate($this->ast, $nodeTraverser);
        }

        $nodeTraverser->traverse($this->ast);

        return $this;
    }

    private function applyClassifyPhase(): void
    {
        $nodeTraverser = new NodeTraverser();

        foreach ($this->analysisStrategies as $analysisStrategy) {
            $analysisStrategy->doClassify($this->ast, $nodeTraverser);
        }

        $nodeTraverser->traverse($this->ast);
    }
}