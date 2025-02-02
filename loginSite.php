<?php	
    session_start();

    /*
    // Sprawdzenie, czy dane zostały przesłane
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
    header('Location: index.php');
        exit();
    }
    */
    if(isset($_POST['username']))
    {
    require_once "connect.php";

    $connection = new mysqli($host, $db_user, $db_password, $db_name);

    // Sprawdzenie połączenia
    if ($connection->connect_errno) {
        echo "Error: " . $connection->connect_errno;
        exit();
    }

    $login = $_POST['username'];
    $password = $_POST['password'];

    // Bezpieczne przygotowanie zapytania
    $query = "SELECT id, login, email, password FROM users WHERE login = ?";
    $stmt = $connection->prepare($query);

    if (!$stmt) {
        echo "Error preparing statement: " . $connection->error;
        exit();
    }

    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Sprawdzenie hasła
        if (password_verify($password, $row['password'])) {
            $_SESSION['logged'] = true;
            $_SESSION['id'] = $row['id'];
            $_SESSION['login'] = $row['login'];
            $_SESSION['email'] = $row['email'];
            
            unset($_SESSION['errorLogin']);
            $_SESSION['message'] = "Zalogowano pomyślnie. Witaj " . $_SESSION['login'];
            header('Location: uzytkownik.php');
        } else {
            $_SESSION['errorLogin'] = "Login or password is invalid";
            //header('Location: loginSite.php');
        }
    } else {
        $_SESSION['errorLogin'] = "Login or password is invalid";
        //header('Location: loginSite.php');
    }

    // Zamknięcie połączenia i zapytania
    $stmt->close();
    $connection->close();
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
                    <li><a href="index.php">Strona główna</a></li>
                    <li><a href="#">O aplikacji</a></li>
                    <li><a href="#">O autorze</a></li>
                    <li><a href = "loginSite.php"><button type="button" class="buttonLogin"> Zaloguj </button></a></li>
                    <li><a href = "registerSite.php"><button type="button" class="buttonSignup">Zarejestruj</button></a></li>
                </ul>

            </nav>
            <h1 class="logo">Zaloguj się</h1>
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

                  </div>
                        <h2>

                        </h2>
                    </div>
                </section>

                <section class="s3">
                    <div class="centralS3">
                        <form id="loginForm" action="" method="POST">
                        <div id="grid">
                            <div id="areaA">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nazwa użytkownika</label>
                                    <input type="text" class="form-control" id="name" value= "<?php
                                        if (isset($_SESSION['username']))
                                        {
                                            echo $_SESSION['username'];
                                            unset($_SESSION['username']);
                                        }
                                    ?>" placeholder = "Nick" name="username" required/>
                                    <?php
                                        if (isset($_SESSION['errorLogin']))
                                        {
                                            echo '<div class="error">'.$_SESSION['errorLogin'].'</div>';
                                            unset($_SESSION['errorLogin']);
                                        }
                                    ?>
                                </div>

                                <div class="mb-3">
                                    <label for="pass1" class="form-label">Hasło</label>
                                    <input type="password" class="form-control" id="pass1" value = "<?php
                                        if (isset($_SESSION['password']))
                                        {
                                            echo $_SESSION['password'];
                                            unset($_SESSION['password']);
                                        }
                                    ?>" placeholder="Password" name = "password" required>
                                    <?php
                                        if (isset($_SESSION['errorLogin']))
                                        {
                                            echo '<div class="error">'.$_SESSION['errorLogin'].'</div>';
                                            unset($_SESSION['errorLogin']);
                                        }
                                    ?>	
                                </div>

                            </div>

                            <div id="areaB">

                                
                            <button class="btn btn-primary btn-lg" type="submit">Zaloguj</button>
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