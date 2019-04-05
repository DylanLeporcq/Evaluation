<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();

        $form = $this->createFormBuilder($user)//PHP VA S OCCUPER DE CREER LE FORMULAIRE
        ->add('username', TextType::class, ['label' => 'Pseudo'])
            ->add('nom', TextType::class, ['label' => 'Nom'])
            ->add('prenom', Texttype::class, ['label' => 'Prenom'])
            ->add('password', TextType::class,['label' => 'Mot de passe'])
            ->add('roleId', ChoiceType::class, ['label' => 'Role',
                'choices'  => [
                    'administrateur'=> 1,
                    'utilisateur'=>2
                ]])
            ->add('Enregistrer', SubmitType::class, ['label' => 'Nouvel Utilisateur'])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $roles= $user->getRoles();
            $user->setRoles($roles);

            $hash= $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $user = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }
        //CREATE VIEW
        return $this->render('user/newUser.html.twig', [
            'form'=> $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_update", methods={"GET","POST"})
     */
    public function edit($id,Request $request, User $user): Response
    {
        //GET USER ID
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        //CREATE FORM
        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class, ['label' => 'Pseudo'])
            ->add('nom', TextType::class, ['label' => 'Nom'])
            ->add('prenom', Texttype::class, ['label' => 'Prenom'])
            ->add('RoleId', ChoiceType::class, ['label' => 'Role',
                'choices'=> [
                    'administrateur'=>1,
                    'utilisateur'=>2,
                ]
            ])
            ->add('Enregistrer', SubmitType::class, ['label' => 'Mettre à jour'])
            ->getForm();


        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $roles= $user->getRoles();
            $user->setRoles($roles);

            //$hash= $encoder->encodePassword($user, $user->getPassword());
            //$user->setPassword($hash);
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('user_index', [
                'id' => $user->getId()
            ]);
        }
        //CREATE VIEW
        return $this->render('user/update.html.twig', [
            'form'=> $form->createView(),
        ]);

    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }



}
