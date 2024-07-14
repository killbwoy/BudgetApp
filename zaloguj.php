 <?php
 
	session_start();
	
	if((!isset($_POST['username'])) || (!isset($_POST['password'])))
	{
		header('Location: index.php');
		exit();
	}
	
	require_once "connect.php";
 
	$connection = @new mysqli($host, $db_user, $db_password, $db_name);
	
	if($connection -> connect_errno != 0){
		echo "Error: ".$connection->connect_errno; 
	}
	else{
		$login = $_POST['username'];
		$password = $_POST['password'];
		
		$login = htmlentities($login, ENT_QUOTES, "UTF-8");
		$password = htmlentities($password, ENT_QUOTES, "UTF-8");
			
			if($result = @$connection->query(
			sprintf("SELECT * FROM users WHERE login = '%s' AND password = '%s'",
			mysqli_real_escape_string($connection, $login),
			mysqli_real_escape_string($connection, $password))))
			{
				$how_users = $result->num_rows;
				if($how_users > 0){
					
					$_SESSION['logged'] = true;
										
					$row = $result->fetch_assoc();
					$_SESSION['id'] = $row['id'];
					$_SESSION['login'] = $row['login'];
					$_SESSION['email'] = $row['email'];
					$_SESSION['password'] = $row['password'];
	
					unset($_SESSION['errorLogin']);
					$result->free_result();
					$_SESSION['message'] = "Zalogowano pomyślnie. Witaj". $_SESSION['login'];
					header('Location: uzytkownik.php');

				} else {
					$_SESSION['login'] = $row['login'];
					$_SESSION['errorLogin'] = "Login or password is invalid";
					header('Location: index.php');
					//echo "Login or password is invalid";
				}
			}
		
		$connection->close();
	}
		
 
 ?>
