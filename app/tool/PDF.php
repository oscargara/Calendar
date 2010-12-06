<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class tool_PDF extends TCPDF{

    function table($header, $data){
        // Colors, line width and bold font
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.2);
        $this->SetFont('', 'B', 10);

        $table_width = 180;
        // Header     
        $num_headers = count($header);
        $w = $table_width/$num_headers;
        for($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w, 7, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = 0;
        $cell_h = 16;
        foreach($data as $row) {

            foreach($row as $txt){
                $ch = 5;

                if ($txt['day']!=''){
                    $text = $txt['day'];
                    $note = '';
                    if ($txt['note']) {
                        $note = $txt['note'];
                    }
                    $this->Cell($w, $ch, $text, 'LRT', 0, 'R', 0, '', 0, false, 'T', 'T');
                }

                if ($txt['outday']!=''){
                    $this->SetTextColor(200,200,200);
                    $this->Cell($w, $ch, $txt['outday'], 'LRT', 0, 'R', 0, '', 0, false, 'T', 'T');
                    $this->SetTextColor(0);
                }
            }

            $this->Ln();

            foreach($row as $txt){
                if ($txt['day']!=''){
                    if ($txt['note']) {
                        $this->SetFont('', '', 6);                        
                        $o = array( 'w'=>$w,
                                    'h'=>$cell_h-$ch,
                                    'maxh'=>$cell_h-$ch,
                                    'txt'=>$txt['note'],
                                    'align'=>'L',
                                    'valign'=>'B',
                                    'border'=>'LRB',
                                    'ln'=>0);
                        
                        $this->pdfMultiCell($o);
                        //$this->Cell($w, $cell_h-$ch, $txt['note'], 'LRB',0, 'L', 0, '', 0, false, 'T', 'B');
                        $this->SetFont('', '', 10);
                    }
                }

                if ($txt['outday']!=''){
                    $this->Cell($w, $cell_h-$ch, '', 'LRB', 0, 'L', 0, '', 0, false, 'T', 'B');
                }
            }
            $this->Ln();
        }
        $this->Cell($table_width, 0, '', 'T');
    }


    public function pdfMultiCell($p){

        //$o['w'];
        //$o['h'];
        //$o['txt'];
        $o['border'] = 0;
        $o['align'] = 'J';
        $o['fill'] = FALSE;
        $o['ln'] = 1;
        $o['x'] = '';
        $o['y'] = '';
        $o['reseth'] = TRUE;
        $o['stretch'] = 0;
        $o['ishtml'] = FALSE;
        $o['autopadding'] = TRUE;
        $o['maxh'] = 0;
        $o['valign'] = 'T';
        $o['fitcell'] = FALSE;

        $o = array_merge($o, $p);

        parent::multiCell($o['w'], $o['h'], $o['txt'], $o['border'], $o['align'], $o['fill'], $o['ln'], $o['x'], $o['y'], $o['reseth'], $o['stretch'], $o['ishtml'], $o['autopadding'], $o['maxh'], $o['valign'], $o['fitcell']);

    }
}
