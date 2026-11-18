<?php
declare(strict_types=1);

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\exception\InvalidPropertyNameException;

class AudioList
{
    protected ?int $id;
    protected string $nom;
    protected array $pistes;
    protected int $duree;

    public function __construct(string $nom, array $pistes = [], ?int $id = null)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->pistes = [];
        $this->duree = 0;

        foreach ($pistes as $piste) {
            if ($piste instanceof AudioTrack) {
                $this->ajoutPiste($piste);
            }
        }
    }

    public function __get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        throw new InvalidPropertyNameException("Propriété inconnue : {$name}");
    }

    public function setID(int $id): void
    {
        $this->id = $id;
    }

    public function getID(): ?int
    {
        return $this->id;
    }

    public function getPistes(): array
    {
        return $this->pistes;
    }

    public function ajoutPiste(AudioTrack $piste): void
    {
        foreach ($this->pistes as $pisteExistante) {
            if ($piste === $pisteExistante) {
                return;
            }
        }

        $this->pistes[] = $piste;
        $this->duree = $this->duree + $piste->duree;
    }

    public function supprListe(int $indice): void
    {
        if (isset($this->pistes[$indice])) {
            $this->duree = $this->duree - $this->pistes[$indice]->duree;
            unset($this->pistes[$indice]);
        }
    }

    public function ajoutListePiste(array $tabp): void
    {
        foreach ($tabp as $piste) {
            if ($piste instanceof AudioTrack) {
                $this->ajoutPiste($piste);
            }
        }
    }
}
