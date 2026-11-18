<?php

namespace iutnc\deefy\action;

class DefaultAction extends Action
{
    public function execute(): string
    {
        return '<h2>Bienvenue sur Deefy !</h2>'
            . '<p>Créez vos playlists et ajoutez vos pistes audio.</p>'
            . '<p><a href="?action=playlists">Voir mes playlists</a></p>';
    }
}
