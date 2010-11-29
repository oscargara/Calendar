<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="keywords" content="calendar pictures year photos">
<meta name="description" content="calendar">
<title>Calendar</title>
<?php //echo $this->renderCssLine(array('href'=>"/css/reset-fonts-grids.css"));?>
<?php //echo $this->renderCssLine(array('href'=>"/css/base.css"));?>
<?php echo $this->renderCssLine(array('href'=>"/css/carousel.css"));?>
<?php echo $this->renderCssLine(array('href'=>"/css/main.css"));?>

<?php echo $this->renderScriptLine(array('src'=>"http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"));?>
<?php echo $this->renderScriptLine(array('src'=>"/js/jquery.tinycarousel.min.js"));?>
<?php echo $this->renderScriptLine(array('src'=>"/js/upload.js"));?>

<script type="text/javascript">

$(document).ready(function(){
    $("#slider-code a.next").html('');
    $("#slider-code a.prev").html('');
    $("#slider-code").tinycarousel();
});
</script>


</head>
<body>
<div id="doc3" class="yui-t7">
	<div id="hd">

	</div>
	<div id="bd">
		<div id="yui-main">
			<div class="yui-b">
				<?php echo $this->regions['content'] ?>
			</div>
		</div>
		<div class="yui-b">
			<!-- PUT SECONDARY COLUMN CODE HERE -->
		</div>
	</div>
	<div id="ft">
		<!-- PUT FOOTER CODE HERE -->
	</div>
</div>
</body>
</html>