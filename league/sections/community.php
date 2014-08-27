<h1>Community</h1>
<?php
$commStats = getCommunityStats();
echo generateCustomTable(
    array(
        'hovered' => true,
        'headers' => array(
                        array('title' => 'Stat'), 
                        array('title' => 'Overall'),
                        array('title' => 'ARAM'),
                        array('title' => 'Classic')),
        'rows' => array(
            generateCommunityStatRow($commStats, 'Total Games', array('totalGames', 'totalARAMGames', 'totalClassicGames')),
            generateCommunityStatRow($commStats, 'Total Wins', array('totalWins', 'totalARAMWins', 'totalClassicWins')),
            generateCommunityStatRow($commStats, 'Total Losses', array('totalLosses', 'totalARAMLosses', 'totalClassicLosses')),
            generateCommunityStatRow($commStats, 'Total Games (no bots)', array('totalGamesNoBots', 'totalARAMGamesNoBots', 'totalClassicGamesNoBots')),
            generateCommunityStatRow($commStats, 'Total Wins (no bots)', array('totalWinsNoBots', 'totalARAMWinsNoBots', 'totalClassicWinsNoBots')),
            generateCommunityStatRow($commStats, 'Total Losses (no bots)', array('totalLossesNoBots', 'totalARAMLossesNoBots', 'totalClassicLossesNoBots'))
        )
    )
); ?>