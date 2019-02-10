<?php
    include_once 'Session.php';
    include 'Database.php';
    
class User{
    private $db;
    public function __construct(){
        $this->db =new Database();
    }
    
    public function userRegistration($data){
        $name =$data['name'];
        $username =$data['username'];
        $address =$data['address'];
        $email =$data['email'];
        $password =$data['password'];
        
        $chk_email=$this->emailCheck($email);
        
        
        if($name=="" OR $username=="" OR $address=="" OR $email=="" OR $password==""){
            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Field must not be empty..</div>";
            return $msg;
        }
        
        if(strlen($username)<3){
            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Username is too Short</div>";
            return $msg;
            }   
        elseif(preg_match('/[^a-z0-9_-]+/i',$username)){
            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Username must contain a-z, _ and - </div>";
            return $msg;
            }
         
        
        if(filter_var($email,FILTER_VALIDATE_EMAIL)===false){
            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Invalid Email Address</div>";
            return $msg;
        }
        
        if($chk_email ==true){
            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Email Already Exists</div>";
            return $msg;
        }
        
        $password =md5($data['password']); //this method use later.

//------- Data Insert ------------

        $sql = "INSERT INTO `tbl_user` (name,username,address,email,password) VALUES (:name,:username,:address,:email,:password)";               
        $query=$this->db->pdo->prepare($sql);
        $query->bindValue(':name',$name);
        $query->bindValue(':username',$username);
        $query->bindValue(':address',$address);
        $query->bindValue(':email',$email);
        $query->bindValue(':password',$password);
        $result =$query->execute();
        if($result){
            $msg = "<div class='alert alert-success'><strong>Success !!</strong> Thank you have been Registered</div>";
            return $msg;
        }else{
            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Sorry, there has been problem inserting your details.</div>";
            return $msg;        
        }
        
// --------------******************------------      
        
    }
    
    
    
//------------ Email Check --------------------------    
    public function emailCheck($email){
        $sql= "SELECT email FROM tbl_user where email = :email";
        $query=$this->db->pdo->prepare($sql);
        $query->bindValue(':email',$email);
        $query->execute();
        if($query->rowCount() >0){
            return true;
        }
        else{
            return false;
        }
    }

// --------------******************------------      

    
    
//---------- Login Part -------------------  
    
    public function getLoginUser($email,$password){
        $sql= "SELECT * FROM tbl_user where email = :email AND password=:password  LIMIT 1";
        $query=$this->db->pdo->prepare($sql);
        $query->bindValue(':email',$email);
        $query->bindValue(':password',$password);
        $query->execute();
        $result=$query->fetch(PDO::FETCH_OBJ);
        return $result;
        
    }
// --------------******************------------      
    
    
    
//-------------- userLogin -------------------  

    public function userLogin($data){
        $email =$data['email'];
        
        $password =md5($data['password']);
        $chk_email=$this->emailCheck($email);
        
        
        if($email=="" OR $password==""){
            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Field must not be empty..</div>";
            return $msg;
        }
        
        if(filter_var($email,FILTER_VALIDATE_EMAIL)===false){
            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Invalid Email Address</div>";
            return $msg;
        }
            
        if($chk_email ==false){
            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Email address not Exists</div>";
            return $msg;
        }
        
        $result =$this->getLoginUser($email,$password);
        if($result){
            Session::init();
            Session::set("login",true);
            Session::set("id",$result->id);
            Session::set("name",$result->name);
            Session::set("username",$result->username);
            Session::set("loginmsg","<div class='alert alert-success'><strong>Success !!</strong> You are LoggedIn</div>");
            header("Location:index.php");
        }
        else{
            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Data Not Found</div>";
            return $msg;
        }
        

 
    }
// --------------******************------------      
    
    
//-------------- Show all data in index from database  -------------------  
    public function getUserData(){
        $sql= "SELECT * FROM tbl_user ORDER BY id DESC";
        $query=$this->db->pdo->prepare($sql);
        $query->execute();
        $result=$query->fetchAll();
        return $result;

    }
    
// --------------******************------------      

    
//-------------- for showing profile-------------------  
    
    public function getUserById($userid){
        $sql= "SELECT * FROM tbl_user WHERE id= :id limit 1";
        $query=$this->db->pdo->prepare($sql);
        $query->bindValue(':id',$userid);
        $query->execute();
        $result=$query->fetch(PDO::FETCH_OBJ);
        return $result;

    }
// --------------******************------------      
    
    
    
    
    
    
    
//-------------- for own update profile-------------------  

    public function updateUserData($id,$data){
        $name =$data['name'];
        $username =$data['username'];
//        $address =$data['address'];
        $email =$data['email'];        
        
//        OR $address==""
        if($name=="" OR $username==""  OR $email==""){
            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Field must not be empty..</div>";
            return $msg;
        }
        
//        if(strlen($username)<3){
//            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Username is too Short</div>";
//            return $msg;
//            }   
//        elseif(preg_match('/[^a-z0-9_-]+/i',$username)){
//            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Username must contain a-z, _ and - </div>";
//            return $msg;
//            }
//         
//        
//        if(filter_var($email,FILTER_VALIDATE_EMAIL)===false){
//            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Invalid Email Address</div>";
//            return $msg;
//        }
//        
//        if($chk_email ==true){
//            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Email Already Exists</div>";
//            return $msg;
//        }
        
//------- Data Update in Profile------------

        $sql = "UPDATE tbl_user set
                    name    = :name,
                    username=:username,
                    email   =:email
                    WHERE id=:id";
                   
        $query=$this->db->pdo->prepare($sql);
        $query->bindValue(':name',$name);
        $query->bindValue(':username',$username);
//        $query->bindValue(':address',$address);
        $query->bindValue(':email',$email);
        $query->bindValue(':id',$id);
        $result =$query->execute();
        if($result){
            $msg = "<div class='alert alert-success'><strong>Success !!</strong> Userdata Updated Succesfully</div>";
            return $msg;
        }else{
            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Userdata not Updated</div>";
            return $msg;        
        }
        
    }
    
//    --------------******************------------

    
    
    
//------- Check Password Change ------------

    private function checkPassword($id,$old_pass){
        $password=md5($old_pass);
        $sql= "SELECT password FROM tbl_user where id=:id AND password= :password ";
        $query=$this->db->pdo->prepare($sql);
        $query->bindValue(':id',$id);
        $query->bindValue(':password',$password);
        $query->execute();
        if($query->rowCount() >0){
            return true;
        }
        else{
            return false;
        }
    }
//    --------------******************------------
    

    
//------- Update Password Change ------------

    public function updatePassword($id,$data){
        $old_pass=$data['old_pass'];
        $new_pass=$data['new_pass'];
        $chk_pass= $this->checkPassword($id, $old_pass);
        
        if($old_pass=="" OR $new_pass==""){
            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Field must not be empty</div>";
            return $msg;
        }
                
        if($chk_pass==false){
            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong>Old password not Exist</div>";
            return $msg;
        }
                
        if(strlen($new_pass)<5){
           $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong>Password is too short</div>";
            return $msg; 
        }
        
        $password=md5($new_pass);
        $sql = "UPDATE tbl_user set
                    password = :password
                    WHERE id =:id";
                   
        $query=$this->db->pdo->prepare($sql);
        
        $query->bindValue(':password',$password);
        $query->bindValue(':id',$id);
        $result =$query->execute();
        if($result){
            $msg = "<div class='alert alert-success'><strong>Success !!</strong> Password Updated Succesfully</div>";
            return $msg;
        }else{
            $msg = "<div class='alert alert-danger'><strong>ERROR !!</strong> Password not Updated</div>";
            return $msg;        
        }

    }
        
//    --------------******************------------
}


?>