<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload</title>
</head>
<body>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
    <label for="images">Images</label>
        <input type="file" name="images[]" multiple id="images">
        <input type="submit" value="Upload">
    </form>
    <?php 
        session_start();
        if(isset($_SESSION['error']) && !empty($_SESSION['error']))
            echo $_SESSION['error'];
        unset($_SESSION['error']);
    ?>
    <div style="display:flex; flex-wrap:wrap">
    <?php 
        $iterator = new FilesystemIterator('uploads'); 
        foreach ($iterator as $file) {
            ?>
            <div>
                <figure>
                    <img width="500px" src="<?php echo $file->getPathname(); ?>" />
                    <figcaption><?php echo $file->getFilename()?></figcaption>
                </figure>
                <form action="delete.php" method="POST">
                    <input type="hidden" name="path" value="<?php echo $file->getPathname(); ?>">
                    <input type="submit" value="Supprimer">
                </form>
            </div>
            <?php
        }
    ?>
    </div>
</body>
</html>

<?php
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $allowedMime = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif' ];
    $sizeMax = 1000000;
    $info_files = $_FILES['images'];

    try {
        foreach ($info_files['name'] as $position => $file_name) {
            if (!in_array($info_files['type'][$position], $allowedMime)) {
                throw new Exception("Le fichier " . $file_name. " n'est pas du bon format. <br/>");
            }
            if ($info_files['size'][$position] > $sizeMax) {
                throw new Exception("Le fichier " . $file_name. " est trop lourd : ".$info_files['size'][$position] . "($sizeMax Octets MAX)  <br/>");
            }
            //Upload le fichier
            $uploadDir = "uploads/";
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $fileNameUpload = uniqid().'.'.$extension;
            $uploadFile = $uploadDir . $fileNameUpload;
            move_uploaded_file($info_files['tmp_name'][$position], $uploadFile);
        }
        header('Location: upload.php');
    }catch (Exception $e) {
        session_start();
        $_SESSION['error'] = $e->getMessage();
        header('Location: upload.php');
    }
}

    