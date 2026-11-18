<?php
declare(strict_types=1);

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AudioTrack;

abstract class AudioTrackRenderer implements Renderer
{
    protected AudioTrack $at;

    public function __construct(AudioTrack $at)
    {
        $this->at = $at;
    }

    public function render(int $selector): string
    {
        if ($selector === Renderer::COMPACT) {
            return $this->renderCompact();
        }

        if ($selector === Renderer::LONG) {
            return $this->renderLong();
        }

        return '';
    }

    protected function e(string $value): string
    {
        return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    protected function renderAudio(): string
    {
        $chemin = $this->e($this->at->chemin);

        if ($chemin === '') {
            return '<p>Aucun fichier audio associé.</p>';
        }

        return '<audio controls src="' . $chemin . '"></audio>';
    }

    abstract protected function renderCompact(): string;

    abstract protected function renderLong(): string;
}
