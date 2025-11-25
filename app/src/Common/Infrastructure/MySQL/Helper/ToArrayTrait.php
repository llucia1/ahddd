<?php

namespace GridCP\Common\Infrastructure\MySQL\Helper;

trait ToArrayTrait
{
    /**
     * toArray
     *
     * @return array
     */
    public function toArray(): array
    {
        $zval = get_object_vars($this);

        return($zval);
    }
}