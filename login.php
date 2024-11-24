<?php
	ob_start();
	session_start();
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
		<h1>Влизане</h1>
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
				<button type="SUBMIT" name="submit" value="Login" id="login-button" required/>Login</button><br/><br/>
				<a style="margin-left: -9em; color: blue" href="forgotten_password.php">Forgotten Password?</a>
			</table>
		</form>

<?php
if(isset($_POST["submit"])){

    $u = $_POST["u"];
    $p = $_POST["p"];

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

	class Login extends Dbh{
	    protected function getUser($u, $p){
	        $stmt = $this->connect()->prepare('SELECT usersPassword FROM accounts WHERE usersName = ? OR usersEmail = ?;');
	        if(!$stmt->execute(array($u, $p))){
	            $stmt = null;
	            header('Location: login.php?info=STMT failed!');
	            exit();
	        }
	        if($stmt->rowCount() == 0){
	            $stmt = null;
	            header('Location: login.php?info=User not found!');
	            exit();
	        }
	        $pwdHashed = $stmt->fetchAll(PDO::FETCH_ASSOC);
	        $checkPwd = password_verify($p, $pwdHashed[0]["usersPassword"]);
	        if($checkPwd == false){
	            $stmt = null;
	            header('Location: login.php?info=Wrong password!');
	            exit();
	        }
	        elseif($checkPwd == true){
	            $stmt = $this->connect()->prepare('SELECT * FROM accounts WHERE usersName = ? OR usersEmail = ? AND usersPassword = ?;');  
	            if(!$stmt->execute(array($u, $u, $p))){
	                $stmt = null;
	                header('Location: login.php?info=STMT failed!');
	                exit();
	            }
	            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
	            $verification = $user[0]['usersVerified'];
	            $email = $user[0]['usersEmail'];
				if($verification == 0){
		            $stmt = null;
		            header('Location: login.php?info=The email address is not verified! <br/>Please confirm your email address!');
		            exit();
		        }
	            $_SESSION["usersId"] = $user[0]["usersId"];
	            $_SESSION["usersName"] = $user[0]["usersName"];
	            header('Location: index.php');
	            $stmt = null;
	        }
	    }
    }
    
    class loginContr extends Login{
	    private $uid;
	    private $pwd;
	    public function __construct($uid, $pwd){
	        $this->uid = $uid;
	        $this->pwd = $pwd;
	    }
	    public function loginUser(){
	        if($this->emptyInput() == false){
	            header('Location: login.php?info=Empty input!');
	            exit();
	        }
	        $this->getUser($this->uid, $this->pwd);
	    }
	    private function emptyInput(){
	        $result;
	        if(empty($this->uid) || empty($this->pwd)){
	            $result = false;
	        }
	        else{
	            $result = true;
	        }
	        return $result;
	    }
	}

    $login = new LoginContr($u, $p);
    $login->loginUser();

    ob_end_flush();
}
?>
		</div>
	</div>
</body>
</html>