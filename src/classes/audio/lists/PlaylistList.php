<?php
declare(strict_types=1);

namespace iutnc\deefy\audio\lists;

class PlaylistList
{
    /** @var AudioList[] */
    private array $playlists;

    public function __construct(array $playlists = [])
    {
        $this->playlists = $playlists;
    }

    public function getPlaylists(): array
    {
        return $this->playlists;
    }

    public function ajoutPlaylist(AudioList $playlist): void
    {
        $this->playlists[] = $playlist;
    }
}
