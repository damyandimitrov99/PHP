<?php
	ob_start();
	session_start();
	include_once 'includes/header_signup_signin.php';
	include_once 'includes/body_etc.php';

	if(isset($_SESSION["usersId"])){
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
				<input type="EMAIL" name="e" placeholder="Email Address" required/>
				<button type="SUBMIT" name="submit" value="Login" id="login-button" required/>Send Password Code</button>
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

	$e = $_POST['e'];
	$code = rand(999999, 111111);

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

	class sentUsersCodePwChange extends Dbh{
		protected function setUsersCodePwChange($code, $e){
			$stmt = $this->connect()->prepare('UPDATE accounts SET usersCodePwChange = ? WHERE usersEmail = ?;');
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
				$mail->Subject = "Password Reset Code";
				$mail->setFrom("dd943512@gmail.com");
				$mail->isHTML(true);
				//$mail->addAttachment('img/attackment.png');
				$mail->Body = "<p>Your password reset code is ".$code.".</p>";
				$mail->addAddress($e);
				if($mail->Send()){
					$info = "You have received a password reset code. <br/>Please check your Email Address!";
					$_SESSION['info'] = $info;
					$_SESSION['e'] = $e;
					header('Location: reset_code.php');
				}
				else{
					header('Location: forgotten_password.php?info=MailSenter error..!');
				}
				$mail->smtpClose();
			}
			else{
				echo $this->connect()->error;
			}
	        if(!$stmt->execute(array($code, $e))){
	            $stmt = null;
	            header('Location: forgotten_password.php?info=STMT error..!');
	            exit();
	        }
	        $stmt = null;
		}
		protected function checkUser($e, $u){
			$stmt = $this->connect()->prepare('SELECT * FROM accounts WHERE usersEmail= ? OR usersName= ?;');
			if(!$stmt->execute(array($e, $e))){
	            $stmt = null;
	            header('Location: forgotten_password.php?info=STMT failed!');
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

	class SignupContr extends sentUsersCodePwChange{
	    private $e;
	    private $code;
	    public function __construct($e, $code){
	        $this->e = $e;
	        $this->code = $code;
	    }
	    public function signupUser(){
	    	if($this->uidTakenCheck() == false){
	    		header('Location: forgotten_password.php?info=The Email Addres is not found!');
	            exit();
	        }
	        if($this->invalidEmail() == false){
	        	header('Location: forgotten_password.php?info=Invalid Email!');
	            exit();
	        }
	        if($this->emptyInput() == false){
	        	header('Location: forgotten_password.php?info=There are empty fields!');
	            exit();
	        } 
	        $this->setUsersCodePwChange($this->code, $this->e);
	    }
	    private function emptyInput(){
	        $result;
	        if(empty($this->e)){
	            $result = false;
	        }
	        else{
	            $result = true;
	        }
	        return $result;
	    }
	    private function invalidEmail(){
	        $result;
	        if(!filter_var($this->e, FILTER_VALIDATE_EMAIL)){
	            $result = false;
	        }
	        else{
	            $result = true;
	        }
	        return $result;
	    }
	    private function uidTakenCheck(){
	        $result;
	        if($this->checkUser($this->e, $this->e)){
	            $result = false;
	        }
	        else{
	        	$result = true;
	        }
	        return $result;
	    }
	}

	$signup = new SignupContr($e, $code);
	$signup->signupUser();

	ob_end_flush();
}	
?>

		</div>
	</div>
</body>
</html>