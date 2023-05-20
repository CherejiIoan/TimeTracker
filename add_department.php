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
                $query = "INSERT INTO users (email, name, password) VALUES ('$email', '$nume', '$hash');";
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
<hr>
<h2>Adauga un departament nou</h2>
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
        <option value="">--Alege un departament--</option>
        <option value="it">IT</option>
        <option value="financiar">Financiar</option>
        <option value="hr">Resurse Umane</option>
        <option value="marketing">Marketing</option>
        <option value="vanzari">Vanzari</option>
        <option value="productie">Productie</option>
    </select>
    <br>
    <br>
    <input type='submit' value="Creaza contul" name='submit' />
</form>
<?php require('templates/footer.template.php'); ?>

</html>