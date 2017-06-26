
# Perfectly Logic

A simple PHP library to parse and simplify boolean logic

# Features

## Parsing

* Support AND and OR boolean logics
* Validates only one of AND or OR is used per group
* Finds missing or extra parenthesis

Restrictions: tokens need to be simple texts [a-zA-Z0-9-_].

## Optimizations

* Remove extra parenthesis:
<pre>
(a OR b)              =>  a OR b
a AND ((b OR (c)))    =>  a AND (b OR c)
a AND (b AND (c))     =>  a AND b AND c
</pre>

* Remove duplicate terms in a group
<pre>
a OR b OR a           =>  a OR b
</pre>

* Detects redundant terms
<pre>
a AND b AND a         =>  a OR b
a AND (b OR a)        =>  a AND b
</pre>

# Usage

There's a composer config file in the project. But you can also use it manually by just cloning the project and including the autoload.php file:

<pre>
$ cat <<EOF | php
<?php
require_once('perfectly/autoload.php');

\$logic = Perfectly\Parser::parse('((((D))) and ((B) OR A OR ((B))))');
echo PHP_EOL . 'Simplified logic: ' . \$logic->get() . PHP_EOL;
EOF

Simplified logic: D AND (B OR A)
</pre>

# Testing suite

There's a very simple test suit that can be used by developers, and it provides a more detailed list of examples.
<pre>
$ php tools/test.php
Tests completed: 11 passed, 0 errors.
</pre>

# TODO

* Properly detect when operator or term is used: "OR a b" is valid, translates to "a OR b"
* Allow custom REGEXP for finding terms
* Improve exceptions messages
* Allow creating logics programatically, using objects instead of string terms (create an interface so objects can provide: a) a unique hash, and b) the string)
