<?php
session_start();

// Verificar si hay una sesión activa
if (isset($_SESSION["username"])) {
    // Si hay una sesión activa, redirigir al usuario a la página de notas
    header("Location: notas.php");
    exit;
}

// Verificar si se envió el formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"], $_POST["password"])) {
    // Obtener los datos del formulario
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Leer el archivo usuarios.json
    $usuarios = [];
    if (file_exists("usuarios.json")) {
        $usuarios = json_decode(file_get_contents("usuarios.json"), true);
    }

    // Verificar si el nombre de usuario existe en el archivo
    if (array_key_exists($username, $usuarios)) {
        // Verificar si la contraseña es correcta
        if (password_verify($password, $usuarios[$username]["password"])) {
            // Iniciar sesión
            $_SESSION["username"] = $username;
            // Redirigir al usuario a la página de notas
            header("Location: notas.php");
            exit;
        } else {
            // Contraseña incorrecta
            $error = "Contraseña incorrecta.";
        }
    } else {
        // Nombre de usuario no encontrado
        $error = "Nombre de usuario no encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlackNote</title>
    <style>
        /* Estilos CSS aquí */
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
.error {
    color: red;
}

        .container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            background-color: #222;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .input-container {
            margin-bottom: 20px;
            position: relative;
        }

        .input-container input {
            width: 100%;
            padding: 10px;
            background-color: transparent;
            border: none;
            border-bottom: 2px solid #fff;
            color: #fff;
            outline: none;
        }

        .input-container input:focus {
            border-color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Inicia Sesión en BlackNote</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="index.php" method="post">
            <div class="input-container">
                <label for="username">Usuario:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="input-container">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required>
                <span class="password-toggle" onclick="togglePassword()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path fill="none" d="M0 0h24v24H0z"/>
                        <path d="M12 4c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9-4.03-9-9-9zm1 13c-.56 0-1.1-.08-.16-.16l2.81-2.81c-.42-.56-.81-1.15-1.16-1.76l-2.8 2.81c-.08.08-.16-.08-.16-.16l2.8-2.81c-1.15-1.61-2.32-2.96-2.82-4.55l1.57-1.57c1.59.5 2.94 1.67 4.55 2.82l2.81-2.8c.08-.08.08-.16.16-.16l2.81-2.81c-.61-.35-1.2-.75-1.76-1.16L13.16 5.16c-.08 0-.24 0-.16-.16l2.81-2.81c.08-.08.16-.08.16-.16L14.01.16c.08-.08 0-.24-.16-.16L10.04 3.99c-1.59-.5-2.94-1.67-4.55-2.82L5.48 2.57c.5 1.59 1.67 2.94 2.82 4.55l-1.57 1.57c-1.6-.5-2.96-1.67-4.55-2.82L2.21 9.04c-.08.08-.08.16-.16.16l-2.81 2.81c.61.35 1.2.75 1.76 1.16l2.8-2.81c.08 0 .16 0 .16.16L4.84 14c1.15 1.61 2.32 2.96 2.82 4.55l-1.57 1.57c-1.59-.5-2.94-1.67-4.55-2.82L2.
                </svg>
                </span>
 <button type="submit">Iniciar Sesión</button>
            </div>
<button type="submit">Iniciar Sesión</button>
        </form>
        <p>¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a>.</p>
    </div>

    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("password");
            var icon = document.querySelector(".password-toggle svg");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.setAttribute("fill", "#4CAF50");
            } else {
                passwordInput.type = "password";
                icon.setAttribute("fill", "#fff");
            }
        }
    </script>
</body>
</html>
