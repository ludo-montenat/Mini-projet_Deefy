<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\AudioList;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class AddPlaylistAction extends Action
{
    public function execute(): string
    {
        if ($this->http_method === 'GET') {
            return '<h2>Créer une playlist</h2>'
                . '<form method="post" action="?action=add-playlist">'
                . '<label for="nom">Nom de la playlist</label>'
                . '<input type="text" id="nom" name="nom" maxlength="100" required>'
                . '<button type="submit">Créer la playlist</button>'
                . '</form>';
        }

        if (!isset($_POST['nom']) || $_POST['nom'] === '') {
            return '<p>Le nom de la playlist est obligatoire.</p>'
                . '<p><a href="?action=add-playlist">Réessayer</a></p>';
        }

        $nom = $_POST['nom'];
        $playlist = new AudioList($nom);

        $repository = DeefyRepository::getInstance();
        $playlist = $repository->saveEmptyPlaylist($playlist);

        $_SESSION['playlist_courante'] = $playlist;

        return '<p>La playlist « ' . $this->e($nom)
            . ' » a été créée et est maintenant la playlist courante.</p>'
            . (new AudioListRenderer($playlist))->render()
            . '<p><a class="button" href="?action=add-track">Ajouter une piste</a> '
            . '<a href="?action=playlists">Voir toutes les playlists</a></p>';
    }
}
