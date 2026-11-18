<?php
declare(strict_types=1);

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;

class AlbumTrackRenderer extends AudioTrackRenderer
{
    public function __construct(AlbumTrack $at)
    {
        parent::__construct($at);
    }

    protected function renderCompact(): string
    {
        return '<p>' . $this->e($this->at->titre) . '</p>' . $this->renderAudio();
    }

    protected function renderLong(): string
    {
        return '<h3>' . $this->e($this->at->titre) . '</h3>'
            . '<p>Artiste : ' . $this->e($this->at->artiste) . '</p>'
            . '<p>Album : ' . $this->e($this->at->album) . '</p>'
            . '<p>Année : ' . (int) $this->at->annee . '</p>'
            . '<p>Genre : ' . $this->e($this->at->genre) . '</p>'
            . '<p>Durée : ' . (int) $this->at->duree . ' secondes</p>'
            . $this->renderAudio();
    }
}
