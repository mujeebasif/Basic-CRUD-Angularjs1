<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class events extends CI_Controller {

   public function __construct()
   {
      parent::__construct();
      $this->load->model('events_model');
   }

   public function index()
   {
      $this->load->view('templates/header');
      $this->load->view('events');
      $this->load->view('templates/footer');
   }

	public function events()
	{
      $events = $this->events_model->get_events();

      if(!empty($this->events_model->error))
      {
         //show_error($this->events_model->error, 500);
         header('HTTP/1.1 Internal Server Error', true, 500);
         echo $this->events_model->error;
         return;
      }

      if(empty($events)) $events = array();

      echo json_encode($events);
	}

   public function loadStands()
   {
      //sleep(1);
      $hall_id = $this->input->get('hall_id');
      $event_id = $this->input->get('event_id');

      if( empty($hall_id) || empty($event_id) )
      {
         header('HTTP/1.1 400 Bad Request',true,400);
         echo 'Required Filed missing! hall_id or event_id';
         return false;
      }

      $stands = $this->events_model->loadStands($hall_id,$event_id);

      if(!empty($this->events_model->error))
      {
         header('HTTP/1.1 Internal Server Error', true, 500);
         echo $this->events_model->error;
         return;
      }

      if(empty($stands)) $stands = array();

      //----------- load docs of subscriber companies only
      $companies = array();

      foreach($stands as $stand)
      {
         if( !empty($stand['company_id']) )
            $companies[] = $stand['company_id'];
      }

      if( !empty($companies) )
      {
         $companies = array_unique($companies);

         $files = $this->events_model->loadFiles($companies);

         if(!empty($this->events_model->error))
         {
            header('HTTP/1.1 Internal Server Error', true, 500);
            echo $this->events_model->error;
            return;
         }
      }

      if(empty($files)) $files = array();

      //var_dump($stands, $files);

      $response = array();
      $response['stands'] = (!empty($stands)) ? $stands : array();
      $response['files'] = (!empty($files)) ? $files : array();

      echo json_encode($response);
   }

   public function download()
   {
      $company_id = $this->input->get('company_id');
      $file_name = $this->input->get('file_name');

      if( empty($company_id) || empty($file_name) )
      {
         header('HTTP/1.1 400 Bad Request',true,400);
         echo 'Required Filed missing! company_id or file_name';
         return false;
      }

      $this->load->helper('download');

      $path = "./assets/uploads/companies/company" . $company_id . "/docs/" . $file_name;

      $data = file_get_contents($path);

      //var_dump($data);

      force_download($file_name, $data);
   }

   public function saveReservation()
   {
      //var_dump($_REQUEST,$_FILES);
      //$this->events_model->do_upload();
      //return;

      $msg = array();

      //----------------- validation

      $data = array();
      $modl = $this->input->post('model');

      if(!empty($modl))
      {
         $data = @json_decode($modl,true);
      }

      $errors = '';
      $required = array(
         'company_name',
         'email',
         'phone',
         'admin_email',
         'event_id',
         'date_from',
         'date_to',
         'stand_id',
      );

      foreach($required as $req)
      {
         if( empty($data[$req]) )
         {
            $errors .= $req . ', ';
         }
      }

      if(empty($_FILES['logo']))$errors.='logo, ';

      $errors = rtrim($errors, ', ');

      if( !empty($errors) )
      {
         header('HTTP/1.1 400 Bad Request',true,400);
         echo 'Required Filed(s) missing! '.$errors;
         return false;
      }

      //------- save company if its not already there in db

      $fields = array(
         'company_id' => (int)$data['company_id'],
         'company_name'=> $data['company_name'],
         'email'=> $data['email'],
         'phone'=> $data['phone'],
         'admin_email'=> $data['admin_email']
      );

      $company_id = $this->events_model->saveCompany($fields);

      if(!$company_id)
      {
         header('HTTP/1.1 Internal server error',true,500);
         echo $this->events_model->error;
         echo ' Details: '.$this->events_model->error_developer;
         return false;
      }

      $msg[] = 'Company Saved Successfully! with id:' . $company_id;

      //------- save reservation

      $data['company_id'] = $company_id;

      $reservation_id = $this->events_model->saveReservation($data);

      if(!$reservation_id)
      {
         header('HTTP/1.1 Internal server error',true,500);
         echo $this->events_model->error;
         echo ' Details: '.$this->events_model->error_developer;
         return false;
      }

      $msg[] = 'Stand reserved successfully! with reservation id:'.$reservation_id;

      //------------------- save logo

      $uploaded = $this->events_model->uploadLogo($company_id);
      //var_dump($uploaded);

      if(!empty($uploaded))
      {
         $fields = array(
            'company_id' => (int)$data['company_id'],
            'logo'=> $uploaded
         );
         $company_updated = $this->events_model->saveCompany($fields);

         /*if(!$company_id)
         {
            header('HTTP/1.1 Internal server error',true,500);
            echo $this->events_model->error;
            echo ' Details: '.$this->events_model->error_developer;
            return false;
         }*/
      }

      //------------------ upload docs (if any)

      $files_errors = array();

      if(!empty($_FILES) && count($_FILES)>1)
      {
         $uploaded_files=array();
         foreach($_FILES as $k=>$v)
         {
            if($k=='logo')continue;
            $uploaded = $this->events_model->uploadDocs($company_id,$k);
            if(!empty($uploaded))
            {
               $uploaded_files[] = $uploaded;
            }
            else
            {
               $files_errors[] = $this->events_model->error;
            }
         }

         //----------- save uploaded files in db
         if(!empty($uploaded_files))
         {
            $saved = $this->events_model->saveDocs($company_id,$uploaded_files);
         }
      }

      echo json_encode(array(
         'company_id' => $company_id,
         'reservation_id' => $reservation_id,
         'files_error' => $files_errors
      ));

   }

   public function register_visit()
   {

      //----------------- validation

      $errors = '';
      $required = array(
         'email',
         'pass',
         'stand_id',
         'event_id',
      );

      foreach($required as $req)
      {
         $v = $this->input->get_post($req);
         if( empty($v) )
         {
            $errors .= $req . ', ';
         }
      }

      $errors = rtrim($errors, ', ');

      if( !empty($errors) )
      {
         header('HTTP/1.1 400 Bad Request',true,400);
         echo 'Required Filed(s) missing! '.$errors;
         return false;
      }

      //------------------------------------------

      $email = $this->input->get_post('email');
      $password = $this->input->get_post('pass');
      $event_id = $this->input->get_post('event_id');
      $stand_id = $this->input->get_post('stand_id');

      $registered = $this->events_model->registerUserVisit($email,$password,$event_id,$stand_id);

      if(empty($registered))
      {
         header('HTTP/1.1 500 Internal sever error',true,400);
         echo $this->events_model->error;
         return false;
      }

      echo 'User visit count registered successfully!';

   }

   /*
    * load all event which is going to end today
    * & send email to company admin
    * i.e.
    * receive a report about the users who visited their stand on the event after it is over.
    * */
   public function sendEventReport()
   {
      $result = $this->events_model->loadEventReport();

      if(!empty($this->events_model->error))
      {
         header('HTTP/1.1 500 Internal sever error',true,400);
         echo $this->events_model->error;
         return false;
      }

      if(empty($result))
      {
         echo 'Nothing to report';
         return false;
      }

      //----------------------

      $this->load->library('email');

      foreach($result as $event)
      {
         $to = $event[0]['admin_email'];
         $mail_subject = "Event reoprt";

         $mail = "
         Hi there,
         Event : ".$event[0]['event_name']." <br/>
         From : ".$event[0]['date_from']." <br/>
         To : ".$event[0]['date_to']." <br/>
         Address : ".$event[0]['address']." <br/>
         User Details: <br/>
         ";

         foreach($event as $user)
         {
            $mail .= "user:".$user['user_name'];
            $mail .= " visit date:".$user['visit_datetime'];
            $mail .= " <br/>";
         }

         $mail .= "Thanks";

         $this->email->from('info@exposition.com', 'Exposition');
         //$this->email->to($to);
         $this->email->to('mmamjb1@gmail.com');
         $this->email->subject($mail_subject);
         $this->email->message($mail);
         $this->email->send();

         var_dump($mail);
      }

   }
}
