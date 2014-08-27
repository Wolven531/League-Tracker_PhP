<?php
    require_once('./util.php');
    $dao = getDao();
        $champInfo = $dao->getAggregateChampStats();
        $champAvg = $dao->getAverageOfAll();
        $roleInfo = $dao->getAggregateRoleStats();
    destroyDao($dao);
    $champAvg = $champAvg[0];
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $SITE_TITLE; ?> Champion Statistics</title>
        <?php include('./basicSiteNeeds.html'); ?>
    </head>
    <body>
        <?php
            include('./header.html');
            include('./navigation.php');
        ?>
        <h3>Aggregate Champion Statistics</h3>
        <div class="content">
            <div>
                <table class="champ-stat-table overall-table">
                    <tr>
                        <th>&nbsp;</th>
                        <th>Avg. KDA</th>
                        <th class="">Avg. Kills</th>
                        <th class="">Avg. Deaths</th>
                        <th class="">Avg. Assists</th>
                        <th class="">Avg. Gold</th>
                        <th class="">Avg. Minions</th>
                        <th class="">Games</th>
                    </tr>
                <?php  
                    echo '<tr>'
                         . '<td class="bold">Overall</td>'
                                . '<td>' . number_format($champAvg['kda'], 4) . '</td>'
                                . '<td class="kills">' . number_format($champAvg['kills'], 2) . '</td>'
                                . '<td class="deaths">' . number_format($champAvg['deaths'], 2) . '</td>'
                                . '<td class="assists">' . number_format($champAvg['assists'], 2) . '</td>'
                                . '<td class="gold">' . number_format($champAvg['gold'], 2) . '</td>'
                                . '<td>' . number_format($champAvg['minions'], 2) . '</td>'
                                . '<td>' . $champAvg['games'] . '</td>'
                        . '</tr>';
                ?>
                </table>
            </div>
            <table class="champ-stat-table sortable">
                <tr>
                    <th>Champion</th>
                    <th class="sorttable_numeric">Avg. KDA</th>
                    <th class="sorttable_numeric">Avg. Kills</th>
                    <th class="sorttable_numeric">Avg. Deaths</th>
                    <th class="sorttable_numeric">Avg. Assists</th>
                    <th class="sorttable_numeric">Avg. Gold</th>
                    <th class="sorttable_numeric">Avg. Minions</th>
                    <th class="sorttable_numeric">Games</th>
                </tr>
            <?php
                for($a = 0; $a < count($champInfo); $a++) {
                    $champ = $champInfo[$a];
                        
                    echo 
                        '<tr>'
                            . '<td>' . $champ['name'] . '</td>'
                            . '<td>' . number_format($champ['kda'], 4) . '</td>'
                            . '<td class="kills">' . number_format($champ['kills'], 2) . '</td>'
                            . '<td class="deaths">' . number_format($champ['deaths'], 2) . '</td>'
                            . '<td class="assists">' . number_format($champ['assists'], 2) . '</td>'
                            . '<td class="gold">' . number_format($champ['gold'], 2) . '</td>'
                            . '<td>' . number_format($champ['minions'], 2) . '</td>'
                            . '<td>' . $champ['games'] . '</td>'
                        . '</tr>';
                }
            ?>
            </table>
        </div>
        <h3>Aggregate Role Stats</h3>
        <div class="content">
            <table class="sortable">
                <tr>
                    <th>Role</th>
                    <th class="sorttable_numeric">Avg. KDA</th>
                    <th class="sorttable_numeric">Avg. Kills</th>
                    <th class="sorttable_numeric">Avg. Deaths</th>
                    <th class="sorttable_numeric">Avg. Assists</th>
                    <th class="sorttable_numeric">Avg. Gold</th>
                    <th class="sorttable_numeric">Avg. Minions</th>
                    <th class="sorttable_numeric">Games</th>
                </tr>
            <?php
                for($a = 0; $a < count($roleInfo); $a++) {
                    $role = $roleInfo[$a];
                    echo 
                        '<tr>'
                            . '<td>' . $role['role'] . '</td>'
                            . '<td>' . number_format($role['kda'], 4) . '</td>'
                            . '<td class="kills">' . number_format($role['kills'], 2) . '</td>'
                            . '<td class="deaths">' . number_format($role['deaths'], 2) . '</td>'
                            . '<td class="assists">' . number_format($role['assists'], 2) . '</td>'
                            . '<td class="gold">' . number_format($role['gold'], 2) . '</td>'
                            . '<td>' . number_format($role['minions'], 2) . '</td>'
                            . '<td>' . $role['games'] . '</td>'
                        . '</tr>';
                }
            ?>
            </table>
        </div>
        <?php
          include('./footer.php');
        ?>
    </body>
</html>