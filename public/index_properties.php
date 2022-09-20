<?php
include __DIR__ . '/index_header.php';
?>
<fieldset><legend>Properties Summary</legend>
	<?php menufiles('properties'); ?>
</fieldset>
<fieldset><legend>Properties</legend> 
	<?php showfiles('properties'); ?> 
</fieldset>
<?php
include __DIR__ . '/index_footer.php';
?>