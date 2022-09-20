<?php
include __DIR__ . '/index_header.php';
?>
<!-------------------- USAGE --------------------> 
<fieldset><legend>Usage</legend> 
	<strong>Require</strong> 
	<pre>PHP5
GD2 (with antilias)</pre> 
	
	<strong>PHP Sample</strong> 
	<pre><span class="s3">require</span>('../autoload.php');
<span class="s1">$matrix</span> = '1 2 1  4 2 4  1 2 1';
<span class="s1">$image</span> = <span class="s3">new </span><span class="s2">PHPImage</span>('php.gif');
<span class="s1">$image</span>-><span class="s2">convolution</span>(<span class="s1">$matrix</span>);
<span class="s1">$image</span>-><span class="s2">format</span> = 'png';
<span class="s1">$image</span>-><span class="s2">display</span>();</pre> 
	
	<strong>Use of cache</strong> 
	<pre><span class="s1">$image</span> = new PHPImage();
<span class="s1">$image</span>-><span class="s2">cachetime</span> = 86400*365*10; <span class="s4">// 10 years</span>
if(<span class="s1">$image</span>-><span class="s2">cacheok</span>(<span class="s1">$source</span>, <span class="s1">$cached_image</span>)) {
	<span class="s1">$image</span>-><span class="s2">display</span>(<span class="s1">$cached_image</span>);
}
else {
	<span class="s1">$image</span>-><span class="s2">loadfromfile</span>(<span class="s1">$source</span>);
	<span class="s4">// put here your image processing
	// it can be : resize, crop, effects, ...</span>
	<span class="s1">$image</span>-><span class="s2">display</span>(<span class="s1">$cached_image</span>);
}
<span class="s3">exit</span>;</pre> 

</fieldset> 
<?php
include __DIR__ . '/index_footer.php';
?>