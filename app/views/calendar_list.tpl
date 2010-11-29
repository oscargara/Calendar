<?php
//letter size: 215.9 mm × 279.4 mm
$ratio = 400 / 215.9;
$this->width = 215.9 * $ratio;
$this->heigth = 279.4 * $ratio;

$this->image_width = $this->width;
$this->image_heigth = $this->image_width/(4/3);

$this->calendar_height = $this->heigth - $this->image_heigth;

$this->font_size = $ratio*10;

?>
<style type="text/css">
.drop_image_size{width:<?php echo $this->image_width; ?>px; height:<?php echo $this->image_heigth; ?>px}
</style>

<div id="slider-code">
	<a class="buttons prev" href="#">left</a>
	<div class="viewport">
		<ul id="calenadar-list" class="overview">
		<?php for ($month=1;$month<13;++$month){ ?>
			<li>
                            <div class="shadowbox">
                            <?php
                            $this->month = $month;
                            $this->unique_id = sprintf("%02s", $this->month) . "-" .  $this->year;
                            echo $this->render("calendar_block.tpl");
                            ?>
                            </div>
			</li>
			<?php } ?>
		</ul>
	</div>
	<a class="buttons next" href="#">right</a>
</div>