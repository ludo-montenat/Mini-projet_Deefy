<?php
declare(strict_types=1);

namespace iutnc\deefy\audio\lists;

class AlbumList
{
    private array $pistes;

    public function __construct(array $pistes = [])
    {
        $this->pistes = $pistes;
    }

    public function getPistes(): array
    {
        return $this->pistes;
    }
}
