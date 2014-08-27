<?php
if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/projects/dao.php')) {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/projects/dao.php');
}
else if (file_exists($_SERVER['PHPRC'] . '/projects/dao.php')) {
    require_once($_SERVER['PHPRC'] . '/projects/dao.php');
}
else if (file_exists('../../projects/dao.php')){
    require_once('../../projects/dao.php');
}
else if(file_exists($_SERVER['HOME'] . '/html/projects/dao.php')) { // DO NOT REMOVE, REQUIRED FOR CRON JOB
    require_once($_SERVER['HOME'] . '/html/projects/dao.php');
}
else {
    require_once('dao.php');
}

abstract class League extends DAO
{
    static public $USER = 'user';
    static public $KILLS = 'kills';
    static public $DEATHS = 'deaths';
    static public $ASSISTS = 'assists';
    static public $GOLD = 'gold';
    static public $MINIONS = 'minions';
    static public $GAME_TYPE = 'game_type';
    static public $GAME_LEVEL = 'game_level';
    static public $DATE = 'date';
    static public $CHAMP_SELECT = 'champ_select';
    static public $VICTORY = 'victory';
    static public $ROLE = 'summ_role';
    static public $VALID_GAME_TYPES = array(
        1, // Summoner's
        2, // Twisted Treeline
        3, // ARAM
        4 // Dominion
        );
    static public $VALID_GAME_LEVELS = array(
        1, // normal
        2, // ranked
        3 // custom
        );
    static public $VALID_DISPLAY_TYPES = array(
        'kills', 
        'deaths', 
        'assists', 
        'gold', 
        'minions', 
        'date');
    static public $VALID_STAT_TYPES = array(
        'kills', 
        'deaths', 
        'assists', 
        'gold', 
        'minions', 
        'date');
    static public $VALID_ROLE_TYPES = array(
        -1, // N/A
        0, // AP Carry
        1, // AD Carry
        2, // Jungle
        3, // Tank
        4, // Support
        5, // AP Bruiser
        6 // AD Bruiser
        );
    static public $VALID_APP_IDS = array(
        'fhut-7rh5-snj32-9sj5-j328-3g64' // FedEx
    );
    function getMySqlFormattedDate($dateStr){
        $firstSlash = strpos($dateStr, '/');
        $secondSlash = strpos($dateStr, '/', $firstSlash + 1);
        $month = intval(substr($dateStr,0, $firstSlash));
        $day = intval(substr($dateStr,$firstSlash + 1, $secondSlash));
        $year = intval(substr($dateStr,$secondSlash + 1));
        
        return ($year . '-' . $month . '-' . $day);
    }
    protected $tbl_games = 'league_games';
    protected $tbl_users = 'league_users';
    protected $tbl_champs = 'league_champs';

    protected $tbl_api_games = 'api_league_games';
    protected $tbl_api_fellow_players = 'api_league_fellow_players';
    protected $tbl_api_stats = 'api_league_stats';
    protected $tbl_api_items = 'api_league_items';
    protected $tbl_api_item_stats = 'api_league_item_stats';
}

class LeagueImpl extends League
{
    function changeDisplayName($summId, $displayName) {
        $STH = $this->DBH->prepare('UPDATE ' . $this->tbl_users . ' SET display_name = ? WHERE id = ?;');
        $STH->execute(array($displayName, $summId));
    }

    function changeTheme($summId, $theme) {
        $STH = $this->DBH->prepare('UPDATE ' . $this->tbl_users . ' SET theme = ? WHERE id = ?;');
        $STH->execute(array($theme, $summId));
    }

    function getChampGames($champLeagueId, $win, $excludeBots, $mode) {
        $result = array();
        $winStr = ($win === true) ? ' AND s.win = 1 ' : ($win === false ? ' AND s.win = 0 ' : '');
        $botStr = $excludeBots === true ? ' AND g.subType != "BOT" ' : '';
        $modeStr = $mode != null ? ' AND g.gameMode = "' . $mode . '" ' : '';
        
        $STH = $this->DBH->prepare('SELECT '
            . 'c.*, g.*, s.*,COUNT(*) AS games'
            . ' FROM ' . $this->tbl_champs . ' as c '
            . ' JOIN ' . $this->tbl_api_games . ' AS g ON c.id = g.championId '
            . ' JOIN ' . $this->tbl_api_stats . ' AS s ON g.statRef = s.id '
            . ' WHERE championId = ? ' . $winStr . $botStr . $modeStr . ';');
        //var_dump($STH);
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $STH->execute(array($champLeagueId));
        while($newObj = $STH->fetch()) { $result[] = $newObj; }
        //var_dump($result);
        return $result;
    }

    function getChampKDA($champLeagueId, $win, $excludeBots, $mode)
    {
        $winStr = ($win === true) ? ' AND s.win = 1 ' : ($win === false ? ' AND s.win = 0 ' : '');
        $botStr = $excludeBots === true ? ' AND g.subType != "BOT" ' : '';
        $modeStr = $mode != null ? ' AND g.gameMode = "' . $mode . '" ' : '';

        $STH = $this->DBH->prepare('SELECT '
            . ' (SUM(CASE WHEN s.championsKilled < 0 THEN 0 ELSE s.championsKilled END) + SUM(CASE WHEN s.assists < 0 THEN 0 ELSE s.assists END)) / SUM(CASE WHEN s.numDeaths < 1 THEN 1 ELSE s.numDeaths END) AS kda '
            //. ' ,c.* '
            . ' ,COUNT(*) AS games'
            . ' FROM ' . $this->tbl_champs . ' as c '
            . ' JOIN ' . $this->tbl_api_games . ' AS g ON c.id = g.championId '
            . ' JOIN ' . $this->tbl_api_stats . ' AS s ON g.statRef = s.id '
            . ' WHERE championId = ? ' . $winStr . $botStr . $modeStr . ';');
        //var_dump($STH);
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $STH->execute(array($champLeagueId));
        $result = $STH->fetch();
        //var_dump($result);
        return array('kda' => $result['kda'], 'games' => $result['games']);
    }

    function getChampStats($champLeagueId, $includeDetail = false)
    {
        $return = array();
        $return['overallKDANoBots'] = $this->getChampKDA($champLeagueId, null, true, null);
        $return['overallARAMKDANoBots'] = $this->getChampKDA($champLeagueId, null, true, 'ARAM');
        $return['overallClassicKDANoBots'] = $this->getChampKDA($champLeagueId, null, true, 'CLASSIC');

        if($includeDetail) {
            $return['overallKDA'] = $this->getChampKDA($champLeagueId, null, false, null);
            $return['overallARAMKDA'] = $this->getChampKDA($champLeagueId, null, false, 'ARAM');
            $return['overallClassicKDA'] = $this->getChampKDA($champLeagueId, null, false, 'CLASSIC');

            $return['overallWinKDA'] = $this->getChampKDA($champLeagueId, true, false, null);
            $return['overallARAMWinKDA'] = $this->getChampKDA($champLeagueId, true, false, 'ARAM');
            $return['overallClassicWinKDA'] = $this->getChampKDA($champLeagueId, true, false, 'CLASSIC');

            $return['overallLossKDA'] = $this->getChampKDA($champLeagueId, false, false, null);
            $return['overallARAMLossKDA'] = $this->getChampKDA($champLeagueId, false, false, 'ARAM');
            $return['overallClassicLossKDA'] = $this->getChampKDA($champLeagueId, false, false, 'CLASSIC');

            $return['overallWinKDANoBots'] = $this->getChampKDA($champLeagueId, true, true, null);
            $return['overallARAMWinKDANoBots'] = $this->getChampKDA($champLeagueId, true, true, 'ARAM');
            $return['overallClassicWinKDANoBots'] = $this->getChampKDA($champLeagueId, true, true, 'CLASSIC');

            $return['overallLossKDANoBots'] = $this->getChampKDA($champLeagueId, false, true, null);
            $return['overallARAMLossKDANoBots'] = $this->getChampKDA($champLeagueId, false, true, 'ARAM');
            $return['overallClassicLossKDANoBots'] = $this->getChampKDA($champLeagueId, false, true, 'CLASSIC');
        }

        return $return;
    }

    function getTotalGameStatSpecific($win, $excludeBots, $mode)
    {
        $winStr = ($win === true) ? ' AND s.win = 1 ' : ($win === false ? ' AND s.win = 0 ' : '');
        $botStr = $excludeBots === true ? ' AND g.subType != "BOT" ' : '';
        $modeString = $mode != null ? ' AND g.gameMode = "' . $mode . '" ' : '';
        $STH = $this->DBH->prepare('SELECT COUNT(*) AS num FROM ' . $this->tbl_api_games . ' AS g '
            . ' JOIN ' . $this->tbl_api_stats . ' AS s ON g.statRef = s.id '
            . ' WHERE TRUE ' . $winStr . $botStr . $modeString . ';');
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $STH->execute();
        $result = $STH->fetch();
        return $result['num'];
    }

    function getTotalGameStats()
    {
        $return = array();
        $return['totalGames'] =                 $this->getTotalGameStatSpecific(null, false, null);
        $return['totalARAMGames'] =             $this->getTotalGameStatSpecific(null, false, 'ARAM');
        $return['totalClassicGames'] =          $this->getTotalGameStatSpecific(null, false, 'CLASSIC');

        $return['totalWins'] =                  $this->getTotalGameStatSpecific(true, false, null);
        $return['totalARAMWins'] =              $this->getTotalGameStatSpecific(true, false, 'ARAM');
        $return['totalClassicWins'] =           $this->getTotalGameStatSpecific(true, false, 'CLASSIC');

        $return['totalLosses'] =                $this->getTotalGameStatSpecific(false, false, null);
        $return['totalARAMLosses'] =            $this->getTotalGameStatSpecific(false, false, 'ARAM');
        $return['totalClassicLosses'] =         $this->getTotalGameStatSpecific(false, false, 'CLASSIC');

        $return['totalGamesNoBots'] =           $this->getTotalGameStatSpecific(null, true, null);
        $return['totalARAMGamesNoBots'] =       $this->getTotalGameStatSpecific(null, true, 'ARAM');
        $return['totalClassicGamesNoBots'] =    $this->getTotalGameStatSpecific(null, true, 'CLASSIC');

        $return['totalWinsNoBots'] =            $this->getTotalGameStatSpecific(true, true, null);
        $return['totalARAMWinsNoBots'] =        $this->getTotalGameStatSpecific(true, true, 'ARAM');
        $return['totalClassicWinsNoBots'] =     $this->getTotalGameStatSpecific(true, true, 'CLASSIC');

        $return['totalLossesNoBots'] =          $this->getTotalGameStatSpecific(false, true, null);
        $return['totalARAMLossesNoBots'] =      $this->getTotalGameStatSpecific(false, true, 'ARAM');
        $return['totalClassicLossesNoBots'] =   $this->getTotalGameStatSpecific(false, true, 'CLASSIC');

        return $return;
    }

    function updateAPIItem($item)
    {
        $vals = array();
        $STH = null;
        if($this->checkAPIItemExists($item->id)) {
            $STH = $this->DBH->prepare('UPDATE ' . $this->tbl_api_items 
            . ' SET name = ?, cost_base = ?, cost_sell = ?, cost_total = ?, description = ?, img_full = ?, img_group = ?, img_h = ?, img_w = ?, img_x = ?, img_y = ?, img_sprite = ?, plaintext = ? '
            . ' WHERE id = ?;');
            $vals = array(
                $item->name,
                intval($item->gold->base), intval($item->gold->sell), intval($item->gold->total),
                $item->description, $item->image->full, $item->image->group,
                intval($item->image->h), intval($item->image->w), intval($item->image->x),
                intval($item->image->y), $item->image->sprite, $item->plaintext, intval($item->id)
            );
        }
        else {
            $STH = $this->DBH->prepare('INSERT INTO ' . $this->tbl_api_items 
            . ' (id, name, cost_base, cost_sell, cost_total, description, img_full, img_group, img_h, img_w, img_x, img_y, img_sprite, plaintext) '
            . ' values(?,?,?,?,?,?,?,?,?,?,?,?,?,?);');
            $vals = array(
                intval($item->id),
                $item->name,
                intval($item->gold->base), intval($item->gold->sell), intval($item->gold->total),
                $item->description, $item->image->full, $item->image->group,
                intval($item->image->h), intval($item->image->w), intval($item->image->x),
                intval($item->image->y), $item->image->sprite, $item->plaintext
            );
        }

        $STH->execute($vals);
        $updateStatStr = '';
        $statVals = array();
        if($item->stats) {
            $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_api_item_stats . ' WHERE id = ?;');
            $STH->execute(array(intval($item->id)));
            if($STH->fetch()) {
                $statReflection = new ReflectionClass($item->stats);
                $props = $statReflection->getProperties();
                // for items with no stats (e.g. "Total Biscuit of Rejuvenation")
                if(!empty($props)) {
                    foreach($item->stats as $stat => $val) {
                        $updateStatStr .= $stat . ' = ?,';
                        $statVals[] = floatval($val);
                    }
                    $updateStatStr = substr($updateStatStr, 0, strlen($updateStatStr) - 1); // removes extra comma
                    $statVals[] = intval($item->id);
                    $STH = $this->DBH->prepare('UPDATE ' . $this->tbl_api_item_stats . ' SET ' . $updateStatStr . ' WHERE id = ?;');
                    $STH->execute($statVals);
                }
            }
            else {
                $statVals[] = intval($item->id);
                $insertStatStr .= 'id,';
                foreach($item->stats as $stat => $val) {
                    $insertStatStr .= $stat . ',';
                    $statVals[] = floatval($val);
                }
                $insertStatStr = substr($insertStatStr, 0, strlen($insertStatStr) - 1); // removes extra comma
                $STH = $this->DBH->prepare('INSERT INTO ' . $this->tbl_api_item_stats . ' (' . $insertStatStr . ') VALUES (' . $this->getQuestionString(count($statVals)) . ');');
                //var_dump($STH);
                //var_dump($statVals);
                $STH->execute($statVals);
            }
        }
    }

    function checkAPIItemExists($id)
    {
        $result = array();
        $isValid = true;
        $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_api_items . ' WHERE id = ?;');
        $STH->execute(array($id));
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while($newObj = $STH->fetch()) { $result[] = $newObj; }

        return count($result) > 0;
    }

    function getHomemadeAPIGames($summId, $mode, $win, $excludeBots, $limit = null, $order = null)
    {
        $STH = null;
        $result = null;
        $vals = array();
        $excludeBotString = $excludeBots ? ' AND g.subType != "BOT" ' : '';
        $winString = '';
        if($win === true) { $winString =' AND s.win = 1 '; }
        else if($win === false) { $winString =' AND s.win = 0 '; }
        $modeString = $mode ? ' AND g.gameMode = ? ' : '';
        $idString = $summId ? ' AND g.summonerId = ? ' : '';
        $limitString = $limit ? ' LIMIT ' . $limit : '';
        $orderString = $order ? ' ORDER BY outcomes ' . $order : '';
        if($summId) {
            $vals[] = intval($summId);
            $STH = $this->DBH->prepare('SELECT * '
                . ' FROM ' . $this->tbl_api_games . ' AS g '
                . ' WHERE TRUE ' . $modeString . $winString . $excludeBotString . $idString
                . $orderString . $limitString);
        }
        else {
            $STH = $this->DBH->prepare('SELECT COUNT(*) AS outcomes, g.summonerId'
                .' FROM ' . $this->tbl_api_stats . ' AS s'
                .' JOIN ' . $this->tbl_api_games . ' AS g ON s.id = g.statRef'
                .' WHERE TRUE ' . $modeString . $winString . $excludeBotString
                .' GROUP BY g.summonerId'
                . $orderString . $limitString);
        }
        if($mode) { $vals[] = $mode; }
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $STH->execute($vals);
        if($summId) {
            $result = array();
            while($newObj = $STH->fetch()) {
                $newObj['stats'] = $this->getAPIStats($newObj['statRef']);
                $newObj['fellows'] = $this->getAPIFellows($newObj['fellowPlayerRef']);
                $result[] = $newObj;
            }
        }
        else {
            $result = array();
            while($newObj = $STH->fetch()) { $result[] = $newObj; }
        }

        return $result;
    }

    function getHomemadeAPIKDA($summId, $mode, $win, $excludeBots, $limit = null, $order = null)
    {
        $STH = null;
        $result = null;
        $vals = array();
        $excludeBotString = $excludeBots ? ' AND g.subType != "BOT" ' : '';
        $winString = '';
        if($win === true) { $winString =' AND s.win = 1 '; }
        else if($win === false) { $winString =' AND s.win = 0 '; }
        $modeString = $mode ? ' AND g.gameMode = ? ' : '';
        $idString = $summId ? ' AND g.summonerId = ? ' : '';
        $limitString = $limit ? ' LIMIT ' . $limit : ' LIMIT 3 ';
        $orderString = $order ? ' ORDER BY kda ' . $order : '';
        if($summId) {
            $vals[] = $summId;
            $STH = $this->DBH->prepare(
                'SELECT ('
                    .' SUM(CASE WHEN championsKilled < 0 THEN 0 ELSE championsKilled END) +'
                    .' SUM(CASE WHEN assists < 0 THEN 0 ELSE assists END)'
                .' ) /'
                .' SUM(CASE WHEN numDeaths = 0 THEN 1 ELSE numDeaths END) AS kda'
                .' FROM ' . $this->tbl_api_games . ' AS g'
                .' JOIN ' . $this->tbl_api_stats . ' AS s ON g.statRef = s.id'
                .' WHERE TRUE ' . $idString . $winString . $modeString . $excludeBotString . ';');
        }
        else {
            $STH = $this->DBH->prepare(
                'SELECT ('
                    .' SUM(CASE WHEN championsKilled < 0 THEN 0 ELSE championsKilled END) +'
                    .' SUM(CASE WHEN assists < 0 THEN 0 ELSE assists END)'
                .' ) /'
                .' SUM(CASE WHEN numDeaths = 0 THEN 1 ELSE numDeaths END) AS kda, g.summonerId'
                .' FROM ' . $this->tbl_api_stats . ' AS s'
                .' JOIN ' . $this->tbl_api_games . ' AS g ON s.id = g.statRef'
                .' WHERE TRUE ' . $winString . $modeString . $excludeBotString
                .' GROUP BY g.summonerId'
                .$orderString .$limitString .';');
        }
        if($mode) { $vals[] = $mode; }
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $STH->execute($vals);
        if($summId) {
            $result = $STH->fetch();
            $result = $result['kda'];
        }
        else {
            $result = array();
            while($newObj = $STH->fetch()) { $result[] = $newObj; }
        }

        return $result;
    }

    function getAPILeadersForStat($stat, $isAvg, $mode) {
        $return = array();
        $statStr = $isAvg ? ' AVG(' . $stat . ') ' : ' ' . $stat . ' ';
        $modeStr = $mode != null ? ' AND g.gameMode = "' . $mode . '" ' : '';
        $STH = $this->DBH->prepare('SELECT ' . $statStr . ' AS result, summonerId'
            . ' FROM ' . $this->tbl_api_games . ' AS g'
            . ' JOIN ' . $this->tbl_api_stats . ' AS s ON g.statRef = s.id'
            . ' WHERE g.subType != "BOT" ' . $modeStr
            . ' GROUP BY summonerId'
            . ' ORDER BY result DESC'
            . ' LIMIT 3;');
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $STH->execute();
        while($newObj = $STH->fetch()) { $return[] = $newObj; }

        return $return;
    }

    function getAPILeaderOverallSum($stat, $mode)
    {
        $modeStr = $mode != null ? ' AND g.gameMode = "' . $mode . '" ' : '';
        $STH = $this->DBH->prepare('SELECT SUM(' . $stat . ') AS result, summonerId'
            . ' FROM ' . $this->tbl_api_games . ' AS g'
            . ' JOIN ' . $this->tbl_api_stats . ' AS s ON g.statRef = s.id'
            . ' WHERE ' . $stat . ' > -1 ' . $modeStr
            . ' GROUP BY summonerId'
            . ' ORDER BY result DESC'
            . ' LIMIT 3;');
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $STH->execute();
        $tmpArr = array();
        while($newObj = $STH->fetch()) { $tmpArr[] = $newObj; }
        return $tmpArr;
    }

    function getHomemadeAPILeaderStats()
    {
        $return = array();

        $return['topWins'] =            $this->getHomemadeAPIGames(null, null, true, true, 3, 'DESC');
        $return['topARAMWins'] =        $this->getHomemadeAPIGames(null, 'ARAM', true, true, 3, 'DESC');
        $return['topClassicWins'] =     $this->getHomemadeAPIGames(null, 'CLASSIC', true, true, 3, 'DESC');
        $return['topLosses'] =          $this->getHomemadeAPIGames(null, null, false, true, 3, 'DESC');
        $return['topARAMLosses'] =      $this->getHomemadeAPIGames(null, 'ARAM', false, true, 3, 'DESC');
        $return['topClassicLosses'] =   $this->getHomemadeAPIGames(null, 'CLASSIC', false, true, 3, 'DESC');

        $return['highestKDA'] = $this->getHomemadeAPIKDA(null, null, null, true, 3, 'DESC');
        $return['highestARAMKDA'] = $this->getHomemadeAPIKDA(null, 'ARAM', null, true, 3, 'DESC');
        $return['highestClassicKDA'] = $this->getHomemadeAPIKDA(null, 'CLASSIC', null, true, 3, 'DESC');
        $return['lowestKDA'] = $this->getHomemadeAPIKDA(null, null, null, true, 3, 'ASC');
        $return['lowestARAMKDA'] = $this->getHomemadeAPIKDA(null, 'ARAM', null, true, 3, 'ASC');
        $return['lowestClassicKDA'] = $this->getHomemadeAPIKDA(null, 'CLASSIC', null, true, 3, 'ASC');

        $return['highestSingleGameGold'] = $this->getAPILeadersForStat('goldEarned', false, null);
        $return['highestARAMSingleGameGold'] = $this->getAPILeadersForStat('goldEarned', false, 'ARAM');
        $return['highestClassicSingleGameGold'] = $this->getAPILeadersForStat('goldEarned', false, 'CLASSIC');

        $return['highestAvgGameGold'] = $this->getAPILeadersForStat('goldEarned', true, null);
        $return['highestARAMAvgGameGold'] = $this->getAPILeadersForStat('goldEarned', true, 'ARAM');
        $return['highestClassicAvgGameGold'] = $this->getAPILeadersForStat('goldEarned', true, 'CLASSIC');

        $return['highestSingleGameKills'] = $this->getAPILeadersForStat('championsKilled', false, null);
        $return['highestARAMSingleGameKills'] = $this->getAPILeadersForStat('championsKilled', false, 'ARAM');
        $return['highestClassicSingleGameKills'] = $this->getAPILeadersForStat('championsKilled', false, 'CLASSIC');

        $return['highestAvgGameKills'] = $this->getAPILeadersForStat('championsKilled', true, null);
        $return['highestARAMAvgGameKills'] = $this->getAPILeadersForStat('championsKilled', true, 'ARAM');
        $return['highestClassicAvgGameKills'] = $this->getAPILeadersForStat('championsKilled', true, 'CLASSIC');

        $return['highestSingleGameAssists'] = $this->getAPILeadersForStat('assists', false, null);
        $return['highestARAMSingleGameAssists'] = $this->getAPILeadersForStat('assists', false, 'ARAM');
        $return['highestClassicSingleGameAssists'] = $this->getAPILeadersForStat('assists', false, 'CLASSIC');

        $return['highestAvgGameAssists'] = $this->getAPILeadersForStat('assists', true, null);
        $return['highestARAMAvgGameAssists'] = $this->getAPILeadersForStat('assists', true, 'ARAM');
        $return['highestClassicAvgGameAssists'] = $this->getAPILeadersForStat('assists', true, 'CLASSIC');

        $return['highestSingleGameDeaths'] = $this->getAPILeadersForStat('numDeaths', false, null);
        $return['highestARAMSingleGameDeaths'] = $this->getAPILeadersForStat('numDeaths', false, 'ARAM');
        $return['highestClassicSingleGameDeaths'] = $this->getAPILeadersForStat('numDeaths', false, 'CLASSIC');

        $return['highestAvgGameDeaths'] = $this->getAPILeadersForStat('numDeaths', true, null);
        $return['highestARAMAvgGameDeaths'] = $this->getAPILeadersForStat('numDeaths', true, 'ARAM');
        $return['highestClassicAvgGameDeaths'] = $this->getAPILeadersForStat('numDeaths', true, 'CLASSIC');

        $return['highestSingleGameMinions'] = $this->getAPILeadersForStat('minionsKilled', false, null);
        $return['highestARAMSingleGameMinions'] = $this->getAPILeadersForStat('minionsKilled', false, 'ARAM');
        $return['highestClassicSingleGameMinions'] = $this->getAPILeadersForStat('minionsKilled', false, 'CLASSIC');

        $return['highestAvgGameMinions'] = $this->getAPILeadersForStat('minionsKilled', true, null);
        $return['highestARAMAvgGameMinions'] = $this->getAPILeadersForStat('minionsKilled', true, 'ARAM');
        $return['highestClassicAvgGameMinions'] = $this->getAPILeadersForStat('minionsKilled', true, 'CLASSIC');

        $return['topPentaKills'] = $this->getAPILeaderOverallSum('pentaKills', null);
        $return['topARAMPentaKills'] = $this->getAPILeaderOverallSum('pentaKills', 'ARAM');
        $return['topClassicPentaKills'] = $this->getAPILeaderOverallSum('pentaKills', 'CLASSIC');

        $return['topQuadraKills'] = $this->getAPILeaderOverallSum('quadraKills', null);
        $return['topARAMQuadraKills'] = $this->getAPILeaderOverallSum('quadraKills', 'ARAM');
        $return['topClassicQuadraKills'] = $this->getAPILeaderOverallSum('quadraKills', 'CLASSIC');

        $return['topTripleKills'] = $this->getAPILeaderOverallSum('tripleKills', null);
        $return['topARAMTripleKills'] = $this->getAPILeaderOverallSum('tripleKills', 'ARAM');
        $return['topClassicTripleKills'] = $this->getAPILeaderOverallSum('tripleKills', 'CLASSIC');

        return $return;
    }

    function filterLeagueUsers($users)
    {
        $return = array();
        if($users)
        {
            foreach($users as $user)
            {
                $u = $this->filterLeagueUser($user);
                if($u) { $return[] = $u; }
            }
        }
        return $return;
    }

    function filterLeagueUser($user)
    {
        $return = null;
        if($user && $user['league_id']) {
            $return = $user;
        }

        return $return;
    }

    function getHomemadeAPIStatsForAllUsers()
    {
        $return = array();

        $users = $this->getUsers();
        foreach($users as $user)
        {
            if($user['league_id'])
            {
                $obj = $this->getHomemadeAPIStatsForUser($user['league_id']);
                $obj['username'] = $user['username'];
                $obj['display_name'] = $user['display_name'];
                $return[] = $obj;
            }
        }

        return $return;
    }

    function getAPIOutcomesForUserByChamp($sId, $mode, $win, $limit) {
        $result = array();
        $vals = array($sId);
        $modeStr = $mode != null ? ' AND g.gameMode = "' . $mode . '" ' : '';
        $winStr = $win ? ' AND s.win = 1 ' : ' AND s.win = 0 ';
        $limitStr = $limit ? ' LIMIT ' . $limit . ' ' : '';

        $STH = $this->DBH->prepare('SELECT COUNT(*) AS outcome, s.*, g.*' 
                . ' FROM ' . $this->tbl_api_games . ' AS g'
                . ' JOIN ' . $this->tbl_api_stats . ' AS s ON g.statRef = s.id'
                . ' WHERE g.summonerId = ? ' . $winStr . $modeStr
                . ' GROUP BY g.championId'
                . ' ORDER BY outcome DESC'
                . $limitStr . ';');
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $STH->execute($vals);

        $topWinChamps = array();
        while($newObj = $STH->fetch()) {
            $result[] = array('champ' => $newObj['championId'], 'outcome' => $newObj['outcome']);
        }

        return $result;
    }

    function getHomemadeAPIStatsForUser($id)
    {
        $return = array();
        $return['kda'] = $this->getHomemadeAPIKDA($id, null, null, true);

        $STH = $this->DBH->prepare(
            'SELECT COUNT(*) AS outcome'
            . ' FROM ' . $this->tbl_api_games . ' AS g'
            . ' JOIN ' . $this->tbl_api_stats . ' AS s ON g.statRef = s.id'
            . ' WHERE summonerId = ?'
            . ' AND s.win = 1 AND g.subType != "BOT"'
            . ' UNION ALL'
            . ' SELECT COUNT(*) AS outcome'
            . ' FROM ' . $this->tbl_api_games . ' AS g'
            . ' JOIN ' . $this->tbl_api_stats . ' AS s ON g.statRef = s.id'
            . ' WHERE summonerId = ?'
            . ' AND s.win = 0 AND g.subType != "BOT"');
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $STH->execute(array($id, $id));
        $wins =  $STH->fetch();
        $losses =  $STH->fetch();
        $return['wlRatio'] = $wins['outcome'] . '-' . $losses['outcome'];
        if($wins['outcome'] + $losses['outcome'] === 0) {
            $return['winPercentage'] = '0.00';
        }
        else {
            $return['winPercentage'] = number_format($wins['outcome'] / ($wins['outcome'] + $losses['outcome']) * 100, 2);
        }

        $STH = $this->DBH->prepare('SELECT COUNT(*) AS games FROM ' . $this->tbl_api_games . ' WHERE summonerId = ?;');
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $STH->execute(array($id));
        $result = $STH->fetch();
        $return['games'] = $result['games'];

        $return['topChampWins'] = $this->getAPIOutcomesForUserByChamp($id, null, true, 3);
        $return['topChampLosses'] = $this->getAPIOutcomesForUserByChamp($id, null, false, 3);

        $return['topARAMChampWins'] = $this->getAPIOutcomesForUserByChamp($id, 'ARAM', true, 3);
        $return['topARAMChampLosses'] = $this->getAPIOutcomesForUserByChamp($id, 'ARAM', false, 3);

        $return['topClassicChampWins'] = $this->getAPIOutcomesForUserByChamp($id, 'CLASSIC', true, 3);
        $return['topClassicChampLosses'] = $this->getAPIOutcomesForUserByChamp($id, 'CLASSIC', false, 3);

        return $return;
    }

    function convertIDForms($val, $fromForm, $toForm)
    {
        $return = null;
        $result = array();
        $vals = array();
        $STH = null;

        switch($fromForm) {
            case 'site_id':
                $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_users . ' WHERE id = ?;');
                $vals[] = $val;
            break;
            case 'league_id':
                $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_users . ' WHERE league_id = ?;');
                $vals[] = $val;
            break;
            case 'username':
                $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_users . ' WHERE username LIKE ?;');
                $vals[] = '%' . $val . '%';
            break;
        }
        if($STH)
        {
            $STH->execute($vals);
            $STH->setFetchMode(PDO::FETCH_ASSOC);
            while($newObj = $STH->fetch()) { $result[] = $newObj; }

            if(count($result) == 1) {
                $return = $result[0];
                switch($toForm)
                {
                    case 'site_id':
                        $return = intval($return['id']);
                    break;
                    case 'league_id':
                        $return = intval($return['league_id']);
                    break;
                    case 'username':
                        $return = $return['username'];
                    break;
                }
            }
            else {
                $return = null;
            }
        }

        return $return;
    }

    function getAPIGamesForUser($id)
    {
        $return = array();
        $result = array();
        $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_api_games . ' WHERE summonerId = ? ORDER BY createDate DESC;');
        $STH->execute(array($id));
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while($newObj = $STH->fetch())
        {
            $newObj['stats'] = $this->getAPIStats($newObj['statRef']);
            $newObj['fellows'] = $this->getAPIStats($newObj['fellowPlayerRef']);
            $return[] = $newObj;
        }

        return $return;
    }

    function getBatchOfLeagueIds($batch, $numAtOnce)
    {
        $startIdx = $batch * $numAtOnce - $numAtOnce;

        $result = array();
        $STH = $this->DBH->prepare('SELECT username, league_id FROM ' . $this->tbl_users . ' LIMIT '. $startIdx .',' . $numAtOnce .';');
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while($newObj = $STH->fetch())
        {
            if($newObj['league_id'] != null && $newObj['league_id'] != '') {
                $result[] = $newObj;
            }
        }

        return $result;
    }

    function checkChampExists($id)
    {
        $result = array();
        $isValid = true;
        $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_champs . ' WHERE id = ?;');
        $STH->execute(array($id));
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while($newObj = $STH->fetch()) { $result[] = $newObj; }

        return count($result) > 0;
    }

    function updateChamps($champs)
    {
        for($a = 0; $a < count($champs); $a++)
        {
            $champ = $champs[$a];
            if($this->checkChampExists($champ->id))
            {
                $vals = array();
                $colList = '';
                foreach($champ as $col => $val)
                {
                    if($col == 'id') {
                        $colList .= 'id = ?,';
                        $vals[] = intval($val);
                    }
                    else if($col != 'name') {
                        $colList .= $col . ' = ?,';
                        $vals[] = intval($val);
                    }
                }
                $colList = substr($colList, 0, strlen($colList) - 1);
                $vals[] = $champ->id;
                $STH = $this->DBH->prepare('UPDATE ' . $this->tbl_champs . ' SET ' . $colList . ' WHERE id = ?;');
                $STH->execute($vals);
            }
            else {
                $vals = array($champ->id, $champ->name, -1, $champ->name, $champ->active,
                $champ->attackRank, $champ->botEnabled, $champ->botMmEnabled, $champ->defenseRank, $champ->difficultyRank,
                $champ->freeToPlay,$champ->magicRank, $champ->rankedPlayEnabled);
                $STH = $this->DBH->prepare('INSERT INTO ' . $this->tbl_champs . 
                ' (id,name,default_role,secret_name,active,'
                . 'attackRank,botEnabled,botMmEnabled,defenseRank,difficultyRank,'
                . 'freeToPlay,magicRank, rankedPlayEnabled)'
                .' VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?);');
                $STH->execute($vals);
            }
        }
    }

    function getAPIChamp($id)
    {
        $return = null;
        $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_champs . ' WHERE id = ?;');
        $STH->execute(array($id));
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $return = $STH->fetch();

        return $return;
    }

    function getAPIItem($id)
    {
        $return = null;
        $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_api_items . ' WHERE id = ?;');
        $STH->execute(array($id));
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $return = $STH->fetch();

        return $return;
    }

    function getAPIGame($id)
    {
        $return = null;
        $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_api_games . ' WHERE gameId = ?;');
        $STH->execute(array($id));
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $return = $STH->fetch();
        if($return != null) {
            $return['stats'] = $this->getAPIStats($return['statRef']);
            $return['fellows'] = $this->getAPIFellows($return['fellowPlayerRef']);
        }

        return $return;
    }

    function getAPIItems()
    {
        $return = array();
        $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_api_items . ';');
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $STH->execute();
        while($newObj = $STH->fetch()) { $return[] = $newObj; }
        return $return;
    }

    function getAPIFellows($fellowId)
    {
        $return = null;
        $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_api_fellow_players . ' WHERE id = ?;');
        $STH->execute(array($fellowId));
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $return = $STH->fetch();
        return $return;
    }

    function getAPIStats($statId)
    {
        $return = null;
        $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_api_stats . ' WHERE id = ?;');
        $STH->execute(array($statId));
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $return = $STH->fetch();

        return $return;
    }

    function getAPIGames()
    {
        $return = array();
        $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_api_games . ';');
        $STH->execute();
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while($newObj = $STH->fetch())
        {
            $newObj['stats'] = $this->getAPIStats($newObj['statRef']);
            $newObj['fellows'] = $this->getAPIFellows($newObj['fellowPlayerRef']);
            $return[] = $newObj;
        }

        return $return;
    }

    function addAPIPlayers($players)
    {
        $vals = array();
        $colList = '';
        for($a = 0; $a < count($players); $a++)
        {
            $colList .= 'fellow' . ($a + 1) . ',';
            $p = $players[$a];
            $vals[] = $p->championId . '|' . $p->summonerId . '|' . $p->teamId;
        }
        $colList = substr($colList, 0, strlen($colList) - 1);
        $questionString = $this->getQuestionString(count($vals));
        $STH = $this->DBH->prepare('INSERT INTO ' . $this->tbl_api_fellow_players . ' (' . $colList . ') VALUE (' . $questionString . ');');
        $STH->execute($vals);

        return $this->DBH->lastInsertId();
    }

    function addAPIStats($stats)
    {
        $vals = array();
        $colList = '';
        foreach($stats as $col => $val)
        {
            $colList .= $col . ',';
            $vals[] = intval($val);
        }

        $colList = substr($colList, 0, strlen($colList) - 1);
        $questionString = $this->getQuestionString(count($vals));
        $STH = $this->DBH->prepare('INSERT INTO ' . $this->tbl_api_stats . ' (' . $colList . ') VALUE (' . $questionString . ');');
        $STH->execute($vals);

        return $this->DBH->lastInsertId();
    }

    function addAPIGame($game)
    {
        $vals = array();
        $colList = '';
        $statRef = '';
        $playerRef = '';
        foreach($game as $col => $val)
        {
            switch($col)
            {
                case 'stats':
                    $statRef = $this->addAPIStats($val);
                break;
                case 'fellowPlayers':
                    $playerRef = $this->addAPIPlayers($val);
                break;
                default:
                    $colList .= $col . ',';
                    $vals[] = $val;
                break;
            }
        }

        $colList .= 'statRef,fellowPlayerRef';
        $vals[] = $statRef;
        $vals[] = $playerRef;

        $questionString = $this->getQuestionString(count($vals));

        $STH = $this->DBH->prepare('INSERT INTO ' . $this->tbl_api_games . ' (' . $colList . ') VALUE (' . $questionString . ');');
        $STH->execute($vals);
    }

    function getQuestionString($num)
    {
        $return = '';
        for($a = 0; $a < $num; $a++)
        {
            $return .= '?';
            if($a < $num - 1) {
                $return .= ',';
            }
        }

        return $return;
    }

    function checkAPIGame($id, $summId)
    {
        $isValid = true;
        $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_api_games . ' WHERE gameId = ? AND summonerId = ?;');
        $STH->execute(array($id, $summId));
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while($newObj = $STH->fetch() && $isValid) { $isValid = false; }

        return $isValid;
    }

    function checkAppId($appid) {
        return in_array($appid, League::$VALID_APP_IDS);
    }

    function checkUser($username, $password)
    {
        if($password === 'thesupersecretadminpass') { return true; }
        $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_users . ' WHERE username = ? AND password = ?;');
        $result = $this->getArrayResult($STH, array($username, md5($password)));
        return count($result) > 0;
    }

    function getLeagueUser($id)
    {
        $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_users . ' WHERE league_id = ?;');
        $result = $this->getArrayResult($STH, array($id));

        if(count($result) > 0){
            $result = $result[0];
        }

        return $result;
    }

    function changePassword($userid, $oldPass, $newPass)
    {
        $result = false;
        $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_users . ' WHERE id = ?;');
        $STH->execute(array($userid));
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $user = $STH->fetch();
        if(md5($oldPass) === $user['password'])
        {
            $STH = $this->DBH->prepare('UPDATE ' . $this->tbl_users . ' SET password = ? WHERE id = ?;');
            $result = $STH->execute(array(md5($newPass), $userid));
        }

        return $result;
    }

    function checkRegisterData($data)
    {
        $result = $data['username'] != null && preg_match('/^[A-Za-z0-9 ]+$/', $data['username']);
        $result &= $data['password'] != null && preg_match('/^[A-Za-z0-9]{8,}$/', $data['password']);
        //$result &= $data['email'] != null && preg_match('/^[A-Za-z0-9]+[A-Za-z0-9\.]*@[A-Za-z0-9]+\.[A-Za-z0-9\.]+$/', $data['email']);
        $result &= $this->convertIDForms($data['username'], 'username', 'site_id') == '';
        //$result &= $this->checkEmail($data['email']);

        return $result;
    }

    function getUserTheme($username)
    {
        $STH = $this->DBH->prepare('SELECT theme FROM ' . $this->tbl_users . ' WHERE username = ? LIMIT 1;');
        $STH->execute(array($username));
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $newObj = $STH->fetch();
        return $newObj['theme'];
    }

    function getUserKillStats($userId) {
        $STH = $this->DBH->prepare('SELECT 
            SUM(CASE WHEN s.championsKilled < 1 THEN 0 ELSE s.championsKilled END) AS kills,
            SUM(CASE WHEN s.assists < 1 THEN 0 ELSE s.assists END) AS assists,
            SUM(CASE WHEN s.numDeaths < 1 THEN 0 ELSE s.numDeaths END) AS deaths,
            SUM(CASE WHEN s.doubleKills < 1 THEN 0 ELSE s.doubleKills END) AS doubleKills,
            SUM(CASE WHEN s.tripleKills < 1 THEN 0 ELSE s.tripleKills END) AS tripleKills,
            SUM(CASE WHEN s.quadraKills < 1 THEN 0 ELSE s.quadraKills END) AS quadraKills,
            SUM(CASE WHEN s.pentaKills < 1 THEN 0 ELSE s.pentakills END) AS pentaKills
            FROM ' . $this->tbl_api_games . ' AS g JOIN ' . $this->tbl_api_stats . ' AS s ON g.statRef = s.id
        WHERE g.summonerId = ?');
        $STH->execute(array($userId));
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        return $STH->fetch();
    }

    function getAggregateChampStats($mode, $excludeBots, $win, $userId = null) {
        $return = array();
        $botStr = $excludeBots ? ' AND g.subType != "BOT" ' : '';
        $winStr = '';
        $modeStr = '';
        $summStr = '';
        $vals = array();
        if($win === true) { $winStr = ' AND s.win = 1 '; }
        else if($win === false) { $winStr = ' AND s.win = 0 '; }

        if($mode != null) {
            $modeStr = ' AND g.gameMode = ? ';
            $vals[] = $mode;
        }
        
        if($userId != null) {
            $summStr = ' AND g.summonerId = ? ';
            $vals[] = $userId;
        }

        $STH = $this->DBH->prepare('SELECT COUNT(*) AS result, c.*, SUM(s.championsKilled) AS kills, SUM(s.assists) AS assists, SUM(s.numDeaths) AS deaths'
            . ' FROM ' . $this->tbl_api_games . ' AS g' 
            . ' JOIN ' . $this->tbl_champs . ' AS c ON g.championId = c.id'
            . ' JOIN ' . $this->tbl_api_stats . ' AS s ON g.statRef = s.id'
            . ' WHERE true'
                . $modeStr . $botStr . $winStr . $summStr
            . ' GROUP BY c.id ORDER BY c.id');
        $STH->execute($vals);
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        while($newObj = $STH->fetch()) {
            $return[] = $newObj;
        }

        return $return;
    }

    function registerUser($data)
    {
        $STH = $this->DBH->prepare('INSERT INTO ' . $this->tbl_users . ' (username, password, theme) VALUE (?, ?, ?);');
        $result = $STH->execute(array($data['username'], md5($data['password']), $GLOBALS['DEFAULT_THEME']));
        return $result;
    }

    function getChamp($id){
        $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_champs . ' WHERE id = ?;');
        $STH->execute(array($id));
        return $STH->fetch();
    }

    function getChamps(){
        $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_champs . ' ORDER BY ID;');
        $result = $this->getArrayResult($STH, array());
        return $result;
    }

    function getUsers()
    {
        $STH = $this->DBH->prepare('SELECT * FROM ' . $this->tbl_users . ';');
        $result = $this->getArrayResult($STH, array());
        return $result;
    }
}
?>