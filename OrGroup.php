<?php

namespace Perfectly;

class  OrGroup extends OperatorGroup {
    const TOKEN = 'OR';

    function getOperator() {
        return static::TOKEN;
    }
}
