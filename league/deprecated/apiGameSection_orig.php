<?php
    /**
     * $user MUST be set at this point
     */
    require_once('./league_dao.php');
    require_once('./util.php');
    $dao = getDao();
        $games = $dao->getAPIGamesForUser($dao->convertIDForms($user, 'username', 'league_id'));
    destroyDao($dao);
?>
<h3>API Retrieved Games</h3>
<div class="content">
<?php if($games != null && count($games) > 0) { ?>
    <table class="sortable api-stat-table p-0 bordered table hovered">
        <tr>
            <th class="">Date</th>
            <th class="">Time</th>
            <th class="">Champ</th>
            <th class="">Victory</th>
            <th class="">KDA</th>
            <th class="">Mode</th>
        </tr>
        <?php
            foreach($games as $game) {
                $gs = $game['stats'];
                
                if(intval($gs['numDeaths']) < 1)                            { $gs['numDeaths'] = 1; }
                if(intval($gs['championsKilled']) < 0)                      { $gs['championsKilled'] = 0; }
                if(intval($gs['assists']) < 0)                              { $gs['assists'] = 0; }
                if(intval($gs['trueDamageDealtPlayer']) < 0)                { $gs['trueDamageDealtPlayer'] = 0; }
                if(intval($gs['trueDamageDealtToChampions']) < 0)           { $gs['trueDamageDealtToChampions'] = 0; }
                if(intval($gs['trueDamageTaken']) < 0)                      { $gs['trueDamageTaken'] = 0; }

                if(intval($gs['minionsKilled']) < 0)                        { $gs['minionsKilled'] = 0; }
                if(intval($gs['minionsDenied']) < 0)                        { $gs['minionsDenied'] = 0; }
                if(intval($gs['neutralMinionsKilled']) < 0)                 { $gs['neutralMinionsKilled'] = 0; }
                if(intval($gs['neutralMinionsKilledYourJungle']) < 0)       { $gs['neutralMinionsKilledYourJungle'] = 0; }
                if(intval($gs['neutralMinionsKilledEnemyJungle']) < 0)      { $gs['neutralMinionsKilledEnemyJungle'] = 0; }
                if(intval($gs['largestCriticalStrike']) < 0)                { $gs['largestCriticalStrike'] = 0; }
                if(intval($gs['totalTimeCrowdControlDealt']) < 0)           { $gs['totalTimeCrowdControlDealt'] = 0; }
                if(intval($gs['totalHeal']) < 0)                            { $gs['totalHeal'] = 0; }
                if(intval($gs['totalUnitsHealed']) < 0)                     { $gs['totalUnitsHealed'] = 0; }
                if(intval($gs['largestKillingSpree']) < 0)                  { $gs['largestKillingSpree'] = 0; }
                if(intval($gs['largestMultiKill']) < 0)                     { $gs['largestMultiKill'] = 0; }
                if(intval($gs['magicDamageDealtPlayer']) < 0)               { $gs['magicDamageDealtPlayer'] = 0; }
                if(intval($gs['magicDamageDealtToChampions']) < 0)          { $gs['magicDamageDealtToChampions'] = 0; }
                
                $gkda = ($gs['championsKilled'] + $gs['assists']) / $gs['numDeaths'];
                
                $html = '<tr class="api-stat-game">'
                        . '<td class="basic"><div">' . date(Util::$L_DATE_FORMAT, $game['createDate']/1000) . '</div></td>'
                        . '<td class="basic"><div>' . date(Util::$L_TIME_FORMAT, $game['createDate']/1000) . '</div></td>'
                        . '<td class="basic champ"><div>' . getChampHTML($champsById[$game['championId']], 'small') . '</div></td>'
                        . '<td class="basic"><div>' . formatWin($gs['win']) . '</div></td>'
                        . '<td class="basic"><div>' . formatKDA($gkda) . '</div></td>'
                        . '<td class="basic"><div>' . formatMode($game) . '</div></td>'
                        . '<td colspan="6" class="invis secret">'
                            . '<div class="close">X</div>'
                            . '<h4>' . formatMode($game) . ' Match as ' . formatChamp($game['championId']) . ' (' . formatKDA($gkda) . ')</h4>'
                            . '<div class="champ-img-container">' . getChampHTML($champsById[$game['championId']], 'large', false) . '</div>'
                            . '<h4>Items</h4>'
                            .'<div class="api-items-container">';
                
                if(intval($gs['item0']) > -1) { $html .= getItemHTML($itemsById[$gs['item0']]); }
                if(intval($gs['item1']) > -1) { $html .= getItemHTML($itemsById[$gs['item1']]); }
                if(intval($gs['item2']) > -1) { $html .= getItemHTML($itemsById[$gs['item2']]); }
                if(intval($gs['item3']) > -1) { $html .= getItemHTML($itemsById[$gs['item3']]); }
                if(intval($gs['item4']) > -1) { $html .= getItemHTML($itemsById[$gs['item4']]); }
                if(intval($gs['item5']) > -1) { $html .= getItemHTML($itemsById[$gs['item5']]); }
                if(intval($gs['item6']) > -1) { $html .= getItemHTML($itemsById[$gs['item6']]); }

                $html .=      '</div>'
                            . '<h4>Damage Dealt</h4>'
                            . '<table class="sortable">'
                                . '<tr>'
                                    . '<th>Type</th>'
                                    . '<th>Dealt</th>'
                                    . '<th>To Champs</th>'
                                    . '<th>Taken</th>'
                                . '</tr>'
                                . '<tr>'
                                    . '<td>Physical</td>'
                                    . '<td>' . number_format($gs['physicalDamageDealtPlayer']) .         '</td>'
                                    . '<td>' . number_format($gs['physicalDamageDealtToChampions']) .    ' (' . number_format($gs['physicalDamageDealtToChampions']/$gs['physicalDamageDealtPlayer']*100, 2) . '%)</td>'
                                    . '<td>' . number_format($gs['physicalDamageTaken']) .               '</td>'
                                . '</tr>'
                                . '<tr>'
                                    . '<td>Magical</td>'
                                    . '<td>' . number_format($gs['magicDamageDealtPlayer']) .            '</td>'
                                    . '<td>' . number_format($gs['magicDamageDealtToChampions']) .       ' (' . (intval($gs['magicDamageDealtPlayer']) === 0 ? '0' : (number_format($gs['magicDamageDealtToChampions']/$gs['magicDamageDealtPlayer']*100, 2))) . '%)</td>'
                                    . '<td>' . number_format($gs['magicDamageTaken']) .                  '</td>'
                                . '</tr>'
                                . '<tr>'
                                    . '<td>True</td>'
                                    . '<td>' . number_format($gs['trueDamageDealtPlayer']) .            '</td>'
                                    . '<td>' . number_format($gs['trueDamageDealtToChampions']) .       ' (' . number_format($gs['trueDamageDealtToChampions']/($gs['trueDamageDealtPlayer'] > 0 ? $gs['trueDamageDealtPlayer'] : 1)*100, 2) . '%)</td>'
                                    . '<td>' . number_format($gs['trueDamageTaken']) .                  '</td>'
                                . '</tr>'
                                . '<tr>'
                                    . '<td>Total</td>'
                                    . '<td>' . number_format($gs['totalDamageDealt']) .                  '</td>'
                                    . '<td>' . number_format($gs['totalDamageDealtToChampions']) .       ' (' . number_format($gs['totalDamageDealtToChampions']/$gs['totalDamageDealt']*100, 2) . '%)</td>'
                                    . '<td>' . number_format($gs['totalDamageTaken']) .                  '</td>'
                                . '</tr>'
                            . '</table>'
                            . '<h4>Minions</h4>'
                            . '<table class="sortable">'
                                . '<tr>'
                                    . '<th>Type</th>'
                                    . '<th>Number</th>'
                                . '</tr>'
                                . '<tr>'
                                    . '<td>Denied</td>'
                                    . '<td>' . $gs['minionsDenied'] .  '</td>'
                                . '</tr>'
                                . '<tr>'
                                    . '<td>Neutral (All)</td>'
                                    . '<td>' . $gs['neutralMinionsKilled'] .  '</td>'
                                . '</tr>'
                                . '<tr>'
                                    . '<td>Neutral (Ally Jungle)</td>'
                                    . '<td>' . $gs['neutralMinionsKilledYourJungle'] .  '</td>'
                                . '</tr>'
                                . '<tr>'
                                    . '<td>Neutral (Enemy Jungle)</td>'
                                    . '<td>' . $gs['neutralMinionsKilledEnemyJungle'] .  '</td>'
                                . '</tr>'
                                . '<tr>'
                                    . '<td>Killed</td>'
                                    . '<td>' . $gs['minionsKilled'] .  '</td>'
                                . '</tr>'
                            . '</table>'
                            . '<h4>Misc.</h4>'
                            . '<table>'
                                . '<tr><td><div class="api-indiv-stat">Victory</td><td>' . formatWin($gs['win']) . '</div></td></tr>'
                                . '<tr><td><div class="api-indiv-stat">KDA</td><td>' . $gs['championsKilled'] . '/' . $gs['numDeaths'] . '/' . $gs['assists'] . '</div></td></tr>'
                                . '<tr><td><div class="api-indiv-stat">Healing</td><td>' . number_format($gs['totalHeal']) . ' on ' . number_format($gs['totalUnitsHealed']) . ' unit' . ((intval($gs['totalUnitsHealed']) > 1 || intval($gs['totalUnitsHealed']) == 0) ? 's' : '') . '</div></td></tr>'
                                . '<tr><td><div class="api-indiv-stat">Largest Crit</td><td>' . $gs['largestCriticalStrike'] . '</div></td></tr>'
                                . '<tr><td><div class="api-indiv-stat">Largest Multi Kill</td><td>' . $gs['largestMultiKill'] . '</div></td></tr>'
                                . '<tr><td><div class="api-indiv-stat">Longest Killing Spree</td><td>' . $gs['largestKillingSpree'] . '</div></td></tr>'
                                . '<tr><td><div class="api-indiv-stat">Gold</td><td>' . number_format($gs['goldEarned']) . ' (Spent: ' . number_format($gs['goldSpent']) . ')</div></td></tr>'
                                . '<tr><td><div class="api-indiv-stat">Crowd Control Inflicted (s)</td><td>' . number_format($gs['totalTimeCrowdControlDealt']) . '</div></td></tr>'
                                . '<tr><td><div class="api-indiv-stat">Time played (s)</td><td>' . number_format($gs['timePlayed'] /60) . ' m ' . number_format($gs['timePlayed'] % 60) . ' s</div></td></tr>'
                            . '</table>'
                            /*
                            . '<h4>Spells</h4>'
                            . '<table>'
                            . '<tr>'
                                . '<th>Q</th>'
                                . '<th>W</th>'
                                . '<th>E</th>'
                                . '<th>Ultimate</th>'
                                . '<th>'. formatSpells($game['spell1']) .'</th>'
                                . '<th>'. formatSpells($game['spell2']) .'</th>'
                            . '</tr>'
                            . '<tr>'
                                    . '<td>' . $gs['spell1Cast'] . '</td>'
                                    . '<td>' . $gs['spell2Cast'] . '</td>'
                                    . '<td>' . $gs['spell3Cast'] . '</td>'
                                    . '<td>' . $gs['spell4Cast'] . '</td>'
                                    . '<td>' . $gs['summonSpell1Cast'] . '</td>'
                                    . '<td>' . $gs['summonSpell2Cast'] . '</td>'
                                . '</tr>'
                            . '</table>'
                             */
                        . '</td>'
                    . '</tr>';
                    echo $html;
            }
        ?>
    </table>
<?php
}
else {?>
    <div>No games recorded. Either your username is not your league username, or your summoner ID has
        not been added to the DB.</div>   
<?php } ?>
</div>