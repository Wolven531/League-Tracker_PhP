<?php
    function generateGameTableForUser($page, $number) {
        $gamesForUser = getGamesForUser(getUser());
        $html = '<div>No games recorded. Either your username is not your league username, or your summoner ID has not been added to the DB.</div>';
        $totalCount = count($gamesForUser);
        if($gamesForUser != null && $totalCount > 0) {
            $html = generatePageContainer($page, $number, $totalCount) . '<table class="table"><tbody>';
            for($count = 0; $count < $number; $count++) {
                $currGameNum = $page * $number - $number + $count;
                $firstGameClasses = $count == 0 ? ' no-padding-top ' : '';
                if($currGameNum < $totalCount) {
                    $game = $gamesForUser[$currGameNum];
                    $gs = normalizeGameStats($game['stats']);
                    $html .= '<tr><td class="' . $firstGameClasses . '"><div class="carousel bg-' . getTheme() . '" data-role="carousel">';
                    $html .= generateSlides($game, $gs);
                    $html .= '</div></td></tr>';
                }
            }
            $html .= '</tbody></table>';
        }

        return $html;
    }

    function generatePageContainer($page, $number, $totalCount){
        $html = '<div class="navigation-bar bg-' . getTheme() . '">'
            . '<div class="navigation-bar-content">';
        if($page > 1) { $html .= generatePageLink($page-1); }
        if(($page + 1) * $number - $number < $totalCount) { $html .= generatePageLink($page+1, 'place-right'); }
        $html .= '</div></div>';
        return $html;
    }

    function generatePageLink($num, $classes = '') {
        return '<a class="element ' . $classes . '" href="./view.php?userid=' . (isset($_GET['userid']) ? $_GET['userid'] : '') . '&page=' . $num . '">Page ' . $num . '</a>';
    }

    function generateSlides($game, $gs) {
        $champsById = getChampsById();
        $itemsById = getItemsById();
        $notableStr = generateNotables($gs);
        $html = '<div class="slide">';
        $html .= '<div class="fg-white ribbed-' . getTheme() . ' padding5">' . date(Util::$L_DATE_FORMAT, $game['createDate']/1000) . ' (' . date(Util::$L_TIME_FORMAT, $game['createDate']/1000) . ')</div>';
        $html .= '<div class="tile">'
            . generateChamp($champsById[$game['championId']], 'large')
            . '<div class="brand"><span class="badge "></span></div>'
            . '</div>';
        $html .= '<div class="tile double double-vertical bg-darkRed padding5">'
                . '<p class="padding5 fg-white">Mode: ' . formatMode($game) . '</p>'
                . '<p class="padding5 fg-white">Outcome: ' . formatWin($gs['win']) . '</p>'
                . '<p class="padding5 fg-white">K/D/A: ' . formatKDA($gs, Util::$KDA_LONG) . '</p>'
                . '<p class="padding5 fg-white">Ratio: ' . formatKDA($gs, Util::$KDA_SHORT) . '</p>'
                . '<p class="padding5 fg-white">Time: ' . formatTimePlayed($gs['timePlayed']) . '</p>'
                . '<p class="padding5 fg-white">Notable: ' . $notableStr . '</p>'
            . '</div>';
        $html .= '<div class="tile quadro double-vertical bg-darkGreen">'
                . '<p class="padding5 fg-white">Side: ' . formatSide($gs['team']) . '</p>'
                . '<p class="padding5 fg-white">Level: ' . $gs['level'] . '</p>'
                . '<p class="padding5 fg-white">Turrets Killed: ' . $gs['turretsKilled'] . '</p>'
                . '<p class="padding5 fg-white">Wards Bought: ' . ($gs['visionWardsBought'] + $gs['sightWardsBought']) . ' (' . $gs['wardPlaced'] . ' placed)</p>'
                . '<p class="padding5 fg-white">Gold Earned: ' . number_format($gs['goldEarned']) . '</p>'
                . '<p class="padding5 fg-white">Minions: ' . number_format($gs['minionsKilled']) . '</p>'
                . '</div>';
        $html .= '</div>';

        $html .= '<div class="slide">';
            $html .= '<div class="fg-white ribbed-' . getTheme() . ' padding5">' . date(Util::$L_DATE_FORMAT, $game['createDate']/1000) . ' (' . date(Util::$L_TIME_FORMAT, $game['createDate']/1000) . ')</div>';
            $html .= intval($gs['item0']) > -1 ? '<div class="tile double-vertical fg-white bg-' . getRandomColor() . '">' . generateItem($itemsById[$gs['item0']]) . '</div>': '';
            $html .= intval($gs['item1']) > -1 ? '<div class="tile double-vertical fg-white bg-' . getRandomColor() . '">' . generateItem($itemsById[$gs['item1']]) . '</div>': '';
            $html .= intval($gs['item2']) > -1 ? '<div class="tile double-vertical fg-white bg-' . getRandomColor() . '">' . generateItem($itemsById[$gs['item2']]) . '</div>': '';
            $html .= intval($gs['item3']) > -1 ? '<div class="tile double-vertical fg-white bg-' . getRandomColor() . '">' . generateItem($itemsById[$gs['item3']]) . '</div>': '';
            $html .= intval($gs['item4']) > -1 ? '<div class="tile double-vertical fg-white bg-' . getRandomColor() . '">' . generateItem($itemsById[$gs['item4']]) . '</div>': '';
            $html .= intval($gs['item5']) > -1 ? '<div class="tile double-vertical fg-white bg-' . getRandomColor() . '">' . generateItem($itemsById[$gs['item5']]) . '</div>': '';
            $html .= intval($gs['item6']) > -1 ? '<div class="tile double-vertical fg-white bg-' . getRandomColor() . '">' . generateItem($itemsById[$gs['item6']]) . '</div>': '';
        $html .= '</div>';
         /* '</div>'
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
                . '<tr><td><div class="api-indiv-stat">Healing</td><td>' . number_format($gs['totalHeal']) . ' on ' . number_format($gs['totalUnitsHealed']) . ' unit' . ((intval($gs['totalUnitsHealed']) > 1 || intval($gs['totalUnitsHealed']) == 0) ? 's' : '') . '</div></td></tr>'
                . '<tr><td><div class="api-indiv-stat">Largest Crit</td><td>' . $gs['largestCriticalStrike'] . '</div></td></tr>'
                . '<tr><td><div class="api-indiv-stat">Largest Multi Kill</td><td>' . $gs['largestMultiKill'] . '</div></td></tr>'
                . '<tr><td><div class="api-indiv-stat">Longest Killing Spree</td><td>' . $gs['largestKillingSpree'] . '</div></td></tr>'
                . '<tr><td><div class="api-indiv-stat">Gold</td><td>' . number_format($gs['goldEarned']) . ' (Spent: ' . number_format($gs['goldSpent']) . ')</div></td></tr>'
                . '<tr><td><div class="api-indiv-stat">Crowd Control Inflicted (s)</td><td>' . number_format($gs['totalTimeCrowdControlDealt']) . '</div></td></tr>'
            . '</table>'
        . '</td>'
    . '</tr>';
 * 
 */

        return $html;
    }

    function generateNotables($gs) {
        $notableStr = '';
        if($gs['didntDie']) { $notableStr .= ', Didn\'t die'; }
        if($gs['largestMultiKill'] > 5) { $notableStr .= ', Hexa Kill'; }
        else if($gs['largestMultiKill'] > 4) { $notableStr .= ', Penta Kill'; }
        else if($gs['largestMultiKill'] > 3) { $notableStr .= ', Quadra Kill'; }
        else if($gs['largestMultiKill'] > 2) { $notableStr .= ', Triple Kill'; }
        else if($gs['largestMultiKill'] > 2) { $notableStr .= ', Triple Kill'; }

        if($gs['timePlayed'] < (60*15)) { $notableStr .= ', Short Game'; }
        else if($gs['timePlayed'] > (60*45)) { $notableStr .= ', Long Game'; }

        if(intval($gs['item0']) > -1 && intval($gs['item1']) > -1
        && intval($gs['item2']) > -1 && intval($gs['item3']) > -1
        && intval($gs['item4']) > -1 && intval($gs['item5']) > -1) { $notableStr .= ', Full Items'; }

        if(intval($gs['largestCriticalStrike']) > 2000) { $notableStr .= ', 2k+ Crit'; }
        else if(intval($gs['largestCriticalStrike']) > 1000) { $notableStr .= ', 1k+ Crit'; }

        if(intval($gs['totalUnitsHealed']) > 4) { $notableStr .= ', Healed Entire Team'; }
        else if(intval($gs['totalUnitsHealed']) === 1) { $notableStr .= ', Healed Only Self'; }
        else if($gs['didntHeal']) { $notableStr .= ', Didn\'t Heal'; }

        if(strlen($notableStr) > 0) { $notableStr = substr($notableStr, 2); }
        else { $notableStr = 'Nothing...'; }

        return $notableStr;
    }

    function generateCustomTable($opts){
        $opts['classes'] = isset($opts['classes']) ? $opts['classes'] : ' ';
        $opts['hovered'] = isset($opts['hovered']) ? ' hovered ' : ' ';
        $opts['sortable'] = isset($opts['sortable']) ? ' sortable ' : ' ';
        $html = '<table class="bordered table ' . $opts['sortable'] . $opts['hovered'] . $opts['classes'] . '"><thead>';
        if($opts['headers']) {
            $html .= '<tr>';
            foreach($opts['headers'] as $header) {
                $header['sortable'] = isset($header['sortable']) &&  $header['sortable'] ? ' ' : ' sorttable_nosort ';
                $header['sortable'] = $header['number']  ? ' sorttable_numeric ' : ' ';
                $hint = $header['hint'] ? ' data-hint="' . $header['hint'] . '" data-hint-position="right" ' : '';
                $html .= '<th class="' . $header['sortable'] . '"><div ' . $hint . '>' . $header['title'] . '</div></th>';
            }
            $html .= '</tr>';
        }
        $html .= '</thead><tbody>';
        if($opts['rows']) {
            foreach($opts['rows'] as $row) {
                $html .= '<tr>' . $row . '</tr>';
            }
        }

        $html .= '</tbody></table>';
        return $html;
    }
    
    function generateUserLink($ref, $title, $class = ''){
        return '<a href="./view.php?userid=' . $ref . '" class="' . $class . '">' . $title . '</a>';
    }

    function generateChamp($champ, $size, $includeName = true) {
        $html = '';
        $cssClass = $size == 'large' ? 'large-champ-img' : 'small-champ-img';
        $html .= '<a href="http://www.wolven531.com/league/champ.php?champ=' . $champ['id'] . '"><img class="' . $cssClass . ' polaroid bg-black" alt="Champion Image" data-hint="Champion|' . formatChamp($champ['id']) . '" data-hint-position="right" src="http://ddragon.leagueoflegends.com/cdn/4.3.18/img/champion/' . $champ['secret_name'] . '.png" /></a>';
        if($includeName) {
            $html .= formatChamp($champ['id']);
        }
        return $html;
    }

    function generateItem($item) {
        $item = sanitizeItem($item);
        $html =
            '<img width="120px" class="margin10" style="height:' . $item['img_h'] . 'px; width:' . $item['img_w'] . 'px; '
            . ' background: url(\'//ddragon.leagueoflegends.com/cdn/4.2.6/img/sprite/' . $item['img_sprite'] . '\') ' 
            . ' ' . $item['img_x'] . 'px ' . $item['img_y'] . 'px no-repeat;" src="" />'
            . '<div class="margin5">' . $item['name'] . '</div>'
                . '<div class="cost margin5">Cost: <span class="gold">' . number_format($item['cost_total']) . '</span></div>'
                . '<div class="margin5">Base: <span class="gold">' . number_format($item['cost_base']) . '</span></div>'
                . '<div class="margin5">Sell: <span class="sell">' . number_format($item['cost_sell']) . '</span></div>';

        return $html;
    }

    function generateAwardStat($stat, $col, $opts = null)
    {
        $stats = getLeaderStats();
        $users = getUsersById();
        $html = '<ul class="api-stat-list">';
        for($a = 0; $a < count($stats[$stat]); $a++)
        {
            $kdaString = '';
            $l = $stats[$stat][$a];
            if($opts)
            {
                $mode = $opts['mode'];
                $win = $opts['win'];
                $excludeBots =  $opts['bots'];
                $dao = $opts['dao'];
                $includeKda = $opts['includeKDA'];
                $dao = getDao();
                if($includeKda) { $kdaString = ' (' . $dao->getHomemadeAPIKDA($l['summonerId'], $mode, $win, $excludeBots) .')'; }
                destroyDao($dao);
            }
            $tmpUser = $users[$l['summonerId']]['username'];
            $highlightClass = isUserRelated($tmpUser) ? ' bg-yellow ' : '';
            
            $html .= '<li class="padding5 inline' . $highlightClass . '">'
                . generateUserLink($tmpUser, $tmpUser, 'padding5') . ' - '
                    . ($opts['isInt'] ? number_format($l[$col]) :
                        ($opts['isKDA'] ? number_format($l[$col], 4) : 
                            number_format($l[$col], 2))) . $kdaString
                .'</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    function generateAwardRow($title, $plaintext, $statpre, $statpost, $col, $opts)
    {
        $html = '<tr>';
        $html .= '<td><span data-hint="' . $plaintext . '|' . $title . '" data-hint-position="top">' . $plaintext .'</span></td>';
        $html .= '<td>' . generateAwardStat($statpre . $statpost, $col, $opts) . '</td>';
        $opts['mode'] = 'ARAM';
        $html .= '<td>' . generateAwardStat($statpre . 'ARAM' . $statpost . $stat, $col, $opts) . '</td>';
        $opts['mode'] = 'Classic';
        $html .= '<td>' . generateAwardStat($statpre . 'Classic' . $statpost . $stat, $col, $opts) . '</td>';

        $html .= '</tr>';
        return $html;
    }

    function generateCommunityStatRow($commStats, $title, $stats)
    {
        $html = '';
        $html =  '<tr>'
            . '<td>' . $title . '</td>';
        for($a = 0; $a < count($stats); $a++)
        {
            $html .= '<td>' . number_format($commStats[$stats[$a]]) . '</td>';
        }
        $html .= '</tr>';

        return $html;
    }
    
    function generateTopChamp($userid, $statName, $color = '')
    {
        $champsById = getChampsById();
        $userStats = getUserStats($userid);
        $html = '';
        for($a = 0; $a < count($userStats[$statName]); $a++) {
            $html .= '<div class="tile">'
            . generateChamp($champsById[$userStats[$statName][$a]['champ']], 'large', true)
            . '<div class="brand"><span class="badge ' . $color . '">' . $userStats[$statName][$a]['outcome'] . '</span></div>'
            . '</div>';
        }
        return $html;
    }
?>