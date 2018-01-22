<?php

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Client", mappedBy="creator")
     */
    private $clients;

    public function __construct()
    {
        parent::__construct();

        $this->clients = new ArrayCollection();
    }

    /**
     * Add client.
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return User
     */
    public function addClient(\AppBundle\Entity\Client $client)
    {
        $this->clients[] = $client;

        return $this;
    }

    /**
     * Remove client.
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeClient(\AppBundle\Entity\Client $client)
    {
        return $this->clients->removeElement($client);
    }

    /**
     * Get clients.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClients()
    {
        return $this->clients;
    }
}
