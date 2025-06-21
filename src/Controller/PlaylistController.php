<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/playlist')]
class PlaylistController extends AbstractController
{
    #[Route('/', name: 'app_playlist_index', methods: ['GET'])]
    public function index(PlaylistRepository $playlistRepository, Security $security): Response
    {

        $user = $security->getUser();
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->render('playlist/index.html.twig', [
                'playlists' => $playlistRepository->findAll(),
                'user' => $user,
            ]);
        }
        return $this->render('playlist/index.html.twig', [
            'playlists' => $playlistRepository->findBy(['user' => $user]),
            'user' => $user,
        ]);
    }

    #[Route('/new', name: 'app_playlist_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $playlist = new Playlist();
        $playlist->setUser($security->getUser());
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($playlist);
            $entityManager->flush();

            $this->addFlash('success', 'La playlist a été créée avec succès.');
            return $this->redirectToRoute('app_playlist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('playlist/new.html.twig', [
            'playlist' => $playlist,
            'form' => $form,
            'user' => $security->getUser(),
        ]);
    }

    #[Route('/{id}', name: 'app_playlist_show', methods: ['GET'])]
    public function show(Playlist $playlist): Response
    {
        return $this->render('playlist/show.html.twig', [
            'playlist' => $playlist,
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_playlist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Playlist $playlist, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La playlist a été modifiée avec succès.');
            return $this->redirectToRoute('app_playlist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('playlist/edit.html.twig', [
            'playlist' => $playlist,
            'form' => $form,
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/{id}', name: 'app_playlist_delete', methods: ['POST'])]
    public function delete(Request $request, Playlist $playlist, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$playlist->getId(), $request->request->get('_token'))) {
            $entityManager->remove($playlist);
            $entityManager->flush();
            $this->addFlash('success', 'La playlist a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_playlist_index', [], Response::HTTP_SEE_OTHER);
    }
}
