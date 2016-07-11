<?php

use Ocean\Xat\API\ActionAPI;

$active = function ($who, $message, $type) {

    $bot  = ActionAPI::getBot();
    $now  = time();
    $userTime = $now - DataAPI::get($who . '_active');
    $displayName = $bot->users[$who]->isRegistered() ? $bot->users[$who]->getRegname() . '(' . $bot->users[$who]->getID() . ')'  : $bot->users[$who]->getID();

    $hours = floor($userTime / 3600);
    $minutes = floor(($userTime / 60) % 60);
    $seconds = $userTime % 60;

    $bot->network->sendMessageAutoDetection($who, $displayName . ' has been at this chat (while I was here) for: ' . sprintf("%02d hours, %02d minutes and %02d seconds", $hours, $minutes, $seconds), $type);
};
