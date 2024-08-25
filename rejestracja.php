 <?php
	session_start();
	header('Content-Type: application/json');

	if (isset($_SESSION["czas"]) && ($_SESSION["czas"] + 60 * 10 < time())) { // 10 minut
		session_unset();
		session_destroy();
		header('Location: index.php');
		exit(); 
	}
	$_SESSION["czas"] = time();

	$response = array();

	if(isset($_POST['email']))
	{
		//Udana walidacja? TAK
		$wszystko_OK = true;
		
		//Sprawdz poprawnosc nickname'a
		$nick = $_POST['name'];
		
		if(strlen($nick) < 3 || strlen($nick) > 20)
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
		
		if((filter_var($emailB, FILTER_VALIDATE_EMAIL) === false) || ($emailB != $email))
		{
			$wszystko_OK = false;
			$_SESSION['e_email'] = "Podaj poprawny adres e-mail";
		}	
		
		//Sprawdzenie poprawnosci wpisanych hasel
		$haslo1 = $_POST['pass1'];
		$haslo2 = $_POST['pass2'];
		
		if((strlen($haslo1) < 8) || (strlen($haslo1) > 20))
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

				// Sprawdzenie reCAPTCHA
				if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
					$recaptchaResponse = $_POST['g-recaptcha-response'];
					$secret = '6LfF5S4qAAAAAPE_FxDAlQ0dy_jAYnvbFd0JJkYq';

					$responseRecaptcha = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$recaptchaResponse");
					$responseKeys = json_decode($responseRecaptcha, true);

					error_log("Odpowiedź reCAPTCHA: " . print_r($responseKeys, true));

					if(isset($responseKeys['success']) && $responseKeys['success']){
						$wszystko_OK = true;
						$response = array('status' => 'success', 'message' => 'Udana rejestracja');
					}
					else {
						$wszystko_OK = false;
						$response = array('status' => 'error', 'message' => 'Potwierdź, że nie jesteś botem! 1');
            			exit();
					}
				} else {
					$wszystko_OK = false;
					$response = array('status' => 'error', 'message' => 'Potwierdź, że nie jesteś botem! 2');
       				exit();
				}
				

		require_once "connect.php";
		
		mysqli_report(MYSQLI_REPORT_STRICT); 
		
		try
		{
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			if ($polaczenie->connect_errno != 0)
			{
				throw new Exception(mysqli_connect_errno());
			} else {
				//Czy email juz istnieje?
				$email = $polaczenie->real_escape_string($email);
				$nick = $polaczenie->real_escape_string($nick);
				
				$rezultat = $polaczenie->query("SELECT id FROM users WHERE email ='$email'");
				
				if(!$rezultat) throw new Exception($polaczenie->error);
				$ile_takich_maili = $rezultat->num_rows;
				if($ile_takich_maili > 0)
				{
					$wszystko_OK = false;
					$response = array(
						'status' => 'error',
						'message' => 'Istnieje już konto przypisane do tego adresu e-mail'
					);
					echo json_encode($response);
                	exit();
				}	
				//Czy nick jest juz zarezerwowany?
				$rezultat = $polaczenie->query("SELECT id FROM users WHERE login ='$nick'");
				
				if(!$rezultat) throw new Exception($polaczenie->error);
				$ile_takich_nickow = $rezultat->num_rows;
				if($ile_takich_nickow > 0)
				{
					$wszystko_OK = false;
					$response = array(
						'status' => 'error',
						'message' => 'Istnieje już gracz o takim nicku! Wybierz inny'
					);
					echo json_encode($response);
                	exit();
				}	
				
				if($wszystko_OK == true)
				{
					// Rozpoczęcie transakcji
					$polaczenie->begin_transaction();

					if($polaczenie->query("INSERT INTO users (login, email, password) VALUES('$nick', '$email', '$haslo_hash')"))
					{
						// Pobieranie ID nowego użytkownika
                    	$newUserId = $polaczenie->insert_id;
						
						// Kopiowanie domyślnych kategorii przychodów
						$stmt = $polaczenie->prepare("
                        	INSERT INTO incomes_category_assigned_to_users (userId, name)
                        	SELECT ?, name
                        	FROM incomes_category_default
                    	");
                    	$stmt->bind_param('i', $newUserId);
                    	$stmt->execute();

						// Kopiowanie domyślnych kategorii wydatków
						$stmt2 = $polaczenie->prepare("
                        	INSERT INTO expenses_category_assigned_to_users (userId, name)
                        	SELECT ?, name
                        	FROM expenses_category_default
                    	");
                    	$stmt2->bind_param('i', $newUserId);
                    	$stmt2->execute();

						// Kopiowanie domyślnych kategorii płatnosci
						$stmt3 = $polaczenie->prepare("
                        	INSERT INTO payment_methods_assigned_to_users (userId, name)
                        	SELECT ?, name
                        	FROM payment_methods_default
                    	");
                    	$stmt3->bind_param('i', $newUserId);
                    	$stmt3->execute();

						// Sprawdzenie, czy wstawianie kategorii się powiodło
                    	if ($stmt->affected_rows > 0 && $stmt2->affected_rows > 0 && $stmt3->affected_rows > 0) {
                        	// Zatwierdzenie transakcji
                        	$polaczenie->commit();
							error_log("Transaction committed for user ID $newUserId.");
                        	$_SESSION['logged'] = true;
							$_SESSION['id'] = $newUserId;
                        	$response = array(
                            	'status' => 'success',
                            	'message' => 'Rejestracja przebiegła pomyślnie'
                        );
                    	} else {
							// Wycofanie transakcji w przypadku błędu
							$polaczenie->rollback();
							throw new Exception("Nie udało się skopiować kategorii.");
						}
                    $stmt->close();
					$stmt2->close();
					$stmt3->close();				
					} 
					$polaczenie->close();
				}
			}
		}
		catch(Exception $e)
		{
				error_log("Caught exception: " . $e->getMessage());
				$response = array(
					'status' => 'error',
					'message' => 'Wystąpił błąd serwera podczas rejestracji. Spróbuj ponownie później.'
				);
		}
	}
		// Zwróć odpowiedź jako JSON	
		echo json_encode($response);	
?>