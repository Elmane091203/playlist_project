<?php

namespace App\Controller;

use App\Repository\MediaRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(PlaylistRepository $playlistRepository, MediaRepository $mediaRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $playlists = $playlistRepository->findAll();
            $medias = $mediaRepository->findAll();
            return $this->render('home.html.twig', [
                'playlists' => $playlists,
                'medias' => $medias,
                'user' => $user,
            ]);
        }
        $playlists = $playlistRepository->findBy(['user' => $user]);
        $medias = [];
        foreach ($playlists as $playlist) {
            $medias = array_merge($medias, $playlist->getMedia()->toArray());
        }
        return $this->render('home.html.twig', [
            'playlists' => $playlists,
            'medias' => $medias,
            'user' => $user,
        ]);
    }
}
