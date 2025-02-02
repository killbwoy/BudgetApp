<?php
session_start();

if (isset($_SESSION["czas"]) && $_SESSION["czas"] + 60 * 10 < time()) { // 10 minut
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
  }
  $_SESSION["czas"] = time();

// Sprawdzanie, czy użytkownik jest zalogowany
if(!isset($_SESSION['logged'])){
    header('Location: index.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){

        require_once "connect.php";

    $connection = new mysqli($host, $db_user, $db_password, $db_name);

    // Sprawdzenie połączenia
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $userId = $_SESSION['id'];
    $period = $_POST['period'];

    // Zapis wybranego okresu do zmiennej sesyjnej
    $_SESSION['selected_period'] = $period;

    if($period == "current_month"){
        // Pobranie bieżącej daty
        $currentDate = date('Y-m-d');
        
        // Ustawienie początku bieżącego miesiąca
        $start_date = date('Y-m-01'); 
        
        // Ustawienie końca bieżącego miesiąca
        $end_date = date('Y-m-t'); 
    }
    else if($period == "previous_month"){
        // Ustawienie początku poprzedniego miesiąca
        $start_date = date('Y-m-01', strtotime('first day of last month')); 
        
        // Ustawienie końca poprzedniego miesiąca
        $end_date = date('Y-m-t', strtotime('last day of last month')); 
    }
    else if($period == "current_year"){
        // Pobranie bieżącego roku
        $currentYear = date('Y'); 
        
        // Ustawienie początku bieżącego roku
        $start_date = "$currentYear-01-01"; 
        
        // Ustawienie końca bieżącego roku
        $end_date = "$currentYear-12-31"; 
    }
    else if($period == "custom"){
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        // Zapis dat niestandardowego okresu do zmiennych sesyjnych
        $_SESSION['custom_start_date'] = $start_date;
        $_SESSION['custom_end_date'] = $end_date;
    }
    // Przygotowanie zapytan
    $queryExpenses = "
        SELECT e.amount, ec.name AS category_name
        FROM expenses e
        JOIN expenses_category_assigned_to_users ec ON e.expense_category_assigned_to_user_id = ec.id
        WHERE e.date BETWEEN ? AND ? AND e.userId = ?
    ";

    $queryIncomes = "
        SELECT i.amount, ic.name AS category_name
        FROM incomes i
        JOIN incomes_category_assigned_to_users ic ON i.income_category_assigned_to_user_id = ic.id
        WHERE i.date BETWEEN ? AND ? AND i.userId = ?
    ";
    // Zapytanie dla wydatków
    $stmtExpenses = $connection->prepare($queryExpenses);

    // Sprawdzenie, czy przygotowanie zapytania powiodło się
    if ($stmtExpenses === false) {
        die("Prepare failed: " . $connection->error);
    }

    // Bindowanie parametrów
    $stmtExpenses->bind_param("ssi", $start_date, $end_date, $userId);

    // Wykonanie zapytania
    if ($stmtExpenses->execute()) {
        $result = $stmtExpenses->get_result();
        $_SESSION['expenses'] = $result->fetch_all(MYSQLI_ASSOC);

    } else {
        $_SESSION['messageAdd'] = "Błąd: " . $stmtExpenses->error;
    }

    // Zamknięcie zapytania i połączenia
    $stmtExpenses->close();

    // Zapytanie dla przychodów
    $stmtIncomes = $connection->prepare($queryIncomes);
    if ($stmtIncomes === false) {
        die("Prepare failed: " . $connection->error);
    }
    $stmtIncomes->bind_param("ssi", $start_date, $end_date, $userId);
    if ($stmtIncomes->execute()) {
        $result = $stmtIncomes->get_result();
        $_SESSION['incomes'] = $result->fetch_all(MYSQLI_ASSOC);
        
    } else {
        $_SESSION['messageAdd'] = "Błąd: " . $stmtIncomes->error;
    }
    $stmtIncomes->close();

    $connection->close();

    header('Location: bilansWynik.php');
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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

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
          <li><a href="dodajPrzychod.php">Dodaj przychód</a></li>
          <li><a href="dodajWydatek.php">Dodaj wydatek</a></li>
          <li><a href="bilans.php">Przeglądaj bilans</a></li>
          <li><a href="#">Ustawienia</a></li>
          <li>
            <a href="wyloguj.php"><button type="button" class="buttonSignup" data-bs-toggle="modal"
                data-bs-target="">Wyloguj</button></a>
          </li>
        </ul>

      </nav>
      <div class="burgerButon">
        <button class="burger">
          <div class="line"></div>
          <div class="line"></div>
          <div class="line"></div>
        </button>
      </div>


    </header>

    <!-- Main -->
    <main id="main">
      <article>
        <div id="content">
          <div id="balance-section">
            <form action="bilans.php" method="POST">
              <div id="header">
                <h1>Przeglądaj bilans</h1>
              </div>
              <div id="inside">
                <p> Okres czasu:
                  <select name="period">
                    <option value="current_month">Bieżący miesiąc</option>
                    <option value="previous_month">Poprzedni miesiąc</option>
                    <option value="current_year">Bieżący rok</option>
                    <option value="last_year">Ostatni rok</option>
                    <option value="all_time">Cały czas</option>
                    <option value="custom">Niestandardowy</option>
                  </select>
                </p>
                <div id="custom_dates" style="display: none">
                  <p>Początek: <input type="date" name="start_date" /></p>
                  <p>Koniec: <input type="date" name="end_date" /></p>
                </div>
                <p>
                  <input type="submit" class="button_active" value="Wyświetl" />
                  <input type="reset" value="Anuluj" />
                </p>
            </form>
          </div>
        </div>
  </div>

  <section class="s3">
    <div class="central">
    </div>
  </section>
  </article>
  </main>

  <!-- Featured Section -->

  <!--Footer-->
  <footer>
    <div class="info">
      Wszelkie prawa zastrzeżone &copy 2025 .MK.
    </div>
  </footer>

  </div>


  <script>

    const mobileNav = document.querySelector('ul');
    const burgerIcon = document.querySelector('.burger');

    burgerIcon.addEventListener('click', function () {
      mobileNav.classList.toggle('active');
      burgerIcon.classList.toggle('active');
    });

  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var selectElement = document.querySelector('select[name="period"]');
      var customDatesDiv = document.getElementById('custom_dates');

      selectElement.addEventListener('change', function () {
        if (selectElement.value === 'custom') {
          customDatesDiv.style.display = 'block';
        } else {
          customDatesDiv.style.display = 'none';
        }
      });
    });
  </script>

</body>

</html>