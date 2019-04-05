<?php
/**
 * Created by PhpStorm.
 * User: sophc
 * Date: 02/04/2019
 * Time: 11:03
 */

namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class HomeController extends AbstractController
{
    /**
     * @return Response
     * @Route("/", name="app_homepage")
     */
    public function homepage(Request $request, AuthenticationUtils $authenticationUtils) {

        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('user_index');

        }
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('article_index');
        }


        else {
            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();

            // last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('security/login.html.twig', [
                'last_username' => $lastUsername,
                'error'         => $error,
            ]);
        }

    }


    /**
     * @return Response
     * @Route ("/article", name="article")
     */
    public function show(){


        return $this->render('article/showArt.html.twig', [
            "title"=> "titre"
        ]);
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route ("/enregistrement", name="app_register", methods={"GET","POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {

        $user = new User();

        $form = $this->createFormBuilder($user)//PHP VA S OCCUPER DE CREER LE FORMULAIRE
        ->add('username', TextType::class)
            ->add('nom', TextType::class)
            ->add('prenom', Texttype::class)
            ->add('password', TextType::class)
            ->add('Enregistrer', SubmitType::class, ['label' => 'Enregistrer'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRoles(['ROLE_USER']);
            $user->setRoleId(2);

            $hash= $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $user = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('login');
        }
        //CREATE VIEW
        return $this->render('user/newUser.html.twig', [
            'form'=> $form->createView(),
        ]);

    }

}