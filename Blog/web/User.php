<?php
require_once('class.sqlite3.inc.php');

class User
{
 
    private $email;
    private $name;
    private $password;
    
    public function __construct($email,$name,$password){
        
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        
    }
    
    #DB Query function
    private function dbQuery($query="")
    {
        
        $db = new SQLite3_module();
        $dbPath = "../../DB Browser for SQLite/myDB.db";
        $dbr = $db->db_oper($dbPath,$query);
        return $dbr; 
        
    }

    
    private function convertUserData($email,$name,$password)
    {
        
       #Name Extraction / No HTML TAGS / No Special HTML Chars
       $name = trim($_POST['name']);
       $name = strip_tags($name);
       $name = htmlspecialchars($name);
        
       #Email Extraction / No HTML TAGS / No Special HTML Chars
       $email = trim($_POST['email']);
       $email = strip_tags($email);
       $email = htmlspecialchars($email);
        
       #Password Extraction / No HTML TAGS / No Special HTML Chars    
       $pass = trim($_POST['password']);
       $pass = strip_tags($pass);
       $pass = htmlspecialchars($pass);
        
       #Confirm Password Extraction / No HTML TAGS / No Special HTML Chars
       $confirmPassword = trim($_POST['confirmPassword']);
       $confirmPassword = strip_tags($confirmPassword);
       $confirmPassword = htmlspecialchars($confirmPassword);
        
    }
    
    
    public function registerUser()
    {
        
       $this->convertUserData($this->email,$this->name,$this->password);
       
       $name = SQLite3::escapeString($this->name);
       $email = SQLite3::escapeString($this->email);
       $password = SQLite3::escapeString($this->password);
        
       #Encrypting the password using SHA256
       $password = hash('sha256',$password);
       
       #Registered on Date:    
       $timestamp = new DateTime();
       $timestamp = date_format($timestamp,'Y-m-d T H:i:s');
        
       $query = "INSERT INTO users(name,password,email,register) VALUES('$name','$password','$email','$timestamp')";
       $this->dbQuery($query);
           
    }
    
    public function isNameValid($name){
    
       $error = false;
       $nameError = '';
        
       if(empty($name)){
           
           $error = true;
           $nameError = "Please enter your full name.";
           
       }else if(strlen($name) < 4){
           
           $error = true;
           $nameError = "Name must have atleast 4 characters.";
           
       }else if(!preg_match("/^[a-zA-Z ]+$/", $name)){
           
           $error = true;
           $nameError = "Name must contain only alphabets and space.";
           
       }
        
       return array('errorMsg' => $nameError, 'hasError' => $error);
        
    }
    
    public function isPasswordValid($password){
        
       #String Validation [Password];
        
       $error = false;
       $passwordError = '';
        
       if(empty($pass)){
           
           $error = true;
           $passwordError = "Please enter password.";
           
       }else if(strlen($pass) < 6){
           
           $error = true;
           $passwordError = "Password must have atleast 6 characters.";
           
       }else if($pass != $confirmPassword){
           
           $error = true;
           $passwordError = "Passwords do not match";
           
       }
        
       return array('errorMsg' => $passwordError, 'hasError' => $error);
        
    }
    
    public function isEmailValid($email){
        
       #String Validations [Email]
       $error = false;
       $emailError = '';
       
       if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
           
           $error = true;
           $emailError = "Please enter a valid email adress.";
           
       }else{
           
           #Check if the Email already exists in the Database
           $query = "SELECT email FROM users WHERE email='$email'";
           $result = $this->dbQuery($query);
           
           if(!empty($result)){
               $error = true;
               $emailError = "The provided Email is already in use.";
           }
           
       }
        
       return array('errorMsg' => $emailError, 'hasError' => $error);
        
    }
    
    
    
}

?>