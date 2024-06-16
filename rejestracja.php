 <?php

	session_start();
	header('Content-Type: application/json');
	//var_dump($_POST);

	if(isset($_POST['email']))
	{
		$response = array(); 

		//Udana walidacja? TAK
		$wszystko_OK = true;
		
		//Sprawdz poprawnosc nickname'a
		$nick = $_POST['name'];
		
		if(strlen($nick)<3 || strlen($nick)>20)
		{
			$wszystko_OK = false;
			$_SESSION['e_nick'] = "Nick musi  zawierać się od 3 do 20 znaków!";
		}
		
		if(ctype_alnum($nick)==false)
		{
			$wszystko_OK = false;
			$_SESSION['e_nick'] = "Nick musi  skladać się tylko z liter i cyfr oraz bez polskich znaków!";
		}
		
		//Sprawdzenie poprawnosci adresu email
		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB != $email))
		{
			$wszystko_OK = false;
			$_SESSION['e_email'] = "Podaj poprawny adres e-mail";
		}	
		
		//Sprawdzenie poprawnosci wpisanych hasel
		$haslo1 = $_POST['pass1'];
		$haslo2 = $_POST['pass2'];
		
		if((strlen($haslo1)<8) || (strlen($haslo1)>20))
		{
			$wszystko_OK = false;
			$_SESSION['e_haslo'] = "Haslo musi posiadac od 8 do 20 znaków!";
		}	
		
		if($haslo1 != $haslo2)
		{
			$wszystko_OK = false;
			$_SESSION['e_haslo'] = "Podane hasla muszą być jednakowe!";
		}	
		
		$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);
		/*
		//Bot or not? Oto jest pytanie!
		$sekret = "6Ld1TcIpAAAAANTY1nPyCTosGHenMITOp7v0mSF0";
		
		$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
		
		$odpowiedz = json_decode($sprawdz);
		
		if ($odpowiedz->success==false)
		{
			$wszystko_OK = false;
			$_SESSION['e_bot'] = "Potwierdz, że nie jesteś botem!";
		}	*/

		require_once "connect.php";
		
		mysqli_report(MYSQLI_REPORT_STRICT); 
		
		try
		{
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			if ($polaczenie->connect_errno!=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				//Czy email juz istnieje?
				$rezultat = $polaczenie->query("SELECT id FROM users WHERE email ='$email'");
				
				if(!$rezultat) throw new Exception($polaczenie->error);
				$ile_takich_maili = $rezultat->num_rows;
				if($ile_takich_maili>0)
				{
					$wszystko_OK = false;
					$_SESSION['e_email'] = "Istnieje już konto przypisane do tego adresu e-mail!";
				}	
				//Czy nick jest juz zarezerwowany?
				$rezultat = $polaczenie->query("SELECT id FROM users WHERE login ='$nick'");
				
				if(!$rezultat) throw new Exception($polaczenie->error);
				$ile_takich_nickow = $rezultat->num_rows;
				if($ile_takich_nickow>0)
				{
					$wszystko_OK = false;
					$_SESSION['e_nick'] = "Istnieje już gracz o takim nicku! Wybierz inny.";
				}	
				
				if($wszystko_OK==true)
				{
					if($polaczenie->query("INSERT INTO users VALUES(NULL, '$nick', '$email', '$haslo_hash')"))
					{
						//$_SESSION['udanarejestracja'] = true;
						$_SESSION['logged'] = true;

						$response = array(
							'status' => 'success',
							'message' => 'Rejestracja przebiegła pomyślnie'
						);
					} else {
						throw new Exception($polaczenie->error);
					}
				}
				$polaczenie->close();
			}
		}
		catch(Exception $e)
		{
				
				$response = array(
					'status' => 'error',
					'message' => 'Wystąpił błąd serwera podczas rejestracji. Spróbuj ponownie później.'
				);
		}
		// Zwróć odpowiedź jako JSON	
		echo json_encode($response);	
	}
	
?>