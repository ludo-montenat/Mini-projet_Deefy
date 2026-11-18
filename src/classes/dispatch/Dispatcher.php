<?php

namespace iutnc\deefy\dispatch;

class Dispatcher
{
    protected string $action;
    protected array $actions = [];

    public function __construct()
    {
        $this->action = 'default';
        if (isset($_GET['action']) && is_string($_GET['action'])) {
            $this->action = $_GET['action'];
        }
    }

    public function addRoute(string $action, string $action_class): self
    {
        $this->actions[$action] = $action_class;
        return $this;
    }

    public function run(): void
    {
        if (!isset($this->actions[$this->action])) {
            $html = '<h2>Page introuvable</h2><p>Cette action n’existe pas.</p>';
        } else {
            try {
                $class = $this->actions[$this->action];
                $action = new $class();
                $html = $action->execute();
            } catch (\Exception $exception) {
                $html = '<h2>Une erreur est survenue</h2>'
                    . '<p>Vérifiez la configuration de la base de données et réessayez.</p>';
            }
        }

        $this->renderPage($html);
    }

    private function renderPage(string $html): void
    {
        echo '<!doctype html>'
            . '<html lang="fr">'
            . '<head>'
            . '<meta charset="utf-8">'
            . '<meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>Deefy - Vos playlists audio</title>'
            . '</head><body>'
            . '<header><h1>Deefy</h1><p>Vos playlists, votre musique.</p>'
            . '<nav>'
            . '<a href="?action=default">Accueil</a> |'
            . ' <a href="?action=playlists">Mes playlists</a> |'
            . ' <a href="?action=playlist">Playlist courante</a> |'
            . ' <a href="?action=add-playlist">Créer une playlist</a> |'
            . ' <a href="?action=add-track">Ajouter une piste</a>'
            . '</nav></header>'
            . '<main>' . $html . '</main>'
            . '</body></html>';
    }
}
