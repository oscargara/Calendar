<?php

$ratio = 180 / 215.9;
$this->width = 215.9 * $ratio;
$this->height = 279.4 * $ratio;

$this->image_width = ($this->width);
$this->image_height = ($this->image_width/(4/3));

$this->calendar_height = $this->height - $this->image_height;

$this->month_number = preg_replace("/(.*)\-(.*)\-(.*)/", "$2", $this->month->date);

$image_path = ($this->month->url!='') ? $this->month->url.'_pdf.jpg' : "/images/no-image.jpg";

$image_path = $this->config['webRoot'] . $image_path;

$data = $this->month->getMonthsDays();

// add a page
$this->pdf->AddPage();

// set JPEG quality
$this->pdf->setJPEGQuality(100);

// Image
$this->pdf->Image($image_path, 15, 25, $this->image_width , $this->image_height, 'JPG', '', 'C', false, 300, '', false, false, 1, false, false, false);

$this->pdf->Ln(136);

$this->pdf->SetFont('helvetica', 'B', 25);

$this->pdf->Cell(180, 30, $this->month_names[$this->month_number -1]." ".$this->calendar->year, '', 0, 'C', 0, '', 0, false, 'T', 'T');

$this->pdf->Ln(12);

$this->pdf->SetFont('helvetica', '', 10);
// print colored table
$this->pdf->table($this->header, $data);