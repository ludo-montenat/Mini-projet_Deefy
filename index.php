<?php

require_once __DIR__ . '/autoload.php';

session_start();

use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddPodcastTrackAction;
use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\ListPlaylistsAction;
use iutnc\deefy\action\SelectPlaylistAction;
use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\repository\DeefyRepository;

DeefyRepository::setConfig(__DIR__ . '/config.db.ini');

$app = (new Dispatcher())
    ->addRoute('default', DefaultAction::class)
    ->addRoute('playlists', ListPlaylistsAction::class)
    ->addRoute('select-playlist', SelectPlaylistAction::class)
    ->addRoute('playlist', DisplayPlaylistAction::class)
    ->addRoute('add-playlist', AddPlaylistAction::class)
    ->addRoute('add-track', AddPodcastTrackAction::class);

$app->run();
