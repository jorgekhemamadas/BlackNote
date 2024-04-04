<?php
// Verifica si se han enviado datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera los datos del formulario
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    // Lee el archivo de usuarios y lo convierte a un array
    $users = file("usuarios.json", FILE_IGNORE_NEW_LINES);
    
    // Busca el usuario en el array de usuarios
    foreach ($users as $userData) {
        $userDataArray = json_decode($userData, true);
        if ($userDataArray["username"] === $username && password_verify($password, $userDataArray["password"])) {
            // Usuario y contraseña coinciden, permite el acceso
            session_start();
            $_SESSION["username"] = $username;
            header("Location: notas.php");
            exit;
        }
    }
    
    // Si llega aquí, el usuario no fue encontrado o las credenciales son incorrectas
    header("Location: login.php?error=Usuario o contraseña incorrectos");
    exit;
}
?>
