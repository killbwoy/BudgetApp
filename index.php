<?php
    session_start();

    if((isset($_SESSION['logged'])) && ($_SESSION['logged'] == true))
	{
		header('Location: uzytkownik.php');
		exit();
	}
    $login = "";
    $errorLogin = "";
    if(isset($_SESSION["errorLogin"]) && $_SESSION["errorLogin"] != ""){
        $errorLogin = $_SESSION['errorLogin'];
        $login = $_SESSION['login'];
        unset($_SESSION['errorLogin']);
        unset($_SESSION['login']);
    }
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

    <!-- Custom sheet -->
    <link rel="stylesheet" href="./css/styleIndex.css" />
    <link rel="stylesheet" href="./css/fontello.css" />

    <!-- Custom js -->
    <!--<script src = "LoginSignUp.js"></script>-->

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
                    <li><a href="#">Strona główna</a></li>
                    <li><a href="#">O aplikacji</a></li>
                    <li><a href="#">O autorze</a></li>
                    <li><a>
                        <button id = "loginBtn" type="button" class="buttonLogin" data-bs-toggle="modal" data-bs-target="#loginModal">
                            Zaloguj
                        </button></a>
                    </li>
                    <li>
                        <button type="button" class="buttonSignup" data-bs-toggle="modal" data-bs-target="#signupModal">
                            Zarejestruj
                        </button>
                    </li>
                </ul>

            </nav>
            <h1 class="logo">Twój Partner w Finansach</h1>
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
                        <h2><i>"Zrobić budżet to wskazać swoim pieniądzom, dokąd mają iść, zamiast się zastanawiać,
                                gdzie się rozeszły" -</i>
                            John C. Maxwell
                        </h2>
                    </div>
                </section>
                <section class="s2">
                    <p>Zadbaj o własne finanse, realizuj swoje marzenia:
                        <span><b>Zacznij już dziś !</b></span>
                    </p>
                </section>
                <section class="s3">
                    <div class="central">
                        <div class="centralimg">
                            <img src="./images/spend-money.jpg" alt="spend money">
                        </div>
                        <div class="centraltext">
                            <p>Dobrze opracowany budżet domowy to PLAN, który pomaga świetnie
                                wykorzystać zarabiane przez nas pieniądze i znacznie szybciej
                                realizować cele i marzenia. To proste i bardzo skuteczne
                                narzędzie, które pomaga
                                powiedzieć naszym pieniądzom, dokąd mają iść, zamiast się
                                zastanawiać, gdzie się rozeszły.
                            </p>
                            <button type="button" class="buttonSignup2" data-bs-toggle="modal"
                                data-bs-target="#signupModal">
                                Utwórz konto
                            </button>
                        </div>
                    </div>
                </section>
                <section class="s4">
                    <div class="contact">
                        <h3>Kontakt z autorem</h3>
                        <p>Jeśli masz jakieś pytanie chętnie na nie odpowiem . Wybierz sposób w jaki chcesz sie
                            ze mną skomunikować.</p>
                        <button type="button" class="" data-bs-toggle="modal" data-bs-target="">
                            Napisz do mnie
                        </button>
                    </div>
                </section>
            </article>
        </main>

        <!-- Sign-up Modal -->
        <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true"
            style="display: none">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="signupModalLabel">Utwórz konto</h5></br>
                        <!--
                            <p class="statusMsg"></p>
                        -->
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="registrationForm" action="rejestracja.php" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nazwa użytkownika</label>
                                <input type="text" class="form-control" id="name" name="name" />
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Adres e-mail</label>
                                <input type="email" class="form-control" id="email" name="email" />
                            </div>
                            <div class="mb-3">
                                <label for="pass1" class="form-label">Hasło</label>
                                <input type="password" class="form-control" id="pass1" name="pass1" />
                            </div>
                            <div class="mb-3">
                                <label for="pass2" class="form-label">Powtórz hasło</label>
                                <input type="password" class="form-control" id="pass2" name="pass2" />
                            </div>
                            <label>
                                <input type="checkbox" id="regulamin" name="regulamin" /> Akceptuję regulamin
                            </label>
                            <div class="g-recaptcha" data-sitekey="6Ld1TcIpAAAAAHQ-5zmjvSkOkhZaA7bQD29e22Ms"></div>
                            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
                        </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Zarejestruj</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> Zamknij</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Login Modal -->
        <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" style="display: none"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="loginModalLabel">Zaloguj</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick = "clearMsg()"></button>
                    </div>
                     
                    <div class="modal-body">
                        <form id="loginForm" action="zaloguj.php" method="POST">
                        <p class = "err-msg"> <?php echo $errorLogin; ?></p>
                            <div class="mb-3">
                                <label for="username" class="form-label">Nazwa Użytkownika</label>
                                <input type="username" class="form-control" id="username" name="username" required/>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Hasło</label>
                                <input type="password" class="form-control" id="password" name="password" required />
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Zamknij
                                </button>
                                <button type="submit" class="btn btn-primary" form="loginForm">Zaloguj</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact -->

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
                Wszelkie prawa zastrzeżone &copy 2024 .MK.
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

        $(function(){
        <?php if($errorLogin != "") { ?>
            $("#loginBtn").click();
        <?php } ?>
    });


    $(document).ready(function () {
        console.log("jQuery is loaded and document is ready");
	    $('#registrationForm').submit(function (event) {
            // Zatrzymaj domyślne działanie przycisku "submit"
            event.preventDefault();
            console.log("Form submission prevented");
            submitForm();
		    });
	});

	function submitForm()
	{
        console.log("submitForm function called");
		let reg = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
		let name = $('#name').val().trim();
		let email = $('#email').val().trim();
		let pass1 = $('#pass1').val().trim();
		let pass2 = $('#pass2').val().trim();

        // Debugging
        console.log("Name: ", name);
        console.log("Email: ", email);
        console.log("Pass1: ", pass1);
        console.log("Pass2: ", pass2);

		if(name == '')
		{
			alert('Prosze uzupełnić swoje imie');
			$('#name').focus();
			return false;
		} else if(name.length < 3 || name.length > 20){
            alert('Nick musi  zawierać się od 3 do 20 znaków!');
			$('#name').focus();
			return false;
        }  else if(email == ''){
			alert('Prosze uzupełnić swój adres email');
			$('#email').focus();
			return false;
		} else if(!reg.test(email)){
			alert('Prosze wpisac poprawny email.');
			$('#email').focus();
			return false;
		} else if(pass1 === '' || pass2 === ''){
			alert('Prosze uzupełnić haslo');
			$('#pass1').focus();
			return false;
		} else if(pass1.length < 8 || pass1.length > 20){
            alert('Hasło musi  zawierać się od 8 do 20 znaków!');
			$('#name').focus();
			return false;
        }else if(pass1 !== pass2){
			alert('Podane hasła nie są identyczne');
			$('#pass2').focus();
			return false;
		} else if (!document.getElementById('regulamin').checked) {
            // Sprawdzenie czy checkbox został zaznaczony
            console.log("Regulamin niezaznaczony");
            alert('Akceptuj regulamin.');
            return false;
        } 

        console.log("Wszystko w porządku, wykonuję żądanie AJAX");

   
    $.ajax({
        type:'POST',
        url:'rejestracja.php',
        data:{
            contactFrmSubmit: 1,
            name: name,
            email: email,
            pass1: pass1,
            pass2: pass2
        },
        beforeSend: function () {
            $('.btn-primary').attr("disabled","disabled");
            $('.modal-dialog').css('opacity', '.5');
        },
        dataType: 'json',
        //success:function(msg)
        success:function(response){
            console.log("Odpowiedź z serwera: ", response);
            if(response.status === 'success'){
                $('#name').val('');
                $('#email').val('');
                $('#pass1').val('');
                $('#pass2').val('');
                alert(response.message);
                alert('Rejestracja przebiegła pomyślnie');

               // Przekieruj użytkownika do uzytkownik.php po 2 sekundach. 
                setTimeout(function() {
                window.location.href = 'uzytkownik.php';
                }, 2000);
            }else{
                alert('Pojawiły sie problemy, spróbuj ponownie');
                console.log(response.errors);
                console.log(response.message);
            }
            $('.btn-primary').removeAttr("disabled");
            $('.modal-dialog').css('opacity', '');
        },
        error:function (xhr, status, error) {
            console.error("Błąd AJAX: ", status, error);
            alert("Wystąpił błąd podczas wysyłania żądania. Spróbuj ponownie.");
            $('.btn-primary').removeAttr("disabled");
            $('.modal-dialog').css('opacity', '');
        }    
    });			
	}


    </script>

    <!-- Latest minified bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    
</body>

</html>