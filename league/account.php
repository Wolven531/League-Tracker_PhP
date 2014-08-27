<?php
    require_once('./util.php');
    if (!isUserLoggedIn()) { header('Location: http://www.wolven531.com/league/'); }

    $dao = getDao();
    if(getReqType() === 'POST' && $_POST['changePass'])
    {
        if(is_numeric($_POST['userid'] * 1.0) && $_POST['newPassword'] == $_POST['newPasswordConfirm'] && preg_match('/^[A-Za-z0-9]{8,}$/', $_POST['newPassword'])) {
            $result = $dao->changePassword($_POST['userid'], $_POST['origPassword'], $_POST['newPassword']);
        }
    }
    destroyDao($dao);

    if(getReqType() === 'POST' && $_POST['changeTheme']) {
        $dao = getDao();
        $dao->changeTheme($_POST['userid'], $_POST['theme']);
        destroyDao($dao);
    }

    if(getReqType() === 'POST' && $_POST['changeDisplayName']) {
        $dao = getDao();
        $dao->changeDisplayName($_POST['userid'], $_POST['newDisplayName']);
        destroyDao($dao);
    }
    $userInfo = getUserInfo(getUser());
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $SITE_TITLE; ?> Account</title>
        <?php require('./basicSiteNeeds.php'); ?>
        <script>
            jQuery(document).ready(function($){
                $('#theme').change(function(e){
                    var newColor = $(this).val();
                    $('.themed').attr('class',
                           function(i, c){ return c.replace(/bg-[A-Za-z]+/g, 'bg-' + newColor);
                    });
                });
            });
        </script>
    </head>
    <body class="metro">
        <?php include('./navigation.php'); ?>
        <div class="page">
            <div class="page-region">
                <div class="page-region-content">
                    <div class="container">
                        <h1>Account</h1>
                        <div class="content">
                            <div>
                                <?php
                                    if(getReqType() === 'POST' && $_POST['changePass']) {
                                        echo $result ? 'Password changed.' : 'Could not change password.';
                                    }
                                ?>
                            </div>
                            <table class="bordered table">
                                <thead>
                                    <tr>
                                        <td>Username</td>
                                        <td><?php echo $userInfo['username']; ?></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Display Name</td>
                                        <td>
                                            <form id="changeDisplayNameForm" action="./account.php" method="post">
                                                <div class="input-control text">
                                                    <input type="text" name="newDisplayName" value="<?php echo $userInfo['display_name']; ?>" />
                                                    <input type="hidden" name="changeDisplayName" value="true" />
                                                    <input type="hidden" name="userid" value="<?php echo $userInfo['id']; ?>" />
                                                </div>
                                                <input type="submit" value="Change Display Name" />
                                            </form>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Theme</td>
                                        <td>
                                            <form id="changeThemeForm" action="./account.php" method="post">
                                                <div class="input-control select">
                                                    <select name="theme" id="theme">
                                                        <?php
                                                            $themes = getAllThemes();
                                                            for($a = 0; $a < count($themes); $a++) {
                                                                $selected = getTheme() === $themes[$a] ? ' selected="selected" ' : '';
                                                                echo '<option value="' . $themes[$a] . '"' . $selected . '>' . $themes[$a] . '</option>';
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                                <input type="hidden" name="changeTheme" value="true" />
                                                <input type="hidden" name="userid" value="<?php echo $userInfo['id']; ?>" />
                                                <input type="submit" value="Change Theme" />
                                            </form>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Change Password</td>
                                        <td>
                                            <form id="changePasswordForm" action="./account.php" method="post">
                                                <div class="input-control password">
                                                    <input type="password" name="origPassword" id="origPassword" placeholder="Old password" />
                                                    <button class="btn-reveal"></button>
                                                </div>
                                                <div class="input-control password">
                                                    <input type="password" name="newPassword" id="newPassword" placeholder="New password" />
                                                    <button class="btn-reveal"></button>
                                                </div>
                                                <div class="input-control password">
                                                    <input type="password" name="newPasswordConfirm" id="newPasswordConfirm" placeholder="Password confirm" />
                                                    <button class="btn-reveal"></button>
                                                </div>
                                                <input type="hidden" name="changePass" value="true" />
                                                <input type="hidden" name="userid" value="<?php echo $userInfo['id']; ?>" />
                                                <input type="submit" value="Change Password" />
                                            </form>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
            include('./footer.php');
        ?>
    </body>
</html>