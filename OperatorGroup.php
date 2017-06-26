<?php

namespace Perfectly;

abstract class OperatorGroup extends \ArrayObject implements Term {
    abstract function getOperator();

    static function factory($operator, $terms) {
        switch ($operator) {
            case AndGroup::TOKEN:
                return new AndGroup($terms);
            case OrGroup::TOKEN:
                return new OrGroup($terms);
        }
        throw new \Exception('invalidOperator');
    }

    static function isValidOperator($string) {
        return in_array($string, [AndGroup::TOKEN,  OrGroup::TOKEN]);
    }

    function get() {
        return implode(' ' . $this->getOperator() . ' ', array_unique($strings));
    }

    function __toString() {
        return $this->get();
    }
}
