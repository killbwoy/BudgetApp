<?php
	session_start();
  
	if ($_SESSION["czas"] and $_SESSION["czas"]+60*10<time()) { // 10 minut
    session_unset();
    session_destroy();
    header('Location: index.php');
  }
  $_SESSION["czas"] = time();

	if(!isset($_SESSION['logged'])){
		header('Location: index.php');
		exit();
	}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title>Budget App - Panel użytkownika</title>

  <!-- Latest minified bootstrap css -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />

    <!-- Custom sheet -->
    <link rel="stylesheet" href="./css/styleUser.css">
    <link rel="stylesheet" href="./css/fontello.css">

    <!-- Custom js -->


    <!-- Open Sans font -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">

    <!-- jQuery library -->
    <script  src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

</head>

<body>
   <div>
<!-- Navbar -->
<!-- Header -->
<header>
            <!-- Navbar -->
            <nav>
                <ul class="menu">
                    <li><img class="logoSVG" src="./svg/save-money.png" alt="Logo" /></li>
                    <li><a href="uzytkownik.php">Strona główna</a></li>
                    <li><a href="dodajPrzychod.html">Dodaj przychód</a></li>
                    <li><a href="dodajWydatek.html">Dodaj wydatek</a></li>
                    <li><a href="bilans.html">Przeglądaj bilans</a></li>
                    <li><a href="#">Ustawienia</a></li>
                    <li>
                        <a href = "wyloguj.php"><button type="button" class="buttonSignup" data-bs-toggle="modal" data-bs-target="">Wyloguj</button></a>
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

  <!-- Main -->
  <main id = "main">
    <article>
      <div class = "msgLogin">
        <?php
         if(isset($_SESSION['message']))
         {
         echo '<h4 class="alert alert-warning">'.$_SESSION['message'].'</h4>';
         unset($_SESSION['message']);
        }
        if(isset($_SESSION['messageAdd']))
        {
        echo '<h4 class="alert alert-warning">'.$_SESSION['messageAdd'].'</h4>';
        unset($_SESSION['messageAdd']);
        }
        ?>
      </div>
      <section class = "s1">
        <div class="central">
          <div class="centralimg">
            <img src = "images/budgetkolorowy.jpg">
            <img src = "images/Odczegozacza.jpg">
          </div>
        </div>
      </section>
    </article>
  </main>

  <!-- Featured Section -->

 <!--Footer-->
  <footer>
    <div class="info">
      Wszelkie prawa zastrzeżone &copy 2024 .MK.
    </div>
  </footer>

</div>

  
<script>

  const mobileNav = document.querySelector('ul');
  const burgerIcon = document.querySelector('.burger');

  burgerIcon.addEventListener('click', function() {
    mobileNav.classList.toggle('active');
    burgerIcon.classList.toggle('active');
  });

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>


</body>
</html>