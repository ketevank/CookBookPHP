<?php
/**
 * Photo controller.
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Photo;
use AppBundle\Form\PhotoType;
use AppBundle\Repository\PhotoRepository;
use AppBundle\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PhotoController.
 *
 * @Route("/photo")
 */
class PhotoController extends AbstractController
{
    private $uploaderService = null;

    public function __construct(FileUploader $uploaderService)
    {
        $this->uploaderService = $uploaderService;
    }

    /**
     * New action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \AppBundle\Repository\PhotoRepository           $repository Photo repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/new",
     *     methods={"GET", "POST"},
     *     name="photo_new",
     * )
     */
    public function new(Request $request, PhotoRepository $repository): Response
    {
        $photo = new Photo();
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo->setRecipe($this->getUser());
            $repository->save($photo);
            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'photo/new.html.twig',
            ['form' => $form->createView()]
        );
    }
}