<?php

namespace App\Controller;

use App\Entity\Perro;
use App\Entity\User;
use App\Form\AdopcionType;
use App\Form\PerroFormType;
use App\Repository\PerroRepository;
use App\Repository\PersonaRepository;
use App\Repository\UserRepository;
use App\Service\PerroManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class PerroController extends AbstractController
{
    const ELEMENTOS_POR_PAGINA = 5;
    /**
     * @Route(
     * "/perros/{pagina}", 
     * name="app_listado_perro",
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
    public function listado(int $pagina, PerroRepository $perroRepository): Response
    {
        $perros = $perroRepository->buscarTodas($pagina, self::ELEMENTOS_POR_PAGINA);
        return $this->render('perro/listado.html.twig', [
            'perros' => $perros,
            'pagina' => $pagina,
        ]);
    }

    /**
     * @Route("/perro/insertar", name="app_insertar_perro")
     */
    public function insertar(PerroManager $perroManager, Security $security, Request $request, SluggerInterface $slugger): Response
    {
        $perro = new Perro();
        $form = $this->createForm(PerroFormType::class, $perro);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $foto = $form->get('foto')->getData();
            if ($foto) {
                $nombreOriginal = pathinfo($foto->getClientOriginalname(), PATHINFO_FILENAME);
                $nombreSeguro = $slugger->slug($nombreOriginal);
                $nuevaFoto = $nombreSeguro . '-' . uniqid() . '.' . $foto->guessExtension();

                try {
                    $foto->move(
                        $this->getParameter('fotos_directory'),
                        $nuevaFoto
                    );
                } catch (FileException $e) {
                    throw new \Exception('Ha ocurrido un error al subir la imagen.' . $e);
                }
                $perro->setFoto($nuevaFoto);
            }
            $errores = $perroManager->validar($perro);
            if (empty($errores)) {
                $perroManager->registar($perro);
                $this->addFlash('success', 'Registro insertado correctamente');
                return $this->redirectToRoute('app_index');
            } else {
                foreach ($errores as $error) {
                    $this->addFlash(
                        'warning',
                        $error
                    );
                }
            }
        }

        return $this->render('perro/insertar.html.twig', [
            'perroForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/perro/editar/{id}", name="app_editar_perro")
     */
    public function editar(int $id, PerroManager $perroManager, PerroRepository $perroRepository, Request $request, SluggerInterface $slugger): Response
    {

        $perro = $perroRepository->findOneById($id);
        if (null === $perro) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(PerroFormType::class, $perro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $foto = $form->get('foto')->getData();


            if ($foto) {
                $nombreOriginal = pathinfo($foto->getClientOriginalname(), PATHINFO_FILENAME);
                $nombreSeguro = $slugger->slug($nombreOriginal);
                $nuevaFoto = $nombreSeguro . '-' . uniqid() . '.' . $foto->guessExtension();

                try {
                    $foto->move(
                        $this->getParameter('fotos_directory'),
                        $nuevaFoto
                    );
                } catch (FileException $e) {
                    throw new \Exception('Ha ocurrido un error al subir la imagen.' . $e);
                }
                $perro->setFoto($nuevaFoto);
            }
            $errores = $perroManager->validar($perro);
            if (empty($errores)) {
                $perroManager->editar($perro);
                $this->addFlash('success', 'Registro editado correctamente');
                return $this->redirectToRoute('app_listado_perro');
            } else {
                foreach ($errores as $error) {
                    $this->addFlash(
                        'warning',
                        $error
                    );
                }
            }
        }

        return $this->render('perro/editar.html.twig', [
            'perroForm' => $form->createView(),
            'perro' => $perro
        ]);
    }

    /**
     * @Route("/perro/eliminar/{id}",
     * name="app_eliminar_perro",
     * requirements={"id"="\d+"}
     * )
     */
    public function eliminar(Perro $perro, Request $request, PerroRepository $perroRepository ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$perro->getId(), $request->request->get('_token'))) {
            $perroRepository->remove($perro, true);
        }
        $this->addFlash('success', 'Registro eliminado correctamente');
        return $this->redirectToRoute('app_listado_perro');
    }

    /**
     * @Route("/perro/adoptar/{id}", name="app_adoptar_perro")
     */
    public function adoptar(int $id, Perro $perro, Request $request, PerroRepository $perroRepository, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $perro = $perroRepository->findOneById($id);
        $form = $this->createForm(AdopcionType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $email = (new Email())
            ->from('kelvinguerrero2daw@gmail.com')
            ->to($form->get('email')->getData())
            ->subject('aDogme: Gracias por adoptar')
            ->html('<h1>Enhorabuena! has adoptado a un perrito :)</h1>
            <p>Muchas gracias por tu apoyo, en breves nos pondremos en contacto contigo</p>
            <p>aDogMe Refugio.</p>');
            $perro->setUsuario($user);
            $em->persist($perro);
            $em->flush();
            $mailer->send($email);
            $this->addFlash('success', 'Perro adoptado');
            return $this->redirectToRoute('app_index');

        }

        return $this->render('comunes/_formulario_adopcion.html.twig', [
            'form' => $form->createView(),
            'perro' => $perro,
        ]);
    }
}
