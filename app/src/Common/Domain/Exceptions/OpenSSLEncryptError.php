<?php
declare(strict_types=1);
namespace GridCP\Common\Domain\Exceptions;

use Exception;

final class OpenSSLEncryptError extends Exception
{
    public function __construct()
    {
        parent::__construct(sprintf('Error Encrypt Data'));
    }

}