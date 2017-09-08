<?php
namespace User\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Library\Mvc\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="access_tokens")
 */
class AccessToken extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * Users table reference
     *
     * @ORM\ManyToOne(targetEntity="User\Entity\User", fetch="EXTRA_LAZY")
     */
    protected $user;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $value;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $refresh;

    /**
     * Created time
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    
    /**
     * 
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager = null)
    {
        $this->created = new DateTime();
        
        parent::__construct($entityManager);
    }
        
    public function getId()
    {
        return $this->id;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }
    
    public function getRefresh()
    {
        return $this->refresh;
    }

    public function setRefresh($refresh)
    {
        $this->refresh = $refresh;
    }

    /**
     * @param User $user
     * @return AccessToken
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return User\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * @param String $created
     * @example 2001-12-23
     * @return AccessToken
     */
    public function setCreated()
    {
        $now = new \DateTime(date('y-m-d H:i:s'));
        $created = date_format($now, 'y-m-d H:i:s');
        $this->created = new DateTime($created);
        return $this;
    }

    /**
     * @return Object date
     */
    public function getCreated()
    {
        return $this->created;
    }
}
