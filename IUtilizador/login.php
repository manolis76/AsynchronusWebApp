<?php

session_start();
include_once '../Dados/utilizadorBD.php';
include_once '../Utilitarios/utilitarios.php';
//check that the user is calling the page from the login form and not accessing it directly
//and redirect back to the login form if necessary
if (!isset($_SESSION['utilizador']) && isset($_POST['utilizadorLogin']) && isset($_POST['senhaLogin'])) {

    $user = $_POST['utilizadorLogin'];
    $pass = $_POST['senhaLogin'];
    $dados = obtemUtilizador($user, $pass);
    $dados = limparDados($dados);

    if ($dados['Utilizador'] != '' && $dados['SenhaAcesso'] != '') {

        if ($dados['Utilizador'] == $user && $dados['SenhaAcesso'] == $pass) {
            $_SESSION['utilizador'] = $dados[0];
            $_SESSION['senhaAcesso'] = $dados[1];
            $_SESSION['nome'] = $dados[2];
            $_SESSION['email'] = $dados[3];
            echo ("../IUtilizador/inicio.html");
        } else {
            echo ("Nome de utilizador/senha errados");
        }
    } else {
        echo ("Utilizador inexistente!");
    }
} else if (isset($_SESSION['utilizador'])) {
    header('Location: ../IUtilizador/inicio.html');
} else {
    header('Location: ../IUtilizador/login.html');
}
?>
