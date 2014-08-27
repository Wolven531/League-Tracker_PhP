<h1>Standings</h1>
<?php
    $apiStats = getAPIStats();
    $a = 1;
    $rows = array();
    foreach($apiStats as $statObj)
    {
        if(intval($statObj['games']) > 0) {
            $rows[] = '<tr class="' . (isUserRelated($statObj['username']) ? 'bg-yellow' : '') . '">'
                . '<td>' . generateUserLink(
                                $statObj['username'], 
                                $statObj['username'] . (isset($statObj['display_name']) ? (' (' . $statObj['display_name'] . ')') : '')) 
                . '</td>'
                . '<td>' . $statObj['kda'] . ' (' . formatNumPlace($a) . ')</td>'
                . '<td>' . $statObj['winPercentage'] . '%</td>'
                . '<td>' . $statObj['games'] . '</td>'
            . '</tr>';
            $a++;
        }
    }
    echo generateCustomTable(
        array(
            'hovered' => true,
            'sortable' => true,
            'headers' => array(
                            array('title' => 'User', 'sortable' => true), 
                            array('title' => 'KDA', 'sortable' => true),
                            array('title' => 'Win Percentage', 'sortable' => true),
                            array('title' => 'Games', 'sortable' => true)),
            'rows' => $rows
        )
    );
?>