<?php
session_start();

if (isset($_SESSION["czas"]) && $_SESSION["czas"] + 60*10 < time()) { // 10 minut
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
}
  $_SESSION["czas"] = time();

// Sprawdzanie, czy użytkownik jest zalogowany
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once "connect.php";

// Włączenie raportowania błędów MySQL
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $connection = new mysqli($host, $db_user, $db_password, $db_name);

    // Sprawdzenie połączenia
    if ($connection->connect_error) {
        throw new Exception("Connection failed: " . $connection->connect_error);
    }

    // Przypisanie zmiennych
    $userId = $_SESSION['id'];
    $amount = isset($_POST['number']) ? $_POST['number'] : null;
    $date = isset($_POST['date']) ? $_POST['date'] : null;
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;
    $categoryName = isset($_POST['category']) ? $_POST['category'] : null;
    $cautions = isset($_POST['cautions']) ? $_POST['cautions'] : '';

    // Sprawdzenie, czy amount jest typu float
    if (!is_numeric($amount)) {
        throw new Exception("Kwota musi być liczbą.");
    }
    $amount = floatval($amount);

    // Zabezpieczenie cautions przed XSS
    $cautions = htmlspecialchars($cautions, ENT_QUOTES, 'UTF-8');

    // Pobranie id kategorii z tabeli expenses_category_assigned_to_users
    $categoryStmt = $connection->prepare("SELECT id FROM expenses_category_assigned_to_users WHERE name = ? AND userId = ?");
    if (!$categoryStmt) {
        throw new Exception("Błąd zapytania kategorii: " . $connection->error);
    }

    $categoryStmt->bind_param("si", $categoryName, $userId);

    if($categoryStmt->execute()){
        $categoryStmt->store_result();

        //Sprawdzenie, czy kategoria została znaleziona
        if($categoryStmt->num_rows > 0){
            $categoryStmt->bind_result($categoryId);
            $categoryStmt->fetch();
        } else {
            throw new Exception("Nie znaleziono kategorii.");
        }
    }
    $categoryStmt->close();


    // Pobranie id metody płatności z tabeli payment_methods_assigned_to_users
    $paymentMethodStmt = $connection->prepare("SELECT id FROM payment_methods_assigned_to_users WHERE name = ? AND userId = ?");
    if (!$paymentMethodStmt) {
        throw new Exception("Błąd zapytania metody płatności: " . $connection->error);
    }

    $paymentMethodStmt->bind_param("si", $payment_method, $userId);

    if ($paymentMethodStmt->execute()) {
        $paymentMethodStmt->store_result();

        // Sprawdzenie, czy metoda płatności została znaleziona
        if ($paymentMethodStmt->num_rows > 0) {
            $paymentMethodStmt->bind_result($paymentMethodId);
            $paymentMethodStmt->fetch();
        } else {
            throw new Exception("Nie znaleziono metody płatności.");
        }
    }
    $paymentMethodStmt->close();    


    // Przygotowanie zapytania
    $stmt = $connection->prepare("INSERT INTO expenses (userId, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date, cautions) VALUES (?, ?, ?, ?, ?, ?)");

    // Bindowanie parametrów
    $stmt->bind_param("iisdss", $userId, $categoryId, $paymentMethodId, $amount, $date, $cautions);

    // Wykonanie zapytania
    if ($stmt->execute()) {
        $_SESSION['messageAdd'] = "Nowy wydatek został dodany";
    } else {
        throw new Exception("Błąd podczas dodawania wydatku: " . $stmt->error);
    }


    // Zamknięcie zapytania i połączenia
    $stmt->close();
    $connection->close();


} catch (Exception $e) {
    $_SESSION['messageAdd'] = "Błąd: " . $e->getMessage();

    // Logowanie błędu
    error_log("Error: " . $e->getMessage());

    // Przekierowanie na stronę użytkownika w przypadku błędu
    header('Location: uzytkownik.php');
    exit();
    }
}
    // Pobranie kategorii wydatków i metod platonsci z bazy danych
    // Połączenie z bazą danych
    require_once "connect.php";
    $connection = new mysqli($host, $db_user, $db_password, $db_name);

    // Sprawdzanie połączenia
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Pobranie kategorii wydatkow z bazy danych
    $categories = [];
    $sql = "SELECT name FROM expenses_category_assigned_to_users WHERE userId = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($categoryName);

    while ($stmt->fetch()) {
        $categories[] = $categoryName;
    }

    $stmt->close();

    // Pobranie kategorii płatności z bazy danych
    $paymentCategories = [];
    $sqlPayments = "SELECT name FROM payment_methods_assigned_to_users";
    $stmtPayments = $connection->prepare($sqlPayments); 
    $stmtPayments->execute();
    $stmtPayments->bind_result($paymentCategoryName);

    while ($stmtPayments->fetch()) {
        $paymentCategories[] = $paymentCategoryName;
    }

    $stmtPayments->close();
    $connection->close();

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
      <div class = "msgLogin">
        <?php
        if(isset($_SESSION['messageAdd']))
         {
         echo '<h4 class="alert alert-warning">'.$_SESSION['messageAdd'].'</h4>';
         unset($_SESSION['messageAdd']);
        }
        ?>
      </div>
        <div id="content">
          <div class="" id="expense-section">
            <div id="header">
              <h1>Dodaj wydatek</h1>
            </div>

            <div id="inside" class="my-form">
              <form action="dodajWydatek.php" method="POST">
                <p> Kwota <input type="number" name="number" id="inputNumber"></p>
                <p> Data <input type="date" name="date" id="inputDate"> </p>
                <p> Rodzaj płatności
                  <select name="payment_method">
                  <?php
                    foreach ($paymentCategories as $paymentCategory) {
                        echo "<option value='$paymentCategory'>$paymentCategory</option>";
                    }
                    ?>
                  </select>
                </p>
                <p> Kategoria
                  <select name="category">
                  <?php
                    foreach ($categories as $category) {
                        echo "<option value='$category'>$category</option>";
                    }
                    ?>
                  </select>
                </p>
                <p>
                  Uwagi <input type="text" name="cautions" id="inputText">
                </p>
                <p>
                  <input type="submit" value="Zapisz">
                  <input type="submit" value="Anuluj">
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
  <script>

    // Pobierz dzisiejszą datę
    const today = new Date().toISOString().split('T')[0];
    // Ustaw wartość pola input na dzisiejszą datę
    document.getElementById('inputDate').value = today;

  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>


</body>

</html>