<?php
    require_once('./util.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $SITE_TITLE; ?> Awards</title>
        <?php include('./basicSiteNeeds.html'); ?>
        <script type="text/javascript">
            
        </script>
    </head>
    <body>
        <?php
            include('./header.html');
            include('./navigation.php');
            include('./sections/awardsSection.php');
            include('./footer.php');
        ?>
    </body>
</html>
