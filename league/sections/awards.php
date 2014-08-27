<h1>Awards</h1>
<?php
echo generateCustomTable(
    array(
        'hovered' => true,
        'headers' => array(
                        array('title' => 'Award'), 
                        array('title' => 'Overall'),
                        array('title' => 'ARAM'),
                        array('title' => 'Classic')),
        'rows' => array(
            generateAwardRow('Top KDA', '&quot;That&quot; Guy', 'highest', 'KDA', 'kda', array('isKDA' => true)),
            generateAwardRow('Bottom KDA', 'Fish in a Barrel', 'lowest', 'KDA', 'kda', array('isKDA' => true)),
            generateAwardRow('Top Wins (KDA during wins)', 'Never Gives Up', 'top', 'Wins', 'outcomes', array('win' => true, 'bots' => false, 'includeKDA' => true, 'dao'=>$dao, 'isInt' => true)),
            generateAwardRow('Top Losses (KDA during losses)', 'Sucks', 'top', 'Losses', 'outcomes', array('win' => false, 'bots' => false, 'includeKDA' => true, 'dao'=>$dao, 'isInt' => true)),
            generateAwardRow('Highest Single Game Gold', 'Mogul', 'highest','SingleGameGold', 'result', array('isInt' => true)),
            generateAwardRow('Highest Average Game Gold', 'Baller', 'highest','AvgGameGold', 'result', array('isInt' => false)),
            generateAwardRow('Highest Single Game Kills', 'Assassin', 'highest','SingleGameKills', 'result', array('isInt' => true)),
            generateAwardRow('Highest Average Game Kills', 'Hit Man', 'highest','AvgGameKills', 'result', array('isInt' => false)),
            generateAwardRow('Highest Single Game Assists', 'Nice Guy', 'highest','SingleGameAssists', 'result', array('isInt' => true)),
            generateAwardRow('Highest Average Game Assists', 'Poor Sap', 'highest','AvgGameAssists', 'result', array('isInt' => false)),
            generateAwardRow('Highest Single Game Deaths', 'Cannon Fodder', 'highest','SingleGameDeaths', 'result', array('isInt' => true)),
            generateAwardRow('Highest Average Game Deaths', 'Casual Feeder', 'highest','AvgGameDeaths', 'result', array('isInt' => false)),
            generateAwardRow('Highest Single Game Minions', 'Merciless', 'highest','SingleGameMinions', 'result', array('isInt' => true)),
            generateAwardRow('Highest Average Game Minions', 'Farmer', 'highest','AvgGameMinions', 'result', array('isInt' => false)),
            generateAwardRow('Top Penta Kills', 'Opportunist', 'top', 'PentaKills', 'result', array('isInt' => true)),
            generateAwardRow('Top Quadra Kills', 'Almost', 'top', 'QuadraKills', 'result', array('isInt' => true)),
            generateAwardRow('Top Triple Kills', 'Not Bad', 'top', 'TripleKills', 'result', array('isInt' => true))
        )
    )
);?>