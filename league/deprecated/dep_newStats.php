<?php
    require_once('./league_dao.php');
    session_start();
    $type = strtoupper($_SERVER['REQUEST_METHOD']);
    $dao = new LeagueImpl(DAO::$GO_DADDY);
    $dao->connect();
        $games = $dao->getAPIGames();
        $users = $dao->getUsers();
        $champs = $dao->getChamps();
    $dao->disconnect();
    
    $usersBySummId = array();
    for($a = 0; $a < count($users); $a++)
    {
        $u = $users[$a];
        $usersBySummId[$u['league_id'] . ' '] = $u;
    }
    
    $champsById = array();
    for($a = 0; $a < count($champs); $a++)
    {
        $c = $champs[$a];
        $champsById[$c['league_id'] . ' '] = $c;
    }
    
    $invalidCols =  array(
        'subType', 'statRef', 'invalid', 
        'fellowPlayerRef', 'gameId', 'id', 
        'stats', 'fellows', 'level');
    $invalidStats = array(
        'id', 'item0', 'item1', 'item2', 'item3', 
        'item4', 'item5', 'item6', 'nexusKilled', 'magicDamageDealtPlayer', 'magicDamageDealtToChampions',
        'physicalDamageDealtPlayer', 'physicalDamageDealtToChampions', 
        'totalTimeCrowdControlDealt', 'totalUnitsHealed', 'trueDamageTaken',
        'physicalDamageTaken', 'magicDamageTaken', 'largestKillingSpree',
        'killingSprees', 'team', 'timePlayed', 'neutralMinionsKilled',
        'neutralMinionsKilledEnemyJungle', 'neutralMinionsKilledYourJungle',
        'trueDamageDealtPlayer', 'trueDamageDealtToChampions', 'wardPlaced',
        'largestCriticalStrike', 'doubleKills', 'barracksKilled', 'tripleKills',
        'wardKilled', 'sightWardsBought');
    $COL_HEADERS = array(
        'championId' => 'Champ',
        'createDate' => 'Date',
        'gameMode' => 'Mode',
        'gameType' => 'Type',
        'level' => 'Level',
        'mapId' => 'Map',
        'spell1'=>'Spell 1',
        'spell2'=>'Spell 2',
        //'subType'=>'Spell 1',
        'teamId' => 'Side',
        'summonerId' => 'Player'
    );
    $COL_FORMATTERS = array(
        'championId' => formatChamp,
        'createDate' => formatDate,
        'gameMode' => formatMode,
        'gameType' => formatType,
        'level' => defaultFormat,
        'mapId' => formatMap,
        'spell1'=> formatSpells,
        'spell2'=> formatSpells,
        //'subType'=>'Spell 1',
        'teamId' => formatSide
    );
    $STAT_LABELS = array(
        'assists' => 'Assists',
        'championsKilled' => 'Kills',
        'goldEarned' => 'Gold',
        'largestMultiKill' => 'Largest Multi-kill',
        'minionsKilled' => 'Minions',
        'numDeaths' => 'Deaths',
        'totalDamageDealt' => 'Dmg. Dealt',
        'totalDamageTaken' => 'Dmg. Taken',
        'totalHeal' => 'Healing',
        'totalDamageDealtToChampions' => 'Dmg. To Champs',
        'goldSpent' => 'Gold Spent',
        'level' => 'Champ Level',
        'turretsKilled' => 'Turrets',
        'win' => 'Victory',
        'pentaKills' => 'Pentas',
        'quadraKills' => 'Quadras'
    );
    $STAT_FORMATTERS = array(
        'win' => formatWin
    );
    
    function defaultFormat($val) { return $val; }
    
    function formatChamp($id) { return $id; }
    
    function formatDate($time) { return date('m/d/Y', $time/1000) . '<br />' . date('h:i:s A', $time/1000); }
    
    function formatWin($val)
    {
        $return = $val ? 'Win' : 'Loss';
        return $return;
    }
    
    function formatMode($val) {
        $return = $val;
        switch ($val)
        {
            case 'ARAM':
                $return = 'ARAM';
            break;
            default:
                $return = ucfirst(strtolower($val));
            break;
        }
        return $return;
    }
    
    function formatType($val) {
        $return = $val;
        switch ($val)
        {
            case 'MATCHED_GAME':
                $return = 'Normal';
            break;
            default:
            break;
        }
        return $return;
    }
    
    function formatMap($val)
    {
        $return = $val;
        switch ($val)
        {
            case 12:
                $return = 'Howling Abyss';
            break;
            case 1:
                $return = 'Summoner\'s Rift';
            break;
            case 10:
                $return = 'Twisted Treeline';
            break;
            default:
            break;
        }
        return $return;
    }
    
    function formatSide($val)
    {
        $return = $val;
        switch ($val)
        {
            case 100:
                $return = 'Blue';
            break;
            case 200:
                $return = 'Purple';
            break;
            default:
            break;
        }
        return $return;
    }
    
    function formatSpells($val)
    {
        $return = $val;
        switch ($val)
        {
            case 21:
                $return = 'Barrier';
            break;
            case 4:
                $return = 'Flash';
            break;
            case 12:
                $return = 'Teleport';
            break;
            case 3:
                $return = 'Exhaust (?)';
            break;
            case 14:
                $return = 'Ignite';
            break;
            case 6:
                $return = $val . ' (?)';
            break;
            case 7:
                $return = 'Heal (?)';
            break;
            case 2:
                $return = 'Clairvoyance';
            break;
            default:
            break;
        }
        return $return;
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>New Stats Page</title>
        <link type="text/css" rel="stylesheet" href="./style.css" />
        <link type="image/icon" rel="icon" href="./favicon.png" />
        
        <script src="../projects/scripts/sortable.js"></script>
        <script src="../projects/scripts/numeral.js"></script>
        <script src="../projects/scripts/jquery.js"></script>
    </head>
    <body>
        <?php
            include('./header.html');
            include('./navigation.php');
        ?>
        <h3>All Games Recorded</h3>
        <div class="content">
            <table class="sortable api-stat-table">
                <tr>
                    <?php
                        $headerGame = $games[0];
                        foreach ($headerGame as $col => $val)
                        {
                            if(!in_array($col, $invalidCols))
                            {
                                echo '<th>' . $COL_HEADERS[$col] . '</th>';
                            }
                        }
                    ?>
                    <th>Stats</th>
                </tr>
                <?php
                    for($a = 0; $a < count($games); $a++)
                    {
                        $game = $games[$a];
                        echo '<tr>';
                        foreach ($game as $col => $val)
                        {
                            if(!in_array($col, $invalidCols))
                            {
                                if($col == 'summonerId') {
                                    echo '<td>' . $usersBySummId[$val . ' ']['username'] . '</td>';
                                }
                                else if($col == 'championId') {
                                    echo '<td>' . $champsById[$val . ' ']['name'] . '</td>';
                                }
                                else {
                                    echo '<td>' . $COL_FORMATTERS[$col]($val) . '</td>';
                                    
                                }
                            }
                            else if($col == 'stats')
                            {
                                $s = $val;
                                echo '<td><ul>';
                                foreach($s as $label => $quantity)
                                {
                                    if($quantity > -1 
                                        && !in_array($label, $invalidStats))
                                    {
                                        $quantity = $STAT_FORMATTERS[$label] ? $STAT_FORMATTERS[$label]($quantity) : number_format($quantity);
                                        $label = $STAT_LABELS[$label] ? $STAT_LABELS[$label] : $label;
                                        echo '<li><div class="api-stat-label">' . $label . ':</div> <div class="api-stat-quantity">' . $quantity . '</div></li>';
                                    }
                                }
                                echo '</ul></td>';
                            }
                        }
                        echo '</tr>';
                    }
                ?>
            </table>
        </div>
        <?php
          include('./footer.php');
        ?>
    </body>
</html>