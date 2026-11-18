<?php
declare(strict_types=1);

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\PodcastTrack;

class PodcastRenderer extends AudioTrackRenderer
{
    public function __construct(PodcastTrack $podcast)
    {
        parent::__construct($podcast);
    }

    protected function renderCompact(): string
    {
        return '<p>' . $this->e($this->at->titre) . '</p>' . $this->renderAudio();
    }

    protected function renderLong(): string
    {
        return '<h3>' . $this->e($this->at->titre) . '</h3>'
            . '<p>Auteur : ' . $this->e($this->at->auteur) . '</p>'
            . '<p>Date : ' . $this->e($this->at->date) . '</p>'
            . '<p>Genre : ' . $this->e($this->at->genre) . '</p>'
            . '<p>Durée : ' . (int) $this->at->duree . ' secondes</p>'
            . $this->renderAudio();
    }
}
