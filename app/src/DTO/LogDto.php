<?php
declare(strict_types=1);

namespace App\DTO;

use DateTime;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class LogDto extends BaseDto
{
    public const ACTION_CONFIRM_EMAIL = 'confirm';
    public const ACTION_CREATE = 'create';
    public const ACTION_DELETE = 'delete';
    public const ACTION_UPDATE = 'update';
    
    /**
     * @Groups({BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST})
     * @Assert\NotBlank(groups={BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="Ramsey\Uuid\Uuid", groups={BaseDto::GROUP_UPDATE})
     * @var Uuid
     */
    public $id;
    
    /**
     * @Groups({BaseDto::GROUP_CREATE})
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="Ramsey\Uuid\Uuid", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var Uuid
     */
    public $userId;

    /**
     * @Groups({BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST})
     * @var DateTime
     */
    public $happenedAt;
    
    /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST})
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="string", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var string
     */
    public $action;

    /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE})
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="string", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var string
     */
    public $table;

    /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="Ramsey\Uuid\Uuid", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var Uuid
     */
    public $recordId;
    
    /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST})
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="array", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var array
     */
    public $value;
}
