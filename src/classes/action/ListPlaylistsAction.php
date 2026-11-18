<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;

class ListPlaylistsAction extends Action
{
    public function execute(): string
    {
        $repository = DeefyRepository::getInstance();
        $playlists = $repository->findAllPlaylists();
        $html = '<h2>Mes playlists</h2>';

        if (count($playlists) === 0) {
            return $html . '<p>Aucune playlist enregistrée. '
                . '<a href="?action=add-playlist">Créer une playlist</a>.</p>';
        }

        $html .= '<ul class="playlist-list">';
        foreach ($playlists as $playlist) {
            $html .= '<li><a href="?action=select-playlist&amp;id='
                . (int) $playlist->id . '">' . $this->e($playlist->nom) . '</a></li>';
        }
        $html .= '</ul>';
        $html .= '<p><a href="?action=add-playlist">Créer une nouvelle playlist</a></p>';

        return $html;
    }
}
