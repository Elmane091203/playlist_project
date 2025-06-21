<?php

namespace App\Controller;

use App\Entity\Media;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/media')]
class MediaController extends AbstractController
{
    #[Route('/', name: 'app_media_index', methods: ['GET'])]
    public function index(MediaRepository $mediaRepository, PlaylistRepository $playlistRepository): Response
    {
        $user = $this->getUser();
        $medias = [];
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $medias = $mediaRepository->findAll();
        } else { 
            $playlists = $playlistRepository->findBy(['user' => $user]);
            foreach ($playlists as $playlist) {
                $medias = array_merge($medias, $playlist->getMedia()->toArray());
            }
        }
        return $this->render('media/index.html.twig', [
            'medias' => $medias,
            'user' => $user,
        ]);
    }

    #[Route('/new', name: 'app_media_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $media = new Media();
        $form = $this->createForm(MediaType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($media);
            $entityManager->flush();

            return $this->redirectToRoute('app_media_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('media/new.html.twig', [
            'media' => $media,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_media_show', methods: ['GET'])]
    public function show(Media $media): Response
    {
        return $this->render('media/show.html.twig', [
            'media' => $media,
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_media_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Media $media, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MediaType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_media_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('media/edit.html.twig', [
            'media' => $media,
            'form' => $form,
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/{id}', name: 'app_media_delete', methods: ['POST'])]
    public function delete(Request $request, Media $media, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$media->getId(), $request->request->get('_token'))) {
            $entityManager->remove($media);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_media_index', [], Response::HTTP_SEE_OTHER);
    }
}
