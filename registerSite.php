<?php
    session_start();

    if((isset($_SESSION['logged'])) && ($_SESSION['logged'] == true))
	{
		header('Location: uzytkownik.php');
		exit();
	}

	if (isset($_SESSION["czas"]) && ($_SESSION["czas"] + 60 * 10 < time())) { // 10 minut
		session_unset();
		session_destroy();
		header('Location: index.php');
		exit(); 
	}
	$_SESSION["czas"] = time();

	if(isset($_POST['email']))
	{
		//Udana walidacja? TAK
		$wszystko_OK = true;
		
		//Sprawdz poprawnosc nickname'a
		$nick = $_POST['nick'];
		
		if((strlen($nick) < 3) || (strlen($nick) > 20))
		{
			$wszystko_OK = false;
			$_SESSION['e_nick'] = "Nick musi  zawierać się od 3 do 20 znaków!";
		}
		
		if(ctype_alnum($nick) == false)
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

		//Czy zaakceptowano regulamin?
		if (!isset($_POST['regulamin']))
		{
			$wszystko_OK=false;
			$_SESSION['e_regulamin']="Potwierdź akceptację regulaminu!";
		}	

		// Sprawdzenie reCAPTCHA

		$sekret = "6Ld1TcIpAAAAANTY1nPyCTosGHenMITOp7v0mSF0";
		
		$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
						
		$odpowiedz = json_decode($sprawdz);
						
		if ($odpowiedz->success == false)
		{
			$wszystko_OK=false;
			$_SESSION['e_bot']="Potwierdź, że nie jesteś botem!";
		}
		
		//Zapamiętaj wprowadzone dane
		$_SESSION['fr_nick'] = $nick;
		$_SESSION['fr_email'] = $email;
		$_SESSION['fr_haslo1'] = $haslo1;
		$_SESSION['fr_haslo2'] = $haslo2;
		if (isset($_POST['regulamin'])) $_SESSION['fr_regulamin'] = true;

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

				//$email = $polaczenie->real_escape_string($email);
				//$nick = $polaczenie->real_escape_string($nick);
				
				$rezultat = $polaczenie->query("SELECT id FROM users WHERE email ='$email'");
				
				if(!$rezultat) throw new Exception($polaczenie->error);

				$ile_takich_maili = $rezultat->num_rows;
				if($ile_takich_maili > 0)
				{
					$wszystko_OK = false;
					$_SESSION['e_email']="Istnieje już konto przypisane do tego adresu e-mail!";
				}
                /*
				//Czy nick jest juz zarezerwowany?
				$rezultat = $polaczenie->query("SELECT id FROM users WHERE login ='$nick'");
				
				if(!$rezultat) throw new Exception($polaczenie->error);

				$ile_takich_nickow = $rezultat->num_rows;
				if($ile_takich_nickow > 0)
				{
					$wszystko_OK = false;
					$_SESSION['e_nick']="Istnieje już użytkownik o takim nicku! Wybierz inny.";
				}	
				*/
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
							//error_log("Transaction committed for user ID $newUserId.");
                        	$_SESSION['logged'] = true;
							$_SESSION['id'] = $newUserId;
							$_SESSION['udanarejestracja']=true;
							header('Location: uzytkownik.php');
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
			echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
			echo '<br />Informacja developerska: '.$e;	
		}
	}
		// Zwróć odpowiedź jako JSON	
		//echo json_encode($response);	
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget App</title>

    <!-- Latest minified bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.4/components/contacts/contact-1/assets/css/contact-1.css">
    
    <!-- Custom sheet -->
    <link rel="stylesheet" href="./css/styleIndex.css">
    <link rel="stylesheet" href="./css/fontello.css">

    <!-- Open Sans font -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">

    <!-- reCaptcha -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!-- jQuery library -->
    <script  src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

</head>

<body>

    <div>

        <!-- Header -->
        <header>
            <!-- Navbar -->
            <nav>
                <ul class="menu">
                    <li><img class="logoSVG" src="./svg/save-money.png" alt="Logo" /></li>
                    <li><a href="index.php">Strona główna</a></li>
                    <li><a href="#">O aplikacji</a></li>
                    <li><a href="#">O autorze</a></li>
                    <li><a href = "loginSite.php"><button type="button" class="buttonLogin"> Zaloguj </button></a></li>
                    <li>
                        <button type="button" class="buttonSignup" data-bs-toggle="" data-bs-target="">
                            Zarejestruj
                        </button>
                    </li>
                </ul>

            </nav>
            <h1 class="logo">Zarejestruj się</h1>
            <div class="burgerButon">
                <button class="burger">
                    <div class="line"></div>
                    <div class="line"></div>
                    <div class="line"></div>
                </button>
            </div>


        </header>

        <!-- Welcome Section -->
        <main id="welcome">
            <article>
                <section class="s1">
                    <div>
                    <div class = "errorLogin">
                     <?php
                    if(isset($_SESSION['errorLogin']))
                    {
                        echo '<h4 class="alert alert-warning">'.$_SESSION['errorLogin'].'</h4>';
                        unset($_SESSION['errorLogin']);
                    } 
                    ?> 
                  </div>
                        <h2>

                        </h2>
                    </div>
                </section>

                <section class="s3">
                    <div class="centralS3">
                        <form id="registrationForm" action="" method="POST">
                        <div id="grid">
                            <div id="areaA">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nazwa użytkownika</label>
                                    <input type="text" class="form-control" id="name" value= "<?php
                                        if (isset($_SESSION['fr_nick']))
                                        {
                                            echo $_SESSION['fr_nick'];
                                            unset($_SESSION['fr_nick']);
                                        }
                                    ?>" placeholder = "Nick" name="nick" required/>
                                    <?php
                                        if (isset($_SESSION['e_nick']))
                                        {
                                            echo '<div class="error">'.$_SESSION['e_nick'].'</div>';
                                            unset($_SESSION['e_nick']);
                                        }
                                    ?>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Adres e-mail</label>
                                    <input type="email" class="form-control" id="email"  value = "<?php
                                        if (isset($_SESSION['fr_email']))
                                        {
                                            echo $_SESSION['fr_email'];
                                            unset($_SESSION['fr_email']);
                                        }
                                    ?>" placeholder="name@example.com" name = "email" required>
                                    <?php
                                        if (isset($_SESSION['e_email']))
                                        {
                                            echo '<div class="error">'.$_SESSION['e_email'].'</div>';
                                            unset($_SESSION['e_email']);
                                        }
                                    ?>
                                </div>
                                <div class="mb-3">
                                    <label for="pass1" class="form-label">Hasło</label>
                                    <input type="password" class="form-control" id="pass1" value = "<?php
                                        if (isset($_SESSION['fr_haslo1']))
                                        {
                                            echo $_SESSION['fr_haslo1'];
                                            unset($_SESSION['fr_haslo1']);
                                        }
                                    ?>" placeholder="Password" name = "pass1" required>
                                    <?php
                                        if (isset($_SESSION['e_haslo']))
                                        {
                                            echo '<div class="error">'.$_SESSION['e_haslo'].'</div>';
                                            unset($_SESSION['e_haslo']);
                                        }
                                    ?>	
                                </div>
                                <div class="mb-3">
                                    <label for="pass2" class="form-label">Powtórz hasło</label>
                                    <input type="password" class="form-control" id="pass2" value="<?php
                                        if (isset($_SESSION['fr_haslo2']))
                                        {
                                            echo $_SESSION['fr_haslo2'];
                                            unset($_SESSION['fr_haslo2']);
                                        }
                                    ?>" placeholder="Password" name="pass2" />
                                </div>
                            </div>

                            <div id="areaB">
                                <label> <input type="checkbox" id="regulamin" name="regulamin" <?php
                                    if (isset($_SESSION['fr_regulamin']))
                                    {
                                        echo "checked";
                                        unset($_SESSION['fr_regulamin']);
                                    }
                                        ?>/> Akceptuję regulamin
                            </label>
                            <?php
                                if (isset($_SESSION['e_regulamin']))
                                {
                                    echo '<div class="error">'.$_SESSION['e_regulamin'].'</div>';
                                    unset($_SESSION['e_regulamin']);
                                }
                            ?>	
                                <div class="g-recaptcha" data-sitekey="6Ld1TcIpAAAAAHQ-5zmjvSkOkhZaA7bQD29e22Ms"></div>
                                <?php
                                    if (isset($_SESSION['e_bot']))
                                    {
                                        echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
                                        unset($_SESSION['e_bot']);
                                    }
                                ?>
                                <!--<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">-->
                                
                            <button class="btn btn-primary btn-lg" type="submit">Zarejestruj</button>
                            </div>
                            
                        </div>    
                        </form>
                    </div>
                </section>

                <section class="s4">
                    <div class="contact">
                        <h3>Kontakt z autorem</h3>
                        <p>Jeśli masz jakieś pytanie chętnie na nie odpowiem . Wybierz sposób w jaki chcesz sie
                            ze mną skomunikować.</p>
                        <button type="button" class="btn btn-outline-dark btn-lg" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Napisz do mnie
                        </button>
                    </div>
                </section>
            </article>
        </main>

        <!--Footer-->
        <footer>
            <div class="socials">
                <div class="socialdivs">
                    <div class="fb">
                        <i class="icon-facebook"></i>
                    </div>
                    <div class="yt">
                        <i class="icon-youtube"></i>
                    </div>
                    <div class="tw">
                        <i class="icon-twitter"></i>
                    </div>
                    <div class="gplus">
                        <i class="icon-gplus"></i>
                    </div>
                    <div style="clear:both"></div>
                </div>
            </div>
            <div class="info">
                Wszelkie prawa zastrzeżone &copy 2024 ..::MK::..
            </div>
        </footer>
    </div>


    <script>
        const mobileNav = document.querySelector("ul");
        const burgerIcon = document.querySelector(".burger");

        burgerIcon.addEventListener("click", function () {
            mobileNav.classList.toggle("active");
            burgerIcon.classList.toggle("active");
        });


    </script>

    <!-- Latest minified bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    
</body>

</html>