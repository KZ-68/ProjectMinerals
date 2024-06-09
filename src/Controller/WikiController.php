<?php

namespace App\Controller;

use App\Entity\Color;
use App\Entity\Image;
use App\Entity\Lustre;
use App\Entity\Comment;
use App\Entity\Mineral;
use App\Entity\Variety;
use App\Entity\Category;
use App\Entity\Favorite;
use App\Form\LustreType;
use App\Form\SearchType;
use App\Form\CommentType;
use App\Form\MineralType;
use App\Form\VarietyType;
use App\Model\SearchData;
use App\Entity\Coordinate;
use App\Entity\Discussion;
use App\Entity\Contribution;
use App\Entity\Notification;
use App\Form\DiscussionType;
use App\Form\EditMineralType;
use App\Form\EditVarietyType;
use App\Service\FileUploader;
use App\Service\PdfGenerator;
use App\Form\MineralColorType;
use App\Service\FileDownloader;
use Doctrine\ORM\EntityManager;
use App\Form\RespondCommentType;
use App\Form\SelectLanguageType;
use App\Form\EditDescriptionType;
use App\Form\MineralVarietiesType;
use App\Repository\UserRepository;
use App\Entity\ModificationHistory;
use App\Entity\Vote;
use App\Repository\ImageRepository;
use App\Repository\CommentRepository;
use App\Repository\MineralRepository;
use App\Repository\VarietyRepository;
use App\Repository\CategoryRepository;
use App\Repository\FavoriteRepository;
use App\Repository\DiscussionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use App\Repository\ModificationHistoryRepository;
use App\Repository\VoteRepository;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class WikiController extends AbstractController
{
    #[Route(
        '/{_locale}/wiki/mineral', 
        name: 'app_mineral', 
        options: ['sitemap' => ['priority' => 0.7, 'changefreq' => UrlConcrete::CHANGEFREQ_WEEKLY, 'section' => 'mineral']],
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('PUBLIC_ACCESS')]
    public function index(MineralRepository $mineralRepository, Request $request): Response
    {        

        $searchData = new SearchData();

        // On crée un formulaire avec le modèle SearchType
        $form = $this->createForm(SearchType::class, $searchData);

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirect('/fr/wiki/mineral');
            } else {
                return $this->redirect('/en/wiki/mineral');
            }
        }

        if ($request->isXmlHttpRequest()) {
            $formData = $request->request->all();
            $minerals = $mineralRepository->findByAjaxSearch($formData['search']);
            
            $jsonData = [];
            if($minerals === []) {
                $jsonData[] = [
                    'name' => 'No Mineral found in the search engine !'
                ];
            } else {
                foreach ($minerals as $mineral) {
                    $jsonData[] = [
                        'slug' => $mineral->getSlug() ?? null,
                        'name' => $mineral->getName() ?? null,
                        'langForm' => $langForm
                    ];
                }
            }

            $response = [
                'data' => $jsonData
            ];

            return $this->json($response);
        }
        
        return $this->render('wiki/index.html.twig', [
            'form' => $form,
            'minerals' => $mineralRepository->findPaginateMinerals($request->query->getInt('page', 1)),
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/show', 
        name: 'show_mineral',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('PUBLIC_ACCESS')]
    public function showMineral(
        Mineral $mineral, 
        ImageRepository $imageRepository, 
        FavoriteRepository $favoriteRepository,
        Request $request
        ): Response
    {
        $user = $this->getUser();

        $image = $imageRepository->findImagesById($mineral->getId());
        $varietyImages = $imageRepository->findVarietyImagesAndNamesInMineral($mineral->getId());
        $favorite = $favoriteRepository->findOneByMineralAndUser($mineral->getId(), $user);

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('show_mineral', ['_locale' => 'fr', 'slug' => $mineral->getSlug()]);
            } else {
                return $this->redirectToRoute('show_mineral', ['_locale' => 'en', 'slug' => $mineral->getSlug()]);
            }
        }

        return $this->render('wiki/show_mineral.html.twig', [
            'image' => $image,
            'varietyImages' => $varietyImages,
            'mineral' => $mineral, 
            'favorite' => $favorite,
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/discussions', 
        name: 'discussions_list',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function discussionsList(Mineral $mineral, Request $request):Response {

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('discussions_list', ['_locale' => 'fr', 'slug' => $mineral->getSlug()]);
            } else {
                return $this->redirectToRoute('discussions_list', ['_locale' => 'en', 'slug' => $mineral->getSlug()]);
            }
        }

        return $this->render('wiki/discussions_list.html.twig', [
            'mineral' => $mineral,
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/discussions/createDiscussion', 
        name: 'new_discussion',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function launchDiscussion(Mineral $mineral, Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $discussion = new Discussion();
        $user = $this->getUser();

        $form = $this->createForm(DiscussionType::class, $discussion);

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('new_discussion', ['_locale' => 'fr', 'slug' => $mineral->getSlug()]);
            } else {
                return $this->redirectToRoute('new_discussion', ['_locale' => 'en', 'slug' => $mineral->getSlug()]);
            }
        }
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $discussion = $form->getData();
            $discussion->setUser($user);
            $discussion->setMineral($mineral);
            
            $entityManager->persist($discussion);
            $entityManager->flush();

            return $this->redirectToRoute('discussions_list', ['slug' => $mineral->getSlug()]);
        }

        return $this->render('wiki/create_discussion.html.twig', [
            'form' => $form,
            'mineral' => $mineral,
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/discussions/{discussionSlug}', 
        name: 'discussion_mineral',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('PUBLIC_ACCESS')]
    public function discussion( 
        #[MapEntity(mapping: ['slug' => 'slug'])] Mineral $mineral,
        #[MapEntity(mapping: ['discussionSlug' => 'slug'])] Discussion $discussion,
        Request $request
        ): Response
    {

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('discussion_mineral', [
                    '_locale' => 'fr', 
                    'slug' => $mineral->getSlug(),
                    'discussionSlug' => $discussion->getSlug()
                ]);
            } else {
                return $this->redirectToRoute('discussion_mineral', [
                    '_locale' => 'en', 
                    'slug' => $mineral->getSlug(),
                    'discussionSlug' => $discussion->getSlug()
                ]);
            }
        }

        return $this->render('wiki/discussions_mineral.html.twig', [
            'mineral' => $mineral,
            'discussion' => $discussion,
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/discussions/{discussionSlug}/newComment', 
        name: 'new_comment',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function newComment(
        #[MapEntity(mapping: ['slug' => 'slug'])] Mineral $mineral,
        #[MapEntity(mapping: ['discussionSlug' => 'slug'])] Discussion $discussion, 
        Request $request, 
        EntityManagerInterface $entityManager
        ): Response {
       
        $comment = new Comment();
        $user = $this->getUser();

        $form = $this->createForm(CommentType::class, $comment);

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('new_comment', [
                    '_locale' => 'fr', 
                    'slug' => $mineral->getSlug(),
                    'discussionSlug' => $discussion->getSlug()
                ]);
            } else {
                return $this->redirectToRoute('new_comment', [
                    '_locale' => 'en', 
                    'slug' => $mineral->getSlug(),
                    'discussionSlug' => $discussion->getSlug()
                ]);
            }
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setUser($user);
            $comment->setDiscussion($discussion);

            $notification = new Notification();

            $discussionUser = $discussion->getUser();

            $notification->setUser($user);
            $notification->setComment($comment);
            $notification->setDiscussion($discussion);

            $discussionUser->addNotification($notification);

            $entityManager->persist($notification);
            $entityManager->persist($discussionUser);
            
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute(
                'discussion_mineral', [
                    'slug' => $discussion->getMineral()->getSlug(), 
                    'discussionSlug' => $discussion->getSlug()
                ]
            );
        }

        return $this->render('wiki/new_comment.html.twig', [
            'form' => $form,
            'mineral' => $mineral,
            'discussion' => $discussion,
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/discussions/{discussionSlug}/comment/{commentSlug}/respond', 
        name: 'respond_comment',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function respondComment(
        #[MapEntity(mapping: ['slug' => 'slug'])] Mineral $mineral,
        #[MapEntity(mapping: ['commentSlug' => 'slug'])] Comment $comment, 
        #[MapEntity(mapping: ['discussionSlug' => 'slug'])] Discussion $discussion, 
        Request $request,
        CommentRepository $commentRepository, 
        EntityManagerInterface $entityManager
        ): Response {
        
        $respondComment = new Comment;

        $user = $this->getUser();

        $form = $this->createForm(RespondCommentType::class, $respondComment);

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('respond_comment', [
                    '_locale' => 'fr', 
                    'slug' => $mineral->getSlug(),
                    'discussionSlug' => $discussion->getSlug(),
                    'commentSlug' => $comment->getSlug()
                ]);
            } else {
                return $this->redirectToRoute('respond_comment', [
                    '_locale' => 'en', 
                    'slug' => $mineral->getSlug(),
                    'discussionSlug' => $discussion->getSlug(),
                    'commentSlug' => $comment->getSlug()
                ]);
            }
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $respondComment = $form->getData();
            $respondComment->setUser($user);
            $respondComment->setDiscussion($discussion);
            $respondComment->setParent($comment);

            $notification = new Notification();

            $parent_comment_user = $respondComment->getParent()->getUser();
            
            $notification->setUser($parent_comment_user);
            $notification->setComment($respondComment);

            $parent_comment_user->addNotification($notification);
            
            $entityManager->persist($notification);
            $entityManager->persist($parent_comment_user);

            $entityManager->persist($respondComment);
            $entityManager->flush();

            return $this->redirectToRoute(
                'discussion_mineral',
                [
                    'mineral' => $mineral,
                    'slug' => $discussion->getMineral()->getSlug(), 
                    'discussionSlug' => $comment->getDiscussion()->getSlug()
                ]
            );
        }

        return $this->render('wiki/_respond_comment.html.twig', [
            'form' => $form,
            'mineral' => $mineral,
            'discussion' => $discussion,
            'slug' => $discussion->getMineral()->getSlug(), 
            'discussionSlug' => $comment->getDiscussion()->getSlug(),
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/discussions/{discussionSlug}/delete', 
        name:'delete_discussion', 
        methods:['GET'],
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function deleteDiscussion(
        #[MapEntity(mapping: ['discussionSlug' => 'slug'])] Discussion $discussion, 
        DiscussionRepository $discussionRepository, 
        EntityManagerInterface $entityManager
        ): Response {


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
            'discussion_mineral',
            [
                'slug' => $discussion->getMineral()->getSlug(), 
                'discussionSlug' => $discussion->getSlug(),
            ]
        );
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/discussions/{discussionSlug}/comment/{commentSlug}/delete', 
        name:'delete_comment', 
        methods:['GET'],
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function deleteComment(
        #[MapEntity(mapping: ['discussionSlug' => 'slug'])] Discussion $discussion, 
        #[MapEntity(mapping: ['commentSlug' => 'slug'])] Comment $comment, 
        CommentRepository $commentRepository, 
        EntityManagerInterface $entityManager
    ): Response {

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
            'discussion_mineral',
            [
            'slug' => $discussion->getMineral()->getSlug(), 
            'discussionSlug' => $comment->getDiscussion()->getSlug(),
            'commentSlug' => $comment->getSlug()
            ]
        );

    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/discussions/{discussionSlug}/comment/{commentSlug}/report', 
        name:'report_user_comment', 
        methods:['GET'],
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function reportUserComment(
        #[MapEntity(mapping: ['discussionSlug' => 'slug'])] Discussion $discussion, 
        #[MapEntity(mapping: ['commentSlug' => 'slug'])] Comment $comment, 
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ) : Response {
        
        $moderators = $userRepository->findAll(['roles' => 'ROLE_MODERATOR']);

        foreach ($moderators as $moderator) {
            $newNotification = new Notification();
            $newNotification->setUser($moderator);
            $newNotification->setComment($comment);

            $entityManager->persist($newNotification);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'discussion_mineral',
            [
            'slug' => $discussion->getMineral()->getSlug(), 
            'discussionSlug' => $comment->getDiscussion()->getSlug(),
            'commentSlug' => $comment->getSlug()
            ]
        );
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/discussions/{discussionSlug}/upvote', 
        name: 'upvote',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function upvote(
        CommentRepository $commentRepository,
        EntityManagerInterface $entityManager,
        VoteRepository $voteRepository,
        Request $request,
        ): Response
    {
        if($request->isXmlHttpRequest()) {
            $data = $request->request->all();
            $userData = $data['user'];
            $user = $this->getUser($userData);
            
            if($data['commentSlug']) {
                $commentData = $data['commentSlug'];
                $comment = $commentRepository->findOneBy(['slug' => $commentData]);
                
                $uniqueVote = $voteRepository->findBy(['user' => $user, 'comment' => $comment]);

                if($uniqueVote === []) {
                    $vote = new Vote;
                    $vote->setUpvote(true);

                    $vote->setComment($comment);
                    $vote->setUser($user);

                    $entityManager->persist($comment);
                    $entityManager->persist($vote);
                    $entityManager->flush();

                    $response = [
                        'score' => $comment->getScore()
                    ];
        
                    return $this->json($response);
                } else {
                    $response = [
                        'alert' => "There's already a vote on this comment"
                    ];

                    return $this->json($response);
                }

            }

        }
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/discussions/{discussionSlug}/downvote', 
        name: 'downvote',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function downvote(
        CommentRepository $commentRepository,
        EntityManagerInterface $entityManager,
        VoteRepository $voteRepository,
        Request $request,
        ): Response
    {
        if($request->isXmlHttpRequest()) {
            $data = $request->request->all();
            $userData = $data['user'];
            $user = $this->getUser($userData);
            
            if($data['commentSlug']) {
                $commentData = $data['commentSlug'];
                $comment = $commentRepository->findOneBy(['slug' => $commentData]);
                
                $uniqueVote = $voteRepository->findBy(['user' => $user, 'comment' => $comment]);

                if($uniqueVote === []) {

                    $vote = new Vote;
                    $vote->setDownvote(true);

                    $comment = $commentRepository->findOneBy(['slug' => $commentData]);
                    $user = $this->getUser($userData);

                    $vote->setComment($comment);
                    $vote->setUser($user);

                    $entityManager->persist($comment);
                    $entityManager->persist($vote);
                    $entityManager->flush();

                    $response = [
                        'score' => $comment->getScore()
                    ];
        
                    return $this->json($response);

                } else {
                    $response = [
                        'alert' => "There's already a vote on this comment"
                    ];

                    return $this->json($response);
                }
            }

        }
    }

    #[Route(
        '/{_locale}/wiki/mineral/new', 
        name: 'new_mineral',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function new_mineral(FileUploader $fileUploader, Request $request, EntityManagerInterface $entityManager): Response
    {

        $mineral = new Mineral();
        $coordinate = new Coordinate();

        $form = $this->createForm(MineralType::class, $mineral);

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('new_mineral', [
                    '_locale' => 'fr'
                ]);
            } else {
                return $this->redirectToRoute('new_mineral', [
                    '_locale' => 'en'
                ]);
            }
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mineral = $form->getData();
            $name = $form->get('name')->getData();
            $formula = $form->get('formula')->getData();
            $density = $form->get('density')->getData();
            $hardness = $form->get('hardness')->getData();
            $streak = $form->get('streak')->getData();
            
            $imageTitleData = $form->get('image_title')->getData();
            // On récupère une collection d'images
            $images = $form->get('images')->getData();
            $latitude = $form->get('latitude')->getData();
            $longitude = $form->get('longitude')->getData();

            if($imageTitleData) {
                $newFileNameTitle = $fileUploader->upload($imageTitleData);
                $imgTitle = new Image;
                $imgTitle->setFileName($newFileNameTitle);
                $mineral->addImage($imgTitle);
                $mineral->setImageTitle($newFileNameTitle);
            }

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
            if ($latitude && $longitude) {
                $coordinate->setLatitude($latitude);
                $coordinate->setLongitude($longitude);
                $coordinate->addMineral($mineral);
                $entityManager->persist($coordinate);
            }
            // On prépare les données pour l'envoi
            $entityManager->persist($mineral);
            if($name) {
                $newContribution = new Contribution();
                $newContribution->setContent($name);
                $newContribution->setMineral($mineral);
                $entityManager->persist($newContribution);
                $entityManager->flush();
            }

            if($formula) {
                $newContribution = new Contribution();
                $newContribution->setContent($formula);
                $newContribution->setMineral($mineral);
                $entityManager->persist($newContribution);
                $entityManager->flush();
            }

            if($density) {
                $newContribution = new Contribution();
                $newContribution->setContent($density);
                $newContribution->setMineral($mineral);
                $entityManager->persist($newContribution);
                $entityManager->flush();
            }

            if($hardness) {
                $newContribution = new Contribution();
                $newContribution->setContent($hardness);
                $newContribution->setMineral($mineral);
                $entityManager->persist($newContribution);
                $entityManager->flush();
            }

            if($streak) {
                $newContribution = new Contribution();
                $newContribution->setContent($streak);
                $newContribution->setMineral($mineral);
                $entityManager->persist($newContribution);
                $entityManager->flush();
            }

            // On envoie les données dans la bdd
            $entityManager->flush();

            return $this->redirectToRoute('app_mineral');
        }

        return $this->render('wiki/new_mineral.html.twig', [
            'form' => $form,
            'mineralId' => $mineral->getId(),
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/edit', 
        name: 'edit_mineral',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function edit(
        Mineral $mineral, ImageRepository $imageRepository, Request $request, 
        EntityManagerInterface $entityManager, FileUploader $fileUploader
        ): Response
    {

        $oldFileNameTitle = $mineral->getImageTitle();

        $editForm = $this->createForm(EditMineralType::class, $mineral);

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('edit_mineral', [
                    '_locale' => 'fr', 
                    'slug' => $mineral->getSlug()
                ]);
            } else {
                return $this->redirectToRoute('edit_mineral', [
                    '_locale' => 'en', 
                    'slug' => $mineral->getSlug()
                ]);
            }
        }

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $newImageTitle = $editForm->get('image_title')->getData();
            if ($newImageTitle) {

                if($oldFileNameTitle) {
                    $oldImageTitle = $imageRepository->findOneBy(['filename' => $oldFileNameTitle]);
                    
                    $newImageTitleFileName = $fileUploader->upload($newImageTitle);
                    
                    $imgTitle = new Image;
                    $imgTitle->setFileName($newImageTitleFileName);
                    $mineral->addImage($imgTitle);
                    $mineral->setImageTitle($newImageTitleFileName);

                    $mineral->removeImage($oldImageTitle);
                } else {
                    $newImageTitleFileName = $fileUploader->upload($newImageTitle);
                    
                    $imgTitle = new Image;
                    $imgTitle->setFileName($newImageTitleFileName);
                    $mineral->addImage($imgTitle);
                    $mineral->setImageTitle($newImageTitleFileName);
                }
            }
            $newImages = $editForm->get('images')->getData();

            foreach ($newImages as $image) {
                $newFileName = $fileUploader->upload($image);
                $img = new Image;
                $img->setFileName($newFileName);
                $mineral->addImage($img);
            }

            foreach ($mineral->getImages() as $image) {
                if (false === $mineral->getImages()->contains($image)) {
                    $mineral->removeImage($image);

                    $entityManager->persist($image);
                }
            }

            // Supprime la relation entre la couleur et le mineral
            foreach ($mineral->getColors() as $color) {
                // Si en essayant de récupérer la couleur contenu dans le mineral on obtient false :
                if (false === $mineral->getColors()->contains($color)) {
                    // Supprime le mineral de la couleur
                    $color->getMinerals()->removeElement($mineral);
                    $entityManager->persist($color);
                }
            }

            foreach ($mineral->getLustres() as $lustre) {
                if (false === $mineral->getLustres()->contains($lustre)) {
                    $lustre->getMinerals()->removeElement($mineral);
                    $entityManager->persist($lustre);
                }
            }

            foreach ($mineral->getCoordinates() as $coordinate) {
                if (false === $mineral->getCoordinates()->contains($coordinate)) {
                    $mineral->removeCoordinate($coordinate);
                    $entityManager->persist($coordinate);
                }
            }

            $coordinate = new Coordinate();
            $latitude = $editForm->get('latitude')->getData();
            $longitude = $editForm->get('longitude')->getData();
            if ($latitude != null && $longitude != null) {
                $coordinate->setLatitude($latitude);
                $coordinate->setLongitude($longitude);
                $mineral->addCoordinate($coordinate);
            }
            $entityManager->persist($mineral);
            $entityManager->flush();

            // Redirige vers la page d'édition voulue
            return $this->redirectToRoute('edit_mineral', ['slug' => $mineral->getSlug()]);

        }

        return $this->render('wiki/edit_mineral.html.twig', [
            'form' => $editForm,
            'mineral' => $mineral,
            'langForm' => $langForm
        ]);
    } 

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/delete', 
        name: 'delete_mineral',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted("ROLE_MODERATOR")]
    public function deleteMineral(Mineral $mineral, EntityManagerInterface $entityManager, MineralRepository $mineralRepository) {
        
        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($mineral);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('app_mineral');
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/variety/new', 
        name: 'new_variety',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function new_variety(Mineral $mineral, FileUploader $fileUploader, Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $variety = new Variety();
        
        $form = $this->createForm(VarietyType::class, $variety);

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('new_variety', [
                    '_locale' => 'fr', 
                    'slug' => $mineral->getSlug()
                ]);
            } else {
                return $this->redirectToRoute('new_variety', [
                    '_locale' => 'en', 
                    'slug' => $mineral->getSlug()
                ]);
            }
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $variety = $form->getData();
            $imagePresentationData = $form->get('image_presentation')->getData();
            $images = $form->get('images')->getData();

            if($imagePresentationData) {
                $newFileNamePresentation = $fileUploader->upload($imagePresentationData);
                $imgPresentation = new Image;
                $imgPresentation->setFileName($newFileNamePresentation);
                $mineral->addImage($imgPresentation);
                $variety->addImage($imgPresentation);
                $variety->setImagePresentation($newFileNamePresentation);
            }

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
            'form' => $form,
            'mineral' => $mineral,
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/wiki/variety/{slug}/show/edit', 
        name: 'edit_variety',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function edit_variety(
        Variety $variety, 
        ImageRepository $imageRepository, 
        Request $request, 
        EntityManagerInterface $entityManager, 
        FileUploader $fileUploader
        ): Response
    {
        
        $mineral = $variety->getMineral();
        $oldFileNamePresentation = $variety->getImagePresentation();

        $editForm = $this->createForm(EditVarietyType::class, $variety);

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('edit_variety', [
                    '_locale' => 'fr', 
                    'slug' => $variety->getSlug()
                ]);
            } else {
                return $this->redirectToRoute('edit_variety', [
                    '_locale' => 'en', 
                    'slug' => $variety->getSlug(),
                ]);
            }
        }

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $imagePresentationData = $editForm->get('image_presentation')->getData();
            if ($imagePresentationData) {

                if($oldFileNamePresentation) {
                    $oldImagePresentation = $imageRepository->findOneBy(['filename' => $oldFileNamePresentation]);
                    
                    $newImagePresentationFileName = $fileUploader->upload($imagePresentationData);
                    
                    $imgPresentation = new Image;
                    $imgPresentation->setFileName($newImagePresentationFileName);
                    $variety->addImage($imgPresentation);
                    $mineral->addImage($imgPresentation);
                    $variety->setImagePresentation($newImagePresentationFileName);

                    $variety->removeImage($oldImagePresentation);
                } else {
                    $newImagePresentationFileName = $fileUploader->upload($imagePresentationData);
                    
                    $imgPresentation = new Image;
                    $imgPresentation->setFileName($newImagePresentationFileName);
                    $variety->addImage($imgPresentation);
                    $mineral->addImage($imgPresentation);
                    $variety->setImagePresentation($newImagePresentationFileName);
                }
            }
            $newImages = $editForm->get('images')->getData();

            foreach ($newImages as $image) {
                $newFileName = $fileUploader->upload($image);
                $img = new Image;
                $img->setFileName($newFileName);
                $variety->addImage($img);
                $mineral->addImage($img);
            }

            foreach ($variety->getImages() as $image) {
                if (false === $variety->getImages()->contains($image)) {
                    $variety->removeImage($image);
                    $mineral->removeImage($image);

                    $entityManager->persist($image);
                }
            }

            foreach ($variety->getCoordinates() as $coordinate) {
                if (false === $variety->getCoordinates()->contains($coordinate)) {
                    $variety->removeCoordinate($coordinate);
                    $entityManager->persist($coordinate);
                }
            }

            $coordinate = new Coordinate();
            $latitude = $editForm->get('latitude')->getData();
            $longitude = $editForm->get('longitude')->getData();
            if ($latitude != null && $longitude != null) {
                $coordinate->setLatitude($latitude);
                $coordinate->setLongitude($longitude);
                $variety->addCoordinate($coordinate);
                $entityManager->persist($coordinate);
            }
            $entityManager->persist($variety);
            $entityManager->flush();

            // Redirige vers la page d'édition voulue
            return $this->redirectToRoute('show_variety', ['slug' => $variety->getSlug()]);

        }

        return $this->render('wiki/edit_variety.html.twig', [
            'form' => $editForm,
            'variety' => $variety,
            'langForm' => $langForm
        ]);
    } 
    
    #[Route(
        "/{_locale}/wiki/mineral/{slug}/show/history", 
        name:"mineral_history",
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    public function showHistory(
        Mineral $mineral, 
        ModificationHistoryRepository $modificationHistoryRepository,
        Request $request
        ): Response
    {
        // Récupére l'historique des modifications depuis la base de données.
        $history = $modificationHistoryRepository->findBy(['mineral' => $mineral]);
        
        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('mineral_history', [
                    '_locale' => 'fr', 
                    'slug' => $mineral->getSlug()

                ]);
            } else {
                return $this->redirectToRoute('mineral_history', [
                    '_locale' => 'en', 
                    'slug' => $mineral->getSlug()
                ]);
            }
        }

        // Affiche l'historique dans un template.
        return $this->render('wiki/history.html.twig', [
            'history' => $history,
        ]);
    }

    #[Route(
        '/{_locale}/wiki/variety/{slug}/show', 
        name: 'show_variety',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('PUBLIC_ACCESS')]
    public function showVariety(Variety $variety, Request $request): Response
    {
        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('show_variety', [
                    '_locale' => 'fr', 
                    'slug' => $variety->getSlug(),

                ]);
            } else {
                return $this->redirectToRoute('show_variety', [
                    '_locale' => 'en', 
                    'slug' => $variety->getSlug()
                ]);
            }
        }
        return $this->render('wiki/show_variety.html.twig', [
            'variety' => $variety,
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/show/editColors', 
        name: 'edit_mineral_colors',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function edit_mineral_colors(Mineral $mineral, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(MineralColorType::class, $mineral);

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('edit_mineral_colors', [
                    '_locale' => 'fr', 
                    'slug' => $mineral->getSlug()
                ]);
            } else {
                return $this->redirectToRoute('edit_mineral_colors', [
                    '_locale' => 'en', 
                    'slug' => $mineral->getSlug()
                ]);
            }
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mineral = $form->getData();
            $entityManager->persist($mineral);
            $entityManager->flush();

            return $this->redirectToRoute('show_mineral', ['slug' => $mineral->getSlug()]);
        }

        return $this->render('wiki/edit_mineral_colors.html.twig', [
            'form' => $form,
            'mineral' => $mineral,
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/show/editVarieties', 
        name: 'edit_mineral_varieties',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function edit_mineral_varieties(Mineral $mineral, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(MineralVarietiesType::class, $mineral);
        
        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('edit_mineral_varieties', [
                    '_locale' => 'fr', 
                    'slug' => $mineral->getSlug()
                ]);
            } else {
                return $this->redirectToRoute('edit_mineral_varieties', [
                    '_locale' => 'en', 
                    'slug' => $mineral->getSlug()
                ]);
            }
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mineral = $form->getData();
            $entityManager->persist($mineral);
            $entityManager->flush();

            return $this->redirectToRoute('show_mineral', ['slug' => $mineral->getSlug()]);
        }

        return $this->render('wiki/edit_mineral_varieties.html.twig', [
            'form' => $form,
            'mineral' => $mineral,
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/show/editLustres', 
        name: 'edit_mineral_lustres',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function edit_mineral_lustres(Mineral $mineral, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(LustreType::class, $mineral);

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('edit_mineral_lustres', [
                    '_locale' => 'fr', 
                    'slug' => $mineral->getSlug()
                ]);
            } else {
                return $this->redirectToRoute('edit_mineral_lustres', [
                    '_locale' => 'en', 
                    'slug' => $mineral->getSlug()
                ]);
            }
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mineral = $form->getData();
            $entityManager->persist($mineral);
            $entityManager->flush();

            return $this->redirectToRoute('show_mineral', ['slug' => $mineral->getSlug()]);
        }

        return $this->render('wiki/edit_mineral_lustres.html.twig', [
            'form' => $form,
            'mineral' => $mineral,
            'langForm' => $langForm
        ]);
    }
    
    #[Route(
        '/{_locale}/wiki/category', 
        name: 'app_category', 
        options: ['sitemap' => ['priority' => 0.7, 'changefreq' => UrlConcrete::CHANGEFREQ_WEEKLY, 'section' => 'category']],
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('PUBLIC_ACCESS')]
    public function categorieslist(CategoryRepository $categoryRepository, Request $request): Response
    {
        $categories = $categoryRepository->findBy([], ["name" => "ASC"]);

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('app_category', [
                    '_locale' => 'fr'
                ]);
            } else {
                return $this->redirectToRoute('app_category', [
                    '_locale' => 'en'
                ]);
            }
        }
        return $this->render('wiki/categories_list.html.twig', [
            'categories' => $categories,
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/wiki/category/{slug}/show', 
        name: 'show_category',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('PUBLIC_ACCESS')]
    public function showCategory(Category $category, Request $request): Response
    {
        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('show_category', [
                    '_locale' => 'fr', 
                    'slug' => $category->getSlug()
                ]);
            } else {
                return $this->redirectToRoute('show_category', [
                    '_locale' => 'en', 
                    'slug' => $category->getSlug()
                ]);
            }
        }

        return $this->render('wiki/show_category.html.twig', [
            'category' => $category,
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/wiki/color/{slug}/show', 
        name: 'show_color',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('PUBLIC_ACCESS')]
    public function showColor(Color $color, Request $request): Response
    {
        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('show_color', [
                    '_locale' => 'fr', 
                    'slug' => $color->getSlug()
                ]);
            } else {
                return $this->redirectToRoute('show_color', [
                    '_locale' => 'en', 
                    'slug' => $color->getSlug()
                ]);
            }
        }

        return $this->render('wiki/show_color.html.twig', [
            'color' => $color,
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/wiki/lustre/{slug}/show', 
        name: 'show_lustre'
    )]
    #[IsGranted('PUBLIC_ACCESS')]
    public function showLustre(Lustre $lustre, Request $request): Response
    {
        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('show_lustre', [
                    '_locale' => 'fr', 
                    'slug' => $lustre->getSlug()
                ]);
            } else {
                return $this->redirectToRoute('show_lustre', [
                    '_locale' => 'en', 
                    'slug' => $lustre->getSlug()
                ]);
            }
        }
        return $this->render('wiki/show_lustre.html.twig', [
            'lustre' => $lustre,
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/wiki/image', 
        name: 'app_image',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('PUBLIC_ACCESS')]
    public function imageslist(ImageRepository $imageRepository, Request $request): Response
    {
        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('app_image', [
                    '_locale' => 'fr'
                ]);
            } else {
                return $this->redirectToRoute('app_image', [
                    '_locale' => 'en'
                ]);
            }
        }

        return $this->render('wiki/images_list.html.twig', [
            'images' => $imageRepository->findPaginateImages($request->query->getInt('page', 1)),
            'langForm' => $langForm
        ]);
    }

    #[Route(
        '/{_locale}/wiki/image/{id}/delete', 
        name: 'delete_image',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function deleteImage(
        Image $image, 
        EntityManagerInterface $entityManager, 
        MineralRepository $mineralRepository,
        VarietyRepository $varietyRepository
    ) 
    {
        $imageFilename = $image->getFilename();
        $mineral = $mineralRepository->findOneBy(['image_title' => $imageFilename]);
        $variety = $varietyRepository->findOneBy(['image_presentation' => $imageFilename]);

        if($mineral) {
            $mineralRepository->deleteImageTitle($mineral->getId());
        }

        if($variety) {
            $varietyRepository->deleteImagePresentation($variety->getId());
        }
        // Prépare la suppression d'une instance de l'objet 
        $entityManager->remove($image);
        // Exécute la suppression
        $entityManager->flush();

        return $this->redirectToRoute('app_image');
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/show/pdfgenerator', 
        name:'pdf_generator',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('PUBLIC_ACCESS')]
    public function generatePdf(Mineral $mineral, ImageRepository $imageRepository, PdfGenerator $pdfGenerator) {
        
        $image = $imageRepository->findImagesById($mineral->getId());
        $varietyImages = $imageRepository->findVarietyImagesAndNamesInMineral($mineral->getId());
        
        $html = $this->render('wiki/show_mineral_pdf.html.twig', [
                'image' => $image,
                'varietyImages' => $varietyImages,
                'mineral' => $mineral
            ]);

        $pdfGenerator->generate_pdf($html);

    }

    #[Route(
        '{filename}/download', 
        name:'download_images'
    )]
    public function download(FileDownloader $fileDownloader, Image $image): BinaryFileResponse {
        
        $imageName = $image->getFilename();
        $response = $fileDownloader->download($imageName);

        return $response;
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/show/add-favorite', 
        name:'add_favorite',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )] 
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

            $message = [
                'message' => 'Wiki page added to your favorite !'
            ];

            return $this->json(['data' => $message], 200);
        }
        
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/show/remove-favorite', 
        name:'remove_favorite',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[IsGranted('ROLE_USER')]
    public function removeFavorite(EntityManagerInterface $entityManager, FavoriteRepository $favoriteRepository, Request $request)
    {

        if($request->isXmlHttpRequest()) {
            $data = $request->request->all();
            $favorite = $favoriteRepository->find($data['favorite']);

            $user = $this->getUser();

            $entityManager->remove($favorite);
            $entityManager->flush();

            $message = [
                'message' => 'Wiki page has been removed to your favorite !'
            ];
    
            return $this->json(['data' => $message], 200);
        }   
    }

    #[Route(
        '/{_locale}/wiki/mineral/{slug}/show/edit-description', 
        name:'edit_description',
        requirements: [
            '_locale' => 'en|fr',
        ]
    )]
    #[isGranted('ROLE_USER')]
    public function editDescription(Mineral $mineral, EntityManagerInterface $entityManager, Request $request): Response {

        $form = $this->createForm(EditDescriptionType::class, $mineral);

        $langForm = $this->createForm(SelectLanguageType::class);

        $langForm->handleRequest($request);
        
        if($langForm->isSubmitted() && $langForm->isValid()) {
            $lang = $langForm->get('lang')->getData();
            if($lang === 'fr') {
                return $this->redirectToRoute('edit_description', [
                    '_locale' => 'fr', 
                    'slug' => $mineral->getSlug()
                ]);
            } else {
                return $this->redirectToRoute('edit_description', [
                    '_locale' => 'en', 
                    'slug' => $mineral->getSlug()
                ]);
            }
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $mineralData = $form->get('description')->getData();
            $mineral->setDescription($mineralData);
            $entityManager->persist($mineral);
            $entityManager->flush();

            return $this->redirectToRoute('show_mineral', ['slug' => $mineral->getSlug()]);
        }

        return $this->render('wiki/edit_description.html.twig', [
            'form' => $form,
            'mineral' => $mineral,
            'langForm' => $langForm
        ]);
    }

}
