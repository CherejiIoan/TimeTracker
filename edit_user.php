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

    if (isset($_POST['delete']) && !empty($_POST['delete'])) {

        $id = $_POST['id'];

        $query = "DELETE FROM users WHERE id='$id'";

        $result = mysqli_query($conn, $query);

        header("Location: dashboard.php");
    } else {
        if (isset($_POST['user_update'])) {
            if (
                validateInput([
                    'nume' => [
                        'required' => true,
                    ],
                    'mail' => [
                        'required' => true,
                    ]
                ])
            ) {
                $nume = $_POST['nume'];
                $mail = $_POST['mail'];
                $id = $_POST['id'];
                $depID = $_POST['departaments'];
    
                $query = "UPDATE users SET 
                name='$nume', 
                email='$mail', 
                departament_id='$depID' WHERE id='$id'";
    
                $result = mysqli_query($conn, $query);
    
                header("Location: /itschool/TimeTracker/edit_user.php?id=$id");
    
            } else {
                echo '<p style="color:red">Nu ati introdus datele corect!</p>';
                echo '<br>';
    
            }
        }
        
       
    }

}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['add_hours'] === "add_hours") {

    if (
        validateInput([
            'hours' => ['required' => true],
            'category' => ['required' => true]
        ])
    ) {
        
        $user_mail = $_SESSION['email'];
        $sql = "SELECT id FROM users WHERE email = '$user_mail'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $user_id = $row['id'];

        
        // colectam din formular datele
        $hours = $_POST['hours'];
        $category_id = $_POST['category'];
        $date_work = $_POST['date_work'];

        $selectQuery = "SELECT * FROM categories WHERE id = '$category_id'";
        $result = mysqli_query($conn, $selectQuery);
        $category = mysqli_fetch_assoc($result);
        $category_name = $category['name'];

        $department_id = $category['department_id'];
        $selectQuery = "SELECT * FROM departaments WHERE id = '$department_id'";
        $result = mysqli_query($conn, $selectQuery);
        $department = mysqli_fetch_assoc($result);
        $department_name = $department['name'];

        $date = date('Y-m-d H:i:s');

        // Verifica daca data introdusa este mai mica sau egala cu data curenta
          if ($_POST['date_work'] > date("Y-m-d")) {
            $_POST['errors']['future_time'] = "Nu puteti introduce ore lucrate in viitor.";
          }

          // Verifica daca data introdusa este mai mare decat data curenta minus doua saptamani
          $max_date = date('Y-m-d', strtotime('-2 weeks'));
          if ($_POST['date_work'] < $max_date) {
            $_POST['errors']['future_time'] = "Nu puteti introduce ore lucrate cu mai mult de doua saptamani in urma.";
            
          }

        if (isset($_POST['add_hours']) && empty($_POST['errors'])) {
          $category_id = $_POST['category'];
          $hours = $_POST['hours'];
        
          // selectam totalul de ore pentru departamentul si categoria selectate
          $selectQueryTime = "SELECT SUM(timp) as total_time FROM user_activity WHERE departament IN (SELECT id FROM departaments WHERE id = (SELECT department_id FROM categories WHERE id = '$category_id')) AND categorie = '$category_id'";
          $total_time_result = mysqli_query($conn, $selectQueryTime);
          $total_time_row = mysqli_fetch_assoc($total_time_result);
          $total_time = $total_time_row['total_time'];
          $date = date('Y-m-d H:i:s');

        //   echo  $total_time;
          strtotime($hours);

          $time_str = $_POST['hours'];
          $today_date_str = date("Y-m-d"); // Data curentă
          $time_str_full = $today_date_str . " " . $time_str; // Combinăm data și ora
          $time_sec = strtotime($time_str_full) - strtotime($today_date_str); // Extragem numărul de secunde
         
          // echo "<br>"; 
          // echo "timp in secunde " .$time_sec;

          // verificam daca totalul de ore depaseste 8 ore
          if (($total_time +  $time_sec) > 28800) {
            
            $_POST['errors']['timp'] = "Nu se poate adauga timpul selectat, deoarece depaseste limita maxima de 8 ore.";
            
          } else {
              // adaugam timpul in baza de date
              $insertQueryTime = "INSERT INTO user_activity (user_id, departament, categorie, timp, data, date_work) VALUES ('$user_id', (SELECT department_id FROM categories WHERE id = '$category_id'), '$category_id', '$hours', '$date', '$date_work')";
              mysqli_query($conn, $insertQueryTime);
          }
        }

        //$query = "INSERT INTO user_activity (user_id, timp, categorie, departament, data) VALUES ('$user_id', '$hours', '$category_id', '$department_id', '$date')";
        //mysqli_query($conn, $query);
        // var_dump($user_mail);

        // header('Location: index.php');
        header("Location: /itschool/TimeTracker/edit_user.php?id=$user_id");
    }
}
$user_mail = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email = '$user_mail'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$user_id = $row['id'];
$sql = "SELECT * FROM user_activity WHERE user_id = '$user_id' AND data BETWEEN DATE_SUB(NOW(), INTERVAL 1 WEEK) AND NOW() AND WEEKDAY(data) != 3 ORDER BY data DESC";
$result = mysqli_query($conn, $sql);

?>



<html>
<head>
<style>
        td, th {
            padding: 5px 10px;
        }
    </style>
</head>
<body>
<?php require('templates/nav-admin.template.php')?>
    <br>
    <br>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
        <input name="user_update" value="<?php echo $users['id'] ?>" hidden />
        <input name="id" value="<?php echo $users['id'] ?>" hidden />
        <label>Nume Utilizator:</label>
        <br>
        <input type="text" name="nume" value="<?php echo $users['name'] ?>" />
        <br>
        <br>
        <label>Adresa de mail:</label>
        <br>
        <input type="email" name="mail" value="<?php echo $users['email'] ?>" />
        <br>
        <br>
        <label>Departament</label>
        <br>
        <select name="departaments" id="dep-select">
    <?php
    $selectQuery = "SELECT * FROM departaments";
    $deparaments = mysqli_query($conn, $selectQuery); 

    while ($departament = mysqli_fetch_assoc($deparaments)) :
        $depId = $departament['id'];
    ?>
    <option value="<?php echo $depId; ?>" <?php if ($depId === $users['departament_id']) echo 'selected'; ?>>
        <?php echo $departament['name']; ?>
    </option>
    <?php endwhile; ?>
</select>
           
        <br>
        <br>
      
        <input type="submit" value="Actualizeaza user-ul" />

    </form>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <input type="text" name="delete" value="yes" hidden />
        <input name="id" value="<?php echo $users['id'] ?>" hidden />
        <p>Sterge user-ul</p>
        <input type="submit" value="Sterge">
    </form><br><br><hr>
    <h2>Activitatea utilizatorului <?php echo $users['name'] ?></h2>
    <table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Categorie</th>
      <th>Departament</th>
      <th>Timp</th>
      <th>Data</th>
      <th>Data inregistrarii</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($result as $row): ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td>
          <?php
            $category_id = $row['categorie'];
            $sql_category = "SELECT name FROM categories WHERE id = '$category_id'";
            $result_category = mysqli_query($conn, $sql_category);
            $category = mysqli_fetch_assoc($result_category);
            echo $category['name'];
          ?>
        </td>
        <td>
          <?php
            $department_name = "";
            $category_id = $row['categorie'];
            $sql_department = "SELECT department_id FROM categories WHERE id = '$category_id'";
            $result_department = mysqli_query($conn, $sql_department);
            $department_id = mysqli_fetch_assoc($result_department)['department_id'];
            $sql_department_name = "SELECT name FROM departaments WHERE id = '$department_id'";
            $result_department_name = mysqli_query($conn, $sql_department_name);
            $department = mysqli_fetch_assoc($result_department_name);
            echo $department['name'];
          ?>
        </td>
        <td><?php echo $row['timp']; ?></td>
        <td><?php echo $row['date_work']; ?></td>
        <td><?php echo $row['data']; ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table><br><br>
<h4>Adaugă activitate</h4>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">

            <input type="text" value="add_hours" name="add_hours" hidden>
            <label for="department">Adauga <input type="time" id="hours" name="hours" min="1" required> ore pentru categoria (din departamentul)</label>
            <select id="category" name="category">
                <?php
                 $category_id = $_POST['category'];
                 $hours = $_POST['hours'];
                $selectQueryCat = "SELECT * FROM categories";
                $categories = mysqli_query($conn, $selectQueryCat);
                foreach ($categories as $category) {
                    $department_id = $category['department_id'];
                    $selectQueryDep = "SELECT name FROM departaments WHERE id = '$department_id'";
                    $department_result = mysqli_query($conn, $selectQueryDep);
                    $department = mysqli_fetch_assoc($department_result);
                    $department_name = $department['name'];

                    $category_name = $category['name'];

                    echo "<option value='{$category['id']}'>$category_name ($department_name)</option>";
                }
                ?>
            </select>
            <label> lucrate in data <input type="date" name="date_work">.</label>
            <input type="submit" value="Adaugă orele">
        </form><br><br><br>

</body>
<?php require('templates/footer.template.php')?>

</html>