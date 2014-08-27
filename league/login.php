<?php
    require('./util.php');
    if(isset($_GET['logout'])) {
        logUserOut();
        header('Location: http://www.wolven531.com/league/');
    }
    else {
        if(isUserLoggedIn()) {
            header('Location: http://www.wolven531.com/league/view.php');
        }
        else if(isUserCookieSet()) {
            setUserSession(getUserCookie());
            header('Location: http://www.wolven531.com/league/view.php');
        }
        else if(getReqType() == 'POST' && !isUserLoggedIn()) {
            $uname = $_POST['username'];
            $pwd = $_POST['password'];
            $result = $pwd == $ADMIN_PASS;
            if(!$result) {
                $pwd = $pwd;
                $dao = getDao();
                $result = $dao->checkUser( $uname, $pwd );
                destroyDao($dao);
            }

            if($result) {
                $uname = strtolower($uname);
                setUserSession($uname);
                setUserCookie($uname);
                header('Location: http://www.wolven531.com/league/view.php');
            }
            else {
                header('Location: http://www.wolven531.com/league/register.php');
            }
        }
        else {
            header('Location: http://www.wolven531.com/league/');
        }
    }
?>