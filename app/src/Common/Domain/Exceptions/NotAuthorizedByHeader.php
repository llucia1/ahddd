<?php
declare(strict_types=1);
namespace GridCP\Common\Domain\Exceptions;
use Exception;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final class NotAuthorizedByHeader extends UnauthorizedHttpException
{
    public function __construct()
    {
        parent::__construct(sprintf('The User is not authorized to access this resource.'));
    }
}