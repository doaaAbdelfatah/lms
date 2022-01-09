<?php

    class User{

        protected $id ;
        public $name ;
        public $email ;
        protected  $password ;
        public $role;
        public $created_at;
        public $created_by;

        function __construct($name ,$email)
        {
            $this->name = $name;
            $this->email =$email;
        }

        static function login($email ,$password){
            $user =null;
            $qry = "select * from users where email='$email' and password='$password'" ;
            require_once("config.php");

            $cn =mysqli_connect(DB_HOST,DB_USER_NAME,DB_USER_PW,DB_NAME);
            $rslt = mysqli_query($cn  ,$qry) ;
            // var_dump( $rslt );
            if ($arr = mysqli_fetch_assoc($rslt)){
                switch($arr["role"]){
                    case "student":
                        $user  = new Student($arr["name"] , $arr["email"]);
                        $user->id = $arr["id"];
                        $user->created_by = $arr["created_by"];
                        $user->created_at = $arr["created_at"];
                        $user->department_id = $arr["department_id"];                        
                      
                        break;
                    case "it":
                        $user  = new IT($arr["name"] , $arr["email"]);
                        $user->id = $arr["id"];
                        $user->created_by = $arr["created_by"];
                        $user->created_at = $arr["created_at"];                      
                        break;

                    case "profosser":
                        $user  = new Profosser($arr["name"] , $arr["email"]);
                        $user->id = $arr["id"];
                        $user->created_by = $arr["created_by"];
                        $user->created_at = $arr["created_at"];
                       
                        break;
                }    
                
                
                // array(8) {
                //     ["id"]=>
                //     string(1) "1"
                //     ["name"]=>
                //     string(5) "admin"
                //     ["email"]=>
                //     string(19) "admin@coursehub.com"
                //     ["password"]=>
                //     string(32) "e10adc3949ba59abbe56e057f20f883e"
                //     ["role"]=>
                //     string(2) "it"
                //     ["created_at"]=>
                //     string(19) "2022-01-06 18:55:40"
                //     ["created_by"]=>
                //     NULL
                //     ["department_id"]=>
                //     NULL
                //   }
            }
            mysqli_close($cn);
            return $user;
        }
    }

    class Profosser extends User{

        function __construct($name ,$email)
        {        
            parent::__construct($name ,$email)    ;
            $this->role ="profosser";            
        }
    }

    class Student extends User{
        public $department_id;
        function __construct($name ,$email, $department_id =null)
        {
            parent::__construct($name ,$email)  ;
            $this->department_id =$department_id;
            $this->role ="student";            
        }
    }

    class IT extends User { //admin
        // INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, 
        // `created_at`, `created_by`, `department_id`) VALUES (NULL, 'admin', 
        // 'admin@coursehub.com', MD5('123456'), 'it', current_timestamp(), NULL, NULL);

        function __construct($name ,$email)
        {        
            parent::__construct($name ,$email)    ;
            $this->role ="it";            
        }

        public function create_user(User $user){ 

            $name  =$user->name;
            $email  =$user->email;
            $role  =$user->role;
            $pw = md5("123456");
            $did  = (empty($user->deprtment_id))? "null" : $user->deprtment_id;

            $qry ="insert into users (name ,email ,password ,role,department_id ,created_by) 
            values ('$name' ,'$email' ,'$pw', '$role' , $did ," . $this->id.")";
            require_once("config.php");
            $cn =mysqli_connect(DB_HOST,DB_USER_NAME,DB_USER_PW,DB_NAME);
            $rslt = mysqli_query($cn  ,$qry) ;
            // echo mysqli_error($cn);
            // var_dump($rslt);
            mysqli_close($cn);
            return $rslt;
        }

        public function delete_user($id){
            $qry ="delete from users where id=$id";
            require_once("config.php");
            $cn =mysqli_connect(DB_HOST,DB_USER_NAME,DB_USER_PW,DB_NAME);
            $rslt = mysqli_query($cn  ,$qry) ;
            echo mysqli_error($cn);
            // var_dump($rslt);
            mysqli_close($cn);
            return $rslt;
        }
        public  function list_all_users(){
            require_once("config.php");
            $cn =mysqli_connect(DB_HOST,DB_USER_NAME,DB_USER_PW,DB_NAME);
            $rslt = mysqli_query($cn  ,"select u.* , c.name created_by_name from users u left join users c on (u.created_by =c.id)") ;
            $data =mysqli_fetch_all($rslt ,MYSQLI_ASSOC);
            mysqli_close($cn);
            return $data;
        }

    }

