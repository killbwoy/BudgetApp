<?php
	session_start();

  if (!isset($_SESSION['logged']) || !isset($_SESSION['id'])) {
    header('Location: index.php');
    exit();
}

if (isset($_SESSION["czas"]) && $_SESSION["czas"] + 60 * 10 < time()) { // 10 minut
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
}
$_SESSION["czas"] = time();

  // Załączenie pliku connect.php
  require_once 'connect.php';

  // Nawiązanie połączenia z bazą danych
  $conn = new mysqli($host, $db_user, $db_password, $db_name);

  if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
  }

  $selected_period = $_SESSION['selected_period'] ?? 'all_time';
  $start_date = $_SESSION['custom_start_date'] ?? '2000-01-01';
  $end_date = $_SESSION['custom_end_date'] ?? date('Y-m-d');

  // Inicjalizacja zmiennych
  $incomes = [];
  $expenses = [];
  $balance = 0;

  // Funkcja do określania zakresu dat
function getDateRange($period) {
  switch ($period) {
      case 'current_month':
          return [date('Y-m-01'), date('Y-m-t')];
      case 'previous_month':
          return [
              date('Y-m-01', strtotime('first day of last month')),
              date('Y-m-t', strtotime('last day of last month'))
          ];
      case 'current_year':
          return [date('Y-01-01'), date('Y-12-31')];
      case 'last_year':
        return [date('Y-01-01', strtotime('-1 year')), date('Y-12-31', strtotime('-1 year'))];
      case 'custom':
          return [
              isset($_SESSION['custom_start_date']) ? $_SESSION['custom_start_date'] : '2000-01-01',
              isset($_SESSION['custom_end_date']) ? $_SESSION['custom_end_date'] : date('Y-m-d')
          ];
      case 'all_time':
      default:
          return ['2000-01-01', date('Y-m-d')];
  }
}

  // Przypisanie wartości do parametrów
  $user_id = $_SESSION['id']; 
  //$start_date = isset($_SESSION['custom_start_date']) ? $_SESSION['custom_start_date'] : '2025-01-01'; 
  //$end_date = isset($_SESSION['custom_end_date']) ? $_SESSION['custom_end_date'] : '2025-12-31'; 
  list($start_date, $end_date) = getDateRange($selected_period); // Zmiana sposobu ustalania dat

  // Pobranie przychodów

  // Przygotowanie zapytania SQL
$sql_incomes = "
SELECT 
    incomes_category_assigned_to_users.name AS category, 
    SUM(incomes.amount) AS amount 
FROM 
    incomes 
INNER JOIN 
    incomes_category_assigned_to_users 
ON 
    incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id 
WHERE 
    incomes.userId = ?
    AND incomes.date BETWEEN ? AND ? 
GROUP BY 
    incomes.income_category_assigned_to_user_id 
ORDER BY 
    amount DESC;
";

$stmt = $conn->prepare($sql_incomes);
  if (!$stmt) {
    die("Błąd przygotowania zapytania: " . $conn->error);
  }

// Powiązanie parametrów
$stmt->bind_param("iss", $user_id, $start_date, $end_date);

// Wykonanie zapytania
if (!$stmt->execute()) {
    die("Błąd wykonania zapytania: " . $stmt->error);
}

// Pobranie wyników
$result = $stmt->get_result();
$incomes = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();

  // Pobranie wydatków

  // Przygotowanie zapytania SQL
$sql_expenses = "
SELECT 
    expenses_category_assigned_to_users.name AS category, 
    SUM(expenses.amount) AS amount 
FROM 
    expenses 
INNER JOIN 
    expenses_category_assigned_to_users 
ON 
    expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id 
WHERE 
    expenses.userId = ?
    AND expenses.date BETWEEN ? AND ? 
GROUP BY 
    expenses.expense_category_assigned_to_user_id 
ORDER BY 
    amount DESC;
";

$stmt = $conn->prepare($sql_expenses);
  if (!$stmt) {
    die("Błąd przygotowania zapytania: " . $conn->error);
  }

// Powiązanie parametrów
$stmt->bind_param("iss", $user_id, $start_date, $end_date);

// Wykonanie zapytania
if (!$stmt->execute()) {
    die("Błąd wykonania zapytania: " . $stmt->error);
}

// Pobranie wyników
$result = $stmt->get_result();
$expenses = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();


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
        case 'last_year':
            return 'Ostatni rok';
        case 'custom':
            return '';
        case 'all_time':
            return 'Cały czas';
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
  $start_date = $_SESSION['custom_start_date'] ?? 'brak daty początkowej';
  $end_date = $_SESSION['custom_end_date'] ?? 'brak daty końcowej';
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
  <!-- Charts -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
              <td><?php echo htmlspecialchars($income['category']); ?></td>
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
              <td><?php echo htmlspecialchars($expense['category']); ?></td>
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
            <h2 class="heading">Wykres: Przychody</h2>
            <div class="chart-container">
              <canvas id="incomeChart"></canvas>
            </div>
            <h2 class="heading">Wykres: Wydatki</h2>
            <div class="chart-container">
              <canvas id="expensePolarChart" height="1000"></canvas>
            </div>
        </section>
      </div>

    </div>

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

    // Przekształcenie danych PHP na format JavaScript
    var incomeCategories = <?php echo json_encode(array_column($incomes, 'category') ?: []); ?>;
    var incomeAmounts = <?php echo json_encode(array_column($incomes, 'amount') ?: []); ?>; 
    
    var expenseCategories = <?php echo json_encode(array_column($expenses, 'category') ?: []); ?>;
    var expenseAmounts = <?php echo json_encode(array_column($expenses, 'amount') ?: []); ?>;

    // Wykres dla przychodów
    var incomeCtx = document.getElementById('incomeChart').getContext('2d');
    new Chart(incomeCtx, {
      type: 'bar',
      data: {
        labels: incomeCategories,
        datasets: [{
          label: 'Przychody',
          data: incomeAmounts,
          backgroundColor: 'rgba(54, 162, 235, 0.6)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: { beginAtZero: true }
        }
      }
    });

    // Funkcja generująca losowy kolor RGBA
    function getRandomColor(opacity = 0.6) {
        const r = Math.floor(Math.random() * 255);
        const g = Math.floor(Math.random() * 255);
        const b = Math.floor(Math.random() * 255);
        return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    }

    // Tworzenie tablicy kolorów na podstawie liczby kategorii
    var backgroundColors = expenseCategories.map(() => getRandomColor());

     // Wykres Polar Area Chart dla wydatków
     var expensePolarCtx = document.getElementById('expensePolarChart').getContext('2d');
      new Chart(expensePolarCtx, {
      type: 'polarArea',
      data: {
        labels: expenseCategories,
        datasets: [{
          label: 'Wydatki',
          data: expenseAmounts,
          backgroundColor: backgroundColors,
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });
  
  </script>


</body>

</html>