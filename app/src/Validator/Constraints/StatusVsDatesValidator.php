<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use App\DTO\StatusDto;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class StatusVsDatesValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof StatusVsDates) {
            throw new UnexpectedTypeException($constraint, StatusVsDates::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof StatusDto) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'StatusDto');
        }
  
        if (in_array($value->id, StatusDto::STATUSES_WITH_START_DATE) && !$this->context->getRoot()->startDate) {
            $this->context->buildViolation($constraint->messageStartDate)
                ->addViolation();
        }

        if (in_array($value->id, StatusDto::STATUSES_WITH_END_DATE) && !$this->context->getRoot()->endDate) {
            $this->context->buildViolation($constraint->messageEndDates)
                ->addViolation();
        }
    }
}
