<?php
include __DIR__ . '/index_header.php';
?>
<fieldset><legend>Samples Summary</legend>
	<?php menufiles('samples'); ?>
</fieldset>
<fieldset><legend>Samples</legend>
	<?php showfiles('samples'); ?>
</fieldset>
<?php
include __DIR__ . '/index_footer.php';
?>