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
				<input type="TEXT" name="prc" placeholder="Password Reset Code" required/>
				<button type="SUBMIT" name="submit" value="Confirm" id="login-button" required/>Confirm</button>
			</table>
		</form>

<?php
if(isset($_POST["submit"])){

    $prc = $_POST["prc"];

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
	    protected function getUser($e, $prc){
	        $stmt = $this->connect()->prepare('SELECT * FROM accounts WHERE usersEmail = ? AND usersCodePwChange = ?;');
	        if(!$stmt->execute(array($e, $prc))){
	            $stmt = null;
	            header('Location: reset_code.php?info=STMT failed!');
	            exit();
	        }
	        if($stmt->rowCount() == 0){
	            $stmt = null;
	            header('Location: reset_code.php?info=Invalid Password Reset Code!');
	            exit();
	        }
	        elseif($stmt->rowCount() != 0){
	        	$info = "Please create a new password that you don't use on any other website.";
            	$_SESSION['info'] = $info;
            	header('Location: change_password.php');
	        }
	        $stmt = null;
	    }
	}
    
    class loginContr extends Login{
	    private $e;
	    private $prc;
	    public function __construct($e, $prc){
	        $this->e = $e;
	        $this->prc = $prc;
	    }
	    public function loginUser(){
	        if($this->emptyInput() == false){
	        	header('Location: reset_code.php?info=Empty input!');
	            exit();
	        }
	        $this->getUser($this->e, $this->prc);
	    }
	    private function emptyInput(){
	        $result;
	        if(empty($this->prc)){
	            $result = false;
	        }
	        else{
	            $result = true;
	        }
	        return $result;
	    }
	}

    $login = new LoginContr($e, $prc);
    $login->loginUser();

    ob_end_flush();
}
?>

		</div>
	</div>
</body>
</html>