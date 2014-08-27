<?php require_once('./util.php');
$username = '';
if(isset($_GET['userid'])) { $username = $_GET['userid']; }
else { $username = getUser(); }
$dao = getDao();
$summId = $dao->convertIDForms($username, 'username', 'league_id');
$killStats = $dao->getUserKillStats($summId);
destroyDao($dao);
$userStats = getUserStats($summId);
$userInfo = getUserInfo($username);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $SITE_TITLE; ?> Profile: <?php echo $username; ?></title>
        <?php require('./basicSiteNeeds.php'); ?>
        <script src="js/metro-hint.js"></script>
        <script src="js/metro-accordion.js"></script>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $('.carousel').carousel({
                    auto: false,
                    period: 3000
                    /*
                    duration: 2000,
                    markers: {
                        type: "square"
                    }*/
                });
            });
        </script>
        <style>
            .table td.no-padding-top {
                padding-top: 0;
            }
        </style>
    </head>
    <body class="metro">
        <?php include('./navigation.php'); ?>
        <div class="page">
            <div class="page-region">
                <div class="page-region-content">
                    <div class="container">
                        <h1><?php echo $username ?> Profile</h1>
                        <h3>Stats</h3>
                        <div class="bg-grayLighter padding20">
                            <p class="description">
                                Display Name: <?php echo isset($userInfo['display_name']) ? $userInfo['display_name'] : 'Not set'; ?>
                                <br/>
                                Theme: <?php echo $userInfo['theme']; ?>
                            </p>
                        <?php if(intval($userStats['games']) === 0) { ?>
                            <p class="description">No games recorded. Either your username is not your league username,
                                or your summoner ID has not been added to the DB.</p>
                        <?php } else {?>
                            <p class="description">
                                Overall KDA: <?php echo number_format($userStats['kda'], 4); ?>
                                <br/>
                                Games: <?php echo number_format($userStats['games']); ?>
                                <br/>
                                Win/Loss Ratio: <?php echo $userStats['wlRatio']; ?> (<?php echo $userStats['winPercentage']; ?>%)
                            </p>
                            <p class="description">
                                Penta Kills: <?php echo number_format($killStats['pentaKills']); ?>
                                <br/>
                                Quadra Kills: <?php echo number_format($killStats['quadraKills']); ?>
                                <br/>
                                Triple Kills: <?php echo number_format($killStats['tripleKills']); ?>
                                <br/>
                                Double Kills: <?php echo number_format($killStats['doubleKills']); ?>
                            </p>
                            <p class="description">
                                Total Kills: <?php echo number_format($killStats['kills']); ?>
                                <br/>
                                Total Assists: <?php echo number_format($killStats['assists']); ?>
                                <br/>
                                Total Deaths: <?php echo number_format($killStats['deaths']); ?>
                            </p>
                        </div>
                        <h3>Top Champs</h3>
                        <table class="table hovered fg-white bg-<?php echo getTheme(); ?>">
                            <thead>
                                <tr>
                                    <th>Mode</th>
                                    <th>Wins</th>
                                    <th>Losses</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Overall</td>
                                    <td><?php echo generateTopChamp($summId, 'topChampWins', 'bg-green'); ?></td>
                                    <td><?php echo generateTopChamp($summId, 'topChampLosses', 'bg-red'); ?></td>
                                </tr>
                                <tr>
                                    <td>ARAM</td>
                                    <td><?php echo generateTopChamp($summId, 'topARAMChampWins', 'bg-green'); ?></td>
                                    <td><?php echo generateTopChamp($summId, 'topARAMChampLosses', 'bg-red'); ?></td>
                                </tr>
                                <tr>
                                    <td>Classic</td>
                                    <td><?php echo generateTopChamp($summId, 'topClassicChampWins', 'bg-green'); ?></td>
                                    <td><?php echo generateTopChamp($summId, 'topClassicChampLosses', 'bg-red'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <?php include('./sections/apiGameSection.php'); ?>
                        <?php include('./sections/champStats.php'); ?>
                        <?php }?>
                    </div>
                </div>
            </div>
        </div>
        <?php include('./footer.php'); ?>
    </body>
</html>
