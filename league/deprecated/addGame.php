<?php
    require_once('./league_dao.php');
    session_start();
    $type = strtoupper($_SERVER['REQUEST_METHOD']);
    if($type != 'POST' || !isset($_SESSION['league_username_login'])) {
        echo 'Error: User was not authenticated.';
    }
    else {
        $data;
        $data['user'] = $_SESSION['league_username_login'];
        $data['edit'] = $_POST['edit'] == 'true' ? true : false;
        $data['gameid'] =       $data['edit'] ? intval($_POST['gameid']) : -1;
        $data['kills'] =        $data['edit'] ? intval($_POST['edit-kills']) : intval($_POST['kills']);
        $data['deaths'] =       $data['edit'] ? intval($_POST['edit-deaths']) : intval($_POST['deaths']);
        $data['assists'] =      $data['edit'] ? intval($_POST['edit-assists']) : intval($_POST['assists']);
        $data['gold'] =         $data['edit'] ? intval($_POST['edit-gold']) : intval($_POST['gold']);
        $data['minions'] =      $data['edit'] ? intval($_POST['edit-minions']) : intval($_POST['minions']);
        $data['game_type'] =    $data['edit'] ? intval($_POST['edit-game_type']) : intval($_POST['game_type']);
        $data['game_level'] =   $data['edit'] ? intval($_POST['edit-game_level']) : intval($_POST['game_level']);
        $data['date'] =         $data['edit'] ? $_POST['edit-date'] : $_POST['date'];
        $data['victory'] =      $data['edit'] ? $_POST['edit-victory'] : $_POST['victory'];
        $data['summ_role'] =    $data['edit'] ? $_POST['edit-summ_role'] : $_POST['summ_role'];
        
        $data['champ_select'] = $_POST['champ_select'];
        // default numeric pick? not a fan...
        // or at least do it with everything...
        //if($data['gameType'] == 0) { $data['gameType'] = 3; }

        $dao = new LeagueImpl(DAO::$GO_DADDY);
        $dao->connect();
            if($data['edit']) {
                $game = $dao->getGame($data['gameid']);
                $game = $game[0];
                $attemptedUser = $dao->getLeagueUser($game['userid']);
                if(strtolower($attemptedUser['username']) == strtolower($data['user'])) {
                    $result = $dao->editGame($data);
                    if($result) {
                        $result = $dao->getGame($data['gameid']);
                        echo json_encode($result[0]);
                    }
                    else {
                        echo 'Error: Failed to edit game (invalid data?):';
                        $obj['data'] = $data;
                        echo json_encode(array($obj));
                    }
                }
                else {
                    echo 'Error: Failed to edit game (unauthenticated user):';
                    $obj['authUser'] = $data['user'];
                    $obj['attemptedUser'] = $attemptedUser['username'];
                    
                    echo json_encode(array($obj));
                }
            }
            else {
                $result = $dao->addGame($data);
                if($result != null && count($result) > 0) {
                    echo json_encode($dao->getGames($data['user']));
                }
                else {
                    echo 'Error: Failed to add game (invalid/empty data?):';
                    echo json_encode($result);
                }
            }
        $dao->disconnect();
    }
?>