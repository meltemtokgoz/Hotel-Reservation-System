<?php
// veritabanı bağlantımızı yaptık
include('db.php');
// veritabanı bağlantısı sağlanmaz ise hata verdirdik
if (mysqli_connect_errno()) {
    echo "MySQL bağlantısı başarısız: " . mysqli_connect_error();
}

$getImage = $_GET["image"];

session_start();
$email = $_SESSION['email'];
$sql = "SELECT * FROM person WHERE email='$email'";
$result = mysqli_query($baglanti, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$id = $row["id"];

$sql2 = "";
if ($row["p_role"] == "manager") {
    $sql = "SELECT * FROM manager WHERE person_id = $id";
    $result = mysqli_query($baglanti, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    $hotel_id = $row["hotel_id"];
    $sql2 = "SELECT * FROM manager m, hotel h WHERE m.hotel_id = h.id AND m.hotel_id = $hotel_id";
} else if ($row["p_role"] == "employee") {
    $sql = "SELECT * FROM employee WHERE person_id = $id";
    $result = mysqli_query($baglanti, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    $hotel_id = $row["hotel_id"];
    $sql2 = "SELECT * FROM employee e, hotel h WHERE e.hotel_id = h.id AND e.hotel_id = $hotel_id";
}

$result2 = mysqli_query($baglanti, $sql2);
$row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);

$hotel_name = $row2["name"];
?>

<html>

<head>
    <title>Add Room Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" , charset="utf-8">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>



<body>

    <body>
        <?php
        session_start();
        include('navbar-employees.php');
        ?>

        <div class="w3-cell-row">
            <div class="w3-container">
                <h3 style="width:100%; margin: auto; margin-top: 50">Sorry, only JPG, JPEG, PNG & GIF files are allowed.</h3>

                <?php
                if (isset($_POST["submit"])) {
                    $image = "";

                    $target_dir = "C:\AppServ\www\css\image\img-";
                    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
                    $uploadOk = 1;
                    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                    // Check if image file is a actual image or fake image
                    if (isset($_POST["submit"])) {
                        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                        if ($check !== false) {
                            echo "File is an image - " . $check["mime"] . ".";
                            $uploadOk = 1;
                        } else {
                            echo "File is not an image.";
                            $uploadOk = 0;
                        }
                    }
                    // Check if file already exists
                    if (file_exists($target_file)) {
                        echo "Sorry, file already exists.";
                        $uploadOk = 0;
                    }
                    // Check file size
                    if ($_FILES["fileToUpload"]["size"] > 500000) {
                        echo "Sorry, your file is too large.";
                        $uploadOk = 0;
                    }
                    // Allow certain file formats
                    if (
                        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                        && $imageFileType != "gif"
                    ) {
                        $uploadOk = 0;
                    }
                    // Check if $uploadOk is set to 0 by an error
                    if ($uploadOk == 0) {
                        echo "Sorry, Your file is not available for upload.";
                        // if everything is ok, try to upload file
                    } else {
                        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                            $image = basename($_FILES["fileToUpload"]["name"]);
                            echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
                            header("Location: add-room.php?image=$image");
                        } else {
                            echo "Sorry, there was an error uploading your file.";
                        }
                    }
                }
                if (isset($_POST['save'])) {
                    // Register Sayfasından Gelen Bilgileri Değişkenlere Aktarma.
                    $info = $_POST['info'];
                    $price = $_POST['price'];
                    $number = $_POST['number'];
                    $status = $_POST['status'];
                    $capacity = $_POST['capacity'];
                    $feature = $_POST['feature'];
                    $name = $row2["name"];
                    $type = $_POST['type'];


                    $info =  trim($info);
                    $price =  trim($price);
                    $number = trim($number);
                    $status =  trim($status);
                    $capacity = trim($capacity);
                    $feature =  trim($feature);
                    $name =  trim($name);
                    $type =  trim($type);

                    // Kayıt İşlemi
                    // call addroom(:room_info, :room_price, :room_number, :status, :capacity, :image, :feature, :hotel_name, :room_type)
                    $kayit = "CALL addroom('$info','$price','$number','available','$capacity','$getImage','$feature','$name','$type')";
                    echo "CALL addroom('$info','$price','$number','available','$capacity','$getImage','$feature','$name','$type')";

                    $sonuc = mysqli_query($baglanti, $kayit);

                    if ($sonuc) {
                        echo "<script>alert('Success!');</script>";
                        header("Location: profile-employees.php");
                    } else {
                        echo "<script>alert('All Field Are Required!');</script>";
                    }
                }

                ?>

                <form method="post" enctype="multipart/form-data">
                    Select image to upload:
                    <input type="file" name="fileToUpload" id="fileToUpload">
                    <input type="submit" name="submit" id="submit" value="Upload Image">
                </form>
            </div>
        </div>

        <div class="w3-cell-row" style="margin: 10px">
            <div class="w3-container w3-red w3-cell" style="width: 30%; background: linear-gradient(to right, #6190e8, #a7bfe8);">
                <h2 style="width:80%; margin-top: 10; margin-left: 70; color:#243b55"><b>Add Room</b></h2>
                <div class="newForm">
                    <form method="post">
                        <img src="css/image/img-<?php echo $getImage ?>" alt="Room Image" style="width:100%">
                        <input type="text" name="info" id="info" placeholder="Room Info" />
                        <input type="number" style="width: 100%; margin-top: 10" name="price" id="price" placeholder="Room Price" />
                        <input type="number" style="width: 100%; margin-top: 10" name="number" id="number" placeholder="Room Number" />
                        <input type="number" style="width: 100%; margin-top: 10" name="capacity" id="capacity" min="1" max="10" placeholder="Capacity" />
                        <input type="text" name="feature" id="feature" placeholder="Feature" />
                        <input type="text" name="name" id="name" placeholder="<?php echo $hotel_name ?>" disabled />
                        <h3>Room Type</h3>
                        <label class="newContainer">Standart
                            <input type="radio" name="type" id="standart" value="standart">
                            <span class="newCheckmark"></span>
                        </label>
                        <label class="newContainer">Special
                            <input type="radio" name="type" id="special" value="special">
                            <span class="newCheckmark"></span>
                        </label>
                        <input type="submit" name="save" id="save" value="SAVE">
                    </form>
                </div>
            </div>
        </div>
    </body>

</html>