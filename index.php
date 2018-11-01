<?php
function match(int $team1, int $team2) {
    include 'MatchOracle.php';
    include('data.php');
    $oracle = new MatchOracle($data);
    return $oracle->estimate_match($team1, $team2);
}
