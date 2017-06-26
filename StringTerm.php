<?php

namespace Perfectly;

class StringTerm implements Term {
    private $term;

    function __construct($term) {
        if (!is_string($term) || !$term) {
            throw new \Exception('invalidStringTerm');
        }
        $this->term = $term;
    }

    function __toString() {
        return $this->term;
    }
}
