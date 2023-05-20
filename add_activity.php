<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once('includes/db.inc.php');
require_once('includes/validare.inc.php');
require_once('includes/generateInput.inc.php');

// indetificam userul
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        // cules date din baza de date pentru userul respectiv
        $id = $_GET['id'];
        $query = "SELECT * FROM users WHERE id='$id'";

        $result = mysqli_query($conn, $query);
        $users = mysqli_fetch_assoc($result);

    } else {
        // die('utilizator inexistent');
    }
}


if ($_SERVER['REQUEST_METHOD'] === "POST") {

    
        
        if (isset($_POST['add_activity'])) {
            if (
                validateInput([
                    'departaments' => [
                        'required' => true,
                    ],
                    'time' => [
                        'required' => true,
                    ]
                ])
            ) {
                $id = $_POST['id'];
                                //preia valorile din formular
                $user_id = $id; //sau orice altfel de mod doriti sa obtineti user_id-ul
                $department_id = $_POST['departaments'];
                $category_id = $_POST['categories'];
                $time = $_POST['time'];
                
                //insereaza valorile in baza de date
                $insertQuery = "INSERT INTO user_activity (user_id, department_id, category_id, time) VALUES ('$user_id', '$department_id', '$category_id', '$time')";
                
                mysqli_query($conn, $insertQuery);
    
                header("Location: /itschool/TimeTracker/edit_user.php?id=$id");
    
            } else {
                echo '<p style="color:red">Nu ati introdus datele corect!</p>';
                echo '<br>';
    
            }
        }
    }





?>



<html>

<body>
<?php require('templates/nav-admin.template.php')?>
    <br>
    <br>
   
  <hr>
    <h2>Activitatea utilizatorului <?php echo $users['name'] ?></h2>
    <?php
    
  
    $user_id = $id;
    // Query-ul pentru selectarea activității utilizatorului
    $sql = "SELECT * FROM user_activity WHERE id = $user_id ORDER BY timp DESC LIMIT 15";
    
    // Rulăm query-ul
    $result = $conn->query($sql);
    
    // Verificăm dacă avem rezultate
  
  
  
    if ($result->num_rows > 0) {
        // Afisăm lista de activități
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            
            $department_id = $row['departament'];
            $department_name_query = "SELECT name FROM departaments WHERE id = '$department_id'";
            $department_name_result = mysqli_query($conn, $department_name_query);
            $department_name = mysqli_fetch_assoc($department_name_result);

            $category_id = $row['categorie'];
            $category_name_query = "SELECT name FROM categories WHERE id = '$category_id'";
            $category_name_result = mysqli_query($conn, $category_name_query);
            $category_name = mysqli_fetch_assoc($category_name_result);

            echo "<li>A lucrat <strong>" . $row['timp'] . "</strong> in cadrul departamentului <strong>" . $department_name['name'] . "</strong> la <strong>" . $category_name['name'] . "</strong>.</li>";
        }
        echo "</ul>";
    } else {
        echo "Nu s-a găsit nicio activitate pentru acest utilizator.";
    }
    
    ?>
     <select name="departaments" id="dep-select">
            <?php
                $selectQuery = "SELECT * FROM departaments";
                $deparaments = mysqli_query($conn, $selectQuery); 
                $depName =$departament['name'];
                
                foreach ($deparaments as $departament) : ?>
                    <option value="<?php echo $departament['id'];?>" <?php if ($depName == $departament['name']) echo 'selected'; ?>><?php echo($departament['name']);?></option>
                <?php endforeach ?>
            
        </select><br><br>
<h4>Adaugă activitate</h4>
<form  method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
<input type="text" value="add_activity" hidden>
<label for="">Selecteaza activitatea:</label>
<select name="departaments" id="dep-select">
            <?php
             
                $selectQuery = "SELECT * FROM departaments";
                $deparaments = mysqli_query($conn, $selectQuery); 
                $depName =$departament['name'];
                
                $selectQuery = "SELECT * FROM categories";
                $categories = mysqli_query($conn, $selectQuery); 
                $catName =$category['name'];
                
                foreach ($categories as $category) : ?>
                    <option value="<?php echo $departament['id'];?>"><?php echo"" . $category['name'] . " (" . $departament['name'] . ")";?></option>
                <?php endforeach ?>


            
</select> <br>
<label for="">Ore lucrate</label>
<input type="time" value="time"><br><br>
<input type="submit" value="Adauga activitatea">
</form><br><br><br>

</body>
<?php require('templates/footer.template.php')?>

</html>