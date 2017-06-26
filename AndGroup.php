<?php

namespace Perfectly;

class AndGroup extends OperatorGroup {
    const TOKEN = 'AND';

    function getOperator() {
        return static::TOKEN;
    }
}
