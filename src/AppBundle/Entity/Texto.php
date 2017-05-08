<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Trascastro\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Texto
 *
 * @ORM\Table(name="texto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TextoRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\Column(name="cuerpo", type="text")
     */
    private $cuerpo;

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


    //PagPrincipales


    /**
     * @ORM\ManyToMany(targetEntity="Trascastro\UserBundle\Entity\User", inversedBy="textosPagPrincipal")
     * @ORM\JoinTable(name="textos_usuarios",
     *      joinColumns={@ORM\JoinColumn(name="texto_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="usuario_id", referencedColumnName="id")}
     *      )
     */
    private $pagPrincipales;

    /**
     * @param User $user
     */
    public function addPag(User $user)
    {
        if (!$this->pagPrincipales->contains($user)) {
            $this->pagPrincipales->add($user);
            $user->addTextoPag($this);
        }
    }

    /**
     * @return array
     */
    public function getTPag()
    {
        return $this->pagPrincipales->toArray();
    }

    /**
     * @param User $user
     */
    public function removePagPrincipal(User $user)
    {
        if (!$this->pagPrincipales->contains($user)) {
            return;
        }
        $this->pagPrincipales->removeElement($user);
        $user->removeTextosPagPrincipal($this);
    }

    /**
     *
     */
    public function removeAllPagPrincipal()
    {
        $this->pagPrincipales->clear();
    }


    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = $this->createdAt;
        $this->pagPrincipales = new ArrayCollection();

    }

}

