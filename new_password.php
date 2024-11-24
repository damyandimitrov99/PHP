<?php
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
		<h1>Добре дошли!</h1>
		<form method="POST" class="form">
			<table border="0" align="center" cellpadding="5">
				<input type="PASSWORD" name="p" placeholder="Password" required/>
				<input type="PASSWORD" name="p2" placeholder="Repeat Password" required/>
				<button type="SUBMIT" name="submit" value="Register" id="login-button" required/>Register</button>
			</table>
		</form>
			<div style="color:red; font-size:1.5em; font-weight:bold" class="font-weight-bold">
<?php
$error = NULL;

//Include required phpmailer
require 'includes/phpMailer/PHPMailer.php';
require 'includes/phpMailer/SMTP.php';
require 'includes/phpMailer/Exception.php';
//Define name space
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['submit'])){

//Get form data
	$p = $_POST['p'];
	$p2 = $_POST['p2'];
	$e = $_POST['e'];

	// <-- DataBase Connection -->
    class Dbh {
	    protected function connect() {
	        try {
	            $username = "root";
	            $password = "";
	            $dbh = new PDO('mysql:host=localhost;dbname=test', $username, $password);
	            return $dbh;
	        } 
	        catch (PDOException $e) {
	            print "Error!: " . $e->getMessage() . "<br/>";
	            die();
	        }
	    }
	}
	// <-- /DataBase Connection -->

	class Signup extends Dbh {
	    protected function setUser($u, $p, $e, $vkey, $verify) {
	        $stmt = $this->connect()->prepare('UPDATE accounts SET usersCodePwChange = NULL, usersPassword = '$encpass' WHERE email = '$email';');
	        $hashedPwd = password_hash($p, PASSWORD_DEFAULT);
	        // <-- PHP Mailer -->
	        if($stmt){
				$base_url = "http://localhost//";
				//Send Email
				$mail = new PHPMailer();
				//Set mailer to use smtp
				$mail->isSMTP();
				//Define smtp host
				$mail->Host = "smtp.gmail.com";
				//Enable smtp authentication
				$mail->SMTPAuth = "true";
				//Set type of encryption (ssl/tls)
				$mail->SMTPSecure = "tls";
				//Set port to connect smtp
				$mail->Port = "587";
				//Set gmail username
				$mail->Username = "dd943512@gmail.com";
				//Set gmail password
				$mail->Password = "hjkjjkleihjgoynk";
				//Set email subject
				$mail->Subject = "Password Updated";
				//Set sender email
				$mail->setFrom("dd943512@gmail.com");
				//Enable HTML
				$mail->isHTML(true);
				//Attackment
				//$mail->addAttachment('img/attackment.png');
				//Email body
				$mail->Body = "<p>Your password is updated!</p>";
				//Add recipient
				$mail->addAddress($e);
				if($mail->Send()){
				}else{
					echo "MailSenter error..!";
				}
				$mail->smtpClose();
			}
			else{
				echo $this->connect()->error;
			}
			// <-- /PHP Mailer -->
	        if(!$stmt->execute(array($u, $hashedPwd, $e, $vkey, $verify))) {
	            $stmt = null;
	            echo "STMT error..!";
	            exit();
	        }
	        $stmt = null;
	    }
	    protected function checkUser($u, $e) {
	        $stmt = $this->connect()->prepare('SELECT usersName FROM accounts WHERE usersName = ? OR usersEmail = ?;');
	        if(!$stmt->execute(array($u, $e))){
	            $stmt = null;
	            echo "STMT failed!";
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

	// <--ERRORS-->
	class SignupContr extends Signup{

	    private $pwd;
	    private $pwdRepeat;
	    private $email;
	    
	    public function __construct($pwd, $pwdRepeat, $email){
	        $this->pwd = $pwd;
	        $this->pwdRepeat = $pwdRepeat;
	        $this->email = $email;
	    }

	    public function signupUser() {
	        if($this->invalidPwd() == false){
	            echo "Invalid password!</br>Length: 8-25</br>Requirements:</br>At least one: A-Z</br>At least one: a-z</br>At least one: 0-9</br>At least one: ! @ # $ %</br>";
	            exit();
	        }
	        if($this->pwdMatch() == false){
	            echo "Passwords don't match!</br>";
	            exit();
	        }
	        if($this->emptyInput() == false){
	            echo 'There are empty fields!</br>';
	            exit();
	        } 
	        $this->setUser($this->uid, $this->pwd, $this->email);
	    }
	    // <--/ERRORS-->

	    // <--FUNCTIONS ERRORS-->
	    private function emptyInput() {
	        $result;
	        if(empty($this->uid) || empty($this->pwd) || empty($this->pwdRepeat) || empty($this->email)){
	            $result = false;
	        }
	        else{
	            $result = true;
	        }
	        return $result;
	    }
	    private function invalidPwd() {
	        $result;
	        if (!preg_match("/^(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%])[0-9A-Za-z!@#$%]{8,25}$/", $this->pwd)){
	            $result = false;
	        }
	        else{
	        	$result = true;
	        }
	        return $result;
	    }
	    private function pwdMatch() {
	        $result;
	        if ($this->pwd !== $this->pwdRepeat){
	            $result = false;
	        }
	        else{
	        	$result = true;
	        }
	        return $result;
	    }
	    // <--/FUNCTIONS ERRORS-->

	}
	
    $signup = new SignupContr($u, $p, $p2, $e);

    // Running error handlers and user signup
    $signup->signupUser();	
}
?>
			</div>
		</div>
	</div>
</body>
</html>