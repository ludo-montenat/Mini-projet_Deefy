<?php
declare(strict_types=1);

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\lists\AudioList;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use PDO;

class DeefyRepository
{
    private PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf)
    {
        $dsn = $conf['driver'] . ':host=' . $conf['host']
            . ';dbname=' . $conf['database'];

        $this->pdo = new PDO($dsn, $conf['username'], $conf['password']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function setConfig(string $file): void
    {
        $conf = parse_ini_file($file);

        if ($conf === false) {
            throw new \Exception('Impossible de lire le fichier de configuration.');
        }

        self::$config = $conf;
    }

    public static function getInstance(): DeefyRepository
    {
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository(self::$config);
        }

        return self::$instance;
    }

    // Retourne toutes les playlists sans charger leurs pistes (TD13).
    public function findAllPlaylists(): array
    {
        $sql = 'SELECT id, nom FROM playlist ORDER BY id';
        $statement = $this->pdo->query($sql);
        $playlists = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $playlist = new AudioList($row['nom'], [], (int) $row['id']);
            $playlists[] = $playlist;
        }

        return $playlists;
    }

    // Enregistre une playlist vide dans la base de données (TD13).
    public function saveEmptyPlaylist(AudioList $playlist): AudioList
    {
        $sql = 'INSERT INTO playlist (nom) VALUES (:nom)';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['nom' => $playlist->nom]);

        $playlist->setID((int) $this->pdo->lastInsertId());
        return $playlist;
    }

    // Récupère une playlist et les pistes qui lui appartiennent.
    public function findPlaylistById(int $id): ?AudioList
    {
        $sql = 'SELECT id, nom FROM playlist WHERE id = :id';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id' => $id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $playlist = new AudioList($row['nom'], [], (int) $row['id']);

        $sql = 'SELECT track.* FROM track '
            . 'INNER JOIN playlist2track ON track.id = playlist2track.id_track '
            . 'WHERE playlist2track.id_pl = :id '
            . 'ORDER BY playlist2track.no_piste_dans_liste';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id' => $id]);

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $piste = $this->createTrackFromRow($row);
            $playlist->ajoutPiste($piste);
        }

        return $playlist;
    }

    // Enregistre une nouvelle PodcastTrack dans la table track.
    public function savePodcastTrack(PodcastTrack $piste): PodcastTrack
    {
        // Le chemin de la piste est par exemple audio/123456.mp3.
        // La base contient seulement le nom du fichier.
        $nomFichier = str_replace('audio/', '', $piste->chemin);

        $sql = 'INSERT INTO track '
            . '(titre, genre, duree, filename, type, auteur_podcast, date_posdcast) '
            . 'VALUES (:titre, :genre, :duree, :filename, :type, :auteur, :date)';

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'titre' => $piste->titre,
            'genre' => $piste->genre,
            'duree' => $piste->duree,
            'filename' => $nomFichier,
            'type' => 'P',
            'auteur' => $piste->auteur,
            'date' => $piste->date,
        ]);

        $piste->setID((int) $this->pdo->lastInsertId());
        return $piste;
    }

    // Ajoute une piste existante à une playlist (table playlist2track).
    public function addTrackToPlaylist(int $playlistId, int $trackId): void
    {
        $sql = 'SELECT MAX(no_piste_dans_liste) AS derniere_piste '
            . 'FROM playlist2track WHERE id_pl = :id_pl';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id_pl' => $playlistId]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        $numeroPiste = 1;
        if ($row['derniere_piste'] !== null) {
            $numeroPiste = (int) $row['derniere_piste'] + 1;
        }

        $sql = 'INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) '
            . 'VALUES (:id_pl, :id_track, :numero)';
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'id_pl' => $playlistId,
            'id_track' => $trackId,
            'numero' => $numeroPiste,
        ]);
    }

    // Transforme une ligne SQL en objet AlbumTrack ou PodcastTrack.
    private function createTrackFromRow(array $row)
    {
        $chemin = '';
        if ($row['filename'] !== null && $row['filename'] !== '') {
            $chemin = 'audio/' . $row['filename'];
        }

        if ($row['type'] === 'A') {
            $piste = new AlbumTrack(
                $row['titre'],
                $chemin,
                $row['titre_album'],
                (int) $row['numero_album']
            );
            $piste->setArtiste($row['artiste_album']);
            $piste->setAnnee((int) $row['annee_album']);
        } else {
            $piste = new PodcastTrack($row['titre'], $chemin);
            $piste->setAuteur($row['auteur_podcast']);
            $piste->setDate($row['date_posdcast']);
        }

        $piste->setID((int) $row['id']);

        if ($row['genre'] !== null) {
            $piste->setGenre($row['genre']);
        }
        if ($row['duree'] !== null) {
            $piste->setDuree((int) $row['duree']);
        }

        return $piste;
    }
}
