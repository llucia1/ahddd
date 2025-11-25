<?php
namespace GridCP\Common\Domain\Utils;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CIDRValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (is_array($value)) {
            foreach ($value as $ip) {
                if (!$this->isValidIpOrCidr($ip)) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ value }}', $ip)
                        ->addViolation();
                }
            }
        } else {
            if (!$this->isValidIpOrCidr($value)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $value)
                    ->addViolation();
            }
        }
    }

    private function isValidIpOrCidr($ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return true;
        }

        $parts = explode('/', $ip);
        if (count($parts) === 2 && filter_var($parts[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $netmask = (int) $parts[1];
            return $netmask >= 0 && $netmask <= 32;
        }

        return false;
    }
}