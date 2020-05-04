<?php
declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class StatusDto extends BaseDto
{
    /**
     * @Groups({BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST})
     * @Assert\NotBlank(groups={BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="int", groups={BaseDto::GROUP_UPDATE})
     * @var int
     */
    public $id;
    
    /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE})
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="string", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var string
     */
    public $name;

    /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST})
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="string", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var string
     */
    public $translationKey;
}
