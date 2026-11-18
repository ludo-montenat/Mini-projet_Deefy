<?php
declare(strict_types=1);

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\AudioList;

// Nom conservé pour rester proche des exercices du TD.
class ListRenderer extends AudioListRenderer
{
    public function __construct(AudioList $liste)
    {
        parent::__construct($liste);
    }
}
