<?php

namespace Perfectly;

class Parser {
    const MAX_TERMS_PER_GROUP = 20;
    const TERM_REGEXP = '/([a-zA-Z0-9-_]+)(\s|\(|\))?(.*)?$/';

    function parse($logicString) {
        if (!is_string($logicString) || !$logicString) {
            throw new \Exception('invalidStringTerm');
        }
        list($term, $rest) = $this->getNextTermAndRest($logicString);
        if ($rest) {
            throw new \Exception('unknownParsingError');
        }
        return $term;
    }

    function getNextTermAndRest($logic) {
        $terms = [];
        $iterator = new Parser__Iterator($logic);
        foreach ($iterator as $term) {
            $terms[] = $term;
        }
        if ($iterator->getOperator()) {
            $result = OperatorGroup::factory($iterator->getOperator(), $terms);
        } elseif (count($terms) === 1) {
            $result = $terms[0];
        } else {
            throw new \Exception('errorParsingLogic: ' . $logic); // FIXME: provide better error feedback
        }
        return [$result, $iterator->getRest()];
    }
}

class Parser__Iterator implements \Iterator {
    private $logic;
    private $letter;
    private $key;
    private $term;
    private $rest;
    private $operator;

    function __construct($logic) {
        $this->logic = $logic;
    }

    function current() {
        return $this->term;
    }

    function key() {
        return $this->key;
    }

    function next() {
        $this->key++;
        if ($this->key > Parser::MAX_TERMS_PER_GROUP) {
            throw new ParserException('logicTooComplex');
        }
        $this->_next();
    }

    private function _next() {
        $this->letter = $this->rest ? substr($this->rest, 0, 1) : null;
        if ($this->letter && $this->letter !== ')') {
            $this->getTerm();
        } else {
            $this->term = null;
        }
    }

    function rewind() {
        $this->rest = ltrim($this->logic);
        $this->key = -1;
        $this->next();
    }

    function valid() {
        return !!$this->term;
    }

    function getRest() {
        if ($this->valid()) {
            throw new \Exception('cannotCallMethodNow');
        }
        return $this->_getRest();
    }

    private function _getRest() {
        return $this->letter === null ? null : ltrim(substr($this->rest, 1));
    }

    private function getTerm() {
        if ($this->letter === '(') {
            $subParser = new Parser();
            list($this->term, $this->rest) = $subParser->getNextTermAndRest($this->_getRest());
        } elseif (preg_match(Parser::TERM_REGEXP, $this->rest . ' ', $matches)) {
            $this->rest = ltrim($matches[2] . $matches[3]);
            if (OperatorGroup::isValidOperator(strtoupper($matches[1]))) {
                $this->setOperator(strtoupper($matches[1]));
                $this->_next();
            } else {
                $this->term = new StringTerm($matches[1]);
            }
        } else {
            throw new ParserException('invalidLogicString'); // FIXME: provide better error feedback
        }
    }


    function setOperator($operator) {
        if ($this->operator !== null && $this->operator !== $operator) {
            throw new ParserException('cannotMixOperatorsInGroup ' . $this->operator . ' ' . $operator);
        }
        $this->operator = $operator;
    }

    function getOperator() {
        return $this->operator;
    }
}
