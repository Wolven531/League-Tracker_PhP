<?php
    require('./util.php');
    set_time_limit(90);
    for($a = 1; $a <= 7; $a++) {
        updateBatch($a, $GLOBALS['API_KEY']);
        sleep(10);
    }
    echo '<pre>';
    var_dump($_SERVER);
    echo '</pre>';
    
    function updateBatch($param, $key) {
        $numAtOnce = 10;
        $dao = getDao();
        $ids = $dao->getBatchOfLeagueIds($param, $numAtOnce);
        if($ids)
        {
            echo '<div>Batch: ' . $param . '</div>';
            echo '<div>IDs:</div>';
            for($a = 0; $a < count($ids); $a++) {
                $id = $ids[$a]['league_id'];
                $url = 'https://na.api.pvp.net/api/lol/na/v1.3/game/by-summoner/'. $id .'/recent?api_key=' . $key;
                //$url = 'https://prod.api.pvp.net/api/lol/na/v1.3/game/by-summoner/'. $id .'/recent?api_key=' . $key;
                try{
                    $resp = file_get_contents($url);
                    $resp = json_decode($resp);
                    if($resp == null || ($resp->status && $resp->status->status_code == 429)) { // {"status": {"message": "Rate limit exceeded", "status_code": 429}}
                        echo '<div>Reached request rate</div>';
                    }
                    else if($resp->games) {
                        $games = $resp->games;
                        $newGames = 0;
                        foreach($games as $game)
                        {
                            $isValid = $dao->checkAPIGame($game->gameId, $id);
                            if($isValid)
                            {
                                $game->summonerId = $id;
                                $dao->addAPIGame($game);
                                $newGames++;
                            }
                        }
                        echo '<div>Updated ID: ' . $id . ' with ' . $newGames . ' games.</div>';
                    }
                }
                catch(Exception $e)
                {
                    echo '<div>URL</div>';
                    echo '<pre>';
                    var_dump($url);
                    var_dump($e);
                    echo '</pre>';
                }
            }
        }
        else {
            echo 'Failed to run updater: missing summoner IDs.';
        }
        destroyDao($dao);
    }
?>