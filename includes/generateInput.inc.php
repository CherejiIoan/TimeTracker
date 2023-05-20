<?php

// avem nevoie de parametrii pentru particularitatile elementului
// 1. tipul inputului ( text, number, checkbox, radio etc..)
// 2. numele fieldului ( atributul de name)
function generateInput($type, $name, $placeholder = '')
{
    // verrificam daca prin $nameValue daca avem o valoare salvata in $_POST
    $nameValue = isset($_POST[$name]) && !empty($_POST[$name]) ? $_POST[$name] : '';
    // alcautim $input folosint parametrii si valoarea salvata in $_POST
    $input = '<input type="' . $type . '" name="' . $name . '" value="' . $nameValue
        . '" placeholder="' . $placeholder . '"/> <br>';

    // verificam daca exista errori pentru campul respectiv
    if (isset($_POST['errors'][$name]) && !empty($_POST['errors'][$name])) {
        // daca exista errori pentru campul respectiv adaugam in inputul nostru eroare
        $input .= '<p style="color:red">' . $_POST['errors'][$name] . '</p>';
    }

    // arata input
    echo $input;
}