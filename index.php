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
                    <li><a href = "loginSite.php"><button type="button" class="buttonLogin"> Zaloguj </button></a></li>
                    <li><a href = "registerSite.php"><button type="button" class="buttonSignup">Zarejestruj</button></a></li>
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
                            <a href = "registerSite.php"><button type="button" class="buttonSignup2" data-bs-toggle="modal"
                                data-bs-target="">
                                Utwórz konto
                            </button></a>
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

    </script>

    <!-- Latest minified bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    
</body>

</html>