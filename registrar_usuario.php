<?php
// Verifica si se han enviado datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera los datos del formulario
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    // Crea un array asociativo con los datos del nuevo usuario
    $newUser = array(
        "username" => $username,
        "password" => password_hash($password, PASSWORD_DEFAULT) // Hash de la contraseña
    );
    
    // Convierte el array a formato JSON
    $jsonData = json_encode($newUser);
    
    // Guarda los datos en un archivo de texto
    file_put_contents("usuarios.json", $jsonData . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    // Redirige al usuario a una página de confirmación o a donde prefieras
    header("Location: registro_exitoso.php");
    exit;
}
?>
