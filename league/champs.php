<?php require_once('./util.php'); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $SITE_TITLE; ?> Champions</title>
        <?php require('./basicSiteNeeds.php'); ?>
    </head>
    <body class="metro">
        <?php include('./navigation.php'); ?>
        <div class="page">
            <div class="page-region">
                <div class="page-region-content">
                    <div class="container">
                        <div class="grid fluid">
                            <?php include('./sections/champStats.php'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include('./footer.php'); ?>
    </body>
</html>