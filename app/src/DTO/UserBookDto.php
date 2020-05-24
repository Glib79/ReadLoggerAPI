<?php
declare(strict_types=1);

namespace App\DTO;

use App\Validator\Constraints as AppAssert;
use DateTime;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class UserBookDto extends BaseDto
{
    /**
     * @Groups({
     *     BaseDto::GROUP_LIST,
     *     BaseDto::GROUP_LOG, 
     *     BaseDto::GROUP_SINGLE
     * })
     * @Assert\NotBlank(groups={BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="Ramsey\Uuid\Uuid", groups={BaseDto::GROUP_UPDATE})
     * @var Uuid
     */
    public $id;

    /**
     * @Groups({
     *     BaseDto::GROUP_CREATE,
     *     BaseDto::GROUP_LOG
     * })
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="Ramsey\Uuid\Uuid", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var Uuid
     */
    public $userId;
    
    /**
     * @Groups({
     *     BaseDto::GROUP_CREATE,
     *     BaseDto::GROUP_LIST,
     *     BaseDto::GROUP_LOG,
     *     BaseDto::GROUP_SINGLE
     * })
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE})
     * @Assert\Type(type="App\DTO\BookDto", groups={BaseDto::GROUP_CREATE})
     * @var BookDto
     */
    public $book;

    /**
     * @Groups({
     *     BaseDto::GROUP_CREATE,
     *     BaseDto::GROUP_LIST,
     *     BaseDto::GROUP_LOG,
     *     BaseDto::GROUP_SINGLE,
     *     BaseDto::GROUP_UPDATE
     * })
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="App\DTO\StatusDto", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @AppAssert\StatusVsDates(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var StatusDto
     */
    public $status;

    /**
     * @Groups({
     *     BaseDto::GROUP_CREATE,
     *     BaseDto::GROUP_LIST,
     *     BaseDto::GROUP_LOG,
     *     BaseDto::GROUP_SINGLE,
     *     BaseDto::GROUP_UPDATE
     * })
     * @Assert\Type(type="DateTime", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var DateTime
     */
    public $startDate;

    /**
     * @Groups({
     *     BaseDto::GROUP_CREATE,
     *     BaseDto::GROUP_LIST,
     *     BaseDto::GROUP_LOG,
     *     BaseDto::GROUP_SINGLE,
     *     BaseDto::GROUP_UPDATE,
     * })
     * @Assert\Type(type="DateTime", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\GreaterThanOrEqual(
     *     propertyPath="startDate",
     *     groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE}
     * )
     * @var DateTime
     */
    public $endDate;

    /**
     * @Groups({
     *     BaseDto::GROUP_CREATE,
     *     BaseDto::GROUP_LIST,
     *     BaseDto::GROUP_LOG,
     *     BaseDto::GROUP_SINGLE,
     *     BaseDto::GROUP_UPDATE
     * })
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="App\DTO\FormatDto", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var FormatDto
     */
    public $format;

    /**
     * @Groups({
     *     BaseDto::GROUP_CREATE,
     *     BaseDto::GROUP_LOG,
     *     BaseDto::GROUP_SINGLE,
     *     BaseDto::GROUP_UPDATE
     * })
     * @Assert\Type(type="int", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var int
     */
    public $rating;

    /**
     * @Groups({
     *     BaseDto::GROUP_CREATE,
     *     BaseDto::GROUP_LOG,
     *     BaseDto::GROUP_SINGLE,
     *     BaseDto::GROUP_UPDATE
     * })
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Type(type="App\DTO\LanguageDto", groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @var LanguageDto
     */
    public $language;

    /**
     * @Groups({
     *     BaseDto::GROUP_CREATE,
     *     BaseDto::GROUP_LOG,
     *     BaseDto::GROUP_SINGLE,
     *     BaseDto::GROUP_UPDATE
     * })
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
