<?php
$monthNames = Array("January", "February", "March", "April", "May",
    "June", "July", "August", "September", "October", "November",
    "December");

$cMonth = $this->month_number;

$cYear = $this->calendar->year;

$prev_year = $cYear;
$next_year = $cYear;

$prev_month = $cMonth - 1;
$next_month = $cMonth + 1;

if ($prev_month == 0) {
    $prev_month = 12;
    $prev_year = $cYear - 1;
}
if ($next_month == 13) {
    $next_month = 1;
    $next_year = $cYear + 1;
}

$data = array('calendarId' => $this->calendar->_id,
              'month'      => $this->month_number,
              'monthId'    => $this->month->_id,
              'url'        => $this->month->url!='' ? $this->month->url.'_th.jpg' : ''
            );

?>

<div id="calendar-<?php echo $this->unique_id; ?>" class="calendar_div">
    <table width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>">
        <tr height="<?php echo $this->height - $this->calendar_height; ?>">
            <td>
                <div id="dropbox-<?php echo $this->unique_id; ?>" class="dropable" data='<?php echo json_encode($data);?>'>
                <div class="dropbox drop_image_size">
                    <span class="droplabel">Drop file here...</span>
                    <div class="progressbar"></div>
                </div>                
                <img class="preview drop_image_size" style="display:none" src="" alt="[ preview will display here ]"/>
                <a href="#" class="delete-image" style="display:none">Delete</a>
                </div>
             </td>
        </tr>
        <tr>
            <td align="center">
                <table class="month_calendar" style="font-size:<?php echo $this->font_size;?>" width="100%" border="0" cellpadding="2" cellspacing="2" height="<?php echo $this->calendar_height; ?>">
                    <tr align="center" class="month_name">
                        <th colspan="7"><strong><?php echo $monthNames[$cMonth - 1] . ' ' . $cYear; ?></strong></th>
                    </tr>
                    <tr style="font-size:40%" class="weekday_labels">
                        <?php foreach ($this->day_names as $name){?>
                             <th align="center" bgcolor="#999999" style="color:#FFFFFF"><strong><?php echo $name ?></strong></th>
                        <?php }?>                        
                    </tr>

                    <?php
                    $datas = $this->month->getMonthsDays();

                    foreach ($datas as $r=>$row){
                        echo "<tr style='font-size:50%' width='40px'>\n";
                        foreach ($row as $c=>$data){
                            if (isset($data['outday']) && $data['outday']!='') echo "<td class='grey_day'>{$data['outday']}</td>\n";
                            if (isset($data['day']) && $data['day']!=''){
                                $note = '';
                                if (isset($data['note']) && $data['note']!=''){
                                    $note = $data['note'];                                    
                                }
                                
                                echo "<td align='left' onClick='sendNote(this, \"{$this->month->_id}\",\"{$data['day']}\")'>";
                                echo "  <div class='day_number'>{$data['day']}</div>";
                                echo "  <div class='day_note'>{$note}</div>";
                                echo "</td>\n";
                            }
                        }
                        echo "</tr>\n";
                    }                    
                    ?>
                </table>
            </td>
        </tr>
    </table>
</div>

