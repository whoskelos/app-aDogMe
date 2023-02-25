<?php

namespace App\Entity;

use App\Repository\PerroRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PerroRepository::class)
 */
class Perro
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $edad;

    /**
     * @ORM\Column(type="float")
     */
    private $peso;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tamanyo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $foto;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="perros")
     */
    private $usuario;

    /**
     * @ORM\Column(type="boolean")
     */
    private $adoptado;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getEdad(): ?string
    {
        return $this->edad;
    }

    public function setEdad(string $edad): self
    {
        $this->edad = $edad;

        return $this;
    }

    public function getPeso(): ?float
    {
        return $this->peso;
    }

    public function setPeso(float $peso): self
    {
        $this->peso = $peso;

        return $this;
    }

    public function getTamanyo(): ?string
    {
        return $this->tamanyo;
    }

    public function setTamanyo(string $tamanyo): self
    {
        $this->tamanyo = $tamanyo;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): self
    {
        $this->usuario = $usuario;
        $this->setAdoptado(true);
        return $this;
    }

    public function isAdoptado(): ?bool
    {
        return $this->adoptado;
    }

    public function setAdoptado(bool $adoptado): self
    {
        $this->adoptado = $adoptado;

        return $this;
    }

}
