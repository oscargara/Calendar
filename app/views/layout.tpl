<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="keywords" content="calendar pictures year photos"/>
<meta name="description" content="calendar"/>
<title>Calendar</title>
<?php //echo $this->renderCssLine(array('href'=>"/css/reset-fonts-grids.css"));?>
<?php //echo $this->renderCssLine(array('href'=>"/css/base.css"));?>
<?php echo $this->renderCssLine(array('href'=>"/css/carousel.css"));?>
<?php echo $this->renderCssLine(array('href'=>"/css/main.css"));?>

<?php echo $this->renderScriptLine(array('src'=>"/js/jquery-1.4.4.min.js"));?>
<?php //echo $this->renderScriptLine(array('src'=>"http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"));?>
<?php echo $this->renderScriptLine(array('src'=>"/js/jquery.tinycarousel.min.js"));?>
<?php echo $this->renderScriptLine(array('src'=>"/js/upload.js"));?>

<script type="text/javascript">
var baseURL = "<?php print_r($this->config['baseUrl']);?>";



$(document).ready(function(){
    $("#slider-code a.next").html('');
    $("#slider-code a.prev").html('');
    $("#slider-code").tinycarousel();
    $(document).width();

    $("#slider-code .viewport").width($(document).width() - 2*50);

    $(window).resize(function() {
        $("#slider-code .viewport").width($(document).width() - 2*50);
    });

});

function sendNote(element, month_id, day){
    var $e = $(element);
    var $note = $e.children().filter(".day_note");

    note = $note.html();
    
    note = note.replace("\n", ",");
    var new_note = prompt("Please enter your notes, separated by commas",note);
    if (new_note==note || new_note==null) return;
    new_note = new_note.replace(",", "\n");
    this.element = element;
    $.post(baseURL+'/calendar/addNote/'+month_id+'/'+day,{'note':new_note} ,function(data) {
            var $e = $(element);
            var $note = $e.children().filter(".day_note");
            data = data.replace("\n", ",");
            $note.html(data);
            
        });

}

</script>


</head>
<body>
<div id="doc3" class="yui-t7">
	<div id="hd">
        <a href="<?php print_r($this->config['baseUrl']);?>/calendar/PDF">Download PDF</a>

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