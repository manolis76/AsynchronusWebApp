<?php

session_start();
require_once '../Dados/configBD.php';

if (isset($_SESSION['utilizador']) && $_REQUEST) {
    $funcao = $_GET["funcao"];
    $idTarefa = $_GET["idTarefa"];
    $nomeTarefa = $_GET["nomeTarefa"];
    $descricaoTarefa = $_GET['descricaoTarefa'];
    $idPrioridade = $_GET["idPrioridade"];
    $descricaoPrioridade = $_GET['descricaoPrioridade'];
    $pesoPrioridade = $_GET['pesoPrioridade'];

    if ($funcao != "") {
        switch ($funcao) {
            case 0:
                obtemTarefas(NULL, 1);
                break;
            case 1:
                obtemTarefas($idTarefa, 1);
                break;
            case 2:
                obtemPrioridades(NULL, 1);
                break;
            case 3:
                obtemPrioridades($idPrioridade, 1);
                break;
            case 4:
                menuEditaTarefa();
                break;
            case 5:
                editaTarefa($nomeTarefa, $idPrioridade, $descricaoTarefa);
                break;
            case 6:
                menuCriaTarefa();
                break;
            case 7:
                criaTarefa($nomeTarefa, $idPrioridade, $descricaoTarefa);
                break;
            case 8:
                menuEliminaTarefa();
                break;
            case 9:
                eliminaTarefa();
                break;
            case 10:
                menuEditaPrioridade();
                break;
            case 11:
                editaPrioridade($descricaoPrioridade, $pesoPrioridade);
                break;
            case 12:
                menuCriaPrioridade();
                break;
            case 13:
                criaPrioridade($descricaoPrioridade, $pesoPrioridade);
                break;
            case 14:
                menuEliminaPrioridade();
                break;
            case 15:
                eliminaPrioridade();
                break;
        }
    }
}

function menuEliminaTarefa() {
    $tarefa = $_SESSION['tarefaCarregada'];
    $nomeTarefa = $tarefa['nomeTarefa'];
    output(NULL, $nomeTarefa, 7);
}

function menuEliminaPrioridade() {
    $prioridade = $_SESSION['prioridadeCarregada'];
    $descricaoPrioridade = $prioridade['descricaoPrioridade'];
    output(NULL, $descricaoPrioridade, 12);
}

function eliminaTarefa() {

    $tarefa = $_SESSION['tarefaCarregada'];
    $idTarefa = $tarefa['idTarefa'];
    $nomeTarefa = $tarefa['nomeTarefa'];

    $pesquisa = pesquisaRegistosTarefas($idTarefa);

    if ($pesquisa == 0) {
        $db = iniciaBD();
        $sql = "DELETE FROM Tarefa WHERE idTarefa=?";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(array($idTarefa));
            $stmt->closeCursor();
        } catch (PDOException $e) {
            echo ("Erro BD: " . $e->getMessage() . "\n");
            echo ("Código erro BD: " . $e->getCode() . "\n");
            exit;
        }
        output(NULL, $nomeTarefa, 6);
    } else {
        output(NULL, $nomeTarefa, 15);
    }
}

function pesquisaRegistosPrioridade($idPrioridade) {

    $db = iniciaBD();
    $temp = 0;
    $sql = "SELECT * FROM Tarefa WHERE idPrioridadeTarefa=?";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($idPrioridade));
// Save the returned entry array
        $resultado = $stmt->fetchAll();
        foreach ($resultado as $row) {
            $temp++;
        }
    } catch (PDOException $erro) {
        echo ("Erro BD: " . $erro->getMessage() . "\n");
        echo ("Código erro BD: " . $erro->getCode() . "\n");
        exit;
    }
    if ($temp == 0)
        return 0;
    else
        return 1;
}

function pesquisaRegistosTarefas($idTarefa) {

    $db = iniciaBD();
    $temp = 0;
    $sql = "SELECT * FROM PlaneamentoAtividade 
    WHERE idTarefa=?";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($idTarefa));
// Save the returned entry array
        $resultado = $stmt->fetchAll();
        foreach ($resultado as $row) {
            $temp++;
        }
    } catch (PDOException $erro) {
        echo ("Erro BD: " . $erro->getMessage() . "\n");
        echo ("Código erro BD: " . $erro->getCode() . "\n");
        exit;
    }

    $sql = "SELECT * FROM RegistoAtividade 
    WHERE idTarefa=?";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($idTarefa));
// Save the returned entry array
        $resultado = $stmt->fetchAll();
        foreach ($resultado as $row) {
            $temp++;
        }
    } catch (PDOException $erro) {
        echo ("Erro BD: " . $erro->getMessage() . "\n");
        echo ("Código erro BD: " . $erro->getCode() . "\n");
        exit;
    }

    if ($temp == 0)
        return 0;
    else
        return 1;
}

function eliminaPrioridade() {

    $prioridade = $_SESSION['prioridadeCarregada'];
    $idPrioridade = $prioridade['idPrioridadeTarefa'];
    $descricaoPrioridade = $prioridade['descricaoPrioridade'];

    $pesquisa = pesquisaRegistosPrioridade($idPrioridade);

    if ($pesquisa == 0) {
        $db = iniciaBD();
        $sql = "DELETE FROM PrioridadeTarefa WHERE idPrioridadeTarefa=?";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(array($idPrioridade));
            $stmt->closeCursor();
        } catch (PDOException $e) {
            echo ("Erro BD: " . $e->getMessage() . "\n");
            echo ("Código erro BD: " . $e->getCode() . "\n");
            exit;
        }
        output(NULL, $descricaoPrioridade, 13);
    } else {
        output(NULL, $descricaoPrioridade, 14);
    }
}

function editaTarefa($nomeTarefa, $idPrioridadeTarefa, $descricaoTarefa) {
    $db = iniciaBD();
    $tarefa = $_SESSION['tarefaCarregada'];
    $idTarefa = $tarefa['idTarefa'];
    $sql = "UPDATE Tarefa SET NomeTarefa=?,DescricaoTarefa=?,idPrioridadeTarefa=? WHERE idTarefa=?";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($nomeTarefa, $descricaoTarefa, $idPrioridadeTarefa, $idTarefa));
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    output($idTarefa, $nomeTarefa, 2);
}

function editaPrioridade($descricaoPrioridade, $pesoPrioridade) {
    $db = iniciaBD();
    $prioridade = $_SESSION['prioridadeCarregada'];
    $idPrioridade = $prioridade['idPrioridadeTarefa'];
    $sql = "UPDATE PrioridadeTarefa SET DescricaoPrioridade=?,PesoPrioridade=? WHERE idPrioridadeTarefa=?";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($descricaoPrioridade, $pesoPrioridade, $idPrioridade));
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    output($idPrioridade, $descricaoPrioridade, 9);
}

function criaTarefa($nomeTarefa, $idPrioridadeTarefa, $descricaoTarefa) {

    $db = iniciaBD();
// Save the entry into the database
    $sql = "INSERT INTO Tarefa (NomeTarefa, idPrioridadeTarefa, DescricaoTarefa) VALUES (?, ?, ?)";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($nomeTarefa, $idPrioridadeTarefa, $descricaoTarefa));
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    output(NULL, $nomeTarefa, 5);
}

function criaPrioridade($descricaoPrioridade, $pesoPrioridade) {
    $db = iniciaBD();
// Save the entry into the database
    $sql = "INSERT INTO PrioridadeTarefa (DescricaoPrioridade, PesoPrioridade) VALUES (?, ?)";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($descricaoPrioridade, $pesoPrioridade));
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    output(NULL, $descricaoPrioridade, 11);
}

function obtemUltimaTarefaCriada() {
    $db = iniciaBD();
// Get the ID of the entry we just saved
    try {
        $id_obj = $db->query("SELECT LAST_INSERT_ID()");
        $id = $id_obj->fetch();
        $id_obj->closeCursor();
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
}

function obtemTarefas($id, $output) {

    $db = iniciaBD();
    $_SESSION['tarefaCarregada'] = NULL;
    if (isset($id)) {
        $sql = "SELECT T.NomeTarefa, T.DescricaoTarefa, T.idPrioridadeTarefa, P.DescricaoPrioridade, T.idTarefa
                FROM Tarefa AS T,PrioridadeTarefa AS P
                WHERE T.idTarefa=? AND T.idPrioridadeTarefa=P.idPrioridadeTarefa";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(array($id));
// Save the returned entry array
            $e = $stmt->fetch();
        } catch (PDOException $e) {
            echo ("Erro BD: " . $e->getMessage() . "\n");
            echo ("Código erro BD: " . $e->getCode() . "\n");
            exit;
        }
        $_SESSION['tarefaCarregada'] = array(
            'nomeTarefa' => $e['NomeTarefa'],
            'descricaoTarefa' => $e['DescricaoTarefa'],
            'idPrioridadeTarefa' => $e['idPrioridadeTarefa'],
            'descricaoPrioridade' => $e['DescricaoPrioridade'],
            'idTarefa' => $e['idTarefa'],
        );
        $enviar = $_SESSION['tarefaCarregada'];
    }
    /*
     * If no entry ID was supplied, load all entry titles
     */ else {
        $sql = "SELECT idTarefa, NomeTarefa FROM Tarefa ORDER BY NomeTarefa ASC";
// Loop through returned results and store as an array
        try {
            foreach ($db->query($sql) as $row) {
                $enviar[] = array(
                    'idTarefa' => $row['idTarefa'],
                    'nomeTarefa' => $row['NomeTarefa']
                );
            }
        } catch (PDOException $e) {
            echo ("Erro BD: " . $e->getMessage() . "\n");
            echo ("Código erro BD: " . $e->getCode() . "\n");
            exit;
        }
    }
    if ($output == 1) {
        output($id, $enviar, 0);
    } else {
        return $enviar;
    }
}

function menuEditaTarefa() {
    $lista = obtemPrioridades(NULL, 0);
    $tarefaEditar = $_SESSION['tarefaCarregada'];
    $prioridadeRemover[] = array(
        'idPrioridadeTarefa' => $tarefaEditar['idPrioridadeTarefa'],
        'descricaoPrioridade' => $tarefaEditar['descricaoPrioridade']);

    foreach ($lista as $remover) {
        if ($remover['idPrioridadeTarefa'] !== $prioridadeRemover[0]['idPrioridadeTarefa']) {
            $listaPrioridades[] = array(
                'idPrioridadeTarefa' => $remover['idPrioridadeTarefa'],
                'descricaoPrioridade' => $remover['descricaoPrioridade']
            );
        }
    }
    output($tarefaEditar, $listaPrioridades, 3);
}

function menuEditaPrioridade() {
    $prioridadeEditar = $_SESSION['prioridadeCarregada'];
    output($prioridadeEditar, NULL, 8);
}

function menuCriaTarefa() {
    $listaPrioridades = obtemPrioridades(NULL, 0);
    output(NULL, $listaPrioridades, 4);
}

function menuCriaPrioridade() {
    output(NULL, NULL, 10);
}

function obtemPrioridades($id, $output) {

    $db = iniciaBD();
    $_SESSION['prioridadeCarregada'] = NULL;
    if (isset($id)) {
        $sql = "SELECT DescricaoPrioridade, PesoPrioridade FROM PrioridadeTarefa WHERE idPrioridadeTarefa=? 
            LIMIT 1";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(array($id));
// Save the returned entry array
            $e = $stmt->fetch();
        } catch (PDOException $e) {
            echo ("Erro BD: " . $e->getMessage() . "\n");
            echo ("Código erro BD: " . $e->getCode() . "\n");
            exit;
        }
        $_SESSION['prioridadeCarregada'] = array(
            'descricaoPrioridade' => $e['DescricaoPrioridade'],
            'pesoPrioridade' => $e['PesoPrioridade'],
            'idPrioridadeTarefa' => $id
        );
        $enviar = $_SESSION['prioridadeCarregada'];
    }
    /*
     * If no entry ID was supplied, load all entry titles
     */ else {
        try {
            $sql = "SELECT idPrioridadeTarefa, DescricaoPrioridade FROM PrioridadeTarefa ORDER BY DescricaoPrioridade ASC";
// Loop through returned results and store as an array
            foreach ($db->query($sql) as $row) {
                $enviar[] = array(
                    'idPrioridadeTarefa' => $row['idPrioridadeTarefa'],
                    'descricaoPrioridade' => $row['DescricaoPrioridade']
                );
            }
        } catch (PDOException $e) {
            echo ("Erro BD: " . $e->getMessage() . "\n");
            echo ("Código erro BD: " . $e->getCode() . "\n");
            exit;
        }
    }
    if ($output == 1) {
        output($id, $enviar, 1);
    } else {
        return $enviar;
    }
}

function output($id, $dados, $tipoOutput) {
    switch ($tipoOutput) {
        case 0:
            if ($id == NULL) {
                if ($dados != NULL) {
                    echo ("<h1>Tarefas</h1>");
                    echo("<p><form><select id=\"listaTarefas\" onchange=\"MakeXMLHTTPCall(2);\"><option selected=\"selected\" value=\"escolha\">Escolha uma tarefa</option>");
                    foreach ($dados as $entry) {
                        echo ("<option value=\"" . $entry['idTarefa'] . "\">" . $entry['nomeTarefa'] . "</option>");
                    }
                    echo("</select></form></p>");
                } else {
                    echo("<h1>De momento, o sistema não possui Tarefas!<br>Utilize o menu \"Criar\" à direita.</h1>");
                    break;
                }
            } else {
                echo ("<h1 id=\"detalhesTarefa\">Detalhes Tarefa</h1>");
                echo ("<table><tbody>");
                echo ("<tr><td><p>Nome: </p></td><td><p>" . $dados['nomeTarefa'] . "</p></td></tr>");
                echo ("<tr><td><p>Descrição: </p></td><td><p>" . $dados['descricaoTarefa'] . "</p></td></tr>");
                echo ("<tr><td><p>Prioridade: </p></td><td><p>" . $dados['descricaoPrioridade'] . "</p></td></tr>");
                echo ("</tbody></table>");
            }
            break;
        case 1:
            if ($id == NULL) {
                if ($dados != NULL) {
                    echo ("<h1>Prioridades</h1>");
                    echo("<p><form><select id=\"listaPrioridades\" onchange=\"MakeXMLHTTPCall(4);\"><option selected=\"selected\" value=\"escolha\">Escolha uma prioridade</option>");
                    foreach ($dados as $entry) {
                        echo ("<option value=\"" . $entry['idPrioridadeTarefa'] . "\">" . $entry['descricaoPrioridade'] . "</option>");
                    }
                    echo("</select></form></p>");
                } else {
                    echo("<h1>De momento, o sistema não possui Prioridades!<br>Utilize o menu \"Criar\" à direita.</h1>");
                    break;
                }
            } else {
                echo ("<h1 id=\"detalhesPrioridade\">Detalhes Prioridade</h1>");
                echo ("<table><tbody>");
                echo ("<tr><td><p>Descrição: </p></td><td><p>" . $dados['descricaoPrioridade'] . "</p></td></tr>");
                echo ("<tr><td><p>Peso Prioridade: </p></td><td><p>" . $dados['pesoPrioridade'] . "</p></td></tr>");
                echo ("</tbody></table>");
            }
            break;
        case 2:
            echo ("<h1>A tarefa \"" . $dados . "\" foi actualizada com sucesso.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(1);\">Voltar</a>");
            break;
        case 3:
            echo ("<h1>Editar Tarefa</h1></legend>");
            echo ("<table><tbody><tr><td><p>Nome: </p></td>");
            echo ("<td><input type=\"text\" id=\"nomeTarefaActualizar\" autofocus=\"autofocus\" name=\"nomeTarefaActualizar\" maxlength=\"45\" value=\"" . $id['nomeTarefa'] . "\"/></td></tr>");
            echo ("<tr><td><p>Descrição: </p></td>");
            echo ("<td><input type=\"text\" id=\"descricaoActualizar\" name=\"descricaoActualizar\" size=\"80\" maxlength=\"200\" value=\"" . $id['descricaoTarefa'] . "\"/></td></tr>");
            echo ("<tr><td><p>Seleccione Prioridade: </p></td>");
            echo ("<td><select name=\"listaPrioridadesEdiTarefa\" id=\"listaPrioridadesEdiTarefa\"><option selected=\"selected\" value=\"" . $id['idPrioridadeTarefa'] . "\">");
            echo ($id['descricaoPrioridade'] . "</option>");
            foreach ($dados as $entry) {
                echo ("<option value=\"" . $entry['idPrioridadeTarefa'] . "\">" . $entry['descricaoPrioridade'] . "</option>");
            }
            echo ("</select></td></tr>");
            echo ("<tr><td></td><td><button type=\"button\" id=\"guardarTarefa\" onclick=\"MakeXMLHTTPCall(6);\">Guardar</button>");
            echo ("<button type=\"button\" id=\"cancelarTarefa\" onclick=\"MakeXMLHTTPCall(1);\">Cancelar</button></td></tr>");
            echo ("</tbody></table>");
            break;
        case 4:
            if (sizeof($dados) > 0) {
                echo ("<h1>Criar Tarefa</h1>");
                echo ("<table><tbody><tr><td><p>Nome: </p></td>");
                echo ("<td><input type=\"text\" id=\"nomeTarefaCriar\" autofocus=\"autofocus\" name=\"nomeTarefaCriar\" maxlength=\"45\"/></td></tr>");
                echo ("<tr><td><p>Descrição: </p></td>");
                echo ("<td><input type=\"text\" id=\"descricaoCriar\" name=\"descricaoCriar\" maxlength=\"200\"/></td></tr>");
                echo ("<tr><td><p>Seleccione Prioridade: </p></td>");
                echo ("<td><select name=\"listaPrioridadesCriaTarefa\" id=\"listaPrioridadesCriaTarefa\"><option selected=\"selected\" value=\"escolha\">");
                echo ("Escolha um tipo de prioridade</option>");
                foreach ($dados as $entry) {
                    echo ("<option value=\"" . $entry['idPrioridadeTarefa'] . "\">" . $entry['descricaoPrioridade'] . "</option>");
                }
                echo ("</select></td></tr>");
                echo ("<tr><td></td><td><button type=\"button\" id=\"criarTarefa\" onclick=\"MakeXMLHTTPCall(8);\">Criar</button>");
                echo ("<button type=\"button\" id=\"cancelarTarefa\" onclick=\"MakeXMLHTTPCall(1);\">Cancelar</button></td></tr>");
                echo ("</tbody></table>");
                break;
            } else {
                echo("<h1>De momento, o sistema não possui Prioridades!<br>Utilize o menu \"Configurações\" > \"Tarefas\" > \"Prioridades\" para criar.</h1>");
                break;
            }
        case 5:
            echo ("<h1>A tarefa \"" . $dados . "\" foi criada com sucesso.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(1);\">Voltar</a>");
            break;
        case 6:
            echo ("<h1>A tarefa \"" . $dados . "\" foi eliminada com sucesso.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(1);\">Voltar</a>");
            break;
        case 7:
            echo ("<div><h1>Pretende eliminar a tarefa \"" . $dados . "\"?</h1></div><div>");
            echo ("<button type=\"button\" id=\"eliminarTarefa\" onclick=\"MakeXMLHTTPCall(10);\">Sim</button>");
            echo ("<button type=\"button\" id=\"cancelarEliminarTarefa\" onclick=\"MakeXMLHTTPCall(1);\">Não</button>");
            echo ("</div>");
            break;
        case 8:
            echo ("<h1>Editar Prioridade</h1>");
            echo ("<table><tbody><tr><td><p>Descrição: </p></td>");
            echo ("<td><input type=\"text\" id=\"descricaoPrioridadeEditar\" autofocus=\"autofocus\" name=\"descricaoPrioridadeEditar\" maxlength=\"45\" value=\"" . $id['descricaoPrioridade'] . "\"/></td></tr>");
            // echo ("<tr><td><p>Peso (valor entre 1 e 5): </p></td>");
            // echo ("<td><input type=\"text\" id=\"pesoPrioridadeEditar\" name=\"pesoPrioridadeEditar\" maxlength=\"1\" value=\"" . $id['pesoPrioridade'] . "\"/></td></tr>");
            echo ("<tr><td><p>Peso: </p></td>");
            echo ("<td><select name=\"pesoPrioridadeEditar\" id=\"pesoPrioridadeEditar\"><option selected=\"selected\" value=\"" . $id['pesoPrioridade'] . "\">" . $id['pesoPrioridade'] . "</option>");
            for ($i = 1; $i < 6; $i++) {
                if ($i != $id['pesoPrioridade']) {
                    echo("<option value=\"" . $i . "\">" . $i . "</option>");
                }
            }
            echo ("</select></td></tr>");
            echo ("<tr><td></td><td><button type=\"button\" id=\"guardarPrioridade\" onclick=\"MakeXMLHTTPCall(12);\">Guardar</button>");
            echo ("<button type=\"button\" id=\"cancelarPrioridade\" onclick=\"MakeXMLHTTPCall(3);\">Cancelar</button></td></tr>");
            echo ("</tbody></table>");
            break;
        case 9:
            echo ("<h1>A prioridade \"" . $dados . "\" foi actualizada com sucesso.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(3);\">Voltar</a>");
            break;
        case 10:
            echo ("<h1>Criar Prioridade</h1>");
            echo ("<table><tbody><tr><td><p>Descrição: </p></td>");
            echo ("<td><input type=\"text\" id=\"descricaoPrioridadeCriar\" autofocus=\"autofocus\" name=\"descricaoPrioridadeCriar\" maxlength=\"45\"/></td></tr>");
            echo ("<tr><td><p>Peso: </p></td>");
            echo ("<td><select name=\"pesoPrioridadeCriar\" id=\"pesoPrioridadeCriar\"><option selected=\"selected\" value=\"" . $id['pesoPrioridade'] . "\">" . $id['pesoPrioridade'] . "</option>");
            for ($i = 1; $i < 6; $i++) {
                    echo("<option value=\"" . $i . "\">" . $i . "</option>");
            }
            echo ("</select></td></tr>");            
            //echo ("<td><input type=\"text\" id=\"pesoPrioridadeCriar\" name=\"pesoPrioridadeCriar\" maxlength=\"1\"/></td></tr>");
            echo ("<tr><td></td><td><button type=\"button\" id=\"criarPrioridade\" onclick=\"MakeXMLHTTPCall(14);\">Criar</button>");
            echo ("<button type=\"button\" id=\"cancelarPrioridade\" onclick=\"MakeXMLHTTPCall(3);\">Cancelar</button></td></tr>");
            echo ("</tbody></table>");
            break;
        case 11:
            echo ("<h1>A prioridade \"" . $dados . "\" foi criada com sucesso.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(3);\">Voltar</a>");
            break;
        case 12:
            echo ("<div><h1>Pretende eliminar a prioridade \"" . $dados . "\"?</h1></div><div>");
            echo ("<button type=\"button\" id=\"eliminarPrioridade\" onclick=\"MakeXMLHTTPCall(16);\">Sim</button>");
            echo ("<button type=\"button\" id=\"cancelarEliminarPrioridade\" onclick=\"MakeXMLHTTPCall(3);\">Não</button>");
            echo ("</div>");
            break;
        case 13:
            echo ("<h1>A prioridade \"" . $dados . "\" foi eliminada com sucesso.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(3);\">Voltar</a>");
            break;
        case 14:
            echo ("<h1>A prioridade \"" . $dados . "\" não pode ser eliminada, pois encontra-se associada a pelo menos um registo na BD.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(3);\">Voltar</a>");
            break;
        case 15:
            echo ("<h1>A tarefa \"" . $dados . "\" não pode ser eliminada, pois encontra-se associada a pelo menos um registo na BD.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(1);\">Voltar</a>");
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
