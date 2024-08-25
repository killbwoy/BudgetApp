<?php
session_start();

if ($_SESSION["czas"] and $_SESSION["czas"]+60*10<time()) { // 10 minut
    session_unset();
    session_destroy();
    header('Location: index.php');
  }
  $_SESSION["czas"] = time();

// Sprawdzanie, czy użytkownik jest zalogowany
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit();
}

require_once "connect.php";

// Włączenie raportowania błędów MySQL
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try{
    $connection = new mysqli($host, $db_user, $db_password, $db_name);

    // Sprawdzenie połączenia
    if ($connection->connect_error) {
        throw new Exception("Connection failed: " . $connection->connect_error);
    }

    // Przypisanie zmiennych
    $userId = $_SESSION['id'];
    $amount = $_POST['number'];
    $date = $_POST['date'];
    $categoryName = $_POST['category'];
    $cautions = $_POST['cautions'];

    // Sprawdzenie, czy amount jest typu float
    if (!is_numeric($amount)) {
        throw new Exception("Kwota musi być liczbą.");
    }
    $amount = floatval($amount);
    
    // Pobranie id kategorii z tabeli incomes_category_assigned_to_users
    $categoryStmt = $connection->prepare("SELECT id FROM incomes_category_assigned_to_users WHERE name = ? AND userId = ?");
    if (!$categoryStmt) {
        throw new Exception("Błąd zapytania kategorii: " . $connection->error);
    }

    $categoryStmt->bind_param("si", $categoryName, $userId);

    if($categoryStmt->execute()){
        $categoryStmt->store_result();

        //Spradzenie, czy kategoria została znaleziona
        if($categoryStmt->num_rows > 0){
            $categoryStmt->bind_result($categoryId);
            $categoryStmt->fetch();
        } else {
            throw new Exception("Nie znaleziono kategorii.");
        }
    }
    $categoryStmt->close();

    // Przygotowanie zapytania
    $stmt = $connection->prepare("INSERT INTO incomes (userId, amount, income_category_assigned_to_user_id, date, cautions) VALUES (?, ?, ?, ?, ?)");

    // Bindowanie parametrów
    $stmt->bind_param("idiss", $userId, $amount, $categoryId, $date, $cautions);

    // Wykonanie zapytania
    if ($stmt->execute()) {
        $_SESSION['messageAdd'] = "Nowy przychód został dodany";
    } else {
        throw new Exception("Błąd podczas dodawania przychodu: " . $stmt->error);
    }

    // Zamknięcie zapytania i połączenia
    $stmt->close();
    $connection->close();

    // Przekierowanie na stronę użytkownika
    header('Location: uzytkownik.php');
    exit();

} catch (Exception $e) {
    $_SESSION['messageAdd'] = "Błąd: " . $e->getMessage();

    // Logowanie błędu
    error_log("Error: " . $e->getMessage());

    // Przekierowanie na stronę użytkownika w przypadku błędu
    header('Location: uzytkownik.php');
    exit();
}

?>