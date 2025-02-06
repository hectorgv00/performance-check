<?php
// Start the session at the beginning of the script
session_start();

// Define valid credentials
$VALID_USERNAME = 'hectorgv00';
$VALID_PASSWORD = 'hectorgv00';

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy the session
    session_unset();
    session_destroy();
    // Redirect to the login form
    header("Location: index.php");
    exit;
}

// Handle Login Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    if ($username === $VALID_USERNAME && $password === $VALID_PASSWORD) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Usuario o contraseña inválidos.";
    }
}

$loggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Function to retrieve images from a folder
function getImages($folder)
{
    $images = [];
    if (is_dir($folder)) {
        $files = scandir($folder);
        foreach ($files as $file) {
            if (preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $file)) {
                $images[] = $file;
            }
        }
    }
    return $images;
}

// Define chart folders
$chartFolders = [
    'hourly' => 'img/hourly',
    'minute' => 'img/minute',
    'second' => 'img/second',
];

// Get selected chart type
$selectedType = isset($_GET['type']) && isset($chartFolders[$_GET['type']]) ? $_GET['type'] : 'hourly';
$images = $loggedIn ? getImages($chartFolders[$selectedType]) : [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>jocarsa | Tomato</title>
    <link rel="icon" href="./favicon.ico">
    <style>
        <?php include './styles/style.css'; ?>
    </style>

</head>

<body>
    <?php if ($loggedIn): ?>
        <!-- Header -->
        <div class="header">
            <div class="app-name">Performance check 🚀</div>
            <a href="index.php?action=logout" class="logout-button">Cerrar Sesión</a>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <h3>Gráficas</h3>
            <a href="index.php?type=hourly" class="<?php echo $selectedType === 'hourly' ? 'active' : ''; ?>">Última Hora</a>

        </div>

        <!-- Main Content -->
        <div class="content">
            <div class="dashboard">
                <?php if (!empty($images)): ?>
                    <?php foreach ($images as $image): ?>
                        <div class="image-card" onclick="openModal('<?php echo htmlspecialchars($chartFolders[$selectedType] . '/' . $image); ?>')">
                            <img src="<?php echo htmlspecialchars($chartFolders[$selectedType] . '/' . $image); ?>" alt="Chart">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay gráficas disponibles en la carpeta seleccionada.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- The Modal -->
        <div id="myModal" class="modal">
            <span class="close" onclick="closeModal()">&times;</span>
            <img class="modal-content" id="modalImage" alt="Chart Enlarged">
        </div>

        <!-- JavaScript for Modal -->
        <script>
            <?php include './scripts/script.js'; ?>
        </script>
    <?php else: ?>
        <div class="login-container-container">

            <div class="login-container">

                <h2>Performance check 🚀</h2>
                <?php if (isset($error)): ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form action="index.php" method="post">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" required placeholder="Ingresa tu usuario">

                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required placeholder="Ingresa tu contraseña">

                    <button type="submit" name="login">Entrar</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</body>

</html>