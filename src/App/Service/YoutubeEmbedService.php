<?php
namespace App\Service;

class YoutubeEmbedService
{
    public function embed(string $content): string
    {
        $pattern = '/https?:\/\/(www\.)?youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/';

        return preg_replace(
            $pattern,
            '<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/$2" frameborder="0" allowfullscreen loading="lazy"></iframe>',
            $content
        );
    }

    public function extract(string $content): array
    {
        $videos = [];

        preg_match_all(
            '/<figure class="media"><oembed url="https:\/\/youtu\.be\/([a-zA-Z0-9_-]+)[^"]*"><\/oembed><\/figure>/',
            $content,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $m) {
            $videos[] = $this->toIframe($m[1]);
        }

        $text = preg_replace(
            '/<figure class="media"><oembed url="https:\/\/youtu\.be\/([a-zA-Z0-9_-]+)[^"]*"><\/oembed><\/figure>/',
            '',
            $content
        );

        return [
            'text' => $text,
            'videos' => $videos
        ];
    }

    private function toIframe(string $id): string
    {
        return '<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/'.$id.'" frameborder="0" allowfullscreen loading="lazy"></iframe>';
    }
}