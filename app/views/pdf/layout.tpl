<?php

require_once($this->config['appRoot'].'/libs/tcpdf/config/lang/eng.php');

// set document information
$this->pdf->SetCreator(PDF_CREATOR);
$this->pdf->SetAuthor("Oscar ????");
$this->pdf->SetTitle('Calendar '.$this->calendar->year);
$this->pdf->SetSubject('');
$this->pdf->SetKeywords('');


// set default header data
//$this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);

// remove default header/footer
$this->pdf->setPrintHeader(false);
$this->pdf->setPrintFooter(false);

// set header and footer fonts
$this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$this->pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$this->pdf->SetFont('helvetica', '', 12);

//Column titles
$this->header = array('Sunday', 'Monday', 'Tuesday', 'Wenedesday','Thursday', 'Friday', 'Saturday');
$this->month_names = Array("January", "February", "March", "April", "May",
    "June", "July", "August", "September", "October", "November",
    "December");

//$month = $this->calendar->months[0];
foreach($this->calendar->months as $month){
    $this->month = $month;
    $this->month_number = preg_replace("/(.*)\-(.*)\-(.*)/", "$2", $this->month->date);
    $this->render("pdf/calendar_block.tpl");
}


