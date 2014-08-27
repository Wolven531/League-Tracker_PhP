<?php require_once('./util.php'); ?>
<!DOCTYPE html>
<html lan="en">
    <head>
        <title><?php echo $SITE_TITLE; ?> Awards</title>
        <?php require('./basicSiteNeeds.php'); ?>
    </head>
    <body class="metro">
        <?php include('./navigation.php'); ?>
        <div class="page">
            <div class="page-region">
                <div class="page-region-content">
                    <div class="container">
                        <div class="content">
                            <div class="grid fluid">
                                <?php include('./sections/awards.php'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>          
        <?php include('./footer.php'); ?>
    </body>
</html>
