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

        if(isset($_SESSION['login'])) {
            $login = $_SESSION['login'];
        }
        
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
    <link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.4/components/contacts/contact-1/assets/css/contact-1.css">
    
    <!-- Custom sheet -->
    <link rel="stylesheet" href="./css/styleIndex.css">
    <link rel="stylesheet" href="./css/fontello.css">

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
                        <button type="button" class="btn btn-outline-dark btn-lg" data-bs-toggle="modal" data-bs-target="#exampleModal">
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
                            <div class="g-recaptcha" data-sitekey="6LfF5S4qAAAAAK44PkG3he9snrsADwCyuliddxD3"></div>
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

        <!-- Contact Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Napisz do mnie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id = "contactForm" action = "sendEmail.php" method = "POST" onsubmit="return handleFormSubmit(event);">
                <div class="row gy-4 gy-xl-5 p-4 p-xl-5">
                    <div class="col-12">
                    <label for="fullname" class="form-label">Twoje imię <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="fullname" name="fullname" value="" required>
                    </div>
                    <div class="col-12 col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
                        </svg>
                        </span>
                        <input type="email" class="form-control" id="email" name="email" value="" required>
                    </div>
                    </div>
                    <div class="col-12 col-md-6">
                    <label for="phone" class="form-label">Numer telefonu</label>
                    <div class="input-group">
                        <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16">
                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z" />
                        </svg>
                        </span>
                        <input type="tel" class="form-control" id="phone" name="phone" value="">
                    </div>
                    </div>
                    <div class="col-12">
                    <label for="message" class="form-label">Wiadomość <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                    </div>
                    <div class="col-12">
                    <div class="d-grid">
                        <button class="btn btn-primary btn-lg" type="submit">Wyślij</button>
                    </div>
                    </div>
                </div>
                <div id="alertSuccess" class="alert alert-success d-none" role="alert">
                    Dziękujemy! Twoja wiadomość została wysłana.
                </div>

                <div id="alertError" class="alert alert-danger d-none" role="alert">
                    Niestety, wystąpił błąd przy wysyłaniu wiadomości.
                </div>
                </form>
            </div>
            </div>
        </div>
        </div>

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

    document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('registrationForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Zatrzymaj domyślną akcję formularza

        if (validateForm()) {
            var recaptchaResponse = grecaptcha.getResponse();
            if (recaptchaResponse.length === 0) {
                alert('Proszę zakończyć reCAPTCHA!');
                return;
            }

            document.getElementById('g-recaptcha-response').value = recaptchaResponse;

            submitForm();
        }
    });
});

function validateForm() {
    console.log("validateForm function called");

    let reg = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
    let name = $('#name').val().trim();
    let email = $('#email').val().trim();
    let pass1 = $('#pass1').val().trim();
    let pass2 = $('#pass2').val().trim();

    if (name === '') {
        alert('Proszę uzupełnić swoje imię');
        $('#name').focus();
        return false;
    } else if (name.length < 3 || name.length > 20) {
        alert('Nick musi zawierać się od 3 do 20 znaków!');
        $('#name').focus();
        return false;
    } else if (email === '') {
        alert('Proszę uzupełnić swój adres email');
        $('#email').focus();
        return false;
    } else if (!reg.test(email)) {
        alert('Proszę wpisać poprawny email.');
        $('#email').focus();
        return false;
    } else if (pass1 === '' || pass2 === '') {
        alert('Proszę uzupełnić hasło');
        $('#pass1').focus();
        return false;
    } else if (pass1.length < 8 || pass1.length > 20) {
        alert('Hasło musi zawierać się od 8 do 20 znaków!');
        $('#pass1').focus();
        return false;
    } else if (pass1 !== pass2) {
        alert('Podane hasła nie są identyczne');
        $('#pass2').focus();
        return false;
    } else if (!document.getElementById('regulamin').checked) {
        alert('Akceptuj regulamin.');
        return false;
    }

    return true;
}

function submitForm() {
    console.log("submitForm function called");

    $.ajax({
        type: 'POST',
        url: 'rejestracja.php',
        data: {
            contactFrmSubmit: 1,
            name: $('#name').val().trim(),
            email: $('#email').val().trim(),
            pass1: $('#pass1').val().trim(),
            pass2: $('#pass2').val().trim(),
            'g-recaptcha-response': document.getElementById('g-recaptcha-response').value
        },
        beforeSend: function() {
            $('.btn-primary').attr("disabled", "disabled");
            $('.modal-dialog').css('opacity', '.5');
        },
        dataType: 'json',
        success: function(response) {
            console.log("Odpowiedź z serwera: ", response);
            if (response.status === 'success') {
                $('#name').val('');
                $('#email').val('');
                $('#pass1').val('');
                $('#pass2').val('');
                alert(response.message);

                setTimeout(function() {
                    window.location.href = 'uzytkownik.php';
                }, 1500);
            } else {
                alert(response.message);
            }
            $('.btn-primary').removeAttr("disabled");
            $('.modal-dialog').css('opacity', '');
        },
        error: function(xhr, status, error) {
            console.error("Błąd AJAX: ", status, error);
            //alert("Wystąpił błąd podczas wysyłania żądania. Spróbuj ponownie.");
            $('.btn-primary').removeAttr("disabled");
            $('.modal-dialog').css('opacity', '');
        }
    });
}

function handleFormSubmit(event) {
  event.preventDefault(); 

  const form = document.getElementById('contactForm');
  const formData = new FormData(form);

  fetch('sendEmail.php', {
    method: 'POST',
    body: formData,
  })
  .then(response => response.text())
  .then(data => {
    // Ukryj wszystkie alerty
    document.getElementById('alertSuccess').classList.add('d-none');
    document.getElementById('alertError').classList.add('d-none');

    // Sprawdź odpowiedź i wyświetl odpowiedni alert
    if (data.includes('Wiadomość została wysłana.')) {
      document.getElementById('alertSuccess').classList.remove('d-none');
    } else {
      document.getElementById('alertError').classList.remove('d-none');
    }
  })
  .catch(error => {
    // W przypadku błędu sieciowego lub innego
    document.getElementById('alertSuccess').classList.add('d-none');
    document.getElementById('alertError').classList.remove('d-none');
  });

  return false; // Zapobiega ponownemu wysłaniu formularza
}

    </script>

    <!-- Latest minified bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    
</body>

</html>