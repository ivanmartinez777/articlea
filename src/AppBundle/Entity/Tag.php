<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * Tag
 *
 * @ORM\Table(name="tag")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TagRepository")
 */
class Tag
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
     * @ORM\Column(name="Nombre", type="string", length=25, nullable=true)
     */
    private $nombre;


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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Tag
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }



    /**
     * @ORM\ManyToMany(targetEntity="Texto", mappedBy="tags", cascade={"persist"})
     **/
    private $textos;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    /**
     * @param Texto $texxto
     */
    public function addTextoTags(Texto $texto)
    {
        if (!$this->articles->contains($texto)) {
            $this->articles->add($texto);
            $texto->addTag($this);
        }
    }

    /**
     * @return array
     */
    public function getTextoTags()
    {
        return $this->textos->toArray();
    }

    /**
     * @param Texto $texto
     */
    public function removeTextosTag(Texto $texto)
    {
        if (!$this->textos->contains($texto)) {
            return;
        }
        $this->textos->removeElement($texto);
        $texto->removeTag($this);
    }

    /**
     *
     */
    public function removeAllTextosTags()
    {
        $this->textos->clear();
    }
}

