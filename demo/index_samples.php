<?php
include('index_header.php');
?>
<fieldset><legend>Samples Summary</legend>
	<?php menufiles('samples'); ?>
</fieldset>
<fieldset><legend>Samples</legend>
	<?php showfiles('samples'); ?>
</fieldset>
<?php
include('index_footer.php');
?>