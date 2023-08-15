<?php

namespace App\Controller;

use App\Entity\Color;
use App\Entity\Image;
use App\Entity\Lustre;
use App\Entity\Comment;
use App\Entity\Mineral;
use App\Entity\Variety;
use App\Entity\Category;
use App\Form\CommentType;
use App\Form\MineralType;
use App\Form\VarietyType;
use App\Entity\Discussion;
use App\Form\DiscussionType;
use App\Service\FileUploader;
use App\Form\MineralColorType;
use Doctrine\ORM\EntityManager;
use App\Form\RespondCommentType;
use App\Repository\ImageRepository;
use App\Repository\MineralRepository;
use App\Repository\CategoryRepository;
use App\Repository\DiscussionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WikiController extends AbstractController
{
    #[Route('/wiki/mineral', name: 'app_mineral')]
    public function index(MineralRepository $mineralRepository, Request $request): Response
    {        
        return $this->render('wiki/index.html.twig', [
            'minerals' => $mineralRepository->findPaginateMinerals($request->query->getInt('page', 1))
        ]);
    }

    #[Route('/wiki/mineral/{slug}/show', name: 'show_mineral')]
    public function showMineral(Mineral $mineral, ImageRepository $imageRepository): Response
    {
        $image = $imageRepository->findImagesById($mineral->getId());
        $images = $imageRepository->findImagesAndNameInMineral($mineral->getId());
        return $this->render('wiki/show_mineral.html.twig', [
            'image' => $image,
            'images' => $images,
            'mineral' => $mineral
        ]);
    }

    #[Route('/wiki/mineral/{slug}/discussion/createDiscussion', name: 'new_discussion')]
    public function launchDiscussion(Mineral $mineral, Request $request, EntityManagerInterface $entityManager): Response
    {
        $discussion = new Discussion();
        $user = $this->getUser();

        $form = $this->createForm(DiscussionType::class, $discussion);
        

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $discussion = $form->getData();
            $discussion->setUser($user);
            $discussion->setMineral($mineral);
            
            $entityManager->persist($discussion);
            $entityManager->flush();
        }

        return $this->render('wiki/create_discussion.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/wiki/mineral/{slug}/discussion/{id}/newComment', name: 'new_comment')]
    public function newComment(Discussion $discussion, Request $request, EntityManagerInterface $entityManager): Response {
       
        $comment = new Comment();
        $user = $this->getUser();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setUser($user);
            $comment->setDiscussion($discussion);
            
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        return $this->render('wiki/new_comment.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/wiki/mineral/{slug}/discussion/{id}/comment/{comment}/respond', name: 'respond_comment')]
    public function respondComment(Comment $comment, Discussion $discussion, Request $request, EntityManagerInterface $entityManager): Response {
        
        $respondComment = new Comment;

        $user = $this->getUser();

        $form = $this->createForm(RespondCommentType::class, $respondComment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $respondComment = $form->getData();
            $respondComment->setUser($user);
            $respondComment->setDiscussion($discussion);
            $respondComment->setParent($comment);
            
            $entityManager->persist($respondComment);
            $entityManager->flush();
        }

        return $this->render('wiki/_respond_comment.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/wiki/mineral/{slug}/discussions', name: 'discussions_mineral')]
    public function discussions(Mineral $mineral): Response
    {
        return $this->render('wiki/discussions_mineral.html.twig', [
            'mineral' => $mineral
        ]);
    }

    #[Route('/wiki/mineral/new', name: 'new_mineral')]
    // #[IsGranted('ROLE_')]
    public function new_mineral(FileUploader $fileUploader, Request $request, EntityManagerInterface $entityManager): Response
    {
        $mineral = new Mineral();

        $form = $this->createForm(MineralType::class, $mineral);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mineral = $form->getData();
            // On récupère une collection d'images
            $images = $form->get('images')->getData();

            // Pour chaque image soumise :
            foreach ($images as $image) {
                /* On déclare une variable qui contient une image 
                qui est traitée par la fonction upload */
                $newFileName = $fileUploader->upload($image);
                // On déclare une nouvel objet image
                $img = new Image;
                // Le nom du fichier sera celui de l'image uploadé
                $img->setFileName($newFileName);
                // On ajoute l'image dans la collection
                $mineral->addImage($img);
            }
            // On prépare les données pour l'envoi
            $entityManager->persist($mineral);
            // On envoie les données dans la bdd
            $entityManager->flush();

            return $this->redirectToRoute('app_mineral');
        }

        return $this->render('wiki/new_mineral.html.twig', [
            'form' => $form,
            'mineralId' => $mineral->getId()
        ]);
    }

    #[Route('/wiki/mineral/{slug}/edit', name: 'edit_mineral')]
    public function edit(Mineral $mineral, Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $originalColors = new ArrayCollection();
        $originalImages = new ArrayCollection();

        // Pour chaque couleur récupérés :
        foreach ($mineral->getColors() as $color) {
            // On ajoute la couleur dans la Collection
            $originalColors->add($color);
        }

        foreach ($mineral->getImages() as $image) {
            $originalImages->add($image);
        }

        $editForm = $this->createForm(MineralType::class, $mineral);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $newImages = $editForm->get('images')->getData();

            foreach ($originalImages as $image) {
                if (false === $mineral->getImages()->contains($image)) {
                    $mineral->removeImage($image);

                    $entityManager->persist($image);
                }
            }

            foreach ($newImages as $image) {
                $newFileName = $fileUploader->upload($image);
                $img = new Image;
                $img->setFileName($newFileName);
                $mineral->addImage($img);
            }

            // Supprime la relation entre la couleur et le mineral
            foreach ($originalColors as $color) {
                // Si en essayant de récupérer la couleur contenu dans le mineral on obtient false :
                if (false === $mineral->getColors()->contains($color)) {
                    // Supprime le mineral de la couleur
                    $color->getMinerals()->removeElement($mineral);

                    // Dans le cas d'une relation many-to-one, décommenter la ligne suivante :
                    // $color->setMineral(null);

                    $entityManager->persist($color);
                }
            }

            $entityManager->persist($mineral);
            $entityManager->flush();

            // Redirige vers la page d'édition voulue
            return $this->redirectToRoute('edit_mineral', ['slug' => $mineral->getSlug()]);

        }

        return $this->render('wiki/new_mineral.html.twig', [
            'form' => $editForm,
            'mineralId' => $mineral->getId()
        ]);
    } 

    #[Route('/wiki/mineral/{slug}/variety/new', name: 'new_variety')]
    // #[IsGranted('ROLE_')]
    public function new_variety(Variety $variety = null, Mineral $mineral, FileUploader $fileUploader, Request $request, EntityManagerInterface $entityManager): Response
    {
        $variety = new Variety();
        
        $form = $this->createForm(VarietyType::class, $variety);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $variety = $form->getData();
            $images = $form->get('images')->getData();

            foreach ($images as $image) {
                $newFileName = $fileUploader->upload($image);
                $img = new Image;
                $img->setFileName($newFileName);
                $variety->addImage($img);
                $mineral->addImage($img);
            }

            $variety->setMineral($mineral);
            $entityManager->persist($variety);
            $entityManager->flush();

            return $this->redirectToRoute('app_variety');
        }

        return $this->render('wiki/new_variety.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/wiki/mineral/{slug}/show/editColors', name: 'edit_mineral_colors')]
    public function edit_mineral_colors(Mineral $mineral, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(MineralColorType::class, $mineral);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mineral = $form->getData();
            $entityManager->persist($mineral);
            $entityManager->flush();

            return $this->redirectToRoute('show_mineral', ['id' => $mineral->getId()]);
        }

        return $this->render('wiki/edit_mineral_colors.html.twig', [
            'form' => $form,
            'edit' => $mineral->getId()
        ]);
    }

    #[Route('/wiki/mineral/{slug}/show/editLustres', name: 'edit_mineral_lustres')]
    public function edit_mineral_lustres(Mineral $mineral, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(LustreType::class, $mineral);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mineral = $form->getData();
            $entityManager->persist($mineral);
            $entityManager->flush();

            return $this->redirectToRoute('show_mineral', ['id' => $mineral->getId()]);
        }

        return $this->render('wiki/edit_mineral_lustres.html.twig', [
            'form' => $form,
            'edit' => $mineral->getId()
        ]);
    }
    
    #[Route('/wiki/category', name: 'app_category')]
    public function categorieslist(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy([], ["name" => "ASC"]);
        return $this->render('wiki/categories_list.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/wiki/category/{slug}/show', name: 'show_category')]
    public function showCategory(Category $category): Response
    {
        return $this->render('wiki/show_category.html.twig', [
            'category' => $category
        ]);
    }

    #[Route('/wiki/color/{slug}/show', name: 'show_color')]
    public function showColor(Color $color): Response
    {
        return $this->render('wiki/show_color.html.twig', [
            'color' => $color
        ]);
    }

    #[Route('/wiki/lustre/{slug}/show', name: 'show_lustre')]
    public function showLustre(Lustre $lustre): Response
    {
        return $this->render('wiki/show_lustre.html.twig', [
            'lustre' => $lustre
        ]);
    }
}
