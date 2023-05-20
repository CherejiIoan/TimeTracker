<?php
require_once('includes/db.inc.php');
require_once('includes/generateInput.inc.php');
require_once('includes/validare.inc.php');
session_start();



if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['submit'])) {

    if (
        // verificam inputul din formular
        validateInput([
            'email' => ['required' => true],
            'password' => ['required' => true]
        ])
    ) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        // sa verificam in baza de date daca exista
        $query = "SELECT * FROM users WHERE email='$email';";
        $result = mysqli_query($conn, $query);
        $rows = mysqli_num_rows($result);
        // info('rows: ' . $rows);
        if ($rows > 0) {
            //un utilizator cu email si parola care sunt identice cu datele noastre
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {
                // setam valor in superglobala de sesiune
                $_SESSION['email'] = $user['email'];
                // $_SESSION['name'] = $user['name'];
                // info('m-am logat');
                // logToFile($user);
               
                $email = $_SESSION['email'];

                // construiți interogarea SELECT utilizând un parametru de interogare pentru adresa de email
                $query = "SELECT role_id FROM users WHERE email='$email';";

                // pregătiți interogarea pentru a utiliza parametrii de interogare
                $stmt = mysqli_prepare($conn, $query);
    echo 'test';
                // verificați dacă pregătirea interogării a avut succes
                if ($stmt) {  
                    
                    // executați interogarea
                    mysqli_stmt_execute($stmt);
                    
                    // obțineți rezultatele interogării
                    $result = mysqli_stmt_get_result($stmt);
                    
                    // extrageți rândul de rezultate
                    $row = mysqli_fetch_assoc($result);
                    
                    // obțineți adresa de email din rândul de rezultate
                    $role_id = $row['role_id'];
                
                    if ($row['role_id'] === 1) {
                        header('Location: dashboard.php');
                    }else {
                        header('Location: index.php');
                    }
                  
                    
                }



            }
        } else {
            echo 'Datele introduse nu sunt corecte.';
        }
    }
}
?>

<html>
<?php require('templates/nav-admin.template.php') ?><br><br>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
    <!--  sa aratam erori daca sunt -->
    <?php generateInput('email', 'email', 'introduceti email') ?>
    <br>
    <?php generateInput('password', 'password', 'introduceti parola'); ?>
    <br>
    <input type="submit" name="submit" value="login" />
</form>

</html>