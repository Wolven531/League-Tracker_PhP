<?php
    require('./util.php');

    $itemUrl = 'https://na.api.pvp.net/api/lol/static-data/na/v1.2/item?itemListData=all&api_key=' . $GLOBALS['API_KEY'];
    //$itemUrl = 'https://prod.api.pvp.net/api/lol/static-data/na/v1/item?locale=en_US&itemListData=all&api_key=' . $GLOBALS['API_KEY'];
    $itemResp = file_get_contents($itemUrl);
    $itemResp = json_decode($itemResp);
    $champUrl = 'https://na.api.pvp.net/api/lol/static-data/na/v1.2/champion?champData=all&api_key=' . $GLOBALS['API_KEY'];
    //$champUrl = 'https://prod.api.pvp.net/api/lol/na/v1.1/champion?api_key=' . $GLOBALS['API_KEY'];
    $champResp = file_get_contents($champUrl);
    $champResp = json_decode($champResp);
    $dao = getDao();
    foreach($itemResp->data as $id => $item) {
        $gold = $item->gold;
        $img = $item->image;
        $into = $item->into; // array of IDs this item builds into
        $stats = $item->stats;
        if($img->x > 0) { $img->x *= -1; }
        if($img->y > 0) { $img->y *= -1; }
        if(!$item->plaintext) { $item->plaintext = ''; }
        $item->id = $id;
        $dao->updateAPIItem($item);
    }
    $dao->updateChamps($champResp->champions);
    destroyDao($dao);
    
    echo '<h3>Champs</h3>';
    echo '<pre>';
    var_dump($champResp);
    echo '</pre>';
    echo '<h3>Items</h3>';
    echo '<pre>';
    var_dump($itemResp);
    echo '</pre>';
?>