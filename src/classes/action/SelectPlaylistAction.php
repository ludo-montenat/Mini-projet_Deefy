<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class SelectPlaylistAction extends Action
{
    public function execute(): string
    {
        if (!isset($_GET['id'])) {
            return '<p>Identifiant de playlist invalide. '
                . '<a href="?action=playlists">Retour aux playlists</a>.</p>';
        }

        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        if ($id === false || $id < 1) {
            return '<p>Identifiant de playlist invalide. '
                . '<a href="?action=playlists">Retour aux playlists</a>.</p>';
        }

        $repository = DeefyRepository::getInstance();
        $playlist = $repository->findPlaylistById((int) $id);

        if ($playlist === null) {
            return '<p>Cette playlist n’existe pas. '
                . '<a href="?action=playlists">Retour aux playlists</a>.</p>';
        }

        $_SESSION['playlist_courante'] = $playlist;

        return '<p>Playlist sélectionnée : ' . $this->e($playlist->nom) . '</p>'
            . (new AudioListRenderer($playlist))->render()
            . '<p><a class="button" href="?action=add-track">Ajouter une piste</a> '
            . '<a class="button" href="?action=playlist">Afficher la playlist courante</a></p>';
    }
}
