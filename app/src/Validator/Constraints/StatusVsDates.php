<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class StatusVsDates extends Constraint
{
    public $messageStartDate = 'Chosen status needs start date!';
    public $messageEndDates = 'Chosen status needs end date!';
}
