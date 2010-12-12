<?php
//letter size: 215.9 mm × 279.4 mm
$ratio = 400 / 215.9;
$this->width = 215.9 * $ratio;
$this->height = 279.4 * $ratio;

$this->image_width = $this->width;
$this->image_height = $this->image_width/(4/3);

$this->calendar_height = $this->height - $this->image_height;

$this->font_size = $ratio*10;

$this->month_names = Array("January", "February", "March", "April", "May",
    "June", "July", "August", "September", "October", "November",
    "December");
$this->day_names = array('Sunday', 'Monday', 'Tuesday', 'Wenedesday','Thursday', 'Friday', 'Saturday');

?>
<style type="text/css">
.drop_image_size{width:<?php echo $this->image_width; ?>px; height:<?php echo $this->image_height; ?>px}
</style>

<div id="slider-code">
	<a class="buttons prev" href="#">left</a>
	<div class="viewport">
		<ul id="calenadar-list" class="overview">
		<?php foreach($this->calendar->months as $month){ ?>
			<li>
                            <div class="shadowbox">
                            <?php
                            $this->month = $month;
                            $this->month_number = preg_replace("/(.*)\-(.*)\-(.*)/", "$2", $this->month->date);
                            $this->unique_id = sprintf("%02s", $this->month_number) . "-" .  $this->calendar->year;
                            echo $this->render("calendar_block.tpl");
                            ?>
                            </div>
			</li>
			<?php } ?>
		</ul>
	</div>
	<a class="buttons next" href="#">right</a>
</div>