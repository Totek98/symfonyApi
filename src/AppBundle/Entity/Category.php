<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class Category
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="category_name", type="string", length=255)
     */
    protected $category_name;

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="category")
     */
    private $posts;


    public function getId()
    {
        return $this->id;
    }

    /**
     * Set category_name
     *
     * @param string $category_name
     *
     */
    public function setCategoryName($category_name)
    {
        $this->category_name = $category_name;

        return $this;
    }

    /**
     * Get category_name
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->category_name;
    }

    public function setDate()
    {
        $this->created = new \DateTime();

        return $this;
    }

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }
}
