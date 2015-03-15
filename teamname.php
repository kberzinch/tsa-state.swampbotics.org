<?php
namespace TSAState;

function getTeamName($team)
{
    $curl = curl_init('http://api.vex.us.nallen.me/get_teams?team='.$team);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($curl);
    curl_close($curl);

    $output = json_decode($output, true);

    $teamname = $output['result'][0]['team_name'];
    $robotname = $output['result'][0]['robot_name'];

    if ($robotname === '' or $robotname === $teamname) {
        $displayname = $teamname;
    } else {
        $displayname = $teamname.' - '.$robotname;
    }

    return $displayname;
}
