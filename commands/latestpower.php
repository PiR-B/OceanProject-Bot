<?php

$latestpower = function ($who, $message, $type) {
	
	/* Pow2 Indexs
		0 = last
		1 = backs
		2 = actions
		3 = hugs
		4 = topsh
		5 = isgrp
		6 = pssa
		7 = pawns
	*/	
	
    $bot = actionAPI::getBot();

    $pow2 = json_decode(file_get_contents('http://xat.com/web_gear/chat/pow2.php'), true);
    $powers = json_decode(file_get_contents('http://xat.com/json/powers.php'), true);

    if (!$pow2) {
        return $bot->network->sendMessageAutoDetection($who, 'Could not access pow2 at this moment.', $type);
    }
	
	$latestID = $pow2[0][1]['id'];
	if (isset($message[1])) { // if message[1] isset that means check everywhere for new id
		$latestID = end($pow2[6][1]) >= $pow2[0][1]['id'] ? end($pow2[6][1]):$pow2[0][1]['id'];
		$latestID = $latestID >= key($powers) ? $latestID:key($powers);
	}
	
    $latestName = "Unknown";
	
    $latestName = array_search($latestID, $pow2[6][1]);
	$status = "UNRELEASED";
	
	if ($pow2[0][1]['id'] == $latestID) {
		if ($pow2[0][1]['text'] == "[LIMITED]") {
			$status = "LIMITED";
		} else if ($pow2[0][1]['text'] == "[VERY LIMITED]") {
			$status = "LIMITED";
		} else {
			$status = "UNLIMITED";
		}
	}

    $pawns = $smilies = [];
    foreach ($pow2[7][1] as $hatCode => $pawnInfo) {
        if ($hatCode !== 'time' && $pawnInfo[0] == $latestID) {
            $pawns[] = 'h' . $hatCode;
        }
    }
    $smilies = array_merge(array($latestName), array_keys($pow2[4][1], $latestID));
	
    if (isset($powers[$latestID])) {
		$latestName =  $powers[$latestID]['s'];// fail safe
		
		if (isset($powers[$latestID]['r'])) {
			$status = $powers[$latestID]['r'] > 0 ? "LIMITED":$status;
		}
		
		if (isset($powers[$latestID]['f'])) {
			$status = $powers[$latestID]['f'] & 0x2000 ? "LIMITED":$status;
		}
		
        $storePrice = isset($powers[$latestID]['x']) ? $powers[$latestID]['x'].  ' xats' : $powers[$latestID]['d'] . ' days';
    }
	
	$implode = [
		ucfirst($latestName) . ' (ID: '. $latestID . ')',
		'Pawns: ' . implode(', ', $pawns),
		'Smilies: ' . implode(', ', $smilies),
		'Store price: ' . (isset($storePrice) ? $storePrice : "Unknown"),
		'Status: ' . $status
	];

	$bot->network->sendMessageAutoDetection($who, implode(' | ', $implode), $type);

   // $bot->network->sendMessageAutoDetection($who, ucfirst($latestName) . ' (ID: '. $latestID . ') ' . rtrim($pawns, ', ') . ' ' . rtrim($smilies, ', ') . ' | Store price: ' . (isset($storePrice) ? $storePrice : "Unknown"), $type);
};
