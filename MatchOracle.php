<?php

/**
 * Class MatchOracle
 */
class MatchOracle
{
    /**
     * @var array
     */
    private $data;

    /**
     * MatchOracle constructor.
     * @param array $source_data
     */
    public function __construct(array $source_data)
    {
        $this->data = $source_data;
    }

    /**
     * @param int $min
     * @param int $max
     * @return float|int
     */
    private function random_float($min = 0, $max = 1) {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
    /**
     * @param int $team_id
     * @return float|int
     */
    private function get_attack_power(int $team_id){
        $team = $this->data[$team_id];
        return $team['goals']['scored'] / $team['games'];
    }

    /**
     * @param int $team_id
     * @return float|int
     */
    private function get_defence_power(int $team_id) {
        $team = $this->data[$team_id];
        return $team['goals']['skiped'] / $team['games'];
    }

    /**
     * @param int $team_id
     * @return array
     */
    private function team_mean_stats(int $team_id) {
        $team = $this->data[$team_id];
        return [
            'wins' => $team['win']/$team['games'],
            'defeats' => $team['defeat']/$team['games'],
            'draws' => $team['draw']/$team['games']
        ];
    }

    /**
     * @param int $team1
     * @param int $team2
     * @return array
     */
    public function estimate_match(int $team1, int $team2) {
        $team1_stats = $this->team_mean_stats($team1);
        $team2_stats = $this->team_mean_stats($team2);

        $team1_base_score = $this->get_attack_power($team1) * $this->get_defence_power($team2);
        $team2_base_score = $this->get_attack_power($team2) * $this->get_defence_power($team1);

        $team1_win_chance = $team1_stats['wins'] * $team2_stats['defeats'];
        $team2_win_chance = $team2_stats['wins'] * $team1_stats['defeats'];
        $draw_chance = $team1_stats['draws'] * $team2_stats['draws'];

        $team1_luck = $this->random_float($team1_win_chance + $draw_chance - $team2_win_chance, 1);
        $team2_luck = $this->random_float($team2_win_chance + $draw_chance - $team1_win_chance, 1);

        return [
            floor($team1_base_score + $team1_luck),
            floor($team2_base_score + $team2_luck)
        ];
    }
}
