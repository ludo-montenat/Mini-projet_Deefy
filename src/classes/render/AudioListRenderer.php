<?php
declare(strict_types=1);

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\AudioList;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;

class AudioListRenderer
{
    private AudioList $liste;

    public function __construct(AudioList $liste)
    {
        $this->liste = $liste;
    }

    public function render(): string
    {
        $nom = filter_var($this->liste->nom, FILTER_SANITIZE_SPECIAL_CHARS);
        $html = '<section class="playlist">';
        $html .= '<h2>' . $nom . '</h2>';
        $html .= '<p>' . count($this->liste->getPistes()) . ' piste(s) - '
            . (int) $this->liste->duree . ' seconde(s)</p>';

        if (count($this->liste->getPistes()) === 0) {
            $html .= '<p>Cette playlist ne contient aucune piste pour le moment.</p>';
        } else {
            foreach ($this->liste->getPistes() as $piste) {
                if ($piste instanceof PodcastTrack) {
                    $renderer = new PodcastRenderer($piste);
                    $html .= $renderer->render(Renderer::LONG);
                } elseif ($piste instanceof AlbumTrack) {
                    $renderer = new AlbumTrackRenderer($piste);
                    $html .= $renderer->render(Renderer::LONG);
                }
            }
        }

        $html .= '</section>';
        return $html;
    }
}
