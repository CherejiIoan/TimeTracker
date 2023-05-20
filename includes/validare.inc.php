<?php
// require_once "/utils/login.inc.php";
function validateInput($reguliDeValidare)
{
    // 2.Verifica valorile trimise din formular prin $_POST
//      2.1 Folosim un parametru pentru verificare
//      2.2 Folosim ca parametru un array
//      2.3 const $array = ['nume' => 'required|min:3|max:20']
//      2.3.1 $array = ['nume' => ['required' => true, 'min'=> 3, 'max'=>20]] --> varianta folosita
    foreach ($reguliDeValidare as $numeCamp => $reguli) { // parcurgem reguli de validare
        foreach ($reguli as $regulaKey => $regulaValue) { // parcurgem fiecare regula

            // 3.Pune mesaje de eroare pentru fiecare validare esuata
            //      3.1 setam mesajul de eroare $_POST['errors']['numele campului'] 
            switch ($regulaKey) {
                case 'required': {
                        if ($regulaValue) {
                            if (!isset($_POST[$numeCamp]) || empty($_POST[$numeCamp])) {
                                $_POST['errors'][$numeCamp] = $numeCamp . ' este obligatoriu.';
                            }
                        }
                        break;
                    }
                case 'min': {
                        if (strlen($_POST[$numeCamp]) < $regulaValue) {
                            $_POST['errors'][$numeCamp] = $numeCamp . ' este prea scurt.';
                        }

                        break;
                    }
                case 'max': {
                        if (strlen($_POST[$numeCamp]) > $regulaValue) {
                            $_POST['errors'][$numeCamp] = $numeCamp . '  este prea lung.';
                        }
                        break;
                    }
                case 'uppercase': {
                    if ($regulaValue && !preg_match('/[A-Z]/', $_POST[$numeCamp])) {
                        $_POST['errors'][$numeCamp] = $numeCamp . ' trebuie sa contina cel putin o litera mare.';
                    }
                    break;
                }
                case 'lowercase': {
                    if ($regulaValue && !preg_match('/[a-z]/', $_POST[$numeCamp])) {
                        $_POST['errors'][$numeCamp] = $numeCamp . ' trebuie sa contina cel putin o litera mica.';
                    }
                    break;
                }
                case 'number': {
                    if ($regulaValue && !preg_match('/[0-9]/', $_POST[$numeCamp])) {
                        $_POST['errors'][$numeCamp] = $numeCamp . ' trebuie sa contina cel putin un numar.';
                    }
                    break;
                }
                case 'special': {
                    if ($regulaValue && !preg_match('/[!@#$%^&*()\-_=+{};:,<.>\[\]]/', $_POST[$numeCamp])) {
                        $_POST['errors'][$numeCamp] = $numeCamp . ' trebuie sa contina cel putin un caracter special.';
                    }
                    break;
                }
                case 'email': {
                    // Verificam daca adresa de email este valida si contine un domeniu valid
                    if (!filter_var($_POST[$numeCamp], FILTER_VALIDATE_EMAIL)) {
                        $_POST['errors'][$numeCamp] = $numeCamp . ' trebuie sa fie o adresa de email valida.';
                    } else {
                        $domeniuValid = false;
                        $adresaEmail = $_POST[$numeCamp];
                        $domeniu = substr(strrchr($adresaEmail, "@"), 1);
                        $domeniiValizi = array('gmail.com', 'yahoo.com', 'hotmail.com'); // adauga aici domeniile acceptate
                        foreach ($domeniiValizi as $domeniuValabil) {
                            if (strcasecmp($domeniu, $domeniuValabil) == 0) {
                                $domeniuValid = true;
                                break;
                            }
                        }
                        if (!$domeniuValid) {
                            $_POST['errors'][$numeCamp] = $numeCamp . ' trebuie sa fie o adresa de email valida de la un furnizor de email cunoscut.';
                        }
                    }
                    break;
                }
                case 'password_match': {
                    if ($_POST['parola'] !== $_POST['repeta_parola']) {
                        $_POST['errors']['repeta_parola'] = 'Cele două parole nu corespund.';
                    }
                    break;
                }
                default:
                    break;
            }
        }
    }
    // 4.Opreste executia daca sunt validari esuate
    //      4.1 daca este setat $_POST['errors'] si nu este gol $_POST['errors']
    //      4.2 return false
    if (isset($_POST['errors']) && !empty($_POST['errors'])) {
        return false;
    }

    return true;
}