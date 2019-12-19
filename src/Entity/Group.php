<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseGroup as BaseGroup;

/**
 * Class Group.
 *
 * @category Entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 * @ORM\Table(name="admin_group")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class Group extends BaseGroup
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Methods.
     */

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
