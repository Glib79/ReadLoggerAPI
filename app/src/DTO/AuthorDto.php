<?php
declare(strict_types=1);

namespace App\DTO;

use DateTime;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class AuthorDto extends BaseDto
{
    /**
     * @Groups({BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST, BaseDto::GROUP_AUTOSUGGEST})
     * @Assert\NotBlank(groups={BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="object", groups={BaseDto::GROUP_UPDATE})
     * @var Uuid
     */
    public $id;
    
    /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST, BaseDto::GROUP_AUTOSUGGEST})
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="string", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var string
     */
    public $firstName;

    /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST, BaseDto::GROUP_AUTOSUGGEST})
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="string", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var string
     */
    public $lastName;

    /**
     * @Groups({BaseDto::GROUP_SINGLE})
     * @var DateTime
     */
    public $createdAt;
    
    /**
     * @Groups({BaseDto::GROUP_SINGLE})
     * @var DateTime
     */
    public $modifiedAt;
}
