<?php
    function formatTimePlayed($time){
        return number_format($time /60) . 'm ' . number_format($time % 60) . 's';
    }

    function formatNumPlace($num)
    {
        $return = $num . '<sup>';
        if(in_array($num, array(11, 12, 13))) {
            $return .= 'th';
        }
        else {
            switch($num%10) {
                case 1:
                    $return .= 'st';
                break;
                case 2:
                    $return .= 'nd';
                break;
                case 3:
                    $return .= 'rd';
                break;
                default:
                    $return .= 'th';
                break;
            }
        }
        
        return $return . '</sup>';
    }

    function formatWin($val)
        {
            $return = $val ? 'Win' : 'Loss';
            return $return;
        }

    function formatKDA($gs, $mode) {
        $html = 'N/A';
        if($gs){
            if($mode == Util::$KDA_LONG) { $html = $gs['championsKilled'] . ' / ' . $gs['numDeaths'] . ' / ' . $gs['assists']; }
            else { $html = number_format(($gs['championsKilled'] + $gs['assists']) / $gs['numDeaths'], 4); }
        }
        return $html;
    }

    function defaultFormat($val) { return $val; }

    function formatLeagueDate($time) { return date(Util::$L_DATE_FORMAT, $time/1000) . '<br />' . date(Util::$L_TIME_FORMAT, $time/1000); }

    function formatMode($game) {
        $mode = $game['gameMode'];
        $return = $mode;
        switch ($mode)
        {
            case 'ARAM':
                $return = 'ARAM';
            break;
            case 'FIRSTBLOOD':
                $return = 'Showdown';
            break;
            case 'CLASSIC':
                if($game['subType'] == 'SR_6x6') {
                    $return = 'Hexakill';
                }
                else {
                    $return = 'Classic';
                }
            break;
            case 'ODIN':
                $return = 'Dominion';
            break;
            case 'ONEFORALL':
                $return = 'One For All';
            break;
            default:
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
            case 'CUSTOM_GAME':
                $return = 'Custom';
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
            case 1:
                $return = 'Cleanse';
            break;
            case 2:
                $return = 'Clairvoyance';
            break;
            case 3:
                $return = 'Exhaust';
            break;
            case 4:
                $return = 'Flash';
            break;
            case 6:
                $return = 'Ghost';
            break;
            case 7:
                $return = 'Heal';
            break;
            case 11:
                $return = $val . '(?)';
            break;
            case 12:
                $return = 'Teleport';
            break;
            case 13:
                $return = 'Clarity';
            break;
            case 14:
                $return = 'Ignite';
            break;
            case 21:
                $return = 'Barrier';
            break;
            default:
            break;
        }
        return $return;
    }

    function formatChamp($id) {
        $champsById = getChampsById();
        return $champsById[$id]['name'];
    }

    function formatRole($id){
        $result = '?';
        switch($id){
            case 0:
                $result = 'AP Carry';
            break;
            case 1:
                $result = 'AD Carry';
            break;
            case 2:
                $result = 'Jungle';
            break;
            case 3:
                $result = 'Tank';
            break;
            case 4:
                $result = 'Support';
            break;
        }
        return $result;
    }
?>