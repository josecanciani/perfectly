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

    function optimize() {
        $childs = $this->flat();
        $theOthers = [];
        foreach ($childs as $key => $child) {
            if ($child instanceof OperatorGroup) {
                $child->optimize();
                if ($this->getOperator() !== $child->getOperator()) {
                    $theOthers[] = $key;
                }
            }
        }
        $strings = $this->getStrings($childs);
        foreach ($theOthers as $other) {
            $this->removeDuplicates($childs, $strings, $other);
            if (count($childs[$other]) === 1) {
                $childs[$other] = $childs[$other][0];
            }
        }
        return $childs;
    }

    private function removeDuplicates(&$childs, $childStrings, $theOtherIdx) {
        $key = count($childs[$theOtherIdx]) - 1;
        $arrayCopy = null;
        while ($key > 0) {
            if (in_array($childs[$theOtherIdx][$key], $childStrings)) {
                if ($arrayCopy === null) {
                    $arrayCopy = $childs[$theOtherIdx]->getArrayCopy();
                }
                array_splice($arrayCopy, $key, 1);
            }
            $key--;
        }
        if ($arrayCopy !== null) {
            $childs[$theOtherIdx]->exchangeArray($arrayCopy);
        }
    }

    function get() {
        $childs = $this->optimize();
        $strings = $this->getStrings($childs);
        return implode(' ' . $this->getOperator() . ' ', array_unique($strings));
    }

    private function flat() {
        $childs = [];
        foreach ($this as $child) {
            if ($child instanceof OperatorGroup && $this->getOperator() === $child->getOperator()) {
                foreach ($child->flat() as $grandChild) {
                    $childs[] = $grandChild;
                }
            } else {
                $childs[] = $child;
            }
        }
        return $childs;
    }

    private function getStrings(array $childs) {
        $strings = [];
        foreach ($childs as $child) {
            if ($child instanceof OperatorGroup && $this->getOperator() !== $child->getOperator()) {
                $strings[] = '(' . $child->get() . ')';
            } else {
                $strings[] = (string) $child;
            }
        }
        return $strings;
    }

    function __toString() {
        return $this->get();
    }
}
