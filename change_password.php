<?php
	ob_start();
	session_start();
	include_once 'includes/header_signup_signin.php';
	include_once 'includes/body_etc.php';

	$e = $_SESSION['e'];
	if($e == false){
  		header('Location: index.php');
	}
?>

	<div class="wrapper">
		<ul class="bg-bubbles">
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
		</ul>
		<div class="container">
		<h1>Възобновяване на парола</h1>
		<form method="POST" class="form">
			<table border="0" align="center" cellpadding="5">
				<?php 
	                if(isset($_SESSION['info'])){
	            ?>
	                    <p class="error" style="text-align: left; color: red; margin-left: 12.5em">
	                    	<?php 
	                    		echo $_SESSION['info']; 
	                    	?>
	                    </p>
	                    <br/>
	            <?php
	                }   
					if (isset($_GET['info'])) { 
				?>
		     			<p class="error" style="text-align: left; color: red; margin-left: 12.5em">
		     				<?php 
		     					echo $_GET['info']; 
		     				?>
		     			</p>
		     			<br/>
	     		<?php 
	     			} 
	     		?>
				<input type="PASSWORD" name="p" placeholder="Password" required/>
				<input type="PASSWORD" name="p2" placeholder="Repeat Password" required/>
				<button type="SUBMIT" name="submit" value="Login" id="login-button" required/>Login</button><br/><br/>
			</table>
		</form>

<?php
require 'includes/phpMailer/PHPMailer.php';
require 'includes/phpMailer/SMTP.php';
require 'includes/phpMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['submit'])){

	$p = $_POST['p'];
	$p2 = $_POST['p2'];

    class Dbh {
	    protected function connect(){
	        try{
	            $username = "root";
	            $password = "";
	            $dbh = new PDO('mysql:host=localhost;dbname=test', $username, $password);
	            return $dbh;
	        } 
	        catch(PDOException $e){
	            print "Error!: " . $e->getMessage() . "<br/>";
	            die();
	        }
	    }
	}

	class Signup extends Dbh{
	    protected function setUser($p, $e){
	        $stmt = $this->connect()->prepare('UPDATE accounts SET usersCodePwChange = NULL, usersPassword = ? WHERE usersEmail = ?;');
	        $hashedPwd = password_hash($p, PASSWORD_DEFAULT);
	        if(!$stmt->execute(array($hashedPwd, $e))){
	            $stmt = null;
	            header('Location: change_password.php?info=STMT error..!');
	            exit();
	        }
	        $_SESSION['e'] = $e;
	        header('Location: password_changed.php');
	        $stmt = null;
	    }
	}

	class SignupContr extends Signup{
	    private $pwd;
	    private $pwdRepeat;
	    private $email;
	    public function __construct($pwd, $pwdRepeat, $email){
	        $this->pwd = $pwd;
	        $this->pwdRepeat = $pwdRepeat;
	        $this->email = $email;
	    }
	    public function signupUser(){
	        if($this->invalidPwd() == false){
				header('Location: change_password.php?info=Invalid password! <br/><br/>Requirements: <br/>length: 8-25; <br/>at least one: A-Z; a-z; 0-9; !, @, $, %.');
	            exit();
	        }
	        if($this->pwdMatch() == false){
				header("Location: change_password.php?info=Passwords don't match!");
	        	exit();
	        }
	        if($this->emptyInput() == false){
				header('Location: change_password.php?info=There are empty fields!');
	            exit();
	        }
	        $this->setUser($this->pwd, $this->email);
	    }
	    private function emptyInput(){
	        $result;
	        if(empty($this->pwd) || empty($this->pwdRepeat) || empty($this->email)){
	            $result = false;
	        }
	        else{
	            $result = true;
	        }
	        return $result;
	    }
	    private function invalidPwd(){
	        $result;
	        if(!preg_match("/^(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%])[0-9A-Za-z!@#$%]{8,25}$/", $this->pwd)){
	            $result = false;
	        }
	        else{
	        	$result = true;
	        }
	        return $result;
	    }
	    private function pwdMatch(){
	        $result;
	        if($this->pwd !== $this->pwdRepeat){
	            $result = false;
	        }
	        else{
	        	$result = true;
	        }
	        return $result;
	    }
	}
	
    $signup = new SignupContr($p, $p2, $e);
    $signup->signupUser();

    ob_end_flush();
}
?>

		</div>
	</div>
</body>
</html>