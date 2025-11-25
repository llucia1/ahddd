<?php
declare(strict_types=1);

namespace GridCP\Net\Ip4Tag\Domain\Exception;

use Exception;

class Ip4TagInsertErrorDBException extends Exception
{
    public function __construct(string $message = 'Error inserting IP4 tag into the database')
    {
        parent::__construct('Tag Ip4 Error Insert DB -> ' . $message);  
    }

}