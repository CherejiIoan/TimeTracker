<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../main/utils/login.inc.php';
require_once('includes/db.inc.php');
require_once('includes/validare.inc.php');
require_once('includes/generateInput.inc.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['addDepartment'] === "addDepartment") {
    if (
        validateInput([
            'depName' => ['required' => true]
        ])
    ) {
        // colectam din formular datele
        $depName = $_POST['depName'];
        
        $query = "INSERT INTO departaments (name) VALUES ('$depName');";
        mysqli_query($conn, $query);

        header('Location: dashboard.php');

    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['addCat'] === "addCat") {
    if (
        validateInput([
            'catName' => ['required' => true],
            'dep_select' => ['requred' => true]
        ])
    ) {
        // colectam din formular datele
        $catName = $_POST['catName'];
        $cat_select = $_POST['dep_select'];
        
        $query = "INSERT INTO categories (name, department_id) VALUES ('$catName','$cat_select');";
        mysqli_query($conn, $query);

        header('Location: dashboard.php');

    }
}


?>

<html>
<head>
    <title>Time Tracker - ADMIN</title>
</head>
<body>
<?php require('templates/nav-admin.template.php') ?>

<div class="section">
    <h2>Departamente</h2>
    <ul>
    <?php
    $selectQuery = "SELECT * FROM departaments";
    $deparaments = mysqli_query($conn, $selectQuery); 
    foreach ($deparaments as $departament) : ?>
     <li><?php echo($departament['name']);?></li>
    <?php endforeach ?>
    </ul>
    <br>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
        <input type="text" value="addDepartment" name="addDepartment" hidden>
        <label for="">Adauga un departament nou: </label>
        <input type="text" name="depName" placeholder="Denumirea departamentului">
        <input type="submit" value="Adauga">
    </form>
    <br>
</div>

<div class="section">
    <h2>Categorii departamente</h2>
    <table>
        <tr>
            <th>Departament</th>
            <th>Categorie</th>
        </tr>
        <?php
            $selectQueryDep = "SELECT * FROM departaments";
            $deparaments = mysqli_query($conn, $selectQueryDep);
            $selectQueryCat = "SELECT * FROM categories";
            $categories = mysqli_query($conn, $selectQueryCat); 
            foreach ($categories as $category) : ?>
            <tr>
                <td><?php 
                $department_id=$category['department_id'];
                $department_name_query = "SELECT name FROM departaments WHERE id = '$department_id'";
                $department_name_result = mysqli_query($conn, $department_name_query);
                $department_name = mysqli_fetch_assoc($department_name_result);
                if (isset($department_name['name']) && !empty($department_name['name'])) {
                    echo $department_name['name'];
                   }?></td>
                <td><?php echo($category['name'])?></td>
            </tr>
            <?php endforeach ?>

    </table>
    <br>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
        <input type="text" value="addCat" name="addCat" hidden>
        <label for="">Adauga o categorie noua: </label>
        <input type="text" name="catName" placeholder="Denumirea categoriei">
        <label for=""> in departamentul: </label>
        <select name="dep_select" id="depSelect">
            <?php
                $selectQuery = "SELECT * FROM departaments";
                $deparaments = mysqli_query($conn, $selectQuery); 
                $depName =$departament['name'];
                
                foreach ($deparaments as $departament) : ?>
                    <option value="<?php echo $departament['id'];?>" <?php if ($depName == $departament['name']) echo 'selected'; ?>><?php echo($departament['name']);?></option>
                <?php endforeach ?>
        </select>
        <input type="submit" value="Adauga">
    </form>
</div>
<div class="section">
    <h2>Utilizatori</h2>
    <table>
        <tr>
            <th>Nume</th>
            <th>Email</th>
            <th>Departament</th>
            <th></th>
        </tr>
        <?php
            $selectQueryUsers = "SELECT * FROM users";
            $users = mysqli_query($conn, $selectQueryUsers);
            foreach ($users as $user) : ?>
            <tr>
                <td><?php echo ($user['name']);?></td>
                <td><?php echo($user['email'])?></td>
                <td>
                <?php 
                $department_id=$user['departament_id'];
                $department_name_query = "SELECT name FROM departaments WHERE id = '$department_id'";
                $department_name_result = mysqli_query($conn, $department_name_query);
                $department_name = mysqli_fetch_assoc($department_name_result);
                if (isset($department_name['name']) && !empty($department_name['name'])) {
                    echo $department_name['name'];
                   }?>
                </td>                
                <td><a href="edit_user.php?id=<?php echo $user['id'] ?>" target="_blank">Editează</a></td>
            </tr>
            <?php endforeach ?>
    </table>
</div>
<div class="section">
    <h2>Tabel ore lucrare - departamente</h2>
</div>
    <table>
        <tr>
            <th>Departament</th>
        </tr>
    </table>    




</body>


</html>