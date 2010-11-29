<?php
$monthNames = Array("January", "February", "March", "April", "May",
    "June", "July", "August", "September", "October", "November",
    "December");

$cMonth = $this->month;

$cYear = $this->year;

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

?>

<div id="calendar-<?php echo $this->unique_id; ?>" class="calendar_div">
    <table width="<?php echo $this->width; ?>" height="<?php echo $this->heigth; ?>">
        <tr height="<?php echo $this->heigth - $this->calendar_height; ?>">
            <td>
                <div id="dropbox-<?php echo $this->unique_id; ?>" class="dropable">
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
                <table style="font-size:<?php echo $this->font_size;?>" width="100%" border="1" cellpadding="2" cellspacing="2" height="<?php echo $this->calendar_height; ?>">
                    <tr align="center" >
                        <td colspan="7" bgcolor="#999999" style="color:#FFFFFF; font-size:100%"><strong><?php echo $monthNames[$cMonth - 1] . ' ' . $cYear; ?></strong></td>
                    </tr>
                    <tr style="font-size:50%">
                        <td align="center" bgcolor="#999999"
                            style="color:#FFFFFF"><strong>S</strong></td>
                        <td align="center" bgcolor="#999999"
                            style="color:#FFFFFF"><strong>M</strong></td>
                        <td align="center" bgcolor="#999999"
                            style="color:#FFFFFF"><strong>T</strong></td>
                        <td align="center" bgcolor="#999999"
                            style="color:#FFFFFF"><strong>W</strong></td>
                        <td align="center" bgcolor="#999999"
                            style="color:#FFFFFF"><strong>T</strong></td>
                        <td align="center" bgcolor="#999999"
                            style="color:#FFFFFF"><strong>F</strong></td>
                        <td align="center" bgcolor="#999999"
                            style="color:#FFFFFF"><strong>S</strong></td>
                    </tr>

<?php
$timestamp = mktime(0, 0, 0, $cMonth, 1, $cYear);
$maxday = date("t", $timestamp);
$thismonth = getdate($timestamp);
$startday = $thismonth['wday'];

for ($i = 0; $i < ($maxday + $startday); $i++) {
    if (($i % 7) == 0)
        echo "<tr style='font-size:50%'>\n";
    if ($i < $startday)
        echo "<td>x</td>\n";
    else
        echo "<td align='center' valign='middle'>" . ($i - $startday + 1) . "</td>\n";
    if (($i % 7) == 6)
        echo "</tr>\n";
}
?>
                </table>
            </td>
        </tr>
    </table>
</div>

