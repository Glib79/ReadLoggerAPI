<?php
declare(strict_types=1);

namespace App\DTO;

use DateTime;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class BookDto extends BaseDto
{
    /**
     * @Groups({BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST, BaseDto::GROUP_AUTOSUGGEST})
     * @Assert\NotBlank(groups={BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="Ramsey\Uuid\Uuid", groups={BaseDto::GROUP_UPDATE})
     * @var Uuid
     */
    public $id;
    
    /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST, BaseDto::GROUP_AUTOSUGGEST})
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="string", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var string
     */
    public $title;

    /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST})
     * @Assert\Type(type="string", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var string
     */
    public $subTitle;

    /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE})
     * @Assert\Type(type="int", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var int
     */
    public $size;
    
    /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST, BaseDto::GROUP_AUTOSUGGEST})
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="array", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var array
     */
    public $authors;
    
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
