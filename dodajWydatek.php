<?php
session_start();

// Sprawdzanie, czy użytkownik jest zalogowany
if (!isset($_SESSION['id'])) {
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
$amount = $_POST['number'];
$date = $_POST['date'];
$expenseKind = $_POST['expenseKind'];
$category = $_POST['category'];
$cautions = $_POST['cautions'];

// Sprawdzenie, czy amount jest typu float
if (!is_numeric($amount)) {
    echo "Amount must be a number.";
    exit();
}
$amount = floatval($amount);

// Przygotowanie zapytania
$stmt = $connection->prepare("INSERT INTO expenses (userId, amount, expenseKind, category, date, cautions) VALUES (?, ?, ?, ?, ?, ?)");

// Sprawdzenie, czy przygotowanie zapytania powiodło się
if ($stmt === false) {
    die("Prepare failed: " . $connection->error);
}

// Bindowanie parametrów
$stmt->bind_param("idssss", $userId, $amount, $expenseKind, $category, $date, $cautions);

// Wykonanie zapytania
if ($stmt->execute()) {
    //echo "New record created successfully";
	$_SESSION['messageAdd'] = "Nowy wydatek został dodany";
} else {
    //echo "Error: " . $stmt->error;
	$_SESSION['messageAdd'] = "Błąd: " . $stmt->error;
}

// Zamknięcie zapytania i połączenia
$stmt->close();
$connection->close();

header('Location: uzytkownik.php');
exit();

?>