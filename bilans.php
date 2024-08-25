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

?>