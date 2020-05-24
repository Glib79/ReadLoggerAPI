<?php
declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class StatusDto extends BaseDto
{
    public const STATUS_PLANNED = 1;
    public const STATUS_DURING = 2;
    public const STATUS_FINISHED = 3;
    public const STATUS_ABANDONED = 4;
    
    public const STATUSES_WITH_START_DATE = [
        self::STATUS_DURING,
        self::STATUS_FINISHED,
        self::STATUS_ABANDONED
    ];
    
    public const STATUSES_WITH_END_DATE = [
        self::STATUS_FINISHED
    ];
    
    /**
     * @Groups({
     *     BaseDto::GROUP_LIST,
     *     BaseDto::GROUP_LOG, 
     *     BaseDto::GROUP_SINGLE 
     * })
     * @Assert\NotBlank(groups={BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="int", groups={BaseDto::GROUP_UPDATE})
     * @var int
     */
    public $id;
    
    /**
     * @Groups({
     *     BaseDto::GROUP_CREATE,
     *     BaseDto::GROUP_UPDATE
     * })
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="string", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var string
     */
    public $name;

    /**
     * @Groups({
     *     BaseDto::GROUP_CREATE,
     *     BaseDto::GROUP_LIST,
     *     BaseDto::GROUP_SINGLE,
     *     BaseDto::GROUP_UPDATE
     * })
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="string", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var string
     */
    public $translationKey;
}
