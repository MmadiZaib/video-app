<?php

namespace App\Controller;

use App\Controller\traits\Likes;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Video;
use App\Repository\VideoRepository;
use App\Services\Cache\CacheInterface;
use App\Services\VideoForNotValidSubscription;
use App\Utils\CategoryTreeFrontPage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    use Likes;

    /**
     * @Route("/", name="main_page")
     */
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }

    /**
     * @Route("/video-list/category/{category_name},{id}/{page}",  defaults={"page": "1"}, name="video_list")
     */
    public function videoList(
        int $id,
        int $page,
        CategoryTreeFrontPage $categories,
        Request $request,
        VideoForNotValidSubscription $videoForNotValidSubscription,
        CacheInterface $cache
    ): Response
    {
        $categories->getCategoryListAndParent($id);
        $cache = $cache->cache;

        $videoList = $cache->getItem('video_list'. $id . $page . $request->get('sortBy'));
        $videoList->expiresAfter(60);

        if (!$videoList->isHit()) {
            $ids = $categories->getChildIds($id);

            array_push($ids, $id);

            $videos = $this->getDoctrine()
                ->getRepository(Video::class)
                ->findByChildIds($page, $ids, $request->get('sortBy'));

            $response = $this->render('front/video_list.html.twig', [
                'sub_categories' => $categories,
                'videos' => $videos,
                'video_no_members' => $videoForNotValidSubscription->check(),
            ]);

            $videoList->set($response);
            $cache->save($videoList);
        }


        return $videoList->get();
    }

    /**
     * @Route("/video-details/{video}", name="video_details")
     */
    public function videoDetails(Video $video, VideoRepository $videoRepository, VideoForNotValidSubscription $videoForNotValidSubscription): Response
    {
        return $this->render('front/video_details.html.twig', [
            'video' => $videoRepository->videoDetails($video->getId()),
            'video_no_members' => $videoForNotValidSubscription->check(),
        ]);
    }

    /**
     * @Route("/new-comment/{video}", methods={"POST"}, name="new_comment")
     */
    public function newComment(Video $video, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        if (!empty(trim($request->request->get('comment')))) {
            $comment = new Comment();
            $comment->setContent($request->request->get('comment'));
            $comment->setVideo($video);
            $comment->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
        }

        return $this->redirectToRoute('video_details', [
            'video' => $video->getId(),
        ]);
    }

    /**
     * @Route("/search-results/{page}", methods={"GET"}, defaults={"page": "1"} ,name="search_results")
     */
    public function searchResults(int $page, Request $request, VideoForNotValidSubscription $videoForNotValidSubscription): Response
    {
        $videos = null;
        $query = null;

        if ($query = $request->get('query')) {
            $videos = $this->getDoctrine()
                ->getRepository(Video::class)
                ->findByTitle($query, $page, $request->get('sortBy'));

            if (!$videos->getItems()) {
                $videos = null;
            }
        }

        return $this->render('front/search_results.html.twig', [
            'videos' => $videos,
            'query' => $query,
            'video_no_members' => $videoForNotValidSubscription->check(),
        ]);
    }

    public function mainCategories(): Response
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findBy(['parent' => null], ['name' => 'ASC']);

        return $this->render('front/_main_categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/video-list/{video}/like", name="like_video", methods={"POST"})
     * @Route("/video-list/{video}/dislike", name="dislike_video", methods={"POST"})
     * @Route("/video-list/{video}/undolike", name="undo_like_video", methods={"POST"})
     * @Route("/video-list/{video}/undodislike", name="undo_dislike_video", methods={"POST"})
     */
    public function toggleLikesAjax(Video $video, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $result = '';

        switch ($request->get('_route')) {
            case 'like_video':
                $result = $this->likeVideo($video);
                break;
            case 'dislike_video':
                $result = $this->dislikeVideo($video);
                break;
            case 'undo_like_video':
                $result = $this->undoLikeVideo($video);
                break;
            case 'undo_dislike_video':
                $result = $this->undoDislikeVideo($video);
                break;
        }

        return $this->json([
            'action' => $result,
            'id' => $video->getId(),
        ]);
    }

    /**
     * @Route("/delete-comment/{comment}", name="delete_comment")
     * @Security("user.getId() == comment.getUser().getId()")
     */
    public function deleteComment(Comment $comment, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);

        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
