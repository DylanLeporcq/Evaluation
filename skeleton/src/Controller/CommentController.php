<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Article;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/", name="comment_index", methods={"GET"})
     */
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }


    /**
     * @Route("/{id}", name="comment_show", methods={"GET"})
     */
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @param Comment $comment
     * @return Response
     * @throws \Exception
     * @Route("/{id}/edit", name="comment_update", methods={"GET","POST"})
     */
    public function edit($id, Request $request, Comment $comment): Response
    {
        //GET USER ID
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comment::class)->find($id);
        //CREATE FORM
        $form = $this->createFormBuilder($comment)
            ->add('author', TextType::class)
            ->add('content', TextareaType::class)

            ->add('Enregistrer', SubmitType::class, ['label' => 'Modifier'])
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setCreatedAt(new \DateTime());
            $comment= $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($comment);
            $entityManager->flush();


            return $this->redirectToRoute('article_show', [
                'id'=> $comment->getArticle()->getId(),
            ]);
        }

        return $this->render('comment/updateComm.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_show', [
            'id'=> $comment->getArticle()->getId(),
        ]);
    }
}
