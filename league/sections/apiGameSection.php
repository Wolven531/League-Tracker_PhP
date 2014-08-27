<?php
require_once('./util.php');
$pageNum = isset($_GET['page']) ? intval($_GET['page']) : 1;
?>
<h3>API Retrieved Games (Page <?php echo $pageNum; ?>)</h3>
<?php echo generateGameTableForUser($pageNum, 10); ?>