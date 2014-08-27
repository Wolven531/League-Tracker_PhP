<h1>Champ Stats</h1>
<h6>Hover over column head for more info, click to sort</h6>
<?php
    $userStr = null;
    if(isProfilePage()) {
        $dao = getDao();
        $summid = $dao->convertIDForms($_GET['userid'], 'username', 'league_id');
        destroyDao($dao);
        $userStr = $summid;
    }

    $dao = getDao();
    $overallAll   = $dao->getAggregateChampStats(null, true, null, $userStr);
    $overallWin   = $dao->getAggregateChampStats(null, true, true, $userStr);
    $overallLoss  = $dao->getAggregateChampStats(null, true, false, $userStr);
    $aramAll      = $dao->getAggregateChampStats('ARAM', true, null, $userStr);
    $aramWin      = $dao->getAggregateChampStats('ARAM', true, true, $userStr);
    $aramLoss     = $dao->getAggregateChampStats('ARAM', true, false, $userStr);
    $classicAll   = $dao->getAggregateChampStats('Classic', true, null, $userStr);
    $classicWin   = $dao->getAggregateChampStats('Classic', true, true, $userStr);
    $classicLoss  = $dao->getAggregateChampStats('Classic', true, false, $userStr);
    destroyDao($dao);
    $champsById = getChampsById();
    $rows = array();
    foreach($overallAll as $cs) { $champsById[$cs['id']]['overallAll'] = $cs; }
    foreach($overallWin as $cs) { $champsById[$cs['id']]['overallWin'] = $cs; }
    foreach($overallLoss as $cs) { $champsById[$cs['id']]['overallLoss'] = $cs; }
    foreach($aramAll as $cs) { $champsById[$cs['id']]['aramAll'] = $cs; }
    foreach($aramWin as $cs) { $champsById[$cs['id']]['aramWin'] = $cs; }
    foreach($aramLoss as $cs) { $champsById[$cs['id']]['aramLoss'] = $cs; }
    foreach($classicAll as $cs) { $champsById[$cs['id']]['classicAll'] = $cs; }
    foreach($classicWin as $cs) { $champsById[$cs['id']]['classicWin'] = $cs; }
    foreach($classicLoss as $cs) { $champsById[$cs['id']]['classicLoss'] = $cs; }

    $validChamps = array();
    foreach($champsById as $c) {
        if(intval($c['id']) > 0) {
            $cats = array('overallAll', 'overallWin', 'overallLoss', 'aramAll', 'aramWin', 'aramLoss', 'classicAll', 'classicWin', 'classicLoss');
            for($a = 0; $a < count($cats); $a++) { // calculate KDA
                if(!$c[$cats[$a]]['result']) { $c[$cats[$a]]['result'] = 0; }
                $c[$cats[$a]]['kda'] = number_format(($c[$cats[$a]]['kills'] + $c[$cats[$a]]['assists']) / ($c[$cats[$a]]['deaths'] > 0 ? $c[$cats[$a]]['deaths'] : 1), 4);
            }
            $isEmpty = true;
            for($a = 0; $a < count($cats) && $isEmpty; $a++) { // make sure the champ was played before
                if($c[$cats[$a]]['kda'] > 0) { $isEmpty = false; }
            }
            if(!$isEmpty) { $validChamps[] = $c; } // add to valids if champ was played
        }
    }

    foreach($validChamps as $c) {
            $img = generateChamp($c, 'small', false);
$html = <<<HTML
<tr>
    <td>{$img}</td>
    <td>{$c['overallAll']['result']}</td>
    <td>{$c['overallAll']['kda']}</td>
    <td>{$c['overallWin']['result']}</td>
    <td>{$c['overallWin']['kda']}</td>
    <td>{$c['overallLoss']['result']}</td>
    <td>{$c['overallLoss']['kda']}</td>
    <td>{$c['aramAll']['result']}</td>
    <td>{$c['aramAll']['kda']}</td>
    <td>{$c['aramWin']['result']}</td>
    <td>{$c['aramWin']['kda']}</td>
    <td>{$c['aramLoss']['result']}</td>
    <td>{$c['aramLoss']['kda']}</td>
    <td>{$c['classicAll']['result']}</td>
    <td>{$c['classicAll']['kda']}</td>
    <td>{$c['classicWin']['result']}</td>
    <td>{$c['classicWin']['kda']}</td>
    <td>{$c['classicLoss']['result']}</td>
    <td>{$c['classicLoss']['kda']}</td>
</tr>
HTML;
        $rows[] = $html;
    }
    echo generateCustomTable(
        array(
            'classes' => 'heavyData',
            'hovered' => true,
            'sortable' => true,
            'headers' => array(
                array('title' => 'Champ', 'sortable' => false), 
                array('title' => 'TG', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total Games Played'),
                array('title' => 'KDA', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total KDA'),
                array('title' => 'TGW', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total Games Won'),
                array('title' => 'KDA', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total KDA In Games Won'),
                array('title' => 'TGL', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total Games Lost'),
                array('title' => 'KDA', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total KDA In Games Lost'),
                array('title' => 'TAG', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total ARAM Games Played'),
                array('title' => 'KDA', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total KDA In ARAM Games'),
                array('title' => 'TAW', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total ARAM Games Won'),
                array('title' => 'KDA', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total KDA In ARAM Games Won'),
                array('title' => 'TAL', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total ARAM Games Lost'),
                array('title' => 'KDA', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total KDA In ARAM Games Lost'),
                array('title' => 'TCG', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total Classic Games Played'),
                array('title' => 'KDA', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total KDA In Classic Games'),
                array('title' => 'TCW', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total Classic Games Won'),
                array('title' => 'KDA', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total KDA In Classic Games Won'),
                array('title' => 'TCL', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total Classic Games Lost'),
                array('title' => 'KDA', 'sortable' => true, 'number' => true, 'hint' => 'Column|Total KDA In Classic Games Lost')),
            'rows' => $rows
        )
    );
?>