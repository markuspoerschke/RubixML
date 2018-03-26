<?php

include __DIR__ . '/../vendor/autoload.php';

use Rubix\Engine\CART;
use Rubix\Engine\Test;
use Rubix\Engine\Pipeline;
use Rubix\Engine\Math\Stats;
use Rubix\Engine\Math\Random;
use Rubix\Engine\SupervisedDataset;
use League\Csv\Reader;

$minSize = $argv[1] ?? 3;
$maxDepth = $argv[2] ?? PHP_INT_MAX;

echo '╔═════════════════════════════════════════════════════╗' . "\n";
echo '║                                                     ║' . "\n";
echo '║ Iris Classifier using CART model                    ║' . "\n";
echo '║                                                     ║' . "\n";
echo '╚═════════════════════════════════════════════════════╝' . "\n";

$dataset = Reader::createFromPath(dirname(__DIR__) . '/datasets/iris.csv')->setDelimiter(',');

$dataset = new SupervisedDataset(iterator_to_array($dataset));

list ($training, $testing) = $dataset->randomize()->split(0.2);

$estimator = new CART($minSize, $maxDepth);

echo 'Training a CART ... ';

$start = microtime(true);

$estimator->train($training->samples(), $training->outcomes());

echo 'done in ' . (string) round(microtime(true) - $start, 5) . ' seconds.' . "\n";

echo  "\n";

$test = new Test($estimator);

echo 'Testing model ...';

$start = microtime(true);

$accuracy = $test->accuracy($testing->samples(), $testing->outcomes());

echo 'done in ' . (string) round(microtime(true) - $start, 5) . ' seconds.' . "\n";

echo  "\n";

echo 'Model is ' . (string) $accuracy . '% accurate.' . "\n";

echo  "\n";

echo 'Random Sample Input' . "\n";

$sample = [
    Random::float(4.0, 8.0),
    Random::float(2.0, 4.0),
    Random::float(1.0, 7.0),
    Random::float(0.0, 3.0),
];

echo 'Sepal size: ' . $sample[0] . 'cm X ' . $sample[1] . 'cm' . "\n";
echo 'Petal size: ' . $sample[2] . 'cm X ' . $sample[3] . 'cm' . "\n";

echo  "\n";

echo 'Making prediction ... ';

$start = microtime(true);

$prediction = $estimator->predict($sample);

echo 'done in ' . (string) round(microtime(true) - $start, 5) . ' seconds.' . "\n";

echo  "\n";

echo 'Outcome: ' . $prediction['outcome'] . "\n";
echo 'Certainty: ' . $prediction['certainty'] . "\n";
