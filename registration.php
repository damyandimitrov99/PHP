<?php
	ob_start();
	include_once 'includes/header_signup_signin.php';
	include_once 'includes/body_etc.php';
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
		<h1>Регистрация</h1>
		<form method="POST" class="form">
			<table border="0" align="center" cellpadding="5">
				<?php 
					if(isset($_GET['info'])){ 
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
				<input type="TEXT" name="u" placeholder="Username" required/>
				<input type="PASSWORD" name="p" placeholder="Password" required/>
				<input type="PASSWORD" name="p2" placeholder="Repeat Password" required/>
				<input type="EMAIL" name="e" placeholder="Email Address" required/>
				<button type="SUBMIT" name="submit" value="Register" id="login-button" required/>Register</button>
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

	$u = $_POST['u'];
	$p = $_POST['p'];
	$p2 = $_POST['p2'];
	$e = $_POST['e'];
	$vkey = password_hash(time().$u,PASSWORD_DEFAULT);
	$verify = 0;

    class Dbh{
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
	    protected function setUser($u, $p, $e, $vkey, $verify){
	        $stmt = $this->connect()->prepare('INSERT INTO accounts (usersName, usersPassword, usersEmail, usersVkey, usersVerified) VALUES (?, ?, ?, ?, ?);');
	        $hashedPwd = password_hash($p, PASSWORD_DEFAULT);
	        if($stmt){
				$base_url = "http://localhost//";
				$mail = new PHPMailer();
				$mail->isSMTP();
				$mail->Host = "smtp.gmail.com";
				$mail->SMTPAuth = "true";
				//Set type of encryption (ssl/tls)
				$mail->SMTPSecure = "tls";
				$mail->Port = "587";
				$mail->Username = "dd943512@gmail.com";
				$mail->Password = "hjkjjkleihjgoynk";
				$mail->Subject = "Email Verification";
				$mail->setFrom("dd943512@gmail.com");
				$mail->isHTML(true);
				//$mail->addAttachment('img/attackment.png');
				$mail->Body = "<p>Confirm your registration: ".$base_url."email_verification.php?verification_key=".$vkey."</p>";
				$mail->addAddress($e);
				if($mail->Send()){
					header('Location: registration.php?info=You have received a verification email.<br/>Please confirm your registration!');
				}
				else{
					header('Location: registration.php?info=MailSenter error..!');
				}
				$mail->smtpClose();
			}
			else{
				echo $this->connect()->error;
			}
	        if(!$stmt->execute(array($u, $hashedPwd, $e, $vkey, $verify))){
	            $stmt = null;
	            header('Location: registration.php?info=STMT error..!');
	            exit();
	        }
	        $stmt = null;
	    }
	    protected function checkUser($u, $e){
	        $stmt = $this->connect()->prepare('SELECT usersName FROM accounts WHERE usersName = ? OR usersEmail = ?;');
	        if(!$stmt->execute(array($u, $e))){
	            $stmt = null;
	            header('Location: registration.php?info=STMT failed!');
	            exit();
	        }
	        $resultCheck;
	        if($stmt->rowCount() > 0){
	            $resultCheck = false;
	        }
	        else{
	            $resultCheck = true;
	        }
	        return $resultCheck;
	    }
	}

	class SignupContr extends Signup{
	    private $uid;
	    private $pwd;
	    private $pwdRepeat;
	    private $email;
	    private $vkey;
	    private $verify;
	    public function __construct($uid, $pwd, $pwdRepeat, $email, $vkey, $verify){
	        $this->uid = $uid;
	        $this->pwd = $pwd;
	        $this->pwdRepeat = $pwdRepeat;
	        $this->email = $email;
	        $this->vkey = $vkey;
	        $this->verify = $verify;
	    }
	    public function signupUser(){
	    	if($this->uidTakenCheck() == false){
				header('Location: registration.php?info=Username or Email is taken!');
	            exit();
	        }
	        if($this->invalidUid() == false){
				header('Location: registration.php?info=Invalid Username! <br/><br/>Requirements: <br/>length: 6-25; <br/>at least one: A-Z; a-z; 0-9.');
	            exit();
	        }
	        if($this->invalidPwd() == false){
				header('Location: registration.php?info=Invalid password! <br/><br/>Requirements: <br/>length: 8-25; <br/>at least one: A-Z; a-z; 0-9; !, @, $, %.');
	            exit();
	        }
	        if($this->pwdMatch() == false){
				header("Location: registration.php?info=Passwords don't match!");
	        	exit();
	        }
	        if($this->invalidEmail() == false){
				header('Location: registration.php?info=Invalid Email!');
	            exit();
	        }
	        if($this->emptyInput() == false){
				header('Location: registration.php?info=There are empty fields!');
	            exit();
	        }
	        $this->setUser($this->uid, $this->pwd, $this->email, $this->vkey, $this->verify);
	    }
	    private function emptyInput(){
	        $result;
	        if(empty($this->uid) || empty($this->pwd) || empty($this->pwdRepeat) || empty($this->email)){
	            $result = false;
	        }
	        else{
	            $result = true;
	        }
	        return $result;
	    }
	    private function invalidUid(){
	        $result;
	        if(!preg_match("/^[0-9A-Za-z]{6,25}$/", $this->uid)){
	            $result = false;
	        }
	        else{
	            $result = true;
	        }
	        return $result;
	    }
	    private function invalidEmail(){
	        $result;
	        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
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
	    private function uidTakenCheck(){
	        $result;
	        if(!$this->checkUser($this->uid, $this->email)){
	            $result = false;
	        }
	        else{
	        	$result = true;
	        }
	        return $result;
	    }
	}
	
    $signup = new SignupContr($u, $p, $p2, $e, $vkey, $verify);
    $signup->signupUser();

    ob_end_flush();
}
?>
		</div>
	</div>
</body>
</html>