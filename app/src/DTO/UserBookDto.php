<?php
declare(strict_types=1);

namespace App\DTO;

use App\DTO\BookDto;
use App\DTO\FormatDto;
use App\DTO\LanguageDto;
use DateTime;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class UserBookDto extends BaseDto
{
    /**
     * @Groups({BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST})
     * @Assert\NotBlank(groups={BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="object", groups={BaseDto::GROUP_UPDATE})
     * @var Uuid
     */
    public $id;

    /**
     * @Groups({BaseDto::GROUP_CREATE})
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="object", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var Uuid
     */
    public $userId;
    
    /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST})
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE})
     * @Assert\Type(type="object", groups={BaseDto::GROUP_CREATE})
     * @var BookDto
     */
    public $book;

    /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST})
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="object", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var StatusDto
     */
    public $status;

    /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST})
     * @var DateTime
     */
    public $startDate;

    /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST})
     * @var DateTime
     */
    public $endDate;

     /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE, BaseDto::GROUP_LIST})
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="object", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var FormatDto
     */
    public $format;

     /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE})
     * @Assert\Type(type="int", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var int
     */
    public $rating;

     /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE})
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="object", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var LanguageDto
     */
    public $language;

    /**
     * @Groups({BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE, BaseDto::GROUP_SINGLE})
     * @Assert\Type(type="string", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var string
     */
    public $notes;
    
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
