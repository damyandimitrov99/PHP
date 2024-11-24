<?php
	ob_start();
	session_start();
  include_once 'includes/header.php';
  include_once 'includes/body_services.php';

  $e = $_SESSION['e'];
	if($e == false){
  		header('Location: index.php');
	}
	session_unset();
	session_destroy();
?>

<!-- About -->
<div id="about" class="about">
	<div class="container">
		<div class="w3-about-head">
			<h3>Поздравления!</h3>
			</br>
		</div>
		<div class="w3-agileitsline"  id="a1">
			<h3 style="text-align:center">
				Вашата парола е възобновена успешно! <br/> Можете да влезете във Вашият профил от тук - <a href="login.php">Login</a>
			</h3>
			</br>
		</div>
	</div>
</div>
<!-- /About -->

<?php
  include_once 'includes/footer.php';
  ob_end_flush();
?>