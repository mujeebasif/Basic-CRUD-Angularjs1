<?php
/**
 * Created by PhpStorm.
 * User: Scrun3r
 * Date: 01-Aug-15
 * Time: 3:47 PM
 */

class Events_model extends CI_Model {

   public $error;
   public $error_developer;

   public function __construct()
   {
      $this->load->database();
   }

   public function get_events($slug = FALSE)
   {
      $query = "
            SELECT
               `events`.event_id,
               `events`.`name`,
               `events`.address,
               `events`.date_from,
               `events`.date_to,
               `events`.gps_coordinates,
               `events`.hall_id,
               hall.`name` AS hall_name
            FROM
               `events`
            INNER JOIN hall ON `events`.hall_id = hall.hall_id
            AND hall.deleted = 0
            WHERE
               `events`.deleted = 0
         ";

      //$this->db->select('*');
      //$this->db->from('blogs');
      //$this->db->join('comments', 'comments.id = blogs.id','left');
      //$this->db->where('name', $name); // Produces: WHERE name = 'Joe'
      //$this->db->or_where('id >', $id);
      //$query = $this->db->get();
      //http://localhost/crossover_expositions/user_guide/database/query_builder.html

      $executed = $this->db->query($query);

      if( empty($executed) )
      {
         $this->error = "Error in fetching events details";
         $this->error_developer = $this->db->error();
         return false;
      }

      return $executed->result_array();

      /*if ($slug === FALSE)
      {
          $query = $this->db->get('events');
          return (!empty($query))?$query->result_array():array();
      }

      //$column=(is_numeric($slug))?'id':'slug';

      $query = $this->db->get_where('events', array('id' => $slug));

      return (!empty($query))?$query->row_array():array();*/

   }

   public function loadStands($hall_id='',$event_id='')
   {

      //extra check
      if( empty($hall_id) || empty($event_id) )return false;

      $query = "
         SELECT
            stands.stand_id,
            stands.`name`,
            stands.dimensions,
            stands.price,
            stands.hall_id,
            stands.stand_image,
            stands_reservations.company_id,
            stands_reservations.event_id,
            stands_reservations.date_from,
            stands_reservations.date_to,
            company.company_name,
            company.logo,
            company.email,
            company.phone
         FROM
            stands
         LEFT JOIN stands_reservations ON stands.stand_id = stands_reservations.stand_id
         AND stands_reservations.event_id = ".(int)$event_id."
         LEFT JOIN company ON stands_reservations.company_id = company.company_id
         WHERE
            stands.deleted = 0
         AND stands.hall_id = ".(int)$hall_id."
      ";

      $executed = $this->db->query($query);

      if( empty($executed) )
      {
         $this->error = "Error in fetching stand details";
         $this->error_developer = $this->db->error();
         return false;
      }

      return $executed->result_array();
   }

   public function loadFiles($company_id='')
   {

      //extra check
      if( empty($company_id) )return false;

      $query = "
         SELECT
            *
         FROM
            `files`
         WHERE
            files.company_id IN (".implode(',',$company_id).")
      ";

      $executed = $this->db->query($query);

      if( empty($executed) )
      {
         $this->error = "Error in fetching company files details";
         $this->error_developer = $this->db->error();
         return false;
      }

      $result = $executed->result_array();

      //--- group by companyId

      $files = array();

      if(!empty($result))
      {
         foreach($result as $file)
         {
            $company_id = $file['company_id'];
            if(!isset($files[$company_id])) $files[$company_id] = array();
            $files[$company_id][] = $file;
         }
      }

      return $files;
   }

   public function saveCompany($fields)
   {
      if( empty($fields) )
      {
         $this->error = "Required data missing!";
         return false;
      }

      /*$fields = array(
         'company_id' => (int)$data['company_id'],
         'company_name'=> $data['company_name'],
         'email'=> $data['email'],
         'phone'=> $data['phone'],
         'admin_email'=> $data['admin_email']
      );*/

      //All values are escaped automatically producing safer queries.

      if( !empty($fields['company_id']) )
      {
         $this->db->where('company_id', $fields['company_id']);
         $result = $this->db->update('company', $fields);
      }
      else
      {
         $result = $this->db->insert('company', $fields);
         if($result) $result = $this->db->insert_id();
      }

      if( empty($result) )
      {
         $this->error = "Error in saving company details!";
         $this->error_developer = $this->db->error();
         return false;
      }

      return $result;
   }

   public function saveReservation($data)
   {
      if( empty($data) )
      {
         $this->error = "Required data missing!";
         return false;
      }

      $fields = array(
         'stand_id' => (int)$data['stand_id'],
         'company_id' => (int)$data['company_id'],
         'event_id'=> (int)$data['event_id'],
         'date_from'=> date('Y-m-d H:i:s',strtotime($data['date_from'])),
         'date_to'=> date('Y-m-d H:i:s',strtotime($data['date_to']))
      );

      //All values are escaped automatically producing safer queries.

      $result = $this->db->insert('stands_reservations', $fields);
      if($result) $result = $this->db->insert_id();


      if( empty($result) )
      {
         $this->error = "Error in saving stand reservation!";
         $this->error_developer = $this->db->error();
         return false;
      }

      return $result;
   }

   public function uploadLogo($company_id)
   {

      if(empty($company_id))
      {
         $this->error = 'copnay id missing';
         return false;
      }

      $path = "./assets/uploads/companies/company$company_id/";

      if (!is_dir($path))
      {
         $oldumask = umask(0);
         if (!mkdir($path, 0777))
         {
            $error = 'dir creation failed!';
         }
         umask($oldumask);

         if(!empty($error))
         {
            $this->error = $error;
            return false;
         }
      }


      $config['upload_path']          = $path;
      $config['allowed_types']        = 'gif|jpg|png|jpeg';
      $config['max_size']             = 500;//kb
      $config['max_width']            = 250;//px
      $config['max_height']           = 250;//px

      $this->load->library('upload', $config);

      $this->upload->initialize($config);

      if ( ! $this->upload->do_upload('logo'))
      {
         $this->error = $this->upload->display_errors('','');
         return false;
      }
      else
      {
         //$data = array('upload_data' => $this->upload->data());
         $data = $this->upload->data();
         //var_dump($data);
         return $data['file_name'];
      }
   }

   public function uploadDocs($company_id,$index)
   {

      if(empty($company_id))
      {
         $this->error = 'copnay id missing';
         return false;
      }

      $path = "./assets/uploads/companies/company$company_id/docs/";

      if (!is_dir($path))
      {
         $oldumask = umask(0);
         if (!mkdir($path, 0777))
         {
            $error = 'dir creation failed!';
         }
         umask($oldumask);

         if(!empty($error))
         {
            $this->error = $error;
            return false;
         }
      }


      $config['upload_path']          = $path;
      $config['allowed_types']        = 'gif|jpg|png|jpeg|txt';
      $config['max_size']             = 3072;//kb
      $config['max_width']            = 1024;//px
      $config['max_height']           = 1024;//px

      $this->load->library('upload', $config);

      $this->upload->initialize($config);

      if ( ! $this->upload->do_upload($index))
      {
         $this->error = $this->upload->display_errors('','');
         return false;
      }
      else
      {
         //$data = array('upload_data' => $this->upload->data());
         $data = $this->upload->data();
         //var_dump($data);
         return $data['file_name'];
      }
   }

   public function saveDocs($company_id,$files)
   {
      if( empty($files) || empty($company_id) )
      {
         $this->error = "Required data missing to save documents!";
         return false;
      }

      $fields = array();

      foreach($files as $file)
      {
         $fields[] = array(
            "company_id" => $company_id,
            "file_name" => $file,
         );
      }

      //All values are escaped automatically producing safer queries.

      $result = $this->db->insert_batch('files', $fields);
      //if($result) $result = $this->db->insert_id();

      if( empty($result) )
      {
         $this->error = "Error in saving documents!";
         $this->error_developer = $this->db->error();
         return false;
      }

      return $result;
   }

   public function registerUserVisit($email,$password,$event_id,$stand_id)
   {
      //extra check
      if(empty($email)|| empty($password) || empty($event_id) || empty($stand_id))return false;

      //---------------------- load user

      $this->db->select('*');
      $this->db->from('users');
      $this->db->where('email', $email); // Produces: WHERE name = 'Joe'
      $this->db->where('password', $password);
      $executed = $this->db->get();

      if( empty($executed) )
      {
         $this->error = "Error in fetching user details";
         $this->error_developer = $this->db->error();
         return false;
      }

      $row = $executed->row_array();

      if(empty($row))
      {
         $this->error = "Invalid email/password combination!";
         return false;
      }

      //---------------------- register user visit

      //var_dump($row);

      $fields = array(
         'user_id' => $row['user_id'],
         'stand_id' => $stand_id,
         'event_id' => $event_id,
         'datetime' => date('Y-m-d H:i:s')
      );

      $result = $this->db->insert('user_visits', $fields);

      if( empty($result) )
      {
         $this->error = "Error in registering user visit count!";
         $this->error_developer = $this->db->error();
         return false;
      }

      //var_dump($result);

      return $result;

   }

   public function loadEventReport()
   {
      //load all event which is going to end today

      $query = "
         SELECT
            users.`name` AS user_name,
            user_visits.datetime AS visit_datetime,
            `events`.`name` AS event_name,
            `events`.date_from,
            `events`.date_to,
            `events`.address,
            company.admin_email,
            `events`.event_id
         FROM
            `events`
         INNER JOIN user_visits ON `events`.event_id = user_visits.event_id
         INNER JOIN users ON user_visits.user_id = users.user_id
         INNER JOIN company ON user_visits.company_id = company.company_id
         WHERE 1
      ";

      //test case
      /*$query .= "
         AND DATE(`events`.date_to) = DATE('2017-03-05')
	      OR DATE(`events`.date_to) = DATE('2017-03-12')
      ";*/

      $query .= "
         AND DATE(`events`.date_to) = '".date('Y-m-d')."'
      ";

      $executed = $this->db->query($query);

      if( empty($executed) )
      {
         $this->error = "Error in fetching event report";
         $this->error_developer = $this->db->error();
         return false;
      }

      $report = $executed->result_array();

      $events = array();

      //group by event
      if(!empty($report))
      {
         foreach($report as $repo)
         {
            $event_id = $repo['event_id'];
            if(!isset($events[$event_id])) $events[$event_id] = array();

            $events[$event_id][] = $repo;
         }
      }

      //var_dump($events);

      return $events;

   }
}