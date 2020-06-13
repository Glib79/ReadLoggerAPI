<?php
declare(strict_types=1);

namespace App\DTO;

use DateTime;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class UserDto extends BaseDto
{
    /**
     * @Groups({
     *     BaseDto::GROUP_LIST,
     *     BaseDto::GROUP_LOG, 
     *     BaseDto::GROUP_SINGLE,
     *     BaseDto::GROUP_UPDATE
     * })
     * @var Uuid
     */
    public $id;
    
    /**
     * @Groups({
     *     BaseDto::GROUP_CREATE, 
     *     BaseDto::GROUP_LIST,
     *     BaseDto::GROUP_LOG,
     *     BaseDto::GROUP_SINGLE 
     * })
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_CONFIRM})
     * @Assert\Email(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_CONFIRM})
     * @var string
     */
    public $email;
    
    /**
     * @Groups({
     *     BaseDto::GROUP_CREATE,
     *     BaseDto::GROUP_UPDATE
     * })
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE})
     * @Assert\Regex(
     *     pattern="/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{7,20}$/",
     *     message="Password does not meet our standards",
     *     groups={BaseDto::GROUP_CREATE, BaseDto::GROUP_UPDATE}
     * )
     * @var string
     */
    public $password;
    
    /**
     * @Groups({
     *     BaseDto::GROUP_LOG,
     *     BaseDto::GROUP_SINGLE
     * })
     * @var array
     */
    public $roles;
    
    /**
     * @Groups({
     *     BaseDto::GROUP_LOG
     * })
     * @var bool 
     */
    public $isActive;

    /**
     * @Groups({
     *     BaseDto::GROUP_LOG,
     *     BaseDto::GROUP_SINGLE
     * })
     * @var bool
     */
    public $isConfirmed;

    /**
     * @Groups({
     *     BaseDto::GROUP_LOG
     * })
     * @Assert\NotBlank(groups={BaseDto::GROUP_CONFIRM})
     * @var string 
     */
    public $token;

    /**
     * @Groups({
     *     BaseDto::GROUP_LOG
     * })
     * @Assert\NotBlank(groups={BaseDto::GROUP_CREATE})
     * @var string 
     */
    public $language;
}
