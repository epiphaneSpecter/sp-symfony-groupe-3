<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Report;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Repository\ReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    #[Route('/post', name: 'app_list_post')]
    public function index(PostRepository $postRepository): Response
    {
        $allPosts = $postRepository->findAll();

        return $this->render('post/index.html.twig', [
            'posts' => $allPosts,
        ]);
    }

    #[Route('/post/{id}', name: 'app_show_post')]
    public function show(Post $post): Response
    {
        $form = $this->createForm(CommentType::class);

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $post->getComments(),
            'comment_form' => $form->createView(),
        ]);
    }

    #[Route('/post/{id}/comment', name: 'app_post_comment')]
    public function comment(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->setPost($post);
        $comment->setAuthor($this->getUser());
        $comment->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_show_post', ['id' => $post->getId()]);
        }

        // If form is not valid, redirect back to the post show page with errors (optional)
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $post->getComments(),
            'comment_form' => $form->createView(),
        ]);
    }

    #[Route('/post/{id}/comment/{commentId}/report', name: 'app_comment_report', methods: ['POST'])]
    public function reportComment(Request $request, Post $post, Comment $comment, EntityManagerInterface $entityManager, ReportRepository $reportRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($comment->getAuthor() === $user) {
            $this->addFlash('error', 'Vous ne pouvez pas signaler votre propre commentaire.');
            return $this->redirectToRoute('app_show_post', ['id' => $post->getId()]);
        }

        $existingReport = $reportRepository->findOneBy(['comment' => $comment, 'author' => $user]);
        if ($existingReport) {
            $this->addFlash('error', 'Vous avez déjà signalé ce commentaire.');
            return $this->redirectToRoute('app_show_post', ['id' => $post->getId()]);
        }

        // Create a new report
        $report = new Report();
        $report->setReason('Abus');
        $report->setComment($comment);
        $report->setAuthor($user);
        $report->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($report);
        $entityManager->flush();

        $this->addFlash('success', 'Commentaire signalé avec succès.');
        return $this->redirectToRoute('app_show_post', ['id' => $post->getId()]);
    }
}
