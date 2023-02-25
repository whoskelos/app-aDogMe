<?php

namespace App\Service;

use App\Entity\Perro;
use App\Repository\PerroRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PerroManager
{

    private $em;
    private $perroRepository;
    public function __construct(
        PerroRepository $perroRepository,
        EntityManagerInterface $em
    ){
        $this->em = $em;
        $this->perroRepository = $perroRepository;
    }

    public function registar(Perro $perro)
    {
        $this->em->persist($perro);
        $this->em->flush();
    }


    public function editar(Perro $perro)
    {
        $this->em->flush();
    }

    public function eliminar(Perro $perro)
    {
        $this->em->remove($perro);
        $this->em->flush();
    }
    
    public function validar(Perro $perro) 
    {
        $errores = [];
        if (empty($perro->getNombre())) {
            $errores[] = 'Campo nombre obligatorio';
        }
        if (empty($perro->getEdad())) {
            $errores[] = 'Campo edad obligatorio';
        }
        if (empty($perro->getPeso())) {
            $errores[] = 'Campo peso obligatorio';
        }
        if (empty($perro->getTamanyo())) {
            $errores[] = 'Campo tamanyo obligatorio';
        }
        if (empty($perro->getDescripcion())) {
            $errores[] = 'Campo descripcion obligatorio';
        }
        if (empty($perro->getFoto())) {
            $errores[] = 'Campo foto obligatorio';
        }
        
        return $errores;
    }


}