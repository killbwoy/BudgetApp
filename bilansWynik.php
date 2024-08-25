<?php
	session_start();

  if (isset($_SESSION["czas"]) and $_SESSION["czas"] + 60 * 10 < time()) { // 10 minut
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
  }
  $_SESSION["czas"] = time();
	
	if(!isset($_SESSION['logged'])){
		header('Location: index.php');
		exit();
	}

  $expenses = isset($_SESSION['expenses']) ? $_SESSION['expenses'] : [];
  $incomes = isset($_SESSION['incomes']) ? $_SESSION['incomes'] : [];
  $selected_period = isset($_SESSION['selected_period']) ? $_SESSION['selected_period'] : 'Nie Wybrano okresu';

  // Funkcja do obliczania bilansu
function calculate_balance($incomes, $expenses) {
  $total_income = 0;
  $total_expense = 0;

  // Sumowanie przychodów
  foreach ($incomes as $income) {
      if (isset($income['amount'])) {
          $total_income += $income['amount'];
      }
  }

  // Sumowanie wydatków
  foreach ($expenses as $expense) {
      if (isset($expense['amount'])) {
          $total_expense += $expense['amount'];
      }
  }

  // Oblicz bilans
  $balance = $total_income - $total_expense;
  return $balance;
}

$balance = calculate_balance($incomes, $expenses);

  function getPeriodName($period) {
    switch ($period) {
        case 'current_month':
            return 'Bieżący miesiąc';
        case 'previous_month':
            return 'Poprzedni miesiąc';
        case 'current_year':
            return 'Bieżący rok';
        case 'custom':
            return '';
        default:
            return 'Nie wybrano okresu';
    }
}

// Funkcja do wyświetlania dat dla niestandardowego okresu
function getCustomPeriod($start_date, $end_date) {
  return "Od $start_date do $end_date";
}

$selected_period_name = getPeriodName($selected_period);

// Jeżeli wybrano okres niestandardowy, uzyskaj daty z sesji, jeśli są dostępne
$custom_period = '';
if ($selected_period == 'custom') {
    $start_date = isset($_SESSION['custom_start_date']) ? $_SESSION['custom_start_date'] : 'brak daty początkowej';
    $end_date = isset($_SESSION['custom_end_date']) ? $_SESSION['custom_end_date'] : 'brak daty końcowej';
    $custom_period = getCustomPeriod($start_date, $end_date);
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
          <li><a href="dodajPrzychod.html">Dodaj przychód</a></li>
          <li><a href="dodajWydatek.html">Dodaj wydatek</a></li>
          <li><a href="bilans.html">Przeglądaj bilans</a></li>
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
    
    <div>
      <h1 class="zestawienie">
        Zestawienie budżetu z wybranego okresu: <?php echo htmlspecialchars($selected_period_name); ?>
        <?php echo ($selected_period == 'custom') ? $custom_period : ''; ?>
      </h1>
    </div>

    <div class="container">
      <section class="section1">
      <?php if (!empty($incomes)): ?>
        <table class="content-table">
          <thead>
            <tr>
              <th>Przychody</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($incomes as $income): ?>
            <tr>
              <td><?php echo htmlspecialchars($income['category_name']); ?></td>
            </tr>
            <tr class="active-row">
              <td><?php echo htmlspecialchars($income['amount']) . ' PLN'; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
          <table class="content-table">
          <thead>
            <tr>
              <th>Przychody</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Brak danych do wyświetlenia</td>
            </tr>
          </tbody>
        </table>
        <?php endif; ?>

        <?php if (!empty($expenses)): ?>
        <table class="content-table">
          <thead>
            <tr>
              <th>Wydatki</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($expenses as $expense): ?>
            <tr>
              <td><?php echo htmlspecialchars($expense['category_name']); ?></td>
            </tr>
            <tr class="active-row">
              <td><?php echo htmlspecialchars($expense['amount']) . ' PLN'; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
          <table class="content-table">
          <thead>
            <tr>
              <th>Wydatki</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Brak danych do wyświetlenia</td>
            </tr>
          </tbody>
        </table>
        <?php endif; ?>
      </section>

      <section class="section2">
        <table class="content-table">
          <thead>
            <tr>
              <th>Bilans</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($balance > 0): ?>
            <tr>
              <td>Udało Ci się zaoszczędzić:</td>
            </tr>
            <tr class="active-row">
              <td><?php echo htmlspecialchars($balance) . ' PLN'; ?></td>
            </tr>
            <?php elseif ($balance < 0): ?>
            <tr>
              <td>Masz deficyt:</td>
            </tr>
            <tr class="active-row">
              <td><?php echo htmlspecialchars($balance) . ' PLN'; ?></td>
            </tr>
            <?php else: ?>
            <tr>
              <td>Bilans jest równy zero</td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

      <div class="">
        <section class="section3">
          <h2 class="heading">Wydatki z wybranego okresu (Przykładowe)</h2>
          <div class="wrapper">
            <div class="pie-wrap">
              <div class="light-yellow entry">
                <p class="entry-value">Mieszkanie</p>
                <p>25%</p>
              </div>

              <div class="sky-blue entry">
                <p class="entry-value">Jedzenie</p>
                <p>25%</p>
              </div>

              <div class="pink entry">
                <p>12.5%</p>
                <p class="entry-value">Transport</p>
              </div>

              <div class="purple entry">
                <p>12.5%</p>
                <p class="entry-value">Edukacja</p>
              </div>

              <div class="green entry">
                <p>12.5%</p>
                <p class="entry-value">Hobby</p>
              </div>

              <div class="wheat entry">
                <p>12.5%</p>
                <p class="entry-value">Inewstowanie</p>
              </div>

        </section>
      </div>

    </div>

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

    burgerIcon.addEventListener('click', function () {
      mobileNav.classList.toggle('active');
      burgerIcon.classList.toggle('active');
    });

  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>


</body>

</html>