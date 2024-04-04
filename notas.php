<?php
session_start();

// Verificar si hay una sesión activa
if (!isset($_SESSION["username"])) {
    // Si no hay una sesión activa, redirigir al usuario al formulario de inicio de sesión
    header("Location: index.php");
    exit;
}

$username = $_SESSION["username"];

// Manejar la lógica para cerrar sesión
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logout"])) {
    // Destruir la sesión
    session_unset();
    session_destroy();
    // Redirigir al usuario al formulario de inicio de sesión
    header("Location: index.php");
    exit;
}

// Manejar la lógica para agregar y guardar notas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["title"], $_POST["note"])) {
    $title = trim($_POST["title"]);
    $noteContent = trim($_POST["note"]);

    // Verificar si el título y el contenido de la nota no están vacíos
    if (!empty($title) && !empty($noteContent)) {
        // Abrir el archivo de notas del usuario en modo de escritura (añadir al final)
        $file = fopen("notas/$username/notas.txt", "a");
        // Escribir el título y el contenido de la nota en el archivo
        fwrite($file, $title . PHP_EOL);
        fwrite($file, $noteContent . PHP_EOL . PHP_EOL); // Agregar una línea en blanco para separar las notas
        // Cerrar el archivo
        fclose($file);
    }
    // Redirigir al usuario a la página de notas
    header("Location: notas.php");
    exit;
}

// Función para obtener todas las notas del usuario
function getNotes($username) {
    $notes = [];
    if (file_exists("notas/$username/notas.txt")) {
        $lines = file("notas/$username/notas.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        for ($i = 0; $i < count($lines); $i += 2) {
            $title = $lines[$i];
            $content = isset($lines[$i + 1]) ? $lines[$i + 1] : '';
            $notes[] = [
                "title" => $title,
                "content" => $content
            ];
        }
    }
    return ($notes); // Ordena las notas para mostrar las más recientes arriba
}

// Función para eliminar una nota
function deleteNote($username, $index) {
    $notes = getNotes($username);
    unset($notes[$index]);
    // Reescribir el archivo de notas con las notas restantes
    $file = fopen("notas/$username/notas.txt", "w");
    foreach ($notes as $note) {
        fwrite($file, $note["title"] . PHP_EOL);
        fwrite($file, $note["content"] . PHP_EOL . PHP_EOL);
    }
    fclose($file);
}

// Función para editar una nota
function editNote($username, $index, $newTitle, $newContent) {
    $notes = getNotes($username);
    $notes[$index]["title"] = $newTitle;
    $notes[$index]["content"] = $newContent;
    // Reescribir el archivo de notas con la nota editada
    $file = fopen("notas/$username/notas.txt", "w");
    foreach ($notes as $note) {
        fwrite($file, $note["title"] . PHP_EOL);
        fwrite($file, $note["content"] . PHP_EOL . PHP_EOL);
    }
    fclose($file);
}

// Verificar si se ha enviado una solicitud de eliminación de nota
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_note"])) {
    $index = $_POST["delete_note"];
    deleteNote($username, $index);
    // Redirigir al usuario a la página de notas
    header("Location: notas.php");
    exit;
}

// Verificar si se ha enviado una solicitud de edición de nota
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_note"], $_POST["edited_title"], $_POST["edited_content"])) {
    $index = $_POST["edit_note"];
    $editedTitle = trim($_POST["edited_title"]);
    $editedContent = trim($_POST["edited_content"]);
    if (!empty($editedTitle) && !empty($editedContent)) {
        editNote($username, $index, $editedTitle, $editedContent);
        // Redirigir al usuario a la página de notas
        header("Location: notas.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BlackNote</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-zFndTsFVS92uY2F0tTKTmzHx0BIq0W5zxurD4j5WUvPE5Q3kY4p3f8aOlW3ZO+n2" crossorigin="anonymous">
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #000;
    color: #fff;
  }
  
  #container {
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background-color: #222;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    position: relative;
  }
  
  h3 {
    text-align: center;
  }
  
  .logout-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
  }

   #add-note-btn {
    background-color: #4CAF50;
    margin-bottom: 0px;
    color: white;
    border: none;
    width: 50px; /* Ancho del botón */
    height: 50px; /* Alto del botón */
    border-radius: 50%; /* Hacer que el borde del botón sea redondo */
    cursor: pointer;
    margin-bottom: 10px;
    transition: background-color 0.3s; /* Agregar una transición para suavizar el cambio de color */
    font-size: 24px; /* Tamaño del ícono "+" */
    display: flex; /* Alinear contenido al centro vertical y horizontalmente */
    justify-content: center; /* Alinear contenido horizontalmente */
    align-items: center; /* Alinear contenido verticalmente */
  }

  #add-note-btn:active {
    background-color: #3e8e41; /* Cambiar el color de fondo cuando se presiona el botón */
    outline: none; /* Eliminar el contorno al presionar */
  }

  #note-form {
    display: none; /* Ocultar el formulario inicialmente */
  }

  #note-title,
  #note-content {
    width: calc(100% - 20px); /* Restar el padding de 10px a cada lado */
    padding: 10px;
    margin-bottom: 10px;
    border: none;
    border-radius: 5px;
    background-color: #444;
    color: #fff;
    resize: none; /* Evitar redimensionamiento */
    overflow: hidden; /* Ocultar el desbordamiento */
    box-sizing: border-box; /* Incluir el padding en el cálculo del tamaño */
    word-wrap: break-word; /* Romper las palabras largas */
  }

  .save-btn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
  }

  .note {
    margin-bottom: 10px;
    padding: 10px;
    background-color: #333;
    border-radius: 5px;
    position: relative;
    overflow-wrap: break-word;
    word-wrap: break-word;
  }

  .logout-btn {
    font-size: 6px; /* Tamaño de fuente más pequeño */
    /* Otros estilos si los necesitas */
  }

  .note-title {
    font-weight: bold;
    margin-bottom: 5px;
  }

  .note-options {
    position: absolute;
    top: 5px;
    right: 5px;
  }

  .note-options button {
    background-color: #333;
    color: #fff;
    border: none;
    padding: 5px;
    border-radius: 3px;
    cursor: pointer;
    margin-left: 5px;
  }
</style>
</head>
<body>

<div id="container">
  <h3>Bienvenido a BlackNote, <?= $_SESSION["username"] ?></h3>
  <form action="" method="post" onsubmit="return confirm('¿Estás seguro de que deseas cerrar sesión, <?= $_SESSION["username"] ?>?');">
    <button type="submit" name="logout" class="logout-btn">Cerrar Sesión</button>
</form>

  <button id="add-note-btn">+</button>
  <form id="note-form" action="" method="post">
    <input type="text" id="note-title" name="title" placeholder="Título" required>
    <textarea id="note-content" name="note" placeholder="Escribe tu nota aquí" required></textarea>
    <button type="submit" class="save-btn">Guardar</button>
  </form>

  <div id="notes-container">
    <?php
      // Aquí deberías tener el código PHP para obtener las notas

// Obtener todas las notas del usuario
$notas = getNotes($username);

// Verificar si no hay notas disponibles
if (empty($notas)) {
    echo "<p>No hay notas disponibles para mostrar.</p>";
} else {
    // Si hay notas disponibles, mostrarlas
    foreach ($notas as $indice => $nota):
        // Mostrar la nota en HTML
        // ...
    endforeach;
}


      $notas = getNotes($username);
      foreach ($notas as $indice => $nota):
    ?>
    <div class="note">
      <p class="note-title"><?= $nota['title'] ?></p>
      <p><?= $nota['content'] ?></p>
      <div class="note-options">
        <button class="delete-btn" data-index="<?= $indice ?>"><span style="color: red;">Eliminar</span></button>
      </div>
      <form id="delete-note-<?= $indice ?>" action="" method="post" style="display: none;">
        <input type="hidden" name="delete_note" value="<?= $indice ?>">
      </form>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
  document.getElementById("add-note-btn").addEventListener("click", function() {
    document.getElementById("note-form").style.display = "block";
  });

  var deleteButtons = document.querySelectorAll(".delete-btn");

  deleteButtons.forEach(function(button) {
    button.addEventListener("click", function() {
      var index = this.getAttribute("data-index");
      if (confirm("¿Estás seguro de que deseas eliminar esta nota, <?= $_SESSION["username"] ?>?")) {
        document.getElementById("delete-note-" + index).submit();
      }
    });
  });
</script>

</body>
</html>
