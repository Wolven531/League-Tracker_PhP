<?php
    header('Content-Type: application/json');
    header('Accept: application/json');
    require_once('../league_dao.php');
    $method = strtolower($_SERVER['REQUEST_METHOD']);
    $date = array('date' => date('m-d-y, H:i:s'));
    $result = array();
    switch($method){
       case 'get':
           $req = $_REQUEST;
           $dao = new LeagueImpl(DAO::$GO_DADDY);
           $dao->connect();
           switch(getParam($req, 'type')){
               case League::$USER:
                    $result = getUser($req, $dao);
               break;
               case 'game':
                    $result = getGame($req, $dao);
               break;
               case 'stat':
                    $result = getStat($req, $dao);
               break;
           }
           $dao->disconnect();
        break;
        case 'post':
            $req = $_REQUEST;
            $dao = new LeagueImpl(DAO::$GO_DADDY);
            $dao->connect();
            switch(getParam($req, 'type')) {
                case 'login':
                    $result = checkLogin($req, $dao);
                break;
                case 'add_game':
                    $result = addGame($req, $dao);
                break;
            }
            $dao->disconnect();
        break;
        default:
            $result['error'] = 'This API only responds to GET requests.';
        break;
    }

    echo json_encode($result);
    
    function getParam($req, $param){
        return isset($req[$param]) ? strtolower($req[$param]) : '';
    }
    
    function stripPasswords($objects) {
        if(!is_array($objects)) {
            $objects = array($objects);
        }
        for($a = 0; $a < count($objects); $a++){
            unset($objects[$a]->password);
            unset($objects[$a]['password']);
        }
        return $objects;
    }
    
    function checkLogin($req, $dao){
        $result;
        $result['id'] = '-1';
        $result['username'] = 'false';
        $user = getParam($req, League::$USER);
        $password = getParam($req, 'password');
        $appid = getParam($req, 'appid');
        if($dao->checkAppId($appid) && $dao->checkUser($user, $password)){
            $result = stripPasswords($dao->getLeagueUser($user));
        }
            
        return $result;
    }
    
    function addGame($req, $dao){
        $result = array();
        $appid = getParam($req, 'appid');
        if(strlen($appid) > 0) {
            if($dao->checkAppId($appid)){
                $obj[League::$KILLS] = getParam($req, League::$KILLS);
                $obj[League::$DEATHS] = getParam($req, League::$DEATHS);
                $obj[League::$ASSISTS] = getParam($req, League::$ASSISTS);
                $obj[League::$GOLD] = getParam($req, League::$GOLD);
                $obj[League::$MINIONS] = getParam($req, League::$MINIONS);
                $obj[League::$GAME_TYPE] = getParam($req, League::$GAME_TYPE);
                $obj[League::$GAME_LEVEL] = getParam($req, League::$GAME_LEVEL);
                $obj[League::$USER] = getParam($req, League::$USER);
                $obj[League::$DATE] = getParam($req, League::$DATE);
                $obj[League::$CHAMP_SELECT] = getParam($req, League::$CHAMP_SELECT);
                $obj[League::$VICTORY] = getParam($req, League::$VICTORY);

                if($dao->validateFormData($obj)) {
                    $result = $dao->addGame($obj);
                }
            }
        }

        if($result) {
            $result = $obj;
        }
        
        return $result;
    }
    
    function getUser($req, $dao){
        $result = array();
        $filter = getParam($req, 'filter');
        if(strlen($filter) > 0) {
            switch($filter){
                case 'single':
                    $id = getParam($req, 'id');
                    if(strlen($id) > 0) {
                        $result = stripPasswords($dao->getLeagueUser($id));
                    }
                break;
                case 'all':
                    $result = stripPasswords($dao->getUsers());
                break;
            }
        }
        return $result;
    }
    
    function getGame($req, $dao){
        $result = array();
        $filter = getParam($req, 'filter');
        if(strlen($filter) > 0) {
            switch($filter){
                case 'single':
                    $id = getParam($req, 'id');
                    if(strlen($id) > 0) {
                        $result = $dao->getGame($id);
                    }
                break;
                case 'all':
                    $result = $dao->getLeagueGames();
                break;
                case 'search':
                    $user = getParam($req, League::$USER);
                    $min = getParam($req, 'min');
                    $max = getParam($req, 'max');
                    $eq = getParam($req, 'eq');
                    $stat = getParam($req, 'stat');
                    $display = getParam($req, 'display');
                    $result = $dao->searchForGamesByParam($user, $stat, $min, $max, $eq, $display);
                break;
            }
        }
        return $result;
    }
    
    function getStat($req, $dao){
        $result = array();
        $filter = getParam($req, 'filter');
        if(strlen($filter) > 0) {
            switch($filter){
                case 'single':
                    
                break;
                case 'all':
                    
                break;
            }
        }
        return $result;
    }
?>