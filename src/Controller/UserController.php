<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Picture;
use App\Form\PictureFormType;
use App\Repository\PictureRepository;
use DateTime;

/**
 * @Route("/profil", name="profil")
 */

class UserController extends AbstractController
{
      /**
     * @Route("/", name="")
     *
    */
    public function index(
        PictureRepository $PictureRepository
     ) {
        if ($this->getUser() == '') {
            return $this->redirectToRoute('app_login');
        }
        return $this->render(
            'user/index.html.twig',
            [
                'picture' => $PictureRepository->findOneByUser($this->getUser()),
            ]
        );
    }
    
    /**
     * @Route("/password/edit", name="_passwordEdit")
     */
    public function editPass(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($this->getUser() == '') {
            return $this->redirectToRoute('app_login');
        }
        if ($request->isMethod('POST')) {
            $em = $this->getdoctrine()->getManager();
            $user = $this->getUser();

        // on vérifie si les deux mots de passe sont identiques

            if ($request->request->get('pass') == $request->request->get('pass2')) {
                $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('pass')));
                $em -> flush();
                $this-> addFlash('password', 'Mot de passe mis à jours avec succés');
                return $this->redirectToRoute('profil');
            } else {
                    $this->addFlash('error', 'Les Deux mots de passe ne sont pas identiques');
            }
        }


        return $this->render('user/editpass.html.twig');
    }


      /**
     * @Route("/{id}/NewPicture", name="_picture_new", methods={"GET","POST"})
     */
    public function new(Request $request, User $user): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureFormType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $picture->setUser($user);

            $entityManager->persist($picture);
            $entityManager->flush();

            $this->addFlash('addpicture', 'Votre photo a bien été enregistré');

            return $this->redirectToRoute('profil');
        }

        return $this->render('user/pictureEdit.html.twig', [
            'picture' => $picture,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{user}/EditPicture", name="_picture_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, int $user, PictureRepository $pictureRepo): Response
    {

        $picture = $pictureRepo->findOneBy(['user' => $this->getUser()]);
            $form = $this->createForm(PictureFormType::class, $picture);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
            $picture->setupdatedAt(new DateTime('now'));
            $this->getDoctrine()->getManager()->flush();


            $this->addFlash('picture', 'Votre photo a bien été remplacer ');   

            return $this->redirectToRoute('profil');
        }
        return $this->render('user/pictureEdit.html.twig', [
            'picture' => $picture,
            'form' => $form->createView()
        ]);
    }

}
