<?php
    require_once('./util.php');
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $SITE_TITLE; ?> Home</title>
		<?php include('./basicSiteNeeds.html'); ?>
	</head>
	<body>
		<?php
		    include('./header.html');
            echo '<br />';
            include('./navigation.php');
            $dao = getDao();
            $commStats = $dao->getTotalGameStats();
            destroyDao($dao);
            function getCommStatRow($commStats, $title, $stats)
            {
                $return = '';
                $return =  '<tr>'
                    . '<td>' . $title . '</td>';
                for($a = 0; $a < count($stats); $a++)
                {
                    $return .= '<td>' . number_format($commStats[$stats[$a]]) . '</td>';
                }
                $return .= '</tr>';
                
                return $return;
            }
        ?>
        
        <h3>Community Stats</h3>
        <div class="content">
            <table>
                <tr>
                    <th>Stat</th>
                    <th>Overall</th>
                    <th>ARAM</th>
                    <th>Classic</th>
                </tr>
                <?php echo getCommStatRow($commStats, 'Total Games', array('totalGames', 'totalARAMGames', 'totalClassicGames')); ?>
                <?php echo getCommStatRow($commStats, 'Total Wins', array('totalWins', 'totalARAMWins', 'totalClassicWins')); ?>
                <?php echo getCommStatRow($commStats, 'Total Losses', array('totalLosses', 'totalARAMLosses', 'totalClassicLosses')); ?>
                <?php echo getCommStatRow($commStats, 'Total Games (no bots)', array('totalGamesNoBots', 'totalARAMGamesNoBots', 'totalClassicGamesNoBots')); ?>
                <?php echo getCommStatRow($commStats, 'Total Wins (no bots)', array('totalWinsNoBots', 'totalARAMWinsNoBots', 'totalClassicWinsNoBots')); ?>
                <?php echo getCommStatRow($commStats, 'Total Losses (no bots)', array('totalLossesNoBots', 'totalARAMLossesNoBots', 'totalClassicLossesNoBots')); ?>
            </table>
            <?php
            
            ?>
        </div>
        <h3>Current Standings</h3>
        <?php
            include('./sections/kdaRankingSection.php');
            echo '<br />';
		    include('./footer.php');
		?>
	</body>
</html>