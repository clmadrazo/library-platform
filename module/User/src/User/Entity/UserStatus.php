<?php

namespace User\Entity;

use Library\Mvc\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity Class representing the User's Status.
 *
 * @ORM\Entity
 * @ORM\Table(name="users_status")
 */
class UserStatus extends BaseEntity {

    const ID_BANNED = 1;
    const ID_ACTIVE = 2;
    const ID_PENDING = 3;
    const ID_SUSPENDED = 4;
    const ID_BLOCKED = 5;
    const ID_NEW = 6;

    /**
     * Primary Identifier
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Name
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * Slug
     * @ORM\Column(type="string")
     */
    protected $slug;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $name
     * @return UserStatus
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSlug() {
        return $this->slug;
    }

    /**
     * @see Library\Mvc\Entity\BaseEntity
     */
    public function getExpectedArray($params = array()) {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
        );
    }

}
