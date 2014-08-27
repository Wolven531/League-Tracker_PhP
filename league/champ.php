<?php
    require_once('./util.php');
    if(!isset($_GET['champ'])) {
        header('Location: http://www.wolven531.com/league/');
    }

    $champ = getChamp($_GET['champ']);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $SITE_TITLE; ?> <?php echo $champ['name']; ?></title>
        <?php require('./basicSiteNeeds.php'); ?>
    </head>
    <body class="metro">
        <?php include('./navigation.php'); ?>
        <div class="page">
            <div class="page-region">
                <div class="page-region-content">
                    <div class="container">
                        <div class="grid fluid">
                            <h1><?php echo $champ['name']; ?></h1>
                            <div><?php echo generateChamp($champ, 'large', false);?></div>
                            <h3>Information</h3>
                            <?php echo generateCustomTable(
                                array(
                                'headers' => array(
                                    array('title' => 'Stat'),
                                    array('title' => 'Detail'),
                                ),
                                'rows' => array(
                                    '<tr><td>Currently Free</td><td>' . ($champ['freeToPlay'] == 1 ? 'Yes' : 'No') . '</td></tr>',
                                    '<tr><td>Default Role</td><td>' . formatRole($champ['default_role']) . '</td></tr>',
                                    '<tr><td>Active</td><td>' . ($champ['active'] == 1 ? 'Yes' : 'No') . '</td></tr>',
                                    '<tr><td>Bot Enabled</td><td>' . ($champ['botEnabled'] == 1 ? 'Yes' : 'No') . '</td></tr>',
                                    '<tr><td>Ranked Play Enabled</td><td>' . ($champ['rankedPlayEnabled'] == 1 ? 'Yes' : 'No') . '</td></tr>',
                                    '<tr><td>Attack Rank</td><td><div class="progress-bar" data-role="progress-bar" data-value="' . ($champ['attackRank']*10) . '" data-color="bg-darkRed"></div></td></tr>',
                                    '<tr><td>Defense Rank</td><td><div class="progress-bar" data-role="progress-bar" data-value="' . ($champ['defenseRank']*10) . '" data-color="bg-darkGreen"></div></td></tr>',
                                    '<tr><td>Magic Rank</td><td><div class="progress-bar" data-role="progress-bar" data-value="' . ($champ['magicRank']*10) . '" data-color="bg-darkBlue"></div></td></tr>',
                                    '<tr><td>Difficulty Rank</td><td><div class="progress-bar" data-role="progress-bar" data-value="' . ($champ['difficultyRank']*10) . '" data-color="bg-darkViolet"></div></td></tr>',
                                )
                            ));
                            ?>
                            <h3>Game Info</h3>
                            <?php
                            $champStats = getChampStats($champ['id']);
                            echo generateCustomTable(
                                array(
                                'headers' => array(
                                    array('title' => 'Stat'),
                                    array('title' => 'Overall'),
                                    array('title' => 'ARAM'),
                                    array('title' => 'Classic')
                                ),
                                'rows' => array(
                                    '<tr><td>Games</td><td>'
                                        . $champStats['overallKDA']['games'] . ' ('
                                        . $champStats['overallKDA']['kda'] . ')</td><td>'
                                        . $champStats['overallARAMKDA']['games'] . ' ('
                                        . $champStats['overallARAMKDA']['kda'] . ')</td><td>'
                                        . $champStats['overallClassicKDA']['games'] . ' ('
                                        . $champStats['overallClassicKDA']['kda'] . ')</td></tr>',
                                    '<tr><td>Wins</td><td>'
                                        . $champStats['overallWinKDA']['games'] . ' ('
                                        . $champStats['overallWinKDA']['kda'] . ')</td><td>'
                                        . $champStats['overallARAMWinKDA']['games'] . ' ('
                                        . $champStats['overallARAMWinKDA']['kda'] . ')</td><td>'
                                        . $champStats['overallClassicWinKDA']['games'] . ' ('
                                        . $champStats['overallClassicWinKDA']['kda'] . ')</td></tr>',
                                    '<tr><td>Losses</td><td>'
                                        . $champStats['overallLossKDA']['games'] . ' ('
                                        . $champStats['overallLossKDA']['kda'] . ')</td><td>'
                                        . $champStats['overallARAMLossKDA']['games'] . ' ('
                                        . $champStats['overallARAMLossKDA']['kda'] . ')</td><td>'
                                        . $champStats['overallClassicLossKDA']['games'] . ' ('
                                        . $champStats['overallClassicLossKDA']['kda'] . ')</td></tr>',
                                    '<tr><td>Games (No Bots)</td><td>'
                                        . $champStats['overallKDANoBots']['games'] . ' ('
                                        . $champStats['overallKDANoBots']['kda'] . ')</td><td>'
                                        . $champStats['overallARAMKDANoBots']['games'] . ' ('
                                        . $champStats['overallARAMKDANoBots']['kda'] . ')</td><td>'
                                        . $champStats['overallClassicKDANoBots']['games'] . ' ('
                                        . $champStats['overallClassicKDANoBots']['kda'] . ')</td></tr>',
                                    '<tr><td>Wins (No Bots)</td><td>'
                                        . $champStats['overallWinKDANoBots']['games'] . ' ('
                                        . $champStats['overallWinKDANoBots']['kda'] . ')</td><td>'
                                        . $champStats['overallARAMWinKDANoBots']['games'] . ' ('
                                        . $champStats['overallARAMWinKDANoBots']['kda'] . ')</td><td>'
                                        . $champStats['overallClassicWinKDANoBots']['games'] . ' ('
                                        . $champStats['overallClassicWinKDANoBots']['kda'] . ')</td></tr>',
                                    '<tr><td>Losses (No Bots)</td><td>'
                                        . $champStats['overallLossKDANoBots']['games'] . ' ('
                                        . $champStats['overallLossKDANoBots']['kda'] . ')</td><td>'
                                        . $champStats['overallARAMLossKDANoBots']['games'] . ' ('
                                        . $champStats['overallARAMLossKDANoBots']['kda'] . ')</td><td>'
                                        . $champStats['overallClassicLossKDANoBots']['games'] . ' ('
                                        . $champStats['overallClassicLossKDANoBots']['kda'] . ')</td></tr>'
                                )
                            ));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('./footer.php'); ?>
    </body>
</html>