<?php
    require_once('./util.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $SITE_TITLE; ?> Profile: <?php echo $user; ?></title>
        <?php include('./basicSiteNeeds.html'); ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $('.close').on('click', function(e){
                    e.stopPropagation();
                    var row = $(this).parentsUntil('.api-stat-game').parent();
                    row.find('.secret').addClass('invis'); // make the secret cell invisible
                    row.find('.basic').removeClass('invis'); // make the basic cells visible
                });
                $('.api-stat-game').on('click', function(e){
                    var row = $(this);
                    row.find('.secret').removeClass('invis'); // make the secret cell visible
                    row.find('.basic').addClass('invis'); // make the basic cells invisible
                });
                
                $('.api-item-image').on('mousemove', function(e) {
                    var desc = $(this).next('.api-item-info').last();
                    desc.css({
                        'display' : 'block',
                        'position' : 'absolute'
                    });
                    desc.offset({ top: e.pageY + 10, left: e.pageX + 10})
                });
                
                $('.api-item-image').on('mouseout', function(e) {
                    var desc = $(this).next('.api-item-info').last();
                    desc.css({
                        'display' : 'none'
                    });
                });
            });
        </script>
    </head>
    <body>
        <?php
            include('./header.html');
            echo '<br />';
            include('./navigation.php');
        ?>
        <h2><?php echo $user; ?> Profile</h2>
        <h3>Stats</h3>
        <div class="content">
            Overall KDA: <?php echo number_format($userStats['kda'], 4); ?>
            <br/>
            Win/Loss Ratio: <?php echo $userStats['wlRatio']; ?> (<?php echo $userStats['winPercentage']; ?>%)
            <br/>
            <table class="api-champ-stats-individual">
                <tr>
                    <th>Mode</th>
                    <th>Wins</th>
                    <th>Losses</th>
                </tr>
                <tr>
                    <td>Overall</td>
                    <td>
                        <ul>
                        <?php
                            for($a = 0; $a < count($userStats['topChampWins']); $a++) {
                                echo '<li>' .getChampHTML($champsById[$userStats['topChampWins'][$a]['champ']], 'small', true) . ' with ' . $userStats['topChampWins'][$a]['outcome'] . '</li>';
                            }
                        ?>
                        </ul>
                    </td>
                    <td>
                        <ul>
                             <?php
                                for($a = 0; $a < count($userStats['topChampLosses']); $a++) {
                                    echo '<li>' .getChampHTML($champsById[$userStats['topChampLosses'][$a]['champ']], 'small', true) . ' with ' . $userStats['topChampLosses'][$a]['outcome'] . '</li>';
                                }
                            ?>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>ARAM</td>
                    <td>
                        <ul>
                        <?php
                            for($a = 0; $a < count($userStats['topARAMChampWins']); $a++) {
                                echo '<li>' .getChampHTML($champsById[$userStats['topARAMChampWins'][$a]['champ']], 'small', true) . ' with ' . $userStats['topARAMChampWins'][$a]['outcome'] . '</li>';
                            }
                        ?>
                        </ul>
                    </td>
                    <td>
                        <ul>
                             <?php
                                for($a = 0; $a < count($userStats['topARAMChampLosses']); $a++) {
                                    echo '<li>' .getChampHTML($champsById[$userStats['topARAMChampLosses'][$a]['champ']], 'small', true) . ' with ' . $userStats['topARAMChampLosses'][$a]['outcome'] . '</li>';
                                }
                            ?>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>Classic</td>
                    <td>
                        <ul>
                        <?php
                            for($a = 0; $a < count($userStats['topClassicChampWins']); $a++) {
                                echo '<li>' .getChampHTML($champsById[$userStats['topClassicChampWins'][$a]['champ']], 'small', true) . ' with ' . $userStats['topClassicChampWins'][$a]['outcome'] . '</li>';
                            }
                        ?>
                        </ul>
                    </td>
                    <td>
                        <ul>
                             <?php
                                for($a = 0; $a < count($userStats['topClassicChampLosses']); $a++) {
                                    echo '<li>' .getChampHTML($champsById[$userStats['topClassicChampLosses'][$a]['champ']], 'small', true) . ' with ' . $userStats['topClassicChampLosses'][$a]['outcome'] . '</li>';
                                }
                            ?>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>
        <?php
          include('./sections/apiGameSection.php');
          echo '<br />';
          include('./footer.php');
        ?>
    </body>
</html>
