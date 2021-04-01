if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $path = $_POST['path'];
    if (file_exists($path)) {
        unlink($path);
        header('Location: upload.php');
    }
}