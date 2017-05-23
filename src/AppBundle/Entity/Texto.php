<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Trascastro\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Texto
 *
 * @ORM\Table(name="texto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TextoRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class Texto
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }



    //Titulo

    /**
     * @var string
     *@Assert\NotBlank()
     * @ORM\Column(name="titulo", type="string", length=255)
     */
    private $titulo;

    /**
     * Set titulo
     *
     * @param string $titulo
     *
     * @return Texto
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }


    //Cuerpo

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 700,
     *      minMessage = "El texto debe contener como mínimo {{ limit }} caracteres"
     * )
     * @ORM\Column(name="cuerpo", type="text")
     *
     */
    private $cuerpo;

    /**
     * @var string
     * @ORM\Column(name="ejemplo", type="text")
     */
    private $ejemplo;

    /**
     * Set cuerpo
     *
     * @param string $cuerpo
     *
     * @return Texto
     */
    public function setCuerpo($cuerpo)
    {
        $this->cuerpo = $cuerpo;

        return $this;
    }

    /**
     * Get cuerpo
     *
     * @return string
     */
    public function getCuerpo()
    {
        return $this->cuerpo;
    }



    //Categoria

    /**
     * @var mixed
     * @ORM\Column(name="categoria", type="text")
     */
    private $categoria;

    /**
     * @return mixed
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * @param mixed $categoria
     */
    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;
    }

    //CreatedAt


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Texto
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    //UpdatedAt


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * Set updatedAt
     *
     * @ORM\PreUpdate()
     *
     * @return Texto
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    //Author

    /**
     * @ORM\ManyToOne(targetEntity="Trascastro\UserBundle\Entity\User", inversedBy="textos", cascade={"persist"})
     */
    private $author;

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }


    //Comentarios


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comentario", mappedBy="texto", cascade={"remove"} )
     */

    private $comentarios;



    //Tags

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="textos", cascade={"persist"})
     * @ORM\JoinTable(name="textos_tags")
     *          joinColumns={@ORM\JoinColumn(name="texto_id", referencedColumnName="id")},
     *          inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     **/
    private $tags;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RevistaTexto", mappedBy="texto", cascade={"all"})
     */

    private $revistaTexto;

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags->toArray();
    }



    /**
     * @param Tag|null $tag
     */
    public function addTag(Tag $tag = null)
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addTextoTags($this);
        }
    }

    /**
     * @param Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        if (!$this->tags->contains($tag)) {
            return;
        }
        $this->tags->removeElement($tag);
        $tag->removeTextosTag($this);
    }




    //Numero de visitas

    /**
     * @ORM\Column(name="numVisitas", type="integer")
     */
    private $numVisitas;

    public function setNumVisitas()
    {
        $this->numVisitas = $this->getNumVisitas()+1;
    }

    public function getNumVisitas()
    {
        return $this->numVisitas;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="images_upload", fileNameProperty="image",nullable=true)
     * @var File
     */
    private $imageFile;


    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getImage()
    {
        return $this->image;
    }



    public function setEjemplo($string)
    {

       $ejemploTexto = strtok($string, ' ').' '.strtok(' ').' '.strtok(' ').' '.
           strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.
           strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.
           strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.
           strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.
           strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.
           strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.
           strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.
           strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.
           strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.
           strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.
           strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.
           strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.strtok(' ').' '.
           strtok(' ').' '.strtok(' ').' '.strtok(' '). ' ...';
        $this->ejemplo = $ejemploTexto;
        return $this;
    }

    public function getEjemplo()
    {
        return $this->ejemplo;
    }


    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = $this->createdAt;
        $this->tags = new ArrayCollection();
        $this->numVisitas = 0;
        $this->ejemplo = "";



    }



}
