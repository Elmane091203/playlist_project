<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('youtube_embed', [$this, 'getYoutubeEmbedUrl']),
        ];
    }

    public function getYoutubeEmbedUrl(string $url): string
    {
        // Si c'est déjà une URL d'embed, on la retourne
        if (strpos($url, 'youtube.com/embed/') !== false) {
            return $url;
        }
        
        // Extraction de l'ID de la vidéo YouTube
        $videoId = null;
        
        // Format: https://www.youtube.com/watch?v=VIDEO_ID
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }
        // Format: https://youtu.be/VIDEO_ID
        elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }
        // Format: https://www.youtube.com/embed/VIDEO_ID
        elseif (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }
        
        if ($videoId) {
            return "https://www.youtube.com/embed/{$videoId}?rel=0&modestbranding=1&origin=" . urlencode($_SERVER['HTTP_HOST'] ?? 'localhost');
        }
        
        // Si ce n'est pas une URL YouTube valide, on retourne l'URL originale
        return $url;
    }
} 