<?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'autoload.php');

function assertEqual(array &$results, $test, $expected, $msg = null) {
    if ($test === $expected) {
        $results['ok']++;
    } else {
        $results['error']++;
        echo $results['error'] . ') Assertion failed. Found: ' . PHP_EOL . $test . PHP_EOL . 'when expecting:' . PHP_EOL . $expected . PHP_EOL . PHP_EOL;
    }
}

try {
    $results = [
        'ok' => 0,
        'error' => 0
    ];
    $tests = [
        ['logic' => '(A AND (B OR C))', 'expected' => 'A AND (B OR C)'],
        ['logic' => '((((A))) and ((B) and C))', 'expected' => 'A AND B AND C'],
        ['logic' => '((((A))) and ((B) OR C))', 'expected' => 'A AND (B OR C)'],
        ['logic' => '((((A))) OR ((B) OR C))', 'expected' => 'A OR B OR C'],
        ['logic' => '((((A)) OR D) AND ((B) OR C))', 'expected' => '(A OR D) AND (B OR C)'],
        ['logic' => '((((A))) and ((B) and A))', 'expected' => 'A AND B'],
        ['logic' => '((((D))) and ((B) OR A OR ((B))))', 'expected' => 'D AND (B OR A)'],
        ['logic' => '((((D))) and ((B) OR A OR ((D))))', 'expected' => 'D AND (B OR A)'],
        ['logic' => '((((D))) AND ((B) AND A) AND ((B)))', 'expected' => 'D AND B AND A'],
        ['logic' => 'A and (B or A)', 'expected' => 'A AND B'],
        ['logic' => 'A or (B and A)', 'expected' => 'A OR B']
    ];
    $parser = new Perfectly\Parser();
    foreach ($tests as $test) {
        $logic = $parser->parse($test['logic']);
        assertEqual($results, $logic->get(), $test['expected']);
    }
    echo "Tests completed: $results[ok] passed, $results[error] errors." . PHP_EOL;
} catch (Exception $e) {
    echo 'Unexpected exception running tests: ' . $e->getMessage() . PHP_EOL;
}
