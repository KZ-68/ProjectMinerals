<?php

namespace App\Controller;

use App\Entity\Color;
use App\Entity\Image;
use App\Entity\Lustre;
use App\Entity\Comment;
use App\Entity\Mineral;
use App\Entity\Variety;
use App\Entity\Category;
use App\Form\LustreType;
use App\Form\CommentType;
use App\Form\MineralType;
use App\Form\VarietyType;
use App\Entity\Coordinate;
use App\Entity\Discussion;
use App\Entity\Favorite;
use App\Form\DiscussionType;
use App\Service\FileUploader;
use App\Service\PdfGenerator;
use App\Form\MineralColorType;
use Doctrine\ORM\EntityManager;
use App\Form\RespondCommentType;
use App\Form\MineralVarietiesType;
use App\Entity\ModificationHistory;
use App\Repository\ImageRepository;
use App\Repository\CommentRepository;
use App\Repository\MineralRepository;
use App\Repository\CategoryRepository;
use App\Repository\DiscussionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\ModificationHistoryRepository;
use App\Service\FileDownloader;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class WikiController extends AbstractController
{
    #[Route('/wiki/mineral', name: 'app_mineral')]
    #[IsGranted('PUBLIC_ACCESS')]
    public function index(MineralRepository $mineralRepository, Request $request): Response
    {        
        return $this->render('wiki/index.html.twig', [
            'minerals' => $mineralRepository->findPaginateMinerals($request->query->getInt('page', 1))
        ]);
    }

    #[Route('/wiki/mineral/{slug}/show', name: 'show_mineral')]
    #[IsGranted('PUBLIC_ACCESS')]
    public function showMineral(Mineral $mineral, ImageRepository $imageRepository): Response
    {
        $image = $imageRepository->findImagesById($mineral->getId());
        $varietyImages = $imageRepository->findVarietyImagesAndNamesInMineral($mineral->getId());
        $substitutionImage = $imageRepository->findTitleImageSubstitution($mineral->getId());
        return $this->render('wiki/show_mineral.html.twig', [
            'image' => $image,
            'varietyImages' => $varietyImages,
            'substitutionImage' => $substitutionImage,
            'mineral' => $mineral
        ]);
    }

    #[Route('/wiki/mineral/{slug}/discussions/createDiscussion', name: 'new_discussion')]
    #[IsGranted('ROLE_USER')]
    public function launchDiscussion(Mineral $mineral, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
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

            return $this->redirectToRoute('discussions_mineral', ['slug' => $mineral->getSlug()]);
        }

        return $this->render('wiki/create_discussion.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/wiki/mineral/{slug}/discussions/{id}/newComment', name: 'new_comment')]
    #[IsGranted('ROLE_USER')]
    public function newComment(Discussion $discussion, Request $request, EntityManagerInterface $entityManager): Response {
       
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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

            return $this->redirectToRoute('discussions_mineral', ['slug' => $discussion->getMineral()->getSlug(), 'id' => $discussion->getId()]);
        }

        return $this->render('wiki/new_comment.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/wiki/mineral/{slug}/discussions/{id}/comment/{comment}/respond', name: 'respond_comment')]
    #[IsGranted('ROLE_USER')]
    public function respondComment(Comment $comment, Discussion $discussion, Request $request, EntityManagerInterface $entityManager): Response {
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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

            return $this->redirectToRoute(
                'discussions_mineral',
                [
                'slug' => $discussion->getMineral()->getSlug(), 
                'id' => $comment->getDiscussion()->getId(),
                'comment' => $comment->getId()
                ]
            );
        }

        return $this->render('wiki/_respond_comment.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/wiki/mineral/{slug}/discussions/{id}/delete', name:'delete_discussion', methods:['POST'])]
    #[IsGranted('ROLE_USER')]
    public function deleteDiscussion(Discussion $discussion, DiscussionRepository $discussionRepository, EntityManagerInterface $entityManager): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $currentUser = $this->getUser();
        
        if ($discussion->getUser() === $currentUser && $currentUser->getRoles('ROLE_MODERATOR')) {
            $discussionRepository->deleteDiscussionByUser($discussion->getId());
        } else if ($discussion->getUser() !== $currentUser && $currentUser->getRoles('ROLE_MODERATOR')) {
            $discussionRepository->deleteDiscussionByModerator($discussion->getId());
            $entityManager->persist($discussion);
            $entityManager->flush();
        }

        if ($currentUser && $currentUser === $discussion->getUser()) {
            $discussionRepository->deleteDiscussionByUser($discussion->getId());
        }

        return $this->redirectToRoute(
            'discussions_mineral',
            [
            'slug' => $discussion->getMineral()->getSlug(), 
            'id' => $discussion->getId(),
            ]
        );
    }

    #[Route('/wiki/mineral/{slug}/discussions/{id}/comment/{comment}/delete', name:'delete_comment', methods:['POST'])]
    #[IsGranted('ROLE_USER')]
    public function deleteComment(Discussion $discussion, Comment $comment, CommentRepository $commentRepository, EntityManagerInterface $entityManager): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $currentUser = $this->getUser();

        if ($comment->getUser() === $currentUser && $currentUser->getRoles('ROLE_MODERATOR')) {
            $commentRepository->deleteCommentByUser($comment->getId());
        } else if ($comment->getUser() !== $currentUser && $currentUser->getRoles('ROLE_MODERATOR')) {
            $commentRepository->deleteCommentByModerator($comment->getId());
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        if ($currentUser && $currentUser === $comment->getUser()) {
            $commentRepository->deleteCommentByUser($comment->getId());
        }

        return $this->redirectToRoute(
            'discussions_mineral',
            [
            'slug' => $discussion->getMineral()->getSlug(), 
            'id' => $comment->getDiscussion()->getId(),
            'comment' => $comment->getId()
            ]
        );

    }

    #[Route('/wiki/mineral/{slug}/discussions', name: 'discussions_mineral')]
    #[IsGranted('PUBLIC_ACCESS')]
    public function discussions(Mineral $mineral): Response
    {
        return $this->render('wiki/discussions_mineral.html.twig', [
            'mineral' => $mineral
        ]);
    }

    #[Route('/wiki/mineral/new', name: 'new_mineral')]
    #[IsGranted('ROLE_USER')]
    public function new_mineral(FileUploader $fileUploader, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $mineral = new Mineral();
        $coordinate = new Coordinate();

        $form = $this->createForm(MineralType::class, $mineral);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mineral = $form->getData();
            // On récupère une collection d'images
            $images = $form->get('images')->getData();
            $latitude = $form->get('latitude')->getData();
            $longitude = $form->get('longitude')->getData();

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
            $mineral->addCoordinate($coordinate);
            $coordinate->setLatitude($latitude);
            $coordinate->setLongitude($longitude);
            $coordinate->addMineral($mineral);
            $entityManager->persist($coordinate);
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
    #[IsGranted('ROLE_USER')]
    public function edit(Mineral $mineral, Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $originalColors = new ArrayCollection();
        $originalImages = new ArrayCollection();
        $coordinates = new ArrayCollection();

        // Pour chaque couleur récupérés :
        foreach ($mineral->getColors() as $color) {
            // On ajoute la couleur dans la Collection
            $originalColors->add($color);
        }

        foreach ($mineral->getImages() as $image) {
            $originalImages->add($image);
        }

        foreach ($mineral->getCoordinates() as $coordinate) {
            $coordinates->add($coordinate);
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

            foreach ($coordinates as $coordinate) {
                if (false === $mineral->getCoordinates()->contains($coordinate)) {
                    $mineral->removeCoordinate($coordinate);

                    $entityManager->persist($coordinate);
                }
            }

            $coordinate = new Coordinate();
            $latitude = $editForm->get('latitude')->getData();
            $longitude = $editForm->get('longitude')->getData();
            $coordinate->setLatitude($latitude);
            $coordinate->setLongitude($longitude);
            $coordinates->add($coordinate);
            $mineral->addCoordinate($coordinate);

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
    #[IsGranted('ROLE_USER')]
    public function new_variety(Mineral $mineral, FileUploader $fileUploader, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
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
            $mineral->addVariety($variety);
            $entityManager->persist($variety);
            $entityManager->persist($mineral);
            $entityManager->flush();

            return $this->redirectToRoute('show_mineral', ['slug' => $mineral->getSlug()]);
        }

        return $this->render('wiki/new_variety.html.twig', [
            'form' => $form
        ]);
    }

    
    #[Route("/wiki/history", name:"mineral_history")]
    public function showHistory(ModificationHistoryRepository $modificationHistoryRepository): Response
    {
        // Récupére l'historique des modifications depuis la base de données.
        $history = $modificationHistoryRepository->findAll();

        // Affiche l'historique dans un template.
        return $this->render('wiki/history.html.twig', [
            'history' => $history,
        ]);
    }

    #[Route('/wiki/variety/{slug}/show', name: 'show_variety')]
    #[IsGranted('PUBLIC_ACCESS')]
    public function showVariety(Variety $variety): Response
    {
        return $this->render('wiki/show_variety.html.twig', [
            'variety' => $variety
        ]);
    }

    #[Route('/wiki/mineral/{slug}/show/editColors', name: 'edit_mineral_colors')]
    #[IsGranted('ROLE_USER')]
    public function edit_mineral_colors(Mineral $mineral, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(MineralColorType::class, $mineral);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mineral = $form->getData();
            $entityManager->persist($mineral);
            $entityManager->flush();

            return $this->redirectToRoute('show_mineral', ['slug' => $mineral->getSlug()]);
        }

        return $this->render('wiki/edit_mineral_colors.html.twig', [
            'form' => $form,
            'edit' => $mineral->getId()
        ]);
    }

    #[Route('/wiki/mineral/{slug}/show/editVarieties', name: 'edit_mineral_varieties')]
    #[IsGranted('ROLE_USER')]
    public function edit_mineral_varieties(Mineral $mineral, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(MineralVarietiesType::class, $mineral);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mineral = $form->getData();
            $entityManager->persist($mineral);
            $entityManager->flush();

            return $this->redirectToRoute('show_mineral', ['slug' => $mineral->getSlug()]);
        }

        return $this->render('wiki/edit_mineral_varieties.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/wiki/mineral/{slug}/show/editLustres', name: 'edit_mineral_lustres')]
    #[IsGranted('ROLE_USER')]
    public function edit_mineral_lustres(Mineral $mineral, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(LustreType::class, $mineral);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mineral = $form->getData();
            $entityManager->persist($mineral);
            $entityManager->flush();

            return $this->redirectToRoute('show_mineral', ['slug' => $mineral->getSlug()]);
        }

        return $this->render('wiki/edit_mineral_lustres.html.twig', [
            'form' => $form,
            'edit' => $mineral->getId()
        ]);
    }
    
    #[Route('/wiki/category', name: 'app_category')]
    #[IsGranted('PUBLIC_ACCESS')]
    public function categorieslist(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy([], ["name" => "ASC"]);
        return $this->render('wiki/categories_list.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/wiki/category/{slug}/show', name: 'show_category')]
    #[IsGranted('PUBLIC_ACCESS')]
    public function showCategory(Category $category): Response
    {
        return $this->render('wiki/show_category.html.twig', [
            'category' => $category
        ]);
    }

    #[Route('/wiki/color/{slug}/show', name: 'show_color')]
    #[IsGranted('PUBLIC_ACCESS')]
    public function showColor(Color $color): Response
    {
        return $this->render('wiki/show_color.html.twig', [
            'color' => $color
        ]);
    }

    #[Route('/wiki/lustre/{slug}/show', name: 'show_lustre')]
    #[IsGranted('PUBLIC_ACCESS')]
    public function showLustre(Lustre $lustre): Response
    {
        return $this->render('wiki/show_lustre.html.twig', [
            'lustre' => $lustre
        ]);
    }

    #[Route('/wiki/image', name: 'app_image')]
    #[IsGranted('PUBLIC_ACCESS')]
    public function imageslist(ImageRepository $imageRepository, Request $request): Response
    {
        return $this->render('wiki/images_list.html.twig', [
            'images' => $imageRepository->findPaginateImages($request->query->getInt('page', 1))
        ]);
    }

    #[Route('/wiki/image/{id}/delete', name: 'delete_image')]
    #[IsGranted('ROLE_USER')]
    public function deleteImage(Image $image, EntityManagerInterface $entityManager) {
        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($image);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('app_image');
    }

    #[Route('/privacy_policy', name: 'privacy_policy')]
    #[IsGranted('PUBLIC_ACCESS')]
    public function privacyPolicy(Request $request): Response {
        return $this->render('wiki/privacy/privacy_policy.html.twig');
    }

    #[Route('/wiki/mineral/{slug}/show/pdfgenerator', name:'pdf_generator')]
    #[IsGranted('PUBLIC_ACCESS')]
    public function generatePdf(Mineral $mineral, ImageRepository $imageRepository, PdfGenerator $pdfGenerator) {
        
        $image = $imageRepository->findImagesById($mineral->getId());
        $varietyImages = $imageRepository->findVarietyImagesAndNamesInMineral($mineral->getId());
        $substitutionImage = $imageRepository->findTitleImageSubstitution($mineral->getId());
        
        $html = $this->render('wiki/show_mineral_pdf.html.twig', [
                'image' => $image,
                'varietyImages' => $varietyImages,
                'substitutionImage' => $substitutionImage,
                'mineral' => $mineral
            ]);

        $pdfGenerator->generate_pdf($html);

    }

    #[Route('{filename}/download', name:'download_images')]
    public function download(FileDownloader $fileDownloader, Image $image): BinaryFileResponse {
        
        $imageName = $image->getFilename();
        $response = $fileDownloader->download($imageName);

        return $response;
    }

    #[Route('/wiki/mineral/{slug}/show/add-favorite', name:'add_favorite')] 
    #[IsGranted('ROLE_USER')]
    public function addFavorite(EntityManagerInterface $entityManager, MineralRepository $mineralRepository, Request $request)
    {

        if($request->isXmlHttpRequest()) {
            $dataMineralSlug = $request->request->all();
            $mineral = $mineralRepository->findMineralBySlug($dataMineralSlug['slug']);

            $favorite = new Favorite();

            $user = $this->getUser();

            $favorite->setUser($user);
            $favorite->setMineral($mineral[0]);

            $entityManager->persist($favorite);
            $entityManager->flush();

            return $this->json(200);
        }
        
    }
}
