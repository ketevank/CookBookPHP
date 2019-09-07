<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ingredient
 *
 * @ORM\Table(name="ingredient")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IngredientRepository")
 */
class Ingredient
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    // ...
    /**
     * Many Ingredients have Many Recipes.
     * @ORM\ManyToMany(targetEntity="Recipe", mappedBy="ingredients")
     */
    private $recipes;

    /**
     * @var string
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumn(name="user_name", referencedColumnName="name")
     */
    private $user;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Ingredient
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getRecipes()
    {
        return $this->recipes;
    }

    /**
     * @param mixed $recipes
     */
    public function setRecipes($recipes)
    {
        $this->recipes = $recipes;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getUser(): Users
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser(Users $user): void
    {
        $this->user = $user;
    }

}

