<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of controller_calendar
 *
 * @author oscar
 */
class controller_Calendar extends core_Controller{

    function index(){

       $config = Application::getInstance()->getConfig();
       
       $user = model_User::getCurrentUser();
       
       if (is_null($user)){
            $this->createDummyUser();
            $user = model_User::getCurrentUser();
       }

       $calendar =  $user->calendar;

       if (!count($calendar->months)){
           $months = array();
           for ($i=1 ; $i<13; ++$i){
               $date = date("d-m-Y", mktime(0, 0, 0, $i, 1, $calendar->year));               
               $months[] = new model_Month(array('url'=>'', 'date'=>$date,'name'=>'', 'creation_date'=>time()));
           }
           $calendar->months = $months;
           $calendar->putYearHolidays();
           $calendar->save();
       }

       $this->view->calendar = $calendar;

       $this->view->setLayout('layout.tpl');
       $this->view->render("calendar_list.tpl", "content");
       echo $this->view->renderLayout();
    }

    public function upload(){

        $filename = $this->config['webRoot']."/uploads/".time().rand(1000, 9999);

        if (isset($_POST['base64']) && $_POST['base64']) {
            try{
                move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $filename);
                $content = base64_decode(file_get_contents($filename));
                file_put_contents($filename,$content);
                return $this->_createImages($filename, $_POST['calendarId'], $_POST['month'], $_POST['monthId']);
            }catch(Exception $e){
                echo $e->getMessage();
            }
        }elseif(isset($_FILES['uploadedfile'])){
            if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $filename)) {
                return $this->_createImages($filename, $_POST['calendarId'], $_POST['month'], $_POST['monthId']);
            }
        }
    }


    public function uploadURL(){
        $url = $_POST['url'];
        
        /*
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        */
        
        $filename = $this->config['webRoot']."/uploads/".time().rand(1000, 9999);

        $response = file_get_contents($url);
        file_put_contents($filename, $response);

        if(file_put_contents($filename, $response)) {
            return $this->_createImages($filename, $_POST['calendarId'], $_POST['month'], $_POST['monthId']);
        }

    }

    public function addNote($month_id, $day){
        $note = $_POST['note'];
        $month = model_Month::findByIndex($month_id);
        $month->addNote(new model_DayNotes(array('note'=>$note, 'month_day'=>$day)));
        $month->save();
        echo $note;
    }

    public function deletePicture($month_id){
        $month = model_Month::findByIndex($month_id);
        @unlink($this->config['webRoot'].$month->url.'_original.jpg');
        @unlink($this->config['webRoot'].$month->url.'_pdf.jpg');
        @unlink($this->config['webRoot'].$month->url.'_th.jpg');
        $month->url = '';
        $month->save();
    }

    public function PDF($isHtml=FALSE){
        set_time_limit(0);
        
        require_once($this->config['appRoot'].'/libs/tcpdf/tcpdf.php');

        $this->view->setLayout('pdf/layout.tpl');
        
        $user = model_User::getCurrentUser();

        $this->view->pages = array();
        $this->view->calendar = $user->calendar;

        $this->view->pdf = new tool_PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $this->view->renderLayoutByInclude();

        $this->view->pdf->Output('calendar-'.$user->calendar->year.'.pdf', 'I');
        
    }

    public function getCalendarJSON(){
        $user = model_User::getCurrentUser();
        $calendar_model = new model_Calendar();
        if (is_null($user)) return;

        $calendar = $calendar_model->findByIndex($user['calendar']['_id'], TRUE);
        core_Model::getChildren($calendar);
        foreach ($calendar['months'] as $k=>$m){

            foreach($calendar['months'][$k] as $k1=>$d){
                core_Model::getChildren($calendar['months'][$k]);
                if (isset($calendar['months'][$k]['date_notes'])){
                    foreach ($calendar['months'][$k]['date_notes'] as $k2=>$n){
                        unset($calendar['months'][$k]['date_notes'][$k2]['_id']);
                    }
                }
                
            }
            unset($calendar['months'][$k]['_id']);
        }
        unset($calendar['_id']);
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');

        echo json_encode($calendar);
    }

    private function createDummyUser(){
        $obj = array('firstname'=>'oscar',
                    'lastname'=>'garavito',
                    'username'=>$_SESSION['session_id'],
                    'password'=>'xxxxx'
                    );

        $user = new model_User($obj);
        $user->calendar = new model_Calendar(array('year'=>'2011', 'creation_date'=>time()));

        $user->save();
    }

    private function _createImages($original_file, $calendar_id, $month, $month_id){

        $target_path = $this->config['webRoot']."/uploads/";
        $filename = 'calendar_'.$calendar_id.'/'.$month."_original.jpg";
        $filename_th = 'calendar_'.$calendar_id.'/'.$month."_th.jpg";
        $filename_pdf = 'calendar_'.$calendar_id.'/'.$month."_pdf.jpg";

        if (!file_exists(dirname($target_path.$filename))) mkdir(dirname($target_path.$filename));

        if(rename($original_file, $target_path.$filename)) {

            $this->_createthumb($target_path.$filename, $target_path.$filename_th, 400,300);
            $this->_createthumb($target_path.$filename, $target_path.$filename_pdf, 3*400, 3*300);

            $month_model = new model_Month();
            $month_i= model_Month::findByIndex($month_id);
            $month_i->url = '/uploads/calendar_'.$calendar_id.'/'.$month;
            $month_i->save();
            echo $month_i->url."_th.jpg";
        } else{
            echo 0;
        }
    }


    /*
        Function _createthumb($name,$filename,$new_w,$new_h)
        creates a resized image
        variables:
        $name		Original filename
        $filename	Filename of the resized image
        $new_w		width of resized image
        $new_h		height of resized image
    */
    private function _createthumb($name,$filename,$new_w,$new_h)
    {
        $system=explode(".",$name);
        if (preg_match("/jpg|jpeg/",$system[1])){$src_img=imagecreatefromjpeg($name);}
        if (preg_match("/png/",$system[1])){$src_img=imagecreatefrompng($name);}

        $old_x=imageSX($src_img);
        $old_y=imageSY($src_img);

        $thumb_w = $new_w;
        $thumb_h = $new_h;

        $dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
        imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
        if (preg_match("/png/",$system[1]))
        {
            imagepng($dst_img,$filename);
        } else {
            imagejpeg($dst_img,$filename);
        }
        imagedestroy($dst_img);
        imagedestroy($src_img);
    }



}
?>
