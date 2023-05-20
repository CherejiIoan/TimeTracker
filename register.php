<?php
require_once('includes/db.inc.php');
require_once('includes/validare.inc.php');
require_once('includes/generateInput.inc.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if (
        validateInput([
            'email' => ['required' => true, 'email' => true],
            'nume' => ['required' => true],
            'parola' => [ 
                'required' => true,
                'min' => 6,
                'max' => 20,
                'uppercase' => true,
                'lowercase' => true,
                'number' => true,
                'special' => true],
            'repeta_parola' => ['required' => true,'password_match' => true],
            'departaments' => ['required' => true],
        ])
    ) {
        // colectam din formular datele
        $email = $_POST['email'];
        $nume = $_POST['nume'];
        $parola = $_POST['parola'];
        $repetaParola = $_POST['repeta_parola'];
        $departament = $_POST['departaments'];
        $department_name_query = "SELECT id FROM departaments WHERE name = '$departament'";
        $department_name_result = mysqli_query($conn, $department_name_query);
        $department_name = mysqli_fetch_assoc($department_name_result);
        $department_id = $department_name['id'];
        // sa verificam daca un utilizator cu acelasi email exista deja
        $query = "SELECT * FROM users WHERE email='$email';";
        $response = mysqli_query($conn, $query);
        $rows = mysqli_num_rows($response);
        if ($rows > 0) {
            echo 'utilizatorul exista deja';
        } else {
            // sa verificam parolele sa fie identice
            if ($parola === $repetaParola) {
                // sa facem un hash pentru parola si sa il salvam in baza de date 
                $hash = password_hash($parola, PASSWORD_DEFAULT);
                $query = "INSERT INTO users (email, name, password, departament_id) VALUES ('$email', '$nume', '$hash','$department_id');";
                mysqli_query($conn, $query);

                header('Location: login.php');
            } else {
                $_POST['errors']['parola'] = 'parolele trebuie sa fie identice.';
            }

        }

    }
}

?>
<html>
<?php require('templates/nav-admin.template.php'); ?>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
    <br>
    <label>Nume</label><br>
    <?php generateInput('text', 'nume', 'Introduce-ti numele'); ?>
    <br>
    <label>Adresa de e-mail</label><br>
    <?php generateInput('email', 'email', 'Introduce-ti adresa de email'); ?>
    <br>
    <label>Parola</label><br>
    <?php generateInput('password', 'parola', 'introduceti parola'); ?>
    <br>
    <label>Repeta Parola</label><br>
    <?php generateInput('password', 'repeta_parola', 'repetati parola'); ?>
    <br>
    <label>Departamentul</label><br>
    <select name="departaments" id="departaments-select">
    <?php
            $selectQueryDep = "SELECT * FROM departaments";
            $deparaments = mysqli_query($conn, $selectQueryDep);
            foreach ($deparaments as $department) : ?>
            <option><?php 
                $department_id=$department['id'];
                $department_name_query = "SELECT name FROM departaments WHERE id = '$department_id'";
                $department_name_result = mysqli_query($conn, $department_name_query);
                $department_name = mysqli_fetch_assoc($department_name_result);
                if (isset($department_name['name']) && !empty($department_name['name'])) {
                    echo $department_name['name'];
                   }?>
                </option>
            <?php endforeach ?>
    </select>
    <br>
    <br>
    <input type='submit' value="Creaza contul" name='submit' />
</form>
<?php require('templates/footer.template.php'); ?>

</html>