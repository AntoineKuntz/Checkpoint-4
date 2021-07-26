<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Blog;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\CommentFormType;
use App\Form\AddBlogFormType;
use App\Entity\Comment;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Form\EditBlogType;
use App\Repository\BlogRepository;
use App\Repository\PictureRepository;

/**
 * @Route("/blog", name="blog")
 */

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $donnes = $this->getDoctrine()->getRepository(Blog::class)->findall([],
        ['created_at' => 'desc']);
       

        if ($this->getUser() == '') {
            return $this->redirectToRoute('app_login');
        }

        $blog = $paginator->paginate(
            $donnes, 
            $request->query->getInt('page', 1), 
            2
        );
        
       
        return $this->render('blog/index.html.twig', [
            'blog' => $blog,
        ]);
    }

    /**
     * @isGranted("ROLE_USER") 
    * @Route("/{slug}", name="_view")
    */
    public function blogView($slug, Request $request)
    {
           
    $blog = $this->getDoctrine()->getRepository(Blog::class)->findOneBy(['slug' => $slug]);
    
    if(!$blog){
       
        throw $this->createNotFoundException('L\'article n\'existe pas');
    }

    $comment = new Comment();

    $form = $this->createForm(CommentFormType::class, $comment);

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
     
        $comment->setBlog($blog);
    
        $comment->setCreatedAt(new DateTime('now'));

        $doctrine = $this->getDoctrine()->getManager();

        $doctrine->persist($comment);
        
        $doctrine->flush();
    }

   
    return $this->render('blog/article.html.twig', [
        'blog' => $blog,
        'formComment' => $form->createView()
    ]);
    }

    /**
    *@isGranted("ROLE_USER") 
    * @Route("/new/blog", name="_new")
    */
    public function blogNew(Request $request)
    {
    $blog = new Blog();

    $form = $this->createForm(AddBlogFormType::class, $blog);

    $form->handleRequest($request);

    if($form->isSubmitted() &&  $form->isValid()){
      
        $blog->setUser($this->getUser());

        $blog->setCreatedAt(new DateTime('now'));
       
        $doctrine = $this->getDoctrine()->getManager();
       
        $doctrine->persist($blog);
      
        $doctrine->flush();
    

    $this->addFlash('add', 'Votre article a bien été publié');

    return $this->redirectToroute('blog');
    }
    return $this->render('blog/addBlog.html.twig',[
        'articleForm' =>$form->createView()
    ]);
    }

    /**
    * @Route("/{slug}/edit", name="_edit", methods={"GET","POST"})
    */
    public function edit(Request $request, Blog $blog): Response
    {
        $form = $this->createForm(EditBlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->getDoctrine()->getManager()->flush();
            
            $this->addFlash('edit', 'Votre article a bien été mis à jours');

            return $this->redirectToRoute('blog_view', ['slug' => $blog->getSlug()]);
        }

        return $this->render('blog/edit.html.twig', [
            'blog' => $blog,
            'formBlog' => $form->createView(),
        ]);
    }

     /**
     * @Route("/supprimer/{id}", name ="_delete")
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $comment = $this->getDoctrine()->getRepository(Blog::class)->find($id);
            
        if ($comment === null) {
            $comments = $this->getDoctrine()->getManager();
            $comments->remove($comment);
            $comments->flush(); 
        }
    
        $blog = $this->getDoctrine()
            ->getRepository(Blog::class)
            ->find($id);

        $manager = $this->getDoctrine()->getManager();
        
        $manager->remove($blog);
        $manager->flush();
        $this->addFlash('deleteBlog', 'Le sujet a bien étais supprimer');

        return $this->redirectToRoute('blog');
    }
    }


