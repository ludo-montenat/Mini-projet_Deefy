<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\AudioList;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class AddPodcastTrackAction extends Action
{
    public function execute(): string
    {
        if (!isset($_SESSION['playlist_courante'])) {
            return '<h2>Ajouter une piste</h2>'
                . '<p>Vous devez d’abord choisir ou créer une playlist.</p>'
                . '<p><a href="?action=playlists">Mes playlists</a> | '
                . '<a href="?action=add-playlist">Créer une playlist</a></p>';
        }

        $playlist = $_SESSION['playlist_courante'];
        if (!($playlist instanceof AudioList) || $playlist->getID() === null) {
            return '<h2>Ajouter une piste</h2>'
                . '<p>Vous devez d’abord choisir ou créer une playlist.</p>'
                . '<p><a href="?action=playlists">Mes playlists</a> | '
                . '<a href="?action=add-playlist">Créer une playlist</a></p>';
        }

        if ($this->http_method === 'GET') {
            return $this->renderForm($playlist);
        }

        if (!isset($_POST['titre']) || !isset($_POST['auteur'])
            || !isset($_POST['date']) || !isset($_POST['genre'])
            || !isset($_POST['duree'])) {
            return '<p>Veuillez remplir tous les champs.</p>' . $this->renderForm($playlist);
        }

        $titre = $_POST['titre'];
        $auteur = $_POST['auteur'];
        $date = $_POST['date'];
        $genre = $_POST['genre'];
        $duree = filter_var($_POST['duree'], FILTER_VALIDATE_INT);

        if ($titre === '' || $auteur === '' || $date === '' || $genre === '') {
            return '<p>Veuillez remplir tous les champs.</p>' . $this->renderForm($playlist);
        }

        if ($duree === false || $duree < 0) {
            return '<p>La durée doit être un nombre entier positif.</p>' . $this->renderForm($playlist);
        }

        // Le TD12 demande de vérifier l'extension .mp3 et le type audio/mpeg.
        if (!isset($_FILES['audio'])) {
            return '<p>Veuillez choisir un fichier MP3.</p>' . $this->renderForm($playlist);
        }

        if ($_FILES['audio']['error'] != 0) {
            return '<p>Le fichier n’a pas été envoyé correctement.</p>' . $this->renderForm($playlist);
        }

        $nomOriginal = $_FILES['audio']['name'];
        $partiesNom = explode('.', $nomOriginal);
        $extension = $partiesNom[count($partiesNom) - 1];

        if ($extension !== 'mp3' || $_FILES['audio']['type'] !== 'audio/mpeg') {
            return '<p>Le fichier doit être un MP3 (.mp3 et audio/mpeg).</p>'
                . $this->renderForm($playlist);
        }

        // Le dossier audio existe déjà dans le projet : il n'est pas créé par PHP.
        $dossierAudio = __DIR__ . '/../../../audio';
        $nomFichier = rand(100000, 999999) . '.mp3';
        $cheminServeur = $dossierAudio . '/' . $nomFichier;

        if (!move_uploaded_file($_FILES['audio']['tmp_name'], $cheminServeur)) {
            return '<p>Le transfert du fichier audio a échoué. Vérifiez le dossier audio.</p>'
                . $this->renderForm($playlist);
        }

        $piste = new PodcastTrack($titre, 'audio/' . $nomFichier);
        $piste->setAuteur($auteur);
        $piste->setDate($date);
        $piste->setGenre($genre);
        $piste->setDuree((int) $duree);

        $repository = DeefyRepository::getInstance();
        $piste = $repository->savePodcastTrack($piste);
        $repository->addTrackToPlaylist((int) $playlist->getID(), (int) $piste->id);

        $playlist->ajoutPiste($piste);
        $_SESSION['playlist_courante'] = $playlist;

        return '<p>La piste « ' . $this->e($titre) . ' » a été ajoutée à la playlist « '
            . $this->e($playlist->nom) . ' ».</p>'
            . (new AudioListRenderer($playlist))->render()
            . '<p><a class="button" href="?action=add-track">Ajouter encore une piste</a> '
            . '<a class="button" href="?action=playlist">Afficher la playlist courante</a></p>';
    }

    private function renderForm(AudioList $playlist): string
    {
        return '<h2>Ajouter une piste à « ' . $this->e($playlist->nom) . ' »</h2>'
            . '<form method="post" action="?action=add-track" enctype="multipart/form-data">'
            . '<label for="titre">Titre</label>'
            . '<input type="text" id="titre" name="titre" maxlength="100" required><br>'
            . '<label for="auteur">Auteur</label>'
            . '<input type="text" id="auteur" name="auteur" maxlength="100" required><br>'
            . '<label for="date">Date</label>'
            . '<input type="date" id="date" name="date" required><br>'
            . '<label for="genre">Genre</label>'
            . '<input type="text" id="genre" name="genre" maxlength="30" required><br>'
            . '<label for="duree">Durée (en secondes)</label>'
            . '<input type="number" id="duree" name="duree" min="0" step="1" required><br>'
            . '<label for="audio">Fichier audio (MP3)</label>'
            . '<input type="file" id="audio" name="audio" accept=".mp3,audio/mpeg" required><br>'
            . '<button type="submit">Ajouter la piste</button>'
            . '</form>';
    }
}
