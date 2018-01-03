<?php
//connects with mongodb
function connectToDatabase() {
  $mongo = new MongoDB\Driver\Manager("mongodb://127.0.0.1:27017", array("username" => "myUserAdmin", "password" => "abc123", "db" => "wai"));
  $connection = $mongo;
  return $connection;
}
//disconnect from mongodb - since I'm using new mongodb driver this is probably unneccesary but
//I decided to leave it "just in case"
function disconnectFromDatabase(&$connection) {
  $connection = null;
}
//saves images in images folder (original one, thumbnail and image with watermark) and stores data
//about them in database (paths, information about author and info if image is public or private)
//$image is file ($_FILES['someidentifier'])
//$name is author name/login - type is string
//$title and $watermark are strings
//$public is boolean
function uploadImage($image, $name, $title, $watermark, $public) {
  //security checks
  $name = filter_var($name, FILTER_SANITIZE_STRING);
  $title = filter_var($title, FILTER_SANITIZE_STRING);
  $watermark = filter_var($watermark, FILTER_SANITIZE_STRING);
  $public = filter_var($public, FILTER_VALIDATE_BOOLEAN);
  if (!empty($name) && !empty($title) && !empty($watermark) && is_bool($public)) {
    if (strlen($name) <= 24 && strlen($title) <= 24 && strlen($watermark) <= 32) {
      //declaration of variables
      $dir =  dirname(__DIR__)."\\photo-gallery\\images\\";//when on linux use "/" instead of "\\"
      $file = $dir . basename($image["name"]);
      $connection = connectToDatabase();
      $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
      $uid = uniqid();
      $originalFilename = 'img_'.$uid.'-original.'.$ext;
      $filename = 'img_'.$uid.'.'.$ext;
      $thumbnail = 'thumb_'.$uid.'.'.$ext;
      $userId = isset($_SESSION["userId"]) ? $_SESSION["userId"] : null;
      //check if file is provided
      if (!isset($image["tmp_name"])) {
        return showErrorBox('Upload failed', 'No file has been selected');
      }
      //get info about filetype
      $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
      if (!empty($finfo) && !empty($image["tmp_name"])) {
        $fileType = finfo_file($finfo, $image["tmp_name"]);
      } else {
        return showErrorBox('Upload failed', 'No file has been selected or file is too big');
      }
      $fileType = finfo_file($finfo, $image["tmp_name"]);
      finfo_close($finfo);
      //check file format
      if ($fileType != 'image/png' && $fileType != 'image/jpg' && $fileType != 'image/jpeg') {
        return showErrorBox('Upload failed', 'Wrong format (only .jpg or .png is allowed)');
      }
      //check file's size
      if ($image["size"] > 1048576) {
        return showErrorBox('Upload failed', 'File is too big (cannot be bigger than 1MB)');
      }
      //if everything with the file is ok then save it with unique name into images folder
      move_uploaded_file($image["tmp_name"], $dir.$originalFilename);
      //create image from given file
      switch ($fileType) {
        case 'image/png':
          $im = imagecreatefrompng($dir.$originalFilename);
          $thumb = imagecreatefrompng($dir.$originalFilename);
          break;

        case 'image/jpg':
          continue;

        case 'image/jpeg':
          $im = imagecreatefromjpeg($dir.$originalFilename);
          $thumb = imagecreatefromjpeg($dir.$originalFilename);
          break;

        default:
          return showErrorBox('Upload failed', 'Wrong format (only .jpg or .png is allowed)');
          break;
      }
      //process images (add watermark and create thumbnail)
      if ($im) {
        $orange = imagecolorallocate($im, 220, 210, 60);
        $px = (imagesx($im) - 7.5 * strlen($watermark)) / 2;
        $py = imagesy($im) / 2;
        imagestring($im, 4, $px, $py, $watermark, $orange);
        imagejpeg($im, $dir.$filename);
        $thumb = imagescale($thumb, 200, 125);
        imagejpeg($thumb, $dir.$thumbnail, 99);
        //add document to database
        $doc = array(
          "_id" => (string) new MongoDB\BSON\ObjectId(),
          "author" => (object)array(
            "name" => $name,
            "userId" => $userId
          ),
          "image" => (object)array(
            "img_original_path" => '/photo-gallery/images/'.$originalFilename,
            "img_watermark_path" => '/photo-gallery/images/'.$filename,
            "thumb_path" => '/photo-gallery/images/'.$thumbnail,
            "title" => $title
          ),
          "public" => (boolean) $public,
          "addedAt" => date(DATE_ATOM)
        );
        $bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
        $bulk->insert( $doc );
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        try {
          $result = $connection->executeBulkWrite('wai.images', $bulk, $writeConcern);
        } catch (MongoDB\Driver\Exception\BulkWriteException $e) {
          // there was to little time to add better exception handling
          disconnectFromDatabase($connection);
          return showErrorBox('Upload failed', 'Error occured while saving to database');
        }
        disconnectFromDatabase($connection);
        //if image is uploaded succesfully redirect to gallery and send info about success
        header('location: /photo-gallery/index.php/gallery?success');
      } else {
        return showErrorBox('Upload failed', 'Error occured while processing image');
      }
    } else {
      return showErrorBox('Upload failed', 'Error while processing form data. Author, title or watermark is too long');
    }
  } else {
    return showErrorBox('Upload failed', 'Error while processing form data. All form fields are required');
  }
}
//get all images that are public or belong to currently logged userId
function getImages() {
  $connection = connectToDatabase();
  $filter = array('$or' => array(
            array("public" => true),
            array("author.userId" => $_SESSION["userId"])
          ));
  $options = array();
  $query = new \MongoDB\Driver\Query($filter, $options);
  $docs = $connection->executeQuery('wai.images', $query);
  disconnectFromDatabase($connection);
  return iterator_to_array($docs);
}
//show error box
function showErrorBox($eHead, $eMessage) {
  echo '<div class="alert">
    <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>
    <strong>'.$eHead.'!</strong> '.$eMessage.'!
  </div>';
}
//show success box
function showSuccessBox($text) {
  echo '<div class="success">
    <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>
    <strong>'.$text.'</strong>
  </div>';
}
//create new user account
function createNewUser($email, $login, $pass1, $pass2) {
  //sanitizing variables for security
  $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
  $login = filter_var(trim($login), FILTER_SANITIZE_STRING);
  $pass1 = filter_var(trim($pass1), FILTER_SANITIZE_STRING);
  $pass2 = filter_var(trim($pass2), FILTER_SANITIZE_STRING);
  //check if form is filled
  if (!empty($email) && !empty($login) && !empty($pass1) && !empty($pass2) && strlen($login) <= 24) {
    if ($pass1 === $pass2) {
      $connection = connectToDatabase();
      $filter = array('login' => $login);
      $options = array();
      $query = new \MongoDB\Driver\Query($filter, $options);
      $user = $connection->executeQuery('wai.users', $query);
      $user = iterator_to_array($user);
      if (!!!$user[0]) {
        $hash = password_hash($pass1, PASSWORD_BCRYPT);
        $doc = array(
          "_id" => (string) new MongoDB\BSON\ObjectId(),
          "email_adress" => (string) $email,
          "login" => (string) $login,
          "password" => $hash,
          "addedAt" => date(DATE_ATOM)
        );
        $bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
        $bulk->insert( $doc );
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        try {
          $result = $connection->executeBulkWrite('wai.users', $bulk, $writeConcern);
        } catch (MongoDB\Driver\Exception\BulkWriteException $e) {
          disconnectFromDatabase($connection);
          return showErrorBox('Register Failed', 'Error occured while saving to database');
        }
        disconnectFromDatabase($connection);
        loginUser($login, $pass1);
      } else {
        return showErrorBox('Register Failed', 'Username occupied');
      }
    } else {
      return showErrorBox('Register Failed', 'Password and repeated password must be identical');
    }
  } else {
    return showErrorBox('Register Failed', 'All fields are required');
  }
}
//login users
function loginUser($login, $pass) {
  $login = filter_var(trim($login), FILTER_SANITIZE_STRING);
  $pass = filter_var(trim($pass), FILTER_SANITIZE_STRING);
  $connection = connectToDatabase();
  $filter = array('login' => $login);
  $options = array();
  $query = new \MongoDB\Driver\Query($filter, $options);
  $user = $connection->executeQuery('wai.users', $query);
  $user = iterator_to_array($user);
  if (!!$user[0] && $user[0]->login == $login) {
    if (password_verify($pass, $user[0]->password)) {
      $_SESSION['userId'] = $user[0]->_id;
      $_SESSION['login'] = $user[0]->login;
      header('location: /photo-gallery/index.php?success');
    } else {
      return showErrorBox('Login Failed', 'Incorrect password');
    }
  } else {
    return showErrorBox('Login Failed', 'No such user registered');
  }
}
//logout and end current session_start
function logoutUser() {
  $_SESSION["userId"] = null;
  $_SESSION["login"] = null;
  session_destroy();
  header('location: /photo-gallery/index.php');
}
//save images to session
function saveSelectedImages($img, $link) {
  $_SESSION["saved"] = $img;
  header('location: '.$link);
}
//remove images from Favourites
function removeSelectedImages($images) {
  foreach ($images as $img) {
    if (($key = array_search($img, $_SESSION["saved"])) !== false) {
      array_splice($_SESSION["saved"], $key, 1);
    }
  }
  header("location: /photo-gallery/index.php/favourites");
}
//get images from base using _ids from session_start
function getSavedImages() {
  if (isset($_SESSION["saved"]) && count($_SESSION["saved"]) > 0) {
    $connection = connectToDatabase();
    $filter = array('_id' => array('$in' => $_SESSION["saved"]));
    $options = array();
    $query = new \MongoDB\Driver\Query($filter, $options);
    $docs = $connection->executeQuery('wai.images', $query);
    disconnectFromDatabase($connection);
    return iterator_to_array($docs);
  } else {
    header("location: /photo-gallery/index.php/gallery");
  }
}
//send images as an answer to ajax call
function fetchImages($q) {
  $connection = connectToDatabase();
  $filter = array('image.title' => array('$regex' => $regex = new MongoDB\BSON\Regex("^$q", 'i')));
  $options = array();
  $query = new \MongoDB\Driver\Query($filter, $options);
  $docs = $connection->executeQuery('wai.images', $query);
  disconnectFromDatabase($connection);
  return iterator_to_array($docs);
}
?>
