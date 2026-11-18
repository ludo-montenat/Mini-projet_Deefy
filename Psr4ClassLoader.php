<?php

namespace loader;

class Psr4ClassLoader
{
    private string $pref;
    private string $ch;

    public function __construct(string $pref, string $ch)
    {
        $this->pref = $pref;
        $this->ch = $ch;
    }

    public function loadClass(string $nom): void
    {
        $nom = str_replace($this->pref, $this->ch, $nom) . '.php';
        $nom = str_replace('\\', '/', $nom);

        if (is_file($nom)) {
            require_once $nom;
        }
    }

    public function register(): void
    {
        spl_autoload_register([$this, 'loadClass']);
    }
}
