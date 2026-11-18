<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\AudioList;
use iutnc\deefy\render\AudioListRenderer;

class DisplayPlaylistAction extends Action
{
    public function execute(): string
    {
        if (!isset($_SESSION['playlist_courante'])) {
            return '<h2>Playlist courante</h2>'
                . '<p>Aucune playlist n’est sélectionnée. '
                . '<a href="?action=playlists">Choisir une playlist</a>.</p>';
        }

        $playlist = $_SESSION['playlist_courante'];
        if (!($playlist instanceof AudioList)) {
            return '<h2>Playlist courante</h2>'
                . '<p>Aucune playlist n’est sélectionnée. '
                . '<a href="?action=playlists">Choisir une playlist</a>.</p>';
        }

        return '<h2>Playlist courante</h2>'
            . (new AudioListRenderer($playlist))->render()
            . '<p><a class="button" href="?action=add-track">Ajouter une piste</a></p>';
    }
}
