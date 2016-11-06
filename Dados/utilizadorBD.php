<?php

session_start();
require_once '../Dados/configBD.php';
//if (isset($_SESSION['utilizador']) && $_REQUEST) {
if ($_REQUEST) {
    $funcao = $_GET["funcao"];
    $nomeEditar = $_GET["nome"];
    $senhaEditar = $_GET["senha"];
    //$nomeEditar= $_POST['utilizadorLogin'];
    //$senhaEditar = $_POST['senhaLogin'];

    if ($funcao != "") {
        switch ($funcao) {
            case 0:
                obtemUtilizadorSessaoIniciada();
                break;
            case 1:
                menuTerminaSessao();
                break;
            case 2:
                terminaSessao();
                break;
            case 3:
                carregaMenuBar();
                break;
            case 4:
                menuDadosAcesso();
                break;
            case 5:
                menuEditaDadosAcesso();
                break;
            case 6:
                editaDadosAcesso($nomeEditar, $senhaEditar);
                break;
        }
    }
}

function menuTerminaSessao() {
    output(NULL, NULL, 1);
}

function menuDadosAcesso() {
    $enviar = array(
        'nome' => $_SESSION['nome'],
        'senha' => $_SESSION['senhaAcesso'],
        'utilizador' => $_SESSION['utilizador'],
        'email' => $_SESSION['email'],
    );
    output(NULL, $enviar, 4);
}

function menuEditaDadosAcesso() {
    $enviar = array(
        'nome' => $_SESSION['nome'],
        'senha' => $_SESSION['senhaAcesso'],
        'utilizador' => $_SESSION['utilizador'],
        'email' => $_SESSION['email'],
    );
    output($enviar, NULL, 5);
}

function editaDadosAcesso($nome, $senha) {
    $utilizador = $_SESSION['utilizador'];
    $db = iniciaBD();
    $sql = "UPDATE Utilizador SET Nome=?, SenhaAcesso=? WHERE Utilizador=?";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($nome, $senha, $utilizador));
// Save the returned entry array
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    $_SESSION['nome'] = $nome;
    $_SESSION['senhaAcesso'] = $senha;
    output(NULL, $utilizador, 6);
}

function terminaSessao() {

    session_start();

    // Limpa todas as variaveis de sessao.
    $_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
    //Elimina o cookie de sessao
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
        );
    }

    //Destroi a sessao
    session_destroy();
    echo("../IUtilizador/login.html");
}

function carregaMenuBar() {

    if ($_SESSION['utilizador'] == ADMIN) {
        output(NULL, NULL, 2);
    } else {
        output(NULL, NULL, 3);
    }
}

function obtemUtilizadorSessaoIniciada() {

    if (isset($_SESSION['utilizador'])) {
        $utilizador = $_SESSION['utilizador'];
        $nome = $_SESSION['nome'];
        $email = $_SESSION['email'];
        $enviar = array(
            'nome' => $nome,
            'utilizador' => $utilizador,
            'email' => $email
        );

        output(NULL, $enviar, 0);
    } else {
        echo ("../IUtilizador/login.html");
    }
}

function obtemUtilizador($utilizador, $senha) {

    $db = iniciaBD();
    $sql = "SELECT Utilizador, SenhaAcesso, Nome, email FROM Utilizador WHERE Utilizador=? 
            LIMIT 1";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($utilizador));
// Save the returned entry array
        $e = $stmt->fetch();
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }

    if (!is_array($e)) {
        $e = array(
            'Utilizador' => '',
            'SenhaAcesso' => '',
            'Nome' => '',
            'email' => ''
        );
    }
    return $e;
}

function output($id, $dados, $tipoOutput) {
    switch ($tipoOutput) {
        case 0:
            echo ("<h1>Bem-vindo " . $dados['nome'] . ", autenticado como \"" . $dados['utilizador'] . "\" (email: " . $dados['email'] . ")</h1>");
            break;
        case 1:
            echo ("<div><h1>Pretende terminar a sessao?</h1></div><div>");
            echo ("<button type=\"button\" id=\"terminarSessao\" onclick=\"MakeXMLHTTPCall(18);\">Sim</button>");
            echo ("<button type=\"button\" id=\"cancelarTerminarSessao\" onclick=\"MakeXMLHTTPCall(0);\">Não</button>");
            echo ("</div>");
            break;
        case 2:
            $str = <<<EOF
                        <ul id="menu-bar">
                        <li><a href="#" class="first" onclick="MakeXMLHTTPCall(0);">Início</a></li>
                        <li><a href="#">Configurações &raquo;</a>
                            <ul>
                                <li><a href="#" onclick="MakeXMLHTTPCall(21);">Dados Acesso</a></li>
                                <li><a href="#" onclick="carregaPlaneamento();">Planeamento</a></li>
                                <li><a href="#">Smartphone &raquo;</a>
                                    <ul>
                                        <li><a href="#" onclick="MakeXMLHTTPCall(25);">Lista Contactos</a></li>
                                        <li><a href="#" onclick="MakeXMLHTTPCall(34);">Tipo Aplicações</a></li>
                                        <li><a href="#" onclick="MakeXMLHTTPCall(26);">Plataforma S.O.</a></li>
                                    </ul>					
                                </li>                            
                                <li><a href="#">Tarefas &raquo;</a>
                                    <ul>
                                        <li><a href="#" onclick="MakeXMLHTTPCall(1);">Tarefas</a></li>
                                        <li><a href="#" onclick="MakeXMLHTTPCall(3);">Prioridades</a></li>
                                    </ul>					
                                </li>					
                            </ul>
                        </li>
                        <li><a href="#">Desktop &raquo;</a>
                            <ul>
                                <li><a href="#" onclick="carregaGrafico();">Gráficos</a></li>
                                <li><a href="#" onclick="carregaRegistoAtividade();">Histórico</a></li>
                            </ul>				
                        </li>
                        <li><a href="#">SmartPhone &raquo;</a>
                            <ul>
                                <li><a href="#" onclick="MakeXMLHTTPCall(44);">Aplicações</a></li>
                                <li><a href="#" onclick="MakeXMLHTTPCall(50);">Chamadas</a></li>
                                <li><a href="#" onclick="MakeXMLHTTPCall(58);">Contactos</a></li>
                                <li><a href="#" onclick="MakeXMLHTTPCall(53);">Mensagens</a></li>
                            </ul>
                        </li>
                        <li><a href="#" class="last" onclick="sobre();">Sobre</a></li>
                    </ul>
EOF;
            echo $str;
            break;
        case 3:
            $str = <<<EOF
            <ul id="menu-bar">
                <li><a href="#" class="first" onclick="MakeXMLHTTPCall(0);">Início</a></li>
                <li><a href="#">Configurações &raquo;</a>
            <ul>
                <li><a href="#" onclick="MakeXMLHTTPCall(21);">Dados Acesso</a></li>
                <li><a href="#" onclick="carregaPlaneamento()">Planeamento</a></li>
            </ul>
            </li>
                <li><a href="#">Desktop &raquo;</a>
            <ul>
                <li><a href="#" onclick="carregaGrafico();">Gráficos</a></li>
                <li><a href="#" onclick="carregaRegistoAtividade();">Histórico</a></li>
            </ul>
            </li>
            <li><a href="#">SmartPhone &raquo;</a>
            <ul>
                <li><a href="#" onclick="MakeXMLHTTPCall(44);">Aplicações</a></li>
                <li><a href="#" onclick="MakeXMLHTTPCall(50);">Chamadas</a></li>
                <li><a href="#" onclick="MakeXMLHTTPCall(58);">Contactos</a></li>
                <li><a href="#" onclick="MakeXMLHTTPCall(53);">Mensagens</a></li>
            </ul>
            </li>
            <li><a href="#" class="last" onclick="sobre();">Sobre</a></li>
            </ul>
            </ul>
EOF;
            echo $str;
            break;
        case 4:
            echo ("<h1 id=\"detalhesDadosAcesso\">Dados Acesso</h1>");
            echo ("<table><tbody><tr><td><p>Nome: </p></td><td><p>" . $dados['nome'] . "</p></td></tr>");
            echo ("<tr><td><p>email: </p></td><td><p>" . $dados['email'] . "</p></td></tr>");
            echo ("<tr><td><p>Utilizador: </p></td><td><p>" . $dados['utilizador'] . "</p></td></tr></tbody><table>");
            break;
        case 5:
            echo ("<h1>Editar Dados Acesso - \"" . $id['utilizador'] . "\"</h1>");
            echo ("<table><tbody><tr><td><p>Nome: </p></td>");
            echo ("<td><input type=\"text\" autofocus=\"autofocus\" id=\"nomeUtilizadorEditar\" name=\"nomeUtilizadorEditar\" maxlength=\"45\" value=\"" . $id['nome'] . "\"/>");
            echo ("</td></tr>");
            echo ("<tr><td><p>Senha: </p></td>");
            echo ("<td><input type=\"password\" id=\"senhaUtilizadorEditar\" name=\"senhaUtilizadorEditar\" maxlength=\"20\" value=\"" . $id['senha'] . "\"/>");
            echo ("</td></tr>");
            echo ("<tr><td><p>Confirmar Senha: </p></td>");
            echo ("<td><input type=\"password\" id=\"senhaConfirmarUtilizadorEditar\" name=\"senhaConfirmarUtilizadorEditar\" maxlength=\"20\" value=\"" . $id['senha'] . "\"/>");
            echo ("</td></tr>");
            echo ("<tr><td></td><td><button type=\"button\" id=\"guardarTarefa\" onclick=\"MakeXMLHTTPCall(23);\">Guardar</button>");
            echo ("<button type=\"button\" id=\"cancelarTarefa\" onclick=\"MakeXMLHTTPCall(0);\">Cancelar</button>");
            echo ("</td></tr></tbody></table>");
            break;
        case 6:
            echo ("<h1>Os dados do utilizador \"" . $dados . "\" foram actualizados com sucesso.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(0);\">Voltar</a>");
            break;
    }
}

function iniciaBD() {
// Open a database connection
    try {
        $db = new PDO(DB_INFO, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código Erro BD: " . $e->getCode() . "\n");
    }
    return $db;
}

?>
