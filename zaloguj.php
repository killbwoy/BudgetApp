 <?php
	
session_start();

// Sprawdzenie, czy dane zostały przesłane
if (!isset($_POST['username']) || !isset($_POST['password'])) {
    header('Location: index.php');
    exit();
}

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
        header('Location: index.php');
    }
} else {
    $_SESSION['errorLogin'] = "Login or password is invalid";
    header('Location: index.php');
}

// Zamknięcie połączenia i zapytania
$stmt->close();
$connection->close();
?>

		
