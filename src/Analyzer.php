<?php


namespace BaptisteContreras\TaintChecker;


use BaptisteContreras\TaintChecker\Analysis\ClassAnalysis;
use BaptisteContreras\TaintChecker\Analysis\Strategy\HttpFoundationRequestStrategy;
use PhpParser\ParserFactory;

class Analyzer
{
    public function start(): void
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        try {
            $ast = $parser->parse(file_get_contents(sprintf('%s/../demo/DemoController.php', __DIR__)));

            $strategies = [new HttpFoundationRequestStrategy()];

            $classAnalysis = new ClassAnalysis($ast, $strategies);

            $classAnalysis->start();

            dd($classAnalysis->getTainted());
        } catch (\Throwable $error) {
            dd($error->getMessage());
        }
    }

}