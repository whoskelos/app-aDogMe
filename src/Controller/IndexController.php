<?php

namespace App\Controller;

use App\Repository\PerroRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    const ELEMENTOS_POR_PAGINA = 8;
    /**
     * @Route(
     * "/{pagina}",
     * name="app_index",
     * defaults={
     *      "pagina": 1
     * },
     * requirements={
     *      "pagina"="\d+"
     * },
     * methods={
     *      "GET"
     * }
     * )
     */
    public function index(int $pagina,PerroRepository $perroRepository): Response
    {
        // if (!$this->getUser()) {
        //     return $this->redirectToRoute('app_login');
        // }
        $perros = $perroRepository->listarTodas($pagina, self::ELEMENTOS_POR_PAGINA);
        return $this->render('index/index.html.twig', [
            'perros' => $perros,
            'pagina' => $pagina,
        ]);
    }
}
