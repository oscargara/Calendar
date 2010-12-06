<?php
//letter size: 215.9 mm × 279.4 mm
$ratio = 900 / 215.9;
$this->width = 215.9 * $ratio;
$this->height = 279.4 * $ratio;

$this->image_width = ($this->width);
$this->image_height = ($this->image_width/(4/3));

$this->calendar_height = $this->height - $this->image_height;

$this->font_size = $ratio*10;

$textContent= <<< STYLE

#calenadar-list li {list-style-type:none;}
table.calendar_page{}
table.calendar_month{border: 1pt solid #000000;}
tr.calendar_block{}
td.month_name{font-size:34pt; font-weight:bold; padding:3px 0 14px; text-align:center;}
td.day_name{width:100px;text-align:center;font-weight:bold; border: 1pt solid #000000;}
td.date_name{font-size:16pt; height:60px;border: 1pt solid #000000;text-align:right;vertical-align:top;padding:9px;}

STYLE;

$this->setHeadersTag('style', array('textContent'=>$textContent, 'type'=>"text/css"));

?>



<?php

//$month = $this->calendar->months[0];
foreach($this->calendar->months as $month){
    ?>
    <div>

        <?php
        $this->month = $month;
        $this->month_number = preg_replace("/(.*)\-(.*)\-(.*)/", "$2", $this->month->date);

        $this->unique_id = sprintf("%02s", $this->month_number) . "-" .  $this->calendar->year;
        echo $this->render("pdf/calendar_block.tpl");
        ?>

    </div>
    <?php } ?>

