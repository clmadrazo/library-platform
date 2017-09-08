<?php
namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="roles_users")
 */
class RoleUser
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
     * Role table reference
     *
     * @ORM\ManyToOne(targetEntity="User\Entity\Role", fetch="EXTRA_LAZY")
     */
    protected $role;

    /**
     * User table reference
     *
     * @ORM\ManyToOne(targetEntity="User\Entity\User", fetch="EXTRA_LAZY")
     */
    protected $user;

    
    public function getId()
    {
        return $this->id;
    }

    public function getRole()
    {
        return $this->role;
    }
    
    public function setRole(Role $role)
    {
        $this->role = $role;
        return $this;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    
    /**
     * @see Library\Mvc\Entity\BaseEntity
     */
    public function getExpectedArray($params = array())
    {
        return array(
            'id'         => $this->getId(),
            'role_id'    => $this->getRole()->getId(),
            'role'       => $this->getRole()->getExpectedArray(),
            'user'       => $this->getUser()->getExpectedArray(),
        );
    }
}
