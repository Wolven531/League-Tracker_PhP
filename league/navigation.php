<?php require_once('./util.php'); ?>
<header class="bg-<?php echo getTheme(); ?> themed">
    <div class="nav navigation-bar bg-<?php echo getTheme(); ?> themed fixed-top shadow">
        <div class="navigation-bar-content container">
            <a href="./" class="element">Public Stats</a>
            <span class="element-divider"></span>
            <a class="element1 pull-menu" href="#"></a>
            <ul class="element-menu">
                <li><a href="./awards.php" class="element">Awards</a></li>
                <li><a href="./champs.php" class="element">Champion Stats</a></li>
                <!--
                | <a href="./champStats.php">Champ Stats</a>
                <a href="https://play.google.com/store/apps/details?id=league.dev.wolven531_mobile" class="element"><img src="http://developer.android.com/images/brand/en_app_rgb_wo_45.png" width="65" height="22" /></a>
                |-->
                <?php if(isUserLoggedIn())
                {
                ?>
                    <li><a href="./view.php?userid=<?php echo getUser(); ?>" class="element">Profile</a></li>
                    <li><a href="./account.php" class="element">Account</a></li>
                    <li><a href="./login.php?logout=true" class="element">Logout</a></li>
                <?php
                }
                else
                {?>
                    <li><a href="./register.php" class="element place-right">Login / Register</a></li>
                <?php
                }
                ?>
            </ul>
        </div>
    </div>
</header>