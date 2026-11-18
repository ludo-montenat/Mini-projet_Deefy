<?php
declare(strict_types=1);

namespace iutnc\deefy\audio\tracks;

class AlbumTrack extends AudioTrack
{
    protected string $artiste = '';
    protected int $annee = 0;
    protected string $nmfich = '';
    protected string $album;
    protected int $numero;

    public function __construct(string $titre, string $chemin, string $album, int $numero)
    {
        parent::__construct($titre, $chemin);
        $this->album = $album;
        $this->numero = $numero;
    }

    public function setArtiste(string $artiste): void
    {
        $this->artiste = $artiste;
    }

    public function setAnnee(int $annee): void
    {
        $this->annee = $annee;
    }

    public function setNmFich(string $nmfich): void
    {
        $this->nmfich = $nmfich;
    }
}
