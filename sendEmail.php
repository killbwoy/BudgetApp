<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = htmlspecialchars($_POST['fullname']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $message = htmlspecialchars($_POST['message']);

    $to = "michal.kuta.programista.backup@gmail.com";

    $subject = "Nowa wiadomość z formularza kontaktowego";

    // Treść wiadomości
    $body = "Imię i nazwisko: $fullname\n";
    $body .= "Email: $email\n";
    $body .= "Telefon: $phone\n";
    $body .= "Wiadomość:\n$message";

    // Nagłówki
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";

    // Wysyłanie e-maila
    if (mail($to, $subject, $body, $headers)) {
        echo "Wiadomość została wysłana.";
    } else {
        echo "Niestety, wystąpił błąd przy wysyłaniu wiadomości.";
    }
} else {
    echo "Nieprawidłowa metoda wysyłki formularza.";
}
?>