<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicDisplayVideo extends Model
{
    public const YOUTUBE_ID_REGEX = '/^[a-zA-Z0-9_-]{11}$/';
    protected $table = 'public_display_videos';

    protected $fillable = [
        'page_key',
        'youtube_url',
        'title',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeForPage($query, string $pageKey)
    {
        return $query->where('page_key', $pageKey)->where('is_active', true)->orderBy('sort_order');
    }

    public static function extractYoutubeId(string $url): ?string
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }

        // Allow storing/entering raw YouTube ID (old-project style)
        if (preg_match(self::YOUTUBE_ID_REGEX, $url)) {
            return $url;
        }

        // Try parsing as URL first (handles watch?v= even with extra params/order)
        $parts = @parse_url($url);
        if (is_array($parts)) {
            $host = strtolower($parts['host'] ?? '');
            $path = (string) ($parts['path'] ?? '');

            // youtu.be/<id>
            if (str_contains($host, 'youtu.be')) {
                if (preg_match('#^/([a-zA-Z0-9_-]{11})#', $path, $m)) {
                    return $m[1];
                }
            }

            // youtube.com/watch?v=<id>
            if (str_contains($host, 'youtube.com') || str_contains($host, 'youtube-nocookie.com')) {
                parse_str((string) ($parts['query'] ?? ''), $q);
                if (! empty($q['v']) && preg_match(self::YOUTUBE_ID_REGEX, (string) $q['v'])) {
                    return (string) $q['v'];
                }

                // /embed/<id> or /shorts/<id>
                if (preg_match('#/(embed|shorts)/([a-zA-Z0-9_-]{11})#', $path, $m)) {
                    return $m[2];
                }
            }
        }

        // Fallback regex
        $patterns = [
            '/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]{11})/i',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/i',
            '/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/i',
            '/youtu\.be\/([a-zA-Z0-9_-]{11})/i',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $url, $m)) {
                return $m[1];
            }
        }

        return null;
    }

    public function youtubeId(): ?string
    {
        return self::extractYoutubeId((string) $this->youtube_url);
    }

    public static function normalizeYoutubeUrl(string $url): ?string
    {
        // Storage (old-project style): store ONLY the YouTube video id (11 chars)
        $id = self::extractYoutubeId($url);
        return $id ?: null;
    }

    public static function embedUrlFromId(string $videoId): string
    {
        return 'https://www.youtube.com/embed/' . $videoId;
    }

    public function getEmbedUrlAttribute(): ?string
    {
        // Build embed url from stored id (or any accepted URL)
        $id = self::extractYoutubeId($this->youtube_url);
        return $id ? self::embedUrlFromId($id) : null;
    }
}
