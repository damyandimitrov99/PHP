<?php

include('database_connection.php');

$message = '';

if(isset($_GET['verification_key']))
{
	$query = "
		SELECT * FROM accounts 
		WHERE usersVkey = :usersVkey
	";
	$statement = $connect->prepare($query);
	$statement->execute(
		array(
			':usersVkey' => $_GET['verification_key']
		)
	);
	$no_of_row = $statement->rowCount();
	
	if($no_of_row > 0)
	{
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			if($row['usersVerified'] == 0)
			{
				$update_query = "
				UPDATE accounts 
				SET usersVerified = 1 
				WHERE usersId = '".$row['usersId']."'
				";
				$statement = $connect->prepare($update_query);
				$statement->execute();
				$sub_result = $statement->fetchAll();
				if(isset($sub_result))
				{
					$message = '
					<div class="w3-about-head">
						<h3>Поздравления!</h3>
						</br>
					</div>
					<div class="w3-agileitsline"  id="a1">
						<h3 style="text-align:center">
							Вашият Email Address е успешно потвърден! <br/> Можете да влезете във Вашият профил от тук - <a href="../login.php">Login</a>
						</h3>
						</br>
					</div>
					';
				}
			}
			else
			{
				$message = '
				<div class="w3-about-head">
					<h3>Грешка!</h3>
					</br>
				</div>
				<div class="w3-agileitsline"  id="a1">
					<h3 style="text-align:center">
						Вашият Email Address вече е бил потвърден! <br/> Можете да влезете във Вашият профил от тук - <a href="../login.php">Login</a>
					</h3>
					</br>
				</div>
				';
			}
		}
	}
	else
	{
		$message = '
		<div class="w3-about-head">
			<h3>Грешка!</h3>
			</br>
		</div>
		<div class="w3-agileitsline"  id="a1">
			<h3 style="text-align:center">
				Невалиден линк!
			</h3>
			</br>
		</div>';
	}
}

?>

<?php
  include_once 'includes/header.php';
  include_once 'includes/body_etc.php';
	?>

<div id="about" class="about">
	<div class="container">
		<?php
		echo $message;
		?>
	</div>
</div>
	
<?php
  include_once 'includes/footer.php';
?>