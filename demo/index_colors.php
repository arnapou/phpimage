<?php
include('index_header.php');
?>
<!-------------------- COLORS --------------------> 
<fieldset><legend>Colors</legend> 
	<table id="colormap">
	<?php
		$buf = '';
		foreach($PHPImageColors as $color => $RGB) {
			$buf .= '<tr>';
			$buf .= '<td class="c">'.$color.'</td>';
			$buf .= '<td style="background-color:'.sprintf('#%02x%02x%02x', $RGB[0], $RGB[1], $RGB[2]).';"></td>';
			$buf .= '</tr>';
		}
		echo $buf;
	?>
	</table>
</fieldset>
<?php
include('index_footer.php');
?>