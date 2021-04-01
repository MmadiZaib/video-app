<?php

namespace App\Controller\Admin\SuperAdmin;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Video;
use App\Form\CategoryType;
use App\Form\VideoType;
use App\Repository\UserRepository;
use App\Services\Uploader\UploaderInterface;
use App\Utils\CategoryTreeAdminList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class SuperAdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/su/categories", name="categories", methods={"GET", "POST"})
     */
    public function categories(CategoryTreeAdminList $categories, Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $isInvalid = null;

        if ($this->saveCategory($form, $category, $request)) {
            return $this->redirectToRoute('categories');
        } elseif ($request->isMethod('POST')) {
            $isInvalid = 'is-invalid';
        }

        return $this->render('admin/categories.html.twig', [
            'form' => $form->createView(),
            'is_invalid' => $isInvalid,
            'categories' => $categories->getCategoryList($categories->buildTree()),
        ]);
    }

    private function saveCategory(FormInterface $form, Category $category, Request $request): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setName($request->request->get('category')['name']);

            $repository = $this->getDoctrine()->getRepository(Category::class);
            $parent = $repository->find($request->request->get('category')['parent']);

            $category->setParent($parent);

            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    /**
     * @Route("/su/edit-category/{id}", name="edit_category",  methods={"GET", "POST"})
     */
    public function editCategory(Category $category, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $isInvalid = null;

        if ($this->saveCategory($form, $category, $request)) {
            return $this->redirectToRoute('categories');
        } elseif ($request->isMethod('POST')) {
            $isInvalid = 'is-invalid';
        }

        return $this->render('admin/edit_category.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'is_invalid' => $isInvalid,
        ]);
    }

    /**
     * @Route("/su/users", name="users")
     */
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], ['name' => 'ASC']);

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/delete-user/{user}", name="delete_user")
     */
    public function deleteUser(User $user): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('users');
    }

    /**
     * @Route("/su/upload-video-locally", name="upload_video_locally")
     */
    public function uploadVideoLocally(Request $request, UploaderInterface $fileUploader): Response
    {
        $video = new Video();

        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $file = $video->getUploadVideo();

            $fileName = $fileUploader->upload($file);

            $basePath = Video::uploadFolder;
            $video->setPath(sprintf('%s%s', $basePath, $fileName[0]));
            $video->setTitle($fileName[1]);

            $em->persist($video);
            $em->flush();

            return $this->redirectToRoute('videos');
        }

        return $this->render('admin/upload_video_locally.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/su/upload-video-by-vimeo", name="upload_video_by_vimeo")
     */
    public function uploadVideoByVimeo(Request $request): Response
    {
        $vimeoId = preg_replace('/^\/.+\//', '', $request->get('vimeo_uri'));

        if ($request->get('videoName') && $vimeoId) {
            $em = $this->getDoctrine()->getManager();
            $video = new Video();
            $video->setTitle($request->get('videoName'));
            $video->setPath(Video::VIMEO_PATH.$vimeoId);

            $em->persist($video);
            $em->flush();

            return $this->redirectToRoute('videos');
        }

        return $this->render('admin/upload_video_by_vimeo.html.twig');
    }

    /**
     * @Route("/su/delete-video/{video}/{path}", name="delete_video", requirements={"path"=".+"})
     */
    public function deleteVideo(Video $video, string $path, UploaderInterface $fileUploader): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($video);
        $em->flush();

        if ($fileUploader->delete($path)) {
            $this->addFlash('success', 'The video was successfully deleted.');
        } else {
            $this->addFlash('danger', 'We were not able to delete. Check the video.');
        }

        return $this->redirectToRoute('videos');
    }

    /**
     * @Route("/su/update-video-category/{video}", methods={"POST"}, name="update_video_category")
     */
    public function updateVideoCategory(Request $request, Video $video): Response
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Category::class)->find($request->request->get('video_category'));
        $video->setCategory($category);

        $em->persist($video);
        $em->flush();

        return $this->redirectToRoute('videos');
    }

    /**
     * @Route("/set-video-duration/{video}/{vimeoId}", name="set_video_duration", requirements={"vimeo_id"=".+"})
     */
    public function setVideoDuration(Video $video, int $vimeoId): Response
    {
        if (!is_numeric($vimeoId)) {
            return $this->redirectToRoute('videos');
        }

        $userVimeoToken = $this->getUser()->getVimeoApiKey();
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => sprintf('https://api.vimeo.com/videos/%s', $vimeoId),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Accept: application/vnd.vimeo.*+json;version=3.4',
                "Authorization: Bearer $userVimeoToken",
                'Cache-Control: no-cache',
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new ServiceUnavailableHttpException('Error. Try again later. Message: '.$err);
        } else {
            $duration = json_decode($response, true)['duration'] / 60;

            if ($duration) {
                $video->setDuration($duration);
                $em = $this->getDoctrine()->getManager();
                $em->persist($video);
                $em->flush();
            } else {
                $this->addFlash('danger', 'We were not able to update duration. Check the video.');
            }
        }

        return $this->redirectToRoute('videos');
    }
}
