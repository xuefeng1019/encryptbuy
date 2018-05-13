<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <?php include_once '__head.tpl'; ?>
</head>
<body>
	<?php include_once '__independent_head.tpl';?>
<?=$__content?>

<?php include_once '__index_footer.tpl'; ?>
</body>
<?php $js = trim($__js['inline_footer']); if(!empty($js)) :?>
<script type="text/javascript">
<?=$__js['inline_footer']?>
</script>
<?php endif;?>
</html>