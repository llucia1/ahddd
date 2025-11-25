<?php
namespace GridCP\Common\Domain\Utils;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CIDR extends Constraint
{
    public string $message = 'The IP "{{ value }}" is not a valid IPv4 address or CIDR notation.';
}