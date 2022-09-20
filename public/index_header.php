<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"> 
<html> 
<head> 
<title>PHPImage :: arnapou.net</title>
<meta charset="UTF-8" />
<style type="text/css">
* {
	font-family: Verdana, Arial;
}
body {
	background-color: #888888;
	padding: 16px 16px 100px 16px;
	margin: 0px;
}
h1 {
	text-align: center;
	padding: 0px;
	margin: 0px;
}
img {
	vertical-align: top;
}
fieldset {
	display: block;
	padding: 8px;
	margin: 16px auto;
	width: 850px;
	border: 2px solid white;
	background-color: #aaaaaa;
	font-size: 12px;
	overflow: hidden;
}
fieldset span,
fieldset p,
fieldset strong,
fieldset label,
fieldset select,
fieldset option {
	font-size: 12px;
}
legend {
	padding: 1px 8px;
	border: 0px;
	background-color: #ffffff;
	font-weight: bold;
	font-size: 14px;
}
p {
	padding: 2px 0px;
	margin: 0px;
	overflow: hidden;
}
p label {
	display: block;
	float: left;
	text-align: right;
	padding-right: 4px;
	width: 125px;
}
p input.s {
	width: 300px;
}
pre {
	font-family: Consolas, 'Lucida Console', Monaco, monospace, Courier New;
	font-size: 13px;
	border: 1px dotted black;
	background-color: #666666;
	color: #eeeeee;
	padding: 4px;
	line-height: 150%;
	margin: 4px 0px;
}
span.pre,
pre * {
	font-family: Consolas, 'Lucida Console', Monaco, monospace, Courier New;
	font-size: 13px;
}
pre em {
	color: #ddeedd;
}
pre a {
	color: #add8e6;
	text-decoration: underline;
}
pre a:hover {
	color: #ade6b9;
	text-decoration: underline;
}
span.s1 {
	color: lightgreen;
}
span.s2 {
	color: #ff8888;
}
span.s3 {
	color: #8888ff;
}
span.s4 {
	color: #aaaaaa;
}
span.c {
	color: #aabbaa;
}
img.right {
	float: right;
	padding: 25px 5px 5px 5px;
}
div.element {
	padding: 0 0 10px 0;
}
strong.element {
	display:block;
	padding: 5px 0 10px 0;
	color: #006;
}
li {
	overflow: hidden;
}
.element b {
	color: #ff8888;
}
#colormap {
	width: 80%;
}
#colormap td {
	width: 50%;
}
#colormap td.c {
	padding-right: 1em;
	text-align: right;
}
div.menu {
	text-align:center;
	padding: 10px 0;
}
div.menu a {
	text-decoration: none;
	color: black;
	background: white;
	font-weight: bold;
	padding: 1px 5px;
	margin: 0 5px;
	border: 1px solid white;
}
div.menu a:hover {
	text-decoration: none;
	color: darkblue;
	background: #ff8;
}
ul.menu {
	float: left;
	width: 180px;
	margin: 10px;
	padding: 0;
}
ul.menu a {
	text-decoration: none;
	line-height: 1.2;
}
ul.menu a:hover {
	text-decoration: underline;
}
ol {
    list-style: inside decimal;
    padding-left: 0;
}
</style> 
</head> 
<body> 
<?php
include(__DIR__.'/../autoload.php');
$lang = 'fr';
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    if (substr(strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']), 0, 2) != 'fr') {
        $lang = 'en';
    }
}
/*
 *
 */
function menufiles($folder)
{
    if (!file_exists($folder)) {
        echo '<span class="s2">Dossier "'.$folder.'" inexistant !</span>';
    } else {
        $files = glob($folder.'/*.tpl');
        if (is_array($files)) {
            $n = ceil(count($files)/4);
            $buf = '<ul class="menu">';
            foreach ($files as $i=>$file) {
                if ($i%$n==0 && $i>0) {
                    $buf .= '</ul><ul class="menu">';
                }
                $buf .= '<li><a href="#'.basename($file, '.tpl').'">'.basename($file, '.tpl').'</a></li>';
            }
            $buf .= '</ul>';
            echo $buf;
        }
    }
}
/*
 *
 */
function showfiles($folder)
{
    if (!file_exists($folder)) {
        echo '<span class="s2">Dossier "'.$folder.'" inexistant !</span>';
    } else {
        $files = glob($folder.'/*.tpl');
        if (is_array($files)) {
            $buf = '';
            foreach ($files as $i=>$file) {
                if ($i>0) {
                    $buf .= '<hr />';
                }
                $buf .= showfile($file);
            }
            $buf = preg_replace('#(\$[a-z0-9_]+)#si', '<span class="s1">$1</span>', $buf);
            $buf = preg_replace('#(->)([a-z0-9_]+)#si', '$1<span class="s2">$2</span>', $buf);
            $buf = str_replace('PHPImage(', '<span class="s2">PHPImage</span>(', $buf);
            $buf = preg_replace('#(true|false|new |echo |require|array|class |extends |function )#s', '<span class="s3">$1</span>', $buf);
            $buf = preg_replace('#(//[^\n]+)#si', '<span class="s4">$1</span>', $buf);
            echo $buf;
        }
    }
}
/*
 *
 */
function showfile($file)
{
    global $lang;
    if (!file_exists($file)) {
        return '<span class="s2">Fichier "'.$file.'" inexistant !</span>';
    } else {
        $element = basename($file, '.tpl');
        $content = file_get_contents($file);
        $content = str_replace($element.'(', '<b>'.$element.'</b>(', $content);
        $content = str_replace('_ELEMENT_', substr($file, 0, -4), $content);
        $content = str_replace('_FOLDER_', dirname($file), $content);
        $content = str_replace('<'.$lang.'>', '', $content);
        $content = str_replace('</'.$lang.'>', '', $content);
        $content = preg_replace('#<en>.*?</en>#is', '', $content);
        $content = preg_replace('#<fr>.*?</fr>#is', '', $content);
        $content = preg_replace_callback('#\{CODE (.*?)\}#i', 'showfile_cb', $content);
        return '<strong class="element" id="'.$element.'">'.$element.'</strong><div class="element">'.$content.'</div>';
    }
}
/*
 *
 */
function showfile_cb($matches)
{
    $file = $matches[1];
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = preg_replace('!<\?php(\s*/\*.*?\*/)?!s', '', $content);
        $content = str_replace('?>', '', $content);
        $content = preg_replace('!require.*?autoload.php[^\n]+!', '', $content);
        $content = trim($content);
        return '<pre>'.$content.'</pre>';
    }
    return '';
}
?>
<h1>PHPImage</h1>
<div class="menu">
	<a href="index.php">Usage</a>
	<a href="index_methods.php">Methods</a>
	<a href="index_properties.php">Properties</a>
	<a href="index_samples.php">Samples</a>
	<a href="index_colors.php">Colors</a>
</div>