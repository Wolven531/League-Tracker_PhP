<?php
    require_once('./util.php');
    if (!isset($_SESSION['league_username_login'])) { header('Location: http://www.wolven531.com/league/'); }
    
    $dao = getDao();
    if($type === 'POST' && $_POST['changePass'])
    {
        if(is_numeric($_POST['userid'] * 1.0) && $_POST['newPassword'] == $_POST['newPasswordConfirm'] && preg_match('/^[A-Za-z0-9]{8,}$/', $_POST['newPassword']))
        {
            $result = $dao->changePassword($_POST['userid'], $_POST['origPassword'], $_POST['newPassword']);
        }
    }
    $l_id = $dao->convertIDForms($user, 'username', 'league_id');
    $userObject = $dao->getLeagueUser($l_id);
    destroyDao($dao);
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $SITE_TITLE; ?> Account</title>
        <?php include('./basicSiteNeeds.html'); ?>
    </head>
    <body>
        <?php
            include('./header.html');
            include('./navigation.php');
        ?>
        <h3>Account</h3>
        <div class="content">
            <div>
                <?php
                    if($type === 'POST') {
                        echo $result ? 'Password changed.' : 'Could not change password.';
                    }
                ?>
            </div>
            <table>
                <tr>
                    <td>Username</td>
                    <td><?php echo $user; ?></td>
                </tr>
                <tr>
                    <td>Change password</td>
                    <td>
                        <form id="changePasswordForm" action="./account.php" method="post">
                            <input type="password" name="origPassword" id="origPassword" placeholder="Old password" />
                            <br />
                            <input type="password" name="newPassword" id="newPassword" placeholder="New password" />
                            <br />
                            <input type="password" name="newPasswordConfirm" id="newPasswordConfirm" placeholder="Password confirm" />
                            <br />
                            <input type="hidden" name="changePass" value="true" />
                            <input type="hidden" name="userid" value="<?php echo $userObject['id']; ?>" />
                            <input type="submit" value="Change Password" />
                        </form>
                    </td>
                </tr>
            </table>
        </div>
        <br/>
        <?php
            include('./footer.php');
        ?>
    </body>
</html>