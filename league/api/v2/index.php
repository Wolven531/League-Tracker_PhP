<?php
    header('Content-Type: application/json');
    header('Accept: application/json');

    require_once($_SERVER['DOCUMENT_ROOT'] . '/league/util.php');
    //$date = array('date' => date('m-d-y, H:i:s'));
    $result = array();
    switch(getReqType()){
       case 'GET':
           $req = getReq();
           $dao = getDao();
           $password = API_Util::getParam($req, API_Util::$API_PASSWORD);
           $id = intval(API_Util::getParam($req, API_Util::$API_ID));
           $user_name = API_Util::getParam($req, API_Util::$API_USER_NAME);
           $api_type = API_Util::getParam($req, API_Util::$API_TYPE);
           // if we need to convert userId
           if(in_array($api_type, array(API_Util::$TYPE_USER, API_Util::$TYPE_USER_GAMES, API_Util::$TYPE_USER_STATS))) {
               if(!empty($user_name)) { $id = $dao->convertIDForms($user_name, 'username', 'league_id'); }
               else if($id > 0) { $id = $dao->convertIDForms($id, 'site_id', 'league_id'); }
           }
           switch($api_type){
               case API_Util::$TYPE_USER:
                   $result = API_Util::checkId($id) ? API_Util::stripPasswords($dao->getLeagueUser($id)) : API_Util::generateError('Invalid ID.', $id);
               break;
               case API_Util::$TYPE_USERS:
                   $result = API_Util::stripPasswords($dao->getUsers());
               break;
               case API_Util::$TYPE_ITEMS:
                   // TODO: add in multiplier stats to response
                   $result = $dao->getAPIItems();
               break;
               case API_Util::$TYPE_ITEM:
                   // TODO: add in multiplier stats to response
                   $result = API_Util::checkId($id) ? $dao->getAPIItem($id) : API_Util::generateError('Invalid ID.', $id);
               break;
               case API_Util::$TYPE_CHAMPS:
                   $result = $dao->getChamps();
               break;
               case API_Util::$TYPE_CHAMP:
                   $result = API_Util::checkId($id) ? $dao->getAPIChamp($id) : API_Util::generateError('Invalid ID.', $id);
               break;
               case API_Util::$TYPE_USER_GAME:
                   $result = API_Util::checkId($id) ? $dao->getAPIGame($id) : API_Util::generateError('Invalid ID.', $id);
               break;
               case API_Util::$TYPE_USER_GAMES:
                   // TODO: add filters for specifics (like the nulls provided below)
                   $result = API_Util::checkId($id) ? $result = $dao->getHomemadeAPIGames($id, null, null, null, null, null) : API_Util::generateError('Invalid ID.', $id);//($summId, $mode, $win, $excludeBots, $limit = null, $order = null);
               break;
               case API_Util::$TYPE_USER_LOGIN:
                   $result[API_Util::$API_SUCCESS] = $dao->checkUser($user_name, $password);
               break;
               case API_Util::$TYPE_USER_STATS:
                   $result = API_Util::checkId($id) ? $dao->getHomemadeAPIStatsForUser($id) : API_Util::generateError('Invalid ID.', $id);
               break;
               default:
                   // TODO: expand for when invalid params anywhere are entered
                   $result[API_Util::$API_ERROR] = 'Invalid ' . API_Util::$API_TYPE . ' specified: ' . $api_type;
               break;
           }
           destroyDao($dao);
        break;
        default:
            $result[API_Util::$API_ERROR] = 'This API only responds to GET requests.';
        break;
    }

    echo json_encode($result);
    
    class API_Util {
        public static $API_ERROR = 'error';
        public static $API_SUCCESS = 'success';
        public static $API_TYPE = 'type';
        
        public static $TYPE_USERS = 'users';
        public static $TYPE_USER = 'user';
        public static $TYPE_ITEMS = 'items';
        public static $TYPE_ITEM = 'item';
        public static $TYPE_CHAMPS = 'champs';
        public static $TYPE_CHAMP = 'champ';
        public static $TYPE_USER_GAME = 'user_game';
        public static $TYPE_USER_GAMES = 'user_games';
        public static $TYPE_USER_LOGIN = 'user_login';
        public static $TYPE_USER_STATS = 'user_stats';

        public static $API_ID = 'id';
        public static $API_USER_NAME = 'user_name';
        public static $API_PASSWORD = 'password';
        
        static function getParam($req, $param) {
            return isset($req[$param]) ? strtolower($req[$param]) : '';
        }
        
        static function stripPasswords($objects) {
            if(!is_array($objects)) {
                $objects = array($objects);
            }
            for($a = 0; $a < count($objects); $a++){
                unset($objects->password);
                unset($objects['password']);
                unset($objects[$a]->password);
                unset($objects[$a]['password']);
            }
            return $objects;
        }
        
        static function checkId($val) {
            return $val !== null && strlen($val) > 0 && intval($val) > 0;
        }
        
        static function generateError($msg, $val = null) {
            $return = array();
            $return['error'] = $msg;
            $return['value'] = $val;
            return $return;
        }
    }
?>