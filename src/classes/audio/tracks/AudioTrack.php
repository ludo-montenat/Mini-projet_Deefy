<?php

declare(strict_types=1);

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;

class AudioTrack
{
    protected ?int $id = null;
    protected string $titre;
    protected string $chemin;
    protected string $genre = '';
    protected int $duree = 0;

    public function __construct(string $titre, string $chemin)
    {
        $this->titre = $titre;
        $this->chemin = $chemin;
    }

    public function __toString(): string
    {
        return json_encode(get_object_vars($this));
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

    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }

    public function setDuree(int $duree): void
    {
        if ($duree < 0) {
            throw new InvalidPropertyValueException('La durée ne peut pas être négative.');
        }

        $this->duree = $duree;
    }
}
