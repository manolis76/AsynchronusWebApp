<?php

session_start();
require_once '../Dados/configBD.php';
include_once '../Utilitarios/class.vCard.inc.php';


if (isset($_SESSION['utilizador']) && $_REQUEST) {
    $funcao = $_GET['funcao'];
    $idTipoPlataforma = $_GET['idSistemaOperSelec'];
    $descricaoPlataforma = $_GET['descricaoPlataforma'];
    $idTipoApp = $_GET['idTipoApp'];
    $descricaoTipoApp = $_GET['descricaoTipoApp'];
    $idAplicacao = $_GET['idAplicacao'];
    $dataHoraInicio = $_GET['dataInicio'];
    $dataHoraFim = $_GET['dataFim'];
    $_SESSION['dataHoraInicio'] = $dataHoraInicio;
    $_SESSION['dataHoraFim'] = $dataHoraFim;
    $enderecoApp = $_GET['endereco'];
    $comentarioApp = $_GET['comentario'];
    $idContato = $_GET['idContato'];
    $utilizador = $_GET['utilizador'];
    $numProf = $_GET['numProf'];
    $numPessoal = $_GET['numPessoal'];
    $email = $_GET['email'];
    $profPessoal = $_GET['profPessoal'];
    $aplicacaoAtiva = $_GET['ativa'];


    if ($funcao != "") {
        switch ($funcao) {
            case 0:
                obtemContatos(NULL, NULL, 1);
                break;
            case 1:
                obtemPlataformaOS(NULL, 1);
                break;
            case 2:
                obtemPlataformaOS($idTipoPlataforma, 1);
                break;
            case 3:
                menuEditaPlataforma();
                break;
            case 4:
                editaPlataforma($descricaoPlataforma);
                break;
            case 5:
                menuCriaPlataforma();
                break;
            case 6:
                criaPlataforma($descricaoPlataforma);
                break;
            case 7:
                menuEliminaPlataforma();
                break;
            case 8:
                eliminaPlataforma();
                break;
            case 9:
                obtemTipoApp(NULL, 1);
                break;
            case 10:
                obtemTipoApp($idTipoApp, 1);
                break;
            case 11:
                menuEditaTipoApp();
                break;
            case 12:
                editaTipoApp($descricaoTipoApp);
                break;
            case 13:
                menuCriaTipoApp();
                break;
            case 14:
                criaTipoApp($descricaoTipoApp);
                break;
            case 15:
                menuEliminaTipoApp();
                break;
            case 16:
                eliminaTipoApp();
                break;
            case 17:
                obtemAplicacoes(NULL, 1);
                break;
            case 18:
                obtemRegistoAplicacao($idAplicacao, $dataHoraInicio, $dataHoraFim);
                break;
            case 19:
                obtemAplicacoes($idAplicacao, 1);
                break;
            case 20:
                menuEditaAplicacao($idAplicacao);
                break;
            case 21:
                editaAplicacao($idAplicacao, $idTipoPlataforma, $idTipoApp, $enderecoApp, $comentarioApp, $aplicacaoAtiva);
                break;
            case 22:
                obtemChamadas(1);
                break;
            case 23:
                obtemRegistoChamadas($idContato, $dataHoraInicio, $dataHoraFim);
                break;
            case 24:
                obtemMensagens(1);
                break;
            case 25:
                obtemRegistoMensagens($idContato, $dataHoraInicio, $dataHoraFim);
                break;
            case 26:
                menuFiltraContatos();
                break;
            case 27:
                obtemContatos(NULL, $utilizador, 1);
                break;
            case 28:
                obtemContatos(NULL, $_SESSION['utilizador'], 2);
                break;
            case 29:
                obtemContatos($idContato, NULL, 3);
                break;
            case 30:
                menuEditaContato($idContato);
                break;
            case 31:
                editaContato($idContato, $numProf, $numPessoal, $email, $profPessoal);
                break;
            case 32:
                exportaContatos();
                break;
        }
    }
}

function exportaContatos() {
    $contatos = obtemContatos(NULL, $_SESSION['utilizador'], 0);

    foreach ($contatos as $linha) {
        $string.= "BEGIN:VCARD\nVERSION:3.0\n";
        $string.= "N:;" . $linha['nomeContato'] . ";;;\r\n";
        $string.= "FN:" . $linha['nomeContato'] . "\r\n";
        $string.= "TEL;TYPE=WORK:" . $linha['telemovelProfissional'] . "\r\n";
        $string.= "TEL;TYPE=CELL:" . $linha['telemovelPessoal'] . "\r\n";
        $string.= "EMAIL;TYPE=X-INTERNET:" . $linha['email'] . "\r\n";
        $string.= "NOTE:" . (($linha['profissionalPessoal'] == 1) ? "PROFISSIONAL" : "PESSOAL") . "\r\n";
        $string.= "END:VCARD\r\n";
    }

    $ficheiro = fopen("../Utilitarios/Lista Contatos.vcf", 'w') or die("Falhou a tentativa de criar o ficheiro");
    fwrite($ficheiro, $string) or die("Não foi possível escrever para o ficheiro");
    fclose($ficheiro);

    echo ("<h1>Ficheiro \"Lista Contatos.vcf\" gerado com sucesso!</h1>");
    echo ("<p>Clique <a type=\"text/x-vcard\" href=\"../Utilitarios/Lista Contatos.vcf\"><u>aqui</u></a> para o descarregar</p>");
}

function menuFiltraContatos() {
    $listaContatos = obtemTodosUtilizadores();
    output(NULL, $listaContatos, 23);
}

function menuEditaAplicacao($idAplicacao) {
    if (!isset($_SESSION['aplicacaoCarregada'])) {
        obtemAplicacoes($idAplicacao, 0);
    }
    $tipoApp = obtemTipoApp(NULL, 0);
    $tipoPlataforma = obtemPlataformaOS(NULL, 0);
    output($tipoApp, $tipoPlataforma, 17);
}

function menuEditaContato($idContato) {
    if (!isset($_SESSION['contatoCarregado'])) {
        obtemContatos($idContato, NULL, 0);
    }
    output(NULL, NULL, 28);
}

function menuEditaPlataforma() {
    $plataformaEditar = $_SESSION['plataformaOSCarregada'];
    output($plataformaEditar, NULL, 2);
}

function menuCriaPlataforma() {
    output(NULL, NULL, 4);
}

function menuEliminaPlataforma() {
    $plataformaEliminar = $_SESSION['plataformaOSCarregada'];
    $descricaoPlataforma = $plataformaEliminar['nomePlataforma'];
    output(NULL, $descricaoPlataforma, 6);
}

function pesquisaRegistosPlataforma($idPlataforma) {

    $db = iniciaBD();
    $temp = 0;
    $sql = "SELECT * FROM Aplicacao WHERE idTipoPlataforma=?";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($idPlataforma));
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

function pesquisaRegistosTipoApp($idTipoAplicacao) {

    $db = iniciaBD();
    $temp = 0;
    $sql = "SELECT * FROM Aplicacao WHERE idTipoAplicacao=?";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($idTipoAplicacao));
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

function eliminaPlataforma() {

    $plataforma = $_SESSION['plataformaOSCarregada'];
    $idPlataforma = $plataforma['idTipoPlataformaSmartPhone'];
    $descricaoPlataforma = $plataforma['nomePlataforma'];

    $pesquisa = pesquisaRegistosPlataforma($idPlataforma);

    if ($pesquisa == 0) {
        $db = iniciaBD();
        $sql = "DELETE FROM TipoPlataformaSmartPhone WHERE idTipoPlataformaSmartPhone=?";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(array($idPlataforma));
            $stmt->closeCursor();
        } catch (PDOException $e) {
            echo ("Erro BD: " . $e->getMessage() . "\n");
            echo ("Código erro BD: " . $e->getCode() . "\n");
            exit;
        }
        output(NULL, $descricaoPlataforma, 7);
    } else {
        output(NULL, $descricaoPlataforma, 24);
    }
}

function editaAplicacao($idAplicacao, $idTipoPlataforma, $idTipoApp, $enderecoApp, $comentarioApp, $aplicacaoAtiva) {
    $db = iniciaBD();

    $sql = "UPDATE Aplicacao SET Endereco=?, Comentario=?, idTipoAplicacao=?, idTipoPlataforma=?
        WHERE idAplicacao=?";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($enderecoApp, $comentarioApp, $idTipoApp, $idTipoPlataforma, $idAplicacao));
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }

    if ($_SESSION['aplicacaoCarregada']['dataFim'] == NULL && $aplicacaoAtiva == 0) {
        $db = iniciaBD();
        $hoje = getdate();
        $dataFimInserir = $hoje['year'] . "-" . $hoje['mon'] . "-" . $hoje['mday'] . " " . $hoje['hours'] . ":" . $hoje['minutes'] . ":" . $hoje['seconds'];
        $sql = "UPDATE AplicacaoAtiva SET dataFim=?
        WHERE idAplicacao=? AND dataInicio=?";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(array($dataFimInserir, $idAplicacao, $_SESSION['aplicacaoCarregada']['dataInicio']));
            $stmt->closeCursor();
        } catch (PDOException $e) {
            echo ("Erro BD: " . $e->getMessage() . "\n");
            echo ("Código erro BD: " . $e->getCode() . "\n");
            exit;
        }
    }
    if ($_SESSION['aplicacaoCarregada']['dataFim'] != NULL && $aplicacaoAtiva == 1) {
        $db = iniciaBD();
        $hoje = getdate();
        $dataInicioInserir = $hoje['year'] . "-" . $hoje['mon'] . "-" . $hoje['mday'] . " " . $hoje['hours'] . ":" . $hoje['minutes'] . ":" . $hoje['seconds'];
        $sql = "INSERT INTO AplicacaoAtiva (idAplicacao,dataInicio) 
        VALUES (?,?)";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(array($idAplicacao, $dataInicioInserir));
            $stmt->closeCursor();
        } catch (PDOException $e) {
            echo ("Erro BD: " . $e->getMessage() . "\n");
            echo ("Código erro BD: " . $e->getCode() . "\n");
            exit;
        }
    }
    output(NULL, $_SESSION['aplicacaoCarregada']['nomeAplicacao'], 18);
}

function editaContato($idContato, $numProf, $numPessoal, $email, $profPessoal) {
    $db = iniciaBD();

    $sql = "UPDATE Contatos SET TelemovelProfissional=?, TelemovelPessoal=?, Email=?, ProfissionalPessoal=?
        WHERE idContatos=?";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($numProf, $numPessoal, $email, $profPessoal, $idContato));
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    output(NULL, $_SESSION['contatoCarregado']['nomeContato'], 29);
}

function editaPlataforma($descricaoPlataforma) {
    $db = iniciaBD();

    $plataforma = $_SESSION['plataformaOSCarregada'];
    $idPlataforma = $plataforma['idTipoPlataformaSmartPhone'];

    $sql = "UPDATE TipoPlataformaSmartPhone SET NomePlataforma=? WHERE idTipoPlataformaSmartPhone=?";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($descricaoPlataforma, $idPlataforma));
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    output($idPlataforma, $descricaoPlataforma, 3);
}

function criaPlataforma($descricaoPlataforma) {
    $db = iniciaBD();
// Save the entry into the database
    $sql = "INSERT INTO TipoPlataformaSmartPhone (NomePlataforma) VALUES (?)";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($descricaoPlataforma));
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    output(NULL, $descricaoPlataforma, 5);
}

function obtemTodosUtilizadores() {
    $db = iniciaBD();
    $sql = "SELECT Utilizador, Nome FROM Utilizador ORDER BY Utilizador";
    try {
        foreach ($db->query($sql) as $row) {
            $enviar[] = array(
                'utilizador' => $row['Utilizador'],
                'nome' => $row['Nome']
            );
        }
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    return $enviar;
}

function obtemPlataformaOS($idTipoPlataforma, $output) {
    $db = iniciaBD();
    $_SESSION['plataformaOSCarregada'] = NULL;

    if (isset($idTipoPlataforma)) {
        $sql = "SELECT idTipoPlataformaSmartPhone, NomePlataforma
                FROM TipoPlataformaSmartPhone
                WHERE idTipoPlataformaSmartPhone =?";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(array($idTipoPlataforma));
// Save the returned entry array
            $resultado = $stmt->fetch();
        } catch (PDOException $erro) {
            echo ("Erro BD: " . $erro->getMessage() . "\n");
            echo ("Código erro BD: " . $erro->getCode() . "\n");
            exit;
        }
        $_SESSION['plataformaOSCarregada'] = array(
            'idTipoPlataformaSmartPhone' => $resultado['idTipoPlataformaSmartPhone'],
            'nomePlataforma' => $resultado['NomePlataforma'],
        );
        $enviar = $_SESSION['plataformaOSCarregada'];
    }
    /*
     * If no entry ID was supplied, load all entry titles
     */ else {

        $sql = "SELECT * FROM TipoPlataformaSmartPhone ORDER BY NomePlataforma ASC";
// Loop through returned results and store as an array
        try {
            foreach ($db->query($sql) as $resultado) {
                $enviar[] = array(
                    'idTipoPlataformaSmartPhone' => $resultado['idTipoPlataformaSmartPhone'],
                    'nomePlataforma' => $resultado['NomePlataforma'],
                );
            }
        } catch (PDOException $erro) {
            echo ("Erro BD: " . $erro->getMessage() . "\n");
            echo ("Código erro BD: " . $erro->getCode() . "\n");
            exit;
        }
    }

    if ($output == 1) {
        output($idTipoPlataforma, $enviar, 1);
    } else {
        return $enviar;
    }
}

function obtemChamadas($output) {
    $db = iniciaBD();
    $sql = "SELECT DISTINCT CH.idContato, CH.Utilizador, C.NomeContato, C.TelemovelProfissional, C.Email
            FROM Chamada AS CH, Contatos AS C
            WHERE CH.idContato=C.idContatos AND CH.Utilizador = ?
            ORDER BY NomeContato ASC";
// Loop through returned results and store as an array
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($_SESSION['utilizador']));
// Save the returned entry array
        $resultado = $stmt->fetchAll();
        foreach ($resultado as $row) {
            $enviar[] = array(
                'idContato' => $row['idContato'],
                'nomeContato' => $row['NomeContato'],
                'telemovelProfissional' => $row['TelemovelProfissional'],
                'email' => $row['Email'],
            );
        }
    } catch (PDOException $erro) {
        echo ("Erro BD: " . $erro->getMessage() . "\n");
        echo ("Código erro BD: " . $erro->getCode() . "\n");
        exit;
    }
    if ($output == 1) {
        output(NULL, $enviar, 19);
    } else {
        return $enviar;
    }
}

function obtemMensagens($output) {
    $db = iniciaBD();
    $sql = "SELECT DISTINCT M.idContato, M.Utilizador, C.NomeContato, C.TelemovelProfissional, C.Email
            FROM Mensagem AS M, Contatos AS C
            WHERE M.idContato=C.idContatos AND M.Utilizador = ?
            ORDER BY NomeContato ASC";
// Loop through returned results and store as an array
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($_SESSION['utilizador']));
// Save the returned entry array
        $resultado = $stmt->fetchAll();
        foreach ($resultado as $row) {
            $enviar[] = array(
                'idContato' => $row['idContato'],
                'nomeContato' => $row['NomeContato'],
                'telemovelProfissional' => $row['TelemovelProfissional'],
                'email' => $row['Email'],
            );
        }
    } catch (PDOException $erro) {
        echo ("Erro BD: " . $erro->getMessage() . "\n");
        echo ("Código erro BD: " . $erro->getCode() . "\n");
        exit;
    }
    if ($output == 1) {
        output(NULL, $enviar, 21);
    } else {
        return $enviar;
    }
}

function obtemContatos($idContato, $utilizador, $saida) {
    $db = iniciaBD();
    $_SESSION['contatoCarregado'] = NULL;

    if (isset($idContato)) {
        $sql = "SELECT NomeContato, TelemovelProfissional, TelemovelPessoal, ProfissionalPessoal, Email
                FROM Contatos
                WHERE idContatos =?";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(array($idContato));
// Save the returned entry array
            $resultado = $stmt->fetch();
        } catch (PDOException $erro) {
            echo ("Erro BD: " . $erro->getMessage() . "\n");
            echo ("Código erro BD: " . $erro->getCode() . "\n");
            exit;
        }
        $_SESSION['contatoCarregado'] = array(
            'idContato' => $resultado['idContatos'],
            'nomeContato' => $resultado['NomeContato'],
            'telemovelProfissional' => $resultado['TelemovelProfissional'],
            'telemovelPessoal' => $resultado['TelemovelPessoal'],
            'profissionalPessoal' => $resultado['ProfissionalPessoal'],
            'email' => $resultado['Email'],
        );
        $enviar = $_SESSION['contatoCarregado'];
    }
    /*
     * If no entry ID was supplied, load all entry titles
     */ else if ($_SESSION['utilizador'] == ADMIN && !isset($utilizador) && !isset($idContato)) {

        $sql = "SELECT * FROM Contatos ORDER BY NomeContato ASC";
// Loop through returned results and store as an array
        try {
            foreach ($db->query($sql) as $row) {
                $enviar[] = array(
                    'idContato' => $row['idContatos'],
                    'nomeContato' => $row['NomeContato'],
                    'telemovelProfissional' => $row['TelemovelProfissional'],
                    'telemovelPessoal' => $row['TelemovelPessoal'],
                    'profissionalPessoal' => $row['ProfissionalPessoal'],
                    'email' => $row['Email'],
                );
            }
        } catch (PDOException $erro) {
            echo ("Erro BD: " . $erro->getMessage() . "\n");
            echo ("Código erro BD: " . $erro->getCode() . "\n");
            exit;
        }
    } else if (isset($utilizador) && !isset($idContato)) {
        $sql = "SELECT C.idContatos, C.NomeContato, C.TelemovelProfissional, C.TelemovelPessoal, C.ProfissionalPessoal, C.Email
                FROM Contatos AS C,UtilizadorListaContatos AS U
                WHERE U.Utilizador =? AND C.idContatos = U.idContatos";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(array($utilizador));
// Save the returned entry array
            $resultado = $stmt->fetchAll();
            foreach ($resultado as $row) {
                $enviar[] = array(
                    'idContato' => $row['idContatos'],
                    'nomeContato' => $row['NomeContato'],
                    'telemovelProfissional' => $row['TelemovelProfissional'],
                    'telemovelPessoal' => $row['TelemovelPessoal'],
                    'profissionalPessoal' => $row['ProfissionalPessoal'],
                    'email' => $row['Email'],
                );
            }
        } catch (PDOException $e) {
            echo ("Erro BD: " . $e->getMessage() . "\n");
            echo ("Código erro BD: " . $e->getCode() . "\n");
            exit;
        }
    }

    switch ($saida) {
        case 0:
            return $enviar;
            break;
        case 1:
            output($idContato, $enviar, 0);
            break;
        case 2:
            output($idContato, $enviar, 26);
            break;
        case 3:
            output(NULL, $enviar, 27);
            break;
    }
}

function obtemRegistoAplicacao($idAplicacao, $dataHoraInicio, $dataHoraFim) {
    $db = iniciaBD();
    obtemAplicacoes($idAplicacao, 0);

    $utilizador = $_SESSION['utilizador'];
    $enviar = array();

    $dataHoraFimTratada = $dataHoraFim . " 23:59:59";

    $sql = "SELECT DataHoraInicio, DataHoraFim 
            FROM UtilizadorAplicacoes
            WHERE idAplicacao = ? AND DataHoraInicio >= ? AND DataHoraInicio <= ? AND Utilizador = ? ORDER BY DataHoraInicio";
    try {
// Loop through returned results and store as an array
        $stmt = $db->prepare($sql);
        $stmt->execute(array($idAplicacao, $dataHoraInicio, $dataHoraFimTratada, $utilizador));
        $resultado = $stmt->fetchAll();
        foreach ($resultado as $row) {
            $linhaEvento['dataHoraInicio'] = $row['DataHoraInicio'];
            $linhaEvento['dataHoraFim'] = $row['DataHoraFim'];
            $dataInicioUx = strtotime($row['DataHoraInicio']);
            $dataFimUx = strtotime($row['DataHoraFim']);
            $diferencaHoras = round((($dataFimUx - $dataInicioUx) / 3600), 2);
            $linhaEvento['totalTempo'] = $diferencaHoras;
            $enviar[] = $linhaEvento;
        }
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }

    output(NULL, $enviar, 16);
}

function obtemRegistoChamadas($idContato, $dataHoraInicio, $dataHoraFim) {
    $db = iniciaBD();
//Para carregar variavel de SESSION
    obtemContatos($idContato, NULL, 0);

    $utilizador = $_SESSION['utilizador'];
    $enviar = array();

    $dataHoraFimTratada = $dataHoraFim . " 23:59:59";

    $sql = "SELECT DataChamada, Duracao, RecebidaEfectuada 
            FROM Chamada
            WHERE idContato = ? AND DataChamada >= ? AND DataChamada <= ? AND Utilizador = ? ORDER BY DataChamada";
    try {
// Loop through returned results and store as an array
        $stmt = $db->prepare($sql);
        $stmt->execute(array($idContato, $dataHoraInicio, $dataHoraFimTratada, $utilizador));
        $resultado = $stmt->fetchAll();
        foreach ($resultado as $row) {
            $linhaEvento['dataChamada'] = $row['DataChamada'];
            $linhaEvento['duracao'] = $row['Duracao'];
            $linhaEvento['recebidaEfectuada'] = $row['RecebidaEfectuada'];
            $enviar[] = $linhaEvento;
        }
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    output(NULL, $enviar, 20);
}

function obtemRegistoMensagens($idContato, $dataHoraInicio, $dataHoraFim) {
    $db = iniciaBD();
//Para carregar variavel de SESSION
    obtemContatos($idContato, NULL, 0);

    $utilizador = $_SESSION['utilizador'];
    $enviar = array();

    $dataHoraFimTratada = $dataHoraFim . " 23:59:59";

    $sql = "SELECT DataMensagem, Texto, RecebidaEnviada 
            FROM Mensagem
            WHERE idContato = ? AND DataMensagem >= ? AND DataMensagem  <= ? AND Utilizador = ? ORDER BY DataMensagem ";
    try {
// Loop through returned results and store as an array
        $stmt = $db->prepare($sql);
        $stmt->execute(array($idContato, $dataHoraInicio, $dataHoraFimTratada, $utilizador));
        $resultado = $stmt->fetchAll();
        foreach ($resultado as $row) {
            $linhaEvento['dataMensagem'] = $row['DataMensagem'];
            $linhaEvento['texto'] = $row['Texto'];
            $linhaEvento['recebidaEnviada'] = $row['RecebidaEnviada'];
            $enviar[] = $linhaEvento;
        }
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    output(NULL, $enviar, 22);
}

function obtemAplicacoes($idAplicacao, $output) {
    $db = iniciaBD();
    $_SESSION['aplicacaoCarregada'] = NULL;

    if (isset($idAplicacao)) {
        $sql = "SELECT DISTINCT A.NomeAplicacao, A.Endereco, A.Comentario, TA.NomeTipoAplicacao, TP.NomePlataforma, A.idTipoAplicacao, A.idTipoPlataforma, RS.dataInicio,RS.dataFim
                FROM Aplicacao AS A, TipoPlataformaSmartPhone AS TP, TipoAplicacao AS TA, AplicacaoAtiva AS AA, 
                (SELECT AP.idAplicacao, AP.dataInicio, AP.dataFim FROM (SELECT idAplicacao, MAX(dataInicio) AS dataInicioObtida
                    FROM AplicacaoAtiva
                    GROUP BY idAplicacao) RES
                    JOIN fagt.AplicacaoAtiva AP ON RES.idAplicacao = AP.idAplicacao AND AP.dataInicio = RES.dataInicioObtida) AS RS
                WHERE A.idAplicacao =? AND A.idTipoAplicacao = TA.idTipoAplicacao AND A.idAplicacao = AA.idAplicacao AND A.idTipoPlataforma = TP.idTipoPlataformaSmartPhone AND A.idAplicacao=RS.idAplicacao";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(array($idAplicacao));
// Save the returned entry array
            $resultado = $stmt->fetch();
        } catch (PDOException $erro) {
            echo ("Erro BD: " . $erro->getMessage() . "\n");
            echo ("Código erro BD: " . $erro->getCode() . "\n");
            exit;
        }
//        $_SESSION['aplicacaoCarregada'] = array(
//            'idAplicacao' => $resultado['idAplicacao'],
//            'nomeAplicacao' => $resultado['NomeAplicacao'],
//            'endereco' => $resultado['Endereco'],
//            'comentario' => $resultado['Comentario'],
//            'tipoAplicacao' => $resultado['NomeTipoAplicacao'],
//            'tipoSO' => $resultado['NomePlataforma'],
//            'idTipoAplicacao' => $resultado['idTipoAplicacao'],
//            'idTipoPlataforma' => $resultado['idTipoPlataforma'],
//            'dataInicio' => $resultado['dataInicio'],
//            'dataFim' => $resultado['dataFim'],
//        );
        $db2 = iniciaBD();
        $sql2 = "SELECT dataInicio, dataFim
                FROM AplicacaoAtiva 
                WHERE idAplicacao =? ORDER BY dataInicio DESC";
        try {
            $stmt2 = $db2->prepare($sql2);
            $stmt2->execute(array($idAplicacao));
// Save the returned entry array
            $resultado2 = $stmt2->fetchAll();
            foreach ($resultado2 as $row2) {
                $historicoAtivacaoApp[] = array(
                    'dataInicio' => $row2['dataInicio'],
                    'dataFim' => $row2['dataFim']
                );
            }
            $_SESSION['aplicacaoCarregada'] = array(
                'idAplicacao' => $resultado['idAplicacao'],
                'nomeAplicacao' => $resultado['NomeAplicacao'],
                'endereco' => $resultado['Endereco'],
                'comentario' => $resultado['Comentario'],
                'tipoAplicacao' => $resultado['NomeTipoAplicacao'],
                'tipoSO' => $resultado['NomePlataforma'],
                'idTipoAplicacao' => $resultado['idTipoAplicacao'],
                'idTipoPlataforma' => $resultado['idTipoPlataforma'],
                'dataInicio' => $resultado['dataInicio'],
                'dataFim' => $resultado['dataFim'],
                'historicoAtivacaoApp' => $historicoAtivacaoApp
            );
        } catch (PDOException $erro) {
            echo ("Erro BD: " . $erro->getMessage() . "\n");
            echo ("Código erro BD: " . $erro->getCode() . "\n");
            exit;
        }

        $enviar = $_SESSION['aplicacaoCarregada'];
    }
    /*
     * If no entry ID was supplied, load all entry titles
     */ else {

        $sql = "SELECT DISTINCT A.idAplicacao, A.NomeAplicacao, A.Endereco, A.Comentario, TP.NomePlataforma, TA.NomeTipoAplicacao,RS.dataInicio,RS.dataFim
            FROM Aplicacao AS A, TipoPlataformaSmartPhone AS TP, TipoAplicacao AS TA, UtilizadorAplicacoes AS UA, AplicacaoAtiva AS AA,
                    (SELECT AP.idAplicacao, AP.dataInicio, AP.dataFim FROM (SELECT idAplicacao, MAX(dataInicio) AS dataInicioObtida
                        FROM AplicacaoAtiva
                        GROUP BY idAplicacao) RES
                        JOIN fagt.AplicacaoAtiva AP ON RES.idAplicacao = AP.idAplicacao AND AP.dataInicio = RES.dataInicioObtida) AS RS           
            WHERE UA.Utilizador = ? AND A.idTipoAplicacao = TA.idTipoAplicacao AND A.idTipoPlataforma = TP.idTipoPlataformaSmartPhone AND A.idAplicacao = UA.idAplicacao AND A.idAplicacao=RS.idAplicacao
        ORDER BY NomeAplicacao ASC";

// Loop through returned results and store as an array
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(array($_SESSION['utilizador']));
// Save the returned entry array
            $resultado = $stmt->fetchAll();
            foreach ($resultado as $row) {
                $enviar[] = array(
                    'idAplicacao' => $row['idAplicacao'],
                    'nomeAplicacao' => $row['NomeAplicacao'],
                    'endereco' => $row['Endereco'],
                    'comentario' => $row['Comentario'],
                    'tipoAplicacao' => $row['NomeTipoAplicacao'],
                    'tipoSO' => $row['NomePlataforma'],
                    'dataInicio' => $row['dataInicio'],
                    'dataFim' => $row['dataFim']
                );
            }
        } catch (PDOException $erro) {
            echo ("Erro BD: " . $erro->getMessage() . "\n");
            echo ("Código erro BD: " . $erro->getCode() . "\n");
            exit;
        }
    }

    if ($output == 1) {
        output($idAplicacao, $enviar, 15);
    } else {
        return $enviar;
    }
}

function menuEditaTipoApp() {
    $tipoAppEditar = $_SESSION['tipoAppCarregada'];
    output($tipoAppEditar, NULL, 9);
}

function menuCriaTipoApp() {
    output(NULL, NULL, 11);
}

function menuEliminaTipoApp() {
    $tipoAppEliminar = $_SESSION['tipoAppCarregada'];
    $descricaoTipoApp = $tipoAppEliminar['nomeTipoAplicacao'];
    output(NULL, $descricaoTipoApp, 13);
}

function eliminaTipoApp() {

    $tipoApp = $_SESSION['tipoAppCarregada'];
    $idTipoApp = $tipoApp['idTipoAplicacao'];
    $descricaoTipoApp = $tipoApp['nomeTipoAplicacao'];

    $pesquisa = pesquisaRegistosTipoApp($idTipoApp);

    if ($pesquisa == 0) {
        $db = iniciaBD();
        $sql = "DELETE FROM TipoAplicacao WHERE idTipoAplicacao=?";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(array($idTipoApp));
            $stmt->closeCursor();
        } catch (PDOException $e) {
            echo ("Erro BD: " . $e->getMessage() . "\n");
            echo ("Código erro BD: " . $e->getCode() . "\n");
            exit;
        }
        output(NULL, $descricaoTipoApp, 14);
    } else {
        output(NULL, $descricaoTipoApp, 25);
    }
}

function editaTipoApp($descricaoTipoApp) {
    $db = iniciaBD();

    $tipoApp = $_SESSION['tipoAppCarregada'];
    $idTipoApp = $tipoApp['idTipoAplicacao'];

    $sql = "UPDATE TipoAplicacao SET NomeTipoAplicacao=? WHERE idTipoAplicacao=?";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($descricaoTipoApp, $idTipoApp));
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    output($idTipoApp, $descricaoTipoApp, 10);
}

function criaTipoApp($descricaoTipoApp) {
    $db = iniciaBD();
// Save the entry into the database
    $sql = "INSERT INTO TipoAplicacao (NomeTipoAplicacao) VALUES (?)";
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute(array($descricaoTipoApp));
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo ("Erro BD: " . $e->getMessage() . "\n");
        echo ("Código erro BD: " . $e->getCode() . "\n");
        exit;
    }
    output(NULL, $descricaoTipoApp, 12);
}

function obtemTipoApp($idTipoApp, $output) {
    $db = iniciaBD();
    $_SESSION['tipoAppCarregada'] = NULL;

    if (isset($idTipoApp)) {
        $sql = "SELECT idTipoAplicacao, NomeTipoAplicacao
                FROM TipoAplicacao
                WHERE idTipoAplicacao =?";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute(array($idTipoApp));
// Save the returned entry array
            $resultado = $stmt->fetch();
        } catch (PDOException $erro) {
            echo ("Erro BD: " . $erro->getMessage() . "\n");
            echo ("Código erro BD: " . $erro->getCode() . "\n");
            exit;
        }
        $_SESSION['tipoAppCarregada'] = array(
            'idTipoAplicacao' => $resultado['idTipoAplicacao'],
            'nomeTipoAplicacao' => $resultado['NomeTipoAplicacao'],
        );
        $enviar = $_SESSION['tipoAppCarregada'];
    }
    /*
     * If no entry ID was supplied, load all entry titles
     */ else {

        $sql = "SELECT * FROM TipoAplicacao ORDER BY NomeTipoAplicacao ASC";
// Loop through returned results and store as an array
        try {
            foreach ($db->query($sql) as $resultado) {
                $enviar[] = array(
                    'idTipoAplicacao' => $resultado['idTipoAplicacao'],
                    'nomeTipoAplicacao' => $resultado['NomeTipoAplicacao'],
                );
            }
        } catch (PDOException $erro) {
            echo ("Erro BD: " . $erro->getMessage() . "\n");
            echo ("Código erro BD: " . $erro->getCode() . "\n");
            exit;
        }
    }

    if ($output == 1) {
        output($idTipoApp, $enviar, 8);
    } else {
        return $enviar;
    }
}

function output($id, $dados, $tipoOutput) {
    switch ($tipoOutput) {
        case 0:
            if ($dados != NULL) {
                echo ("<h1>Contatos</h1>");
                echo("<div id=\"listaContatosCSS\"><table id=\"listaContatos\" class=\"sortable\" onmouseover=\"ordenaLista(0);\">");
                echo ("<thead><tr><th>Nome</th><th>N. Profissional</th><th>N. Pessoal</th><th>Email</th><th class=\"sorttable_nosort\">Contato Profissional?</th></tr></thead><tbody>");
                foreach ($dados as $entry) {
                    echo ("<tr class=\"alternaCor\" \"id=\"" . $entry['idContato'] . "\"><td>" . $entry['nomeContato'] . "</td><td>" . $entry['telemovelProfissional'] . "</td>");
                    echo ("<td>" . $entry['telemovelPessoal'] . "</td><td>" . $entry['email'] . "</td><td><input type=\"checkbox\" disabled=\"disabled\" " . (($entry['profissionalPessoal'] == 1) ? ("checked=\"checked\" id=\"true\"") : ("id=\"false\"")) . "></td></tr>");
                }
                echo("</tbody></table></div>");
                break;
            } else {
                echo("<h1>De momento, o sistema não possui Contatos!</h1>");
                break;
            }
        case 1:
            if ($id == NULL) {
                if ($dados != NULL) {
                    echo ("<h1>Plataforma S. O.</h1>");
                    echo("<p><form><select id=\"listaSistemaOperativo\" onchange=\"MakeXMLHTTPCall(27);\"><option selected=\"selected\" value=\"escolha\">Escolha um S.O.</option>");
                    foreach ($dados as $entry) {
                        echo ("<option value=\"" . $entry['idTipoPlataformaSmartPhone'] . "\">" . $entry['nomePlataforma'] . "</option>");
                    }
                    echo("</select></form></p>");
                } else {
                    echo("<h1>De momento, o sistema não possui Plataformas de S.Operativos!<br>Utilize o menu \"Criar\" à direita.</h1>");
                    break;
                }
            } else {
                echo ("<h1 id=\"detalhesSistemaOperativo\">Detalhes Plataforma S.O.</h1>");
                echo ("<p>Plataforma: " . $dados['nomePlataforma'] . "</p>");
            }
            break;
        case 2:
            echo ("<h1>Editar Plataforma S.O.</h1>");
            echo ("<table><tbody><tr><td><p>Descrição: </p></td>");
            echo ("<td><input type=\"text\" id=\"descricaoPlataformaEditar\" name=\"descricaoPlataformaEditar\" maxlength=\"45\" value=\"" . $id['nomePlataforma'] . "\"/></td></tr>");
            echo ("<tr><td></td><td><button type=\"button\" id=\"guardarPlataforma\" onclick=\"MakeXMLHTTPCall(29);\">Guardar</button>");
            echo ("<button type=\"button\" id=\"cancelarPlataforma\" onclick=\"MakeXMLHTTPCall(26);\">Cancelar</button></td></tr>");
            echo ("</tbody></table>");
            break;
        case 3:
            echo ("<h1>A plataforma S. O. \"" . $dados . "\" foi actualizada com sucesso.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(26);\">Voltar</a>");
            break;
        case 4:
            echo ("<h1>Criar Plataforma S. O.</h1>");
            echo ("<table><tbody><tr><td><p>Descrição: </p></td>");
            echo ("<td><input type=\"text\" id=\"descricaoPlataformaCriar\" name=\"descricaoPlataformaCriar\" maxlength=\"45\"/></td></tr>");
            echo ("<tr><td></td><td><button type=\"button\" id=\"criarPlataforma\" onclick=\"MakeXMLHTTPCall(31);\">Criar</button>");
            echo ("<button type=\"button\" id=\"cancelarPlataforma\" onclick=\"MakeXMLHTTPCall(26);\">Cancelar</button></td></tr>");
            echo ("</tbody></table>");
            break;
        case 5:
            echo ("<h1>O S. Operativo \"" . $dados . "\" foi criado com sucesso.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(26);\">Voltar</a>");
            break;
        case 6:
            echo ("<div><h1>Pretende eliminar o S. Operativo \"" . $dados . "\"?</h1></div><div>");
            echo ("<button type=\"button\" id=\"eliminarPlataforma\" onclick=\"MakeXMLHTTPCall(33);\">Sim</button>");
            echo ("<button type=\"button\" id=\"cancelarEliminarPlataforma\" onclick=\"MakeXMLHTTPCall(26);\">Não</button>");
            echo ("</div>");
            break;
        case 7:
            echo ("<h1>O S. Operativo \"" . $dados . "\" foi eliminado com sucesso.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(26);\">Voltar</a>");
            break;
        case 8:
            if ($id == NULL) {
                if ($dados != NULL) {
                    echo ("<h1>Tipo Aplicação</h1>");
                    echo("<p><form><select id=\"listaTipoApp\" onchange=\"MakeXMLHTTPCall(35);\"><option selected=\"selected\" value=\"escolha\">Escolha um tipo</option>");
                    foreach ($dados as $entry) {
                        echo ("<option value=\"" . $entry['idTipoAplicacao'] . "\">" . $entry['nomeTipoAplicacao'] . "</option>");
                    }
                    echo("</select></form></p>");
                } else {
                    echo("<h1>De momento, o sistema não possui Tipos de Aplicação!<br>Utilize o menu \"Criar\" à direita.</h1>");
                    break;
                }
            } else {
                echo ("<h1 id=\"detalhesTipoApp\">Detalhes Tipo Aplicação</h1>");
                echo ("<p>Tipo: " . $dados['nomeTipoAplicacao'] . "</p>");
            }
            break;
        case 9:
            echo ("<h1>Editar Tipo Aplicação</h1>");
            echo ("<table><tbody><tr><td><p>Descrição: </p></td>");
            echo ("<td><input type=\"text\" id=\"descricaoTipoAppEditar\" name=\"descricaoTipoAppEditar\" maxlength=\"45\" value=\"" . $id['nomeTipoAplicacao'] . "\"/></td></tr>");
            echo ("<tr><td></td><td><button type=\"button\" id=\"guardarTipoApp\" onclick=\"MakeXMLHTTPCall(37);\">Guardar</button>");
            echo ("<button type=\"button\" id=\"cancelarTipoApp\" onclick=\"MakeXMLHTTPCall(34);\">Cancelar</button></td></tr>");
            echo ("</tbody></table>");
            break;
        case 10:
            echo ("<h1>O tipo de aplicação \"" . $dados . "\" foi actualizado com sucesso.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(34);\">Voltar</a>");
            break;
        case 11:
            echo ("<h1>Criar Tipo Aplicação</h1>");
            echo ("<table><tbody><tr><td><p>Descrição: </p></td>");
            echo ("<td><input type=\"text\" id=\"descricaoTipoAppCriar\" name=\"descricaoTipoAppCriar\" maxlength=\"45\"/>");
            echo ("</td></tr>");
            echo ("<tr><td></td><td><button type=\"button\" id=\"criarTipoApp\" onclick=\"MakeXMLHTTPCall(39);\">Criar</button>");
            echo ("<button type=\"button\" id=\"cancelarTipoApp\" onclick=\"MakeXMLHTTPCall(34);\">Cancelar</button></td></tr>");
            echo ("</tbody></table>");
            break;
        case 12:
            echo ("<h1>O tipo de aplicação \"" . $dados . "\" foi criado com sucesso.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(34);\">Voltar</a>");
            break;
        case 13:
            echo ("<div><h1>Pretende eliminar o tipo de aplicação \"" . $dados . "\"?</h1></div><div>");
            echo ("<button type=\"button\" id=\"eliminarTipoApp\" onclick=\"MakeXMLHTTPCall(41);\">Sim</button>");
            echo ("<button type=\"button\" id=\"cancelarEliminarTipoApp\" onclick=\"MakeXMLHTTPCall(34);\">Não</button>");
            echo ("</div>");
            break;
        case 14:
            echo ("<h1>O tipo de aplicação \"" . $dados . "\" foi eliminado com sucesso.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(34);\">Voltar</a>");
            break;
        case 15:
            $temp = 0;
            if (!isset($id) && $dados != NULL) {
                echo ("<h1 id=\"headerAplicacao\">Aplicações</h1>");
                echo ("<div id=\"listaAplicacoesCSS\"><form name=\"aplicacoesForm\"><table id=\"listaAplicacoes\" class=\"sortable\" onmouseover=\"ordenaLista(1);\">");
                echo ("<thead><tr><th></th><th>Nome</th><th>Tipo Aplicação</th><th>Descrição</th><th class=\"sorttable_nosort\">Ativa?</th></tr></thead><tbody>");
                foreach ($dados as $entry) {
                    echo ("<tr class=\"alternaCor\"><td><input type=\"radio\" value=\"" . $entry['idAplicacao'] . "\" name=\"aplicacoesRadio\" id=\"aplicacoesRadio" . $entry['idAplicacao'] . "\" " . (($temp == 0) ? "checked" : "") . "></td><td>" . $entry['nomeAplicacao'] . "</td><td>" . $entry['tipoAplicacao'] . "</td>");
                    echo ("<td>" . $entry['comentario'] . "</td><td><input type=\"checkbox\" disabled=\"disabled\" value=\"check" . $entry['idAplicacao'] . "\" name=\"aplicacoesCheck\" id=\"aplicacoesCheck" . $entry['idAplicacao'] . "\" " . (($entry['dataFim'] == NULL) ? "checked" : "") . "></td></tr>");
                    $temp = 1;
                }
                echo("</tbody></table></form></div>");
            } else if (isset($id)) {
                echo ("<h1 id=\"headerAplicacao\">Detalhe Aplicação</h1>");
                echo ("<table><tbody><tr><td><p>Nome: </p></td><td><p>" . $_SESSION['aplicacaoCarregada']['nomeAplicacao'] . "</p></td></tr>");
                echo ("<tr><td><p>Tipo Aplicação: </p></td><td><p>" . $_SESSION['aplicacaoCarregada']['tipoAplicacao'] . "</p></td></tr>");
                echo ("<tr><td><p>Plataforma: </p></td><td><p>" . $_SESSION['aplicacaoCarregada']['tipoSO'] . "</p></td></tr>");
                echo ("<tr><td><p>Descriçao: </p></td><td><p>" . $_SESSION['aplicacaoCarregada']['comentario'] . "</p></td></tr>");
                echo ("<tr><td><p>Endereço: </p></td><td><p>" . $_SESSION['aplicacaoCarregada']['endereco'] . "</p></td></tr>");
                if ($_SESSION['aplicacaoCarregada']['dataFim'] == NULL) {
                    echo ("<tr><td><p>Ativa desde: </p></td><td><p>" . $_SESSION['aplicacaoCarregada']['dataInicio'] . "</p></td></tr>");
                } else {
                    echo ("<tr><td><p>Data Ativação: </p></td><td><p>" . $_SESSION['aplicacaoCarregada']['dataInicio'] . "</p></td></tr>");
                    echo ("<tr><td><p>Data Desativação: </p></td><td><p>" . $_SESSION['aplicacaoCarregada']['dataFim'] . "</p></td></tr>");
                }
                echo ("</tbody></table><br><p>Histórico Ativações</p><br>");
                echo("<div id=\"historicoAtivacoesAplicacoesCSS\"><table><thead><tr><th><p>Data Início</p></th><th><p>Data Fim</p></th></tr></thead>");
                echo("<tbody>");
                foreach($_SESSION['aplicacaoCarregada']['historicoAtivacaoApp'] as $historico){
                    echo("<tr><td>".$historico['dataInicio']."&nbsp;</td><td>&nbsp;".$historico['dataFim']."</td></tr>");
                }
                echo("</tbody></table></div>");
                
            } else if ($dados == NULL) {
                echo ("<h1>De momento, não existem Aplicações associadas a este utilizador.<br>Utilize a aplicação desktop para descarregar dados para o sistema!</h1>");
            }
            break;
        case 16:

            echo ("<h1>Histórico Aplicação \"" . $_SESSION['aplicacaoCarregada']['nomeAplicacao'] . "\" - " . $_SESSION['dataHoraInicio'] . " a " . $_SESSION['dataHoraFim'] . "</h1>");
            if ($dados != NULL) {
                echo ("<div id=\"historicoAplicacoesCSS\"><form name=\"aplicacoesHistoricoForm\"><table id=\"historicoAplicacoes\" class=\"sortable\" onmouseover=\"ordenaLista(2);\">");
                echo ("<thead><tr><th>Data Início</th><th>Data Fim</th><th>Total (horas)</th></tr></thead><tbody>");
                foreach ($dados as $entry) {
                    echo ("<tr class=\"alternaCor\"><td>" . $entry['dataHoraInicio'] . "</td><td>" . $entry['dataHoraFim'] . "</td>");
                    echo ("<td>" . $entry['totalTempo'] . "</td></tr>");
                }
                echo("</tbody></table></form></div>");
            } else {
                echo ("<h1><u>Não foram utilizadas aplicações no intervalo de tempo em questão!</u></h1>");
            }
            break;
        case 17:
            echo ("<h1>Editar Aplicação</h1>");
            echo ("<table><tbody><tr><td><p>Nome: </p></td>");
            echo ("<td><input type=\"text\" id=\"descricaoNomeAplicacaoEditar\" name=\"descricaoNomeAplicacaoEditar\" disabled=\"disabled\" maxlength=\"45\" value=\"" . $_SESSION['aplicacaoCarregada']['nomeAplicacao'] . "\"/></td></tr>");
            echo ("<tr><td><p>Endereço: </p></td>");
            echo ("<td><input type=\"text\" id=\"descricaoEnderecoAplicacaoEditar\" autofocus=\"autofocus\" name=\"descricaoEnderecoAplicacaoEditar\" maxlength=\"100\" value=\"" . $_SESSION['aplicacaoCarregada']['endereco'] . "\"/></td></tr>");
            echo ("<tr><td><p>Comentário: </p></td>");
            echo ("<td><input type=\"text\" id=\"descricaoComentarioAplicacaoEditar\" name=\"descricaoComentarioAplicacaoEditar\" maxlength=\"400\" value=\"" . $_SESSION['aplicacaoCarregada']['comentario'] . "\"/></td></tr>");
            echo ("<tr><td><p>Plataforma: </p></td>");
            echo ("<td><select name=\"descricaoPlataformaAplicacao\" id=\"descricaoPlataformaAplicacaoEditar\"><option selected=\"selected\" value=\"" . $_SESSION['aplicacaoCarregada']['idTipoPlataforma'] . "\">" . $_SESSION['aplicacaoCarregada']['tipoSO'] . "</option>");
            foreach ($dados as $entry) {
                if ($entry['idTipoPlataformaSmartPhone'] != $_SESSION['aplicacaoCarregada']['idTipoPlataforma']) {
                    echo ("<option value=\"" . $entry['idTipoPlataformaSmartPhone'] . "\">" . $entry['nomePlataforma'] . "</option>");
                }
            }
            echo ("</select></td></tr>");
            echo ("<tr><td><p>Tipo Aplicação: </p></td>");
            echo ("<td><select name=\"descricaoTipoAplicacao\" id=\"descricaoTipoAplicacaoEditar\"><option selected=\"selected\" value=\"" . $_SESSION['aplicacaoCarregada']['idTipoAplicacao'] . "\">" . $_SESSION['aplicacaoCarregada']['tipoAplicacao'] . "</option>");
            foreach ($id as $entry) {
                if ($entry['idTipoAplicacao'] != $_SESSION['aplicacaoCarregada']['idTipoAplicacao']) {
                    echo ("<option value=\"" . $entry['idTipoAplicacao'] . "\">" . $entry['nomeTipoAplicacao'] . "</option>");
                }
            }
            echo ("</select></td></tr>");
            echo ("<tr><td><p>Ativa? </p></td><td><form name=\"aplicacaoEditar\"><input type=\"checkbox\" value=\"checkEdita" . $_SESSION['aplicacaoCarregada']['idAplicacao'] . "\" name=\"aplicacoesCheckEdita\" id=\"aplicacoesCheckEdita" . $_SESSION['aplicacaoCarregada']['idAplicacao'] . "\" " . (($_SESSION['aplicacaoCarregada']['dataFim'] == NULL) ? "checked" : "") . "></form></td></tr>");
            echo ("<tr><td></td><td><button type=\"button\" id=\"guardarPlataforma\" onclick=\"MakeXMLHTTPCall(49);\">Guardar</button>");
            echo ("<button type=\"button\" id=\"cancelarPlataforma\" onclick=\"MakeXMLHTTPCall(44);\">Cancelar</button></td></tr>");
            echo ("</tbody></table>");
            break;
        case 18:
            echo ("<h1>A Aplicação \"" . $dados . "\" foi actualizada com sucesso.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(44);\">Voltar</a>");
            break;
        case 19:
            $temp = 0;
            if ($dados != NULL) {
                echo ("<h1>Contatos com Chamadas</h1>");
                echo ("<div id=\"listaChamadasCSS\"><form name=\"chamadasForm\"><table id=\"listaChamadas\" class=\"sortable\" onmouseover=\"ordenaLista(3);\">");
                echo ("<thead><tr><th></th><th>Nome</th><th>N. Profissional</th><th>Email</th></tr></thead><tbody>");
                foreach ($dados as $entry) {
                    echo ("<tr class=\"alternaCor\"><td><input type=\"radio\" value=\"" . $entry['idContato'] . "\" name=\"chamadasRadio\" id=\"chamadasRadio" . $entry['idContato'] . "\" " . (($temp == 0) ? "checked" : "") . "></td><td>" . $entry['nomeContato'] . "</td><td>" . $entry['telemovelProfissional'] . "</td>");
                    echo ("<td>" . $entry['email'] . "</td></tr>");
                    $temp = 1;
                }
                echo("</tbody></table></form></div>");
                break;
            } else {
                echo ("<h1>De momento, não existem Chamadas associadas a contatos deste utilizador.<br>Utilize a aplicação desktop para descarregar dados para o sistema!</h1>");
                break;
            }
        case 20:
            echo ("<h1>Histórico Chamadas <br>\"" . $_SESSION['contatoCarregado']['nomeContato'] . "\" (" . $_SESSION['contatoCarregado']['email'] . ") - " . $_SESSION['dataHoraInicio'] . " a " . $_SESSION['dataHoraFim'] . "</h1>");
            if ($dados != NULL) {
                echo ("<div id=\"historicoChamadasCSS\"><form name=\"chamadasHistoricoForm\"><table id=\"historicoChamadas\" class=\"sortable\" onmouseover=\"ordenaLista(4);\">");
                echo ("<thead><tr><th>Data</th><th>Total (minutos)</th><th class=\"sorttable_nosort\">Efectuada?</th></tr></thead><tbody>");
                foreach ($dados as $entry) {
                    echo ("<tr class=\"alternaCor\"><td>" . $entry['dataChamada'] . "</td><td>" . $entry['duracao'] . "</td>");
                    echo ("<td><input type=\"checkbox\" disabled=\"disabled\" " . (($entry['recebidaEfectuada'] == 1) ? ("checked=\"checked\" id=\"true\"") : ("id=\"false\"")) . "</td></tr>");
                }
                echo("</tbody></table></form></div>");
            } else {
                echo ("<h1><u>Não existem chamdas para o intervalo de tempo em questão!</u></h1>");
            }
            break;
        case 21:
            $temp = 0;
            if ($dados != NULL) {
                echo ("<h1>Contatos com Mensagens</h1>");
                echo ("<div id=\"listaMensagensCSS\"><form name=\"mensagensForm\"><table id=\"listaMensagens\" class=\"sortable\" onmouseover=\"ordenaLista(5);\">");
                echo ("<thead><tr><th></th><th>Nome</th><th>N. Profissional</th><th>Email</th></tr></thead><tbody>");
                foreach ($dados as $entry) {
                    echo ("<tr class=\"alternaCor\"><td><input type=\"radio\" value=\"" . $entry['idContato'] . "\" name=\"mensagensRadio\" id=\"mensagensRadio" . $entry['idContato'] . "\" " . (($temp == 0) ? "checked" : "") . "></td><td>" . $entry['nomeContato'] . "</td><td>" . $entry['telemovelProfissional'] . "</td>");
                    echo ("<td>" . $entry['email'] . "</td></tr>");
                    $temp = 1;
                }
                echo("</tbody></table></form></div>");
                break;
            } else {
                echo ("<h1>De momento, não existem Mensagens associadas a contatos deste utilizador.<br>Utilize a aplicação desktop para descarregar dados para o sistema!</h1>");
                break;
            }
        case 22:
            echo ("<h1>Histórico Mensagens <br>\"" . $_SESSION['contatoCarregado']['nomeContato'] . "\" (" . $_SESSION['contatoCarregado']['email'] . ") - " . $_SESSION['dataHoraInicio'] . " a " . $_SESSION['dataHoraFim'] . "</h1>");
            if ($dados != NULL) {
                echo ("<div id=\"historicoMensagensCSS\"><form name=\"mensagensHistoricoForm\"><table id=\"historicoMensagens\" class=\"sortable\" onmouseover=\"ordenaLista(6);\">");
                echo ("<thead><tr><th>Data</th><th>Texto</th><th class=\"sorttable_nosort\">Enviada?</th></tr></thead><tbody>");
                foreach ($dados as $entry) {
                    echo ("<tr class=\"alternaCor\"><td>" . $entry['dataMensagem'] . "</td><td>" . $entry['texto'] . "</td>");
                    echo ("<td><input type=\"checkbox\" disabled=\"disabled\" " . (($entry['recebidaEnviada'] == 1) ? ("checked=\"checked\" id=\"true\"") : ("id=\"false\"")) . "</td></tr>");
                }
                echo("</tbody></table></form></div>");
            } else {
                echo ("<h1><u>Não existem mensagens para o intervalo de tempo em questão!</u></h1>");
            }
            break;
        case 23:
            echo ("<u>Lista Utilizadores</u>");
            echo ("<table><tbody><tr><td><select name=\"filtraContatos\" id=\"filtraContatos\">");
            foreach ($dados as $entry) {
                echo ("<option value=\"" . $entry['utilizador'] . "\">" . $entry['utilizador'] . "</option>");
            }
            echo ("</select><td></td></tr>");
            echo ("<tr><td><button type=\"button\" id=\"filtrarContato\" onclick=\"MakeXMLHTTPCall(57);\">Pesquisar</button></td>");
            echo ("<td><button type=\"button\" id=\"cancelarFiltrarContato\" onclick=\"MakeXMLHTTPCall(25);\">Cancelar</button></td></tr>");
            echo ("</tbody></table>");
            break;
        case 24:
            echo ("<h1>O S. Operativo \"" . $dados . "\"  não pode ser eliminado, pois encontra-se associado a pelo menos um registo na BD.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(26);\">Voltar</a>");
            break;
        case 25:
            echo ("<h1>O tipo de aplicação \"" . $dados . "\"  não pode ser eliminada, pois encontra-se associada a pelo menos um registo na BD.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(34);\">Voltar</a>");
            break;
        case 26:
            $temp = 0;
            if ($dados != NULL) {
                echo ("<h1>Contatos</h1>");
                echo("<div id=\"listaContatosCSS\"><form name=\"contatosForm\"><table id=\"listaContatos\" class=\"sortable\" onmouseover=\"ordenaLista(0);\">");
                echo ("<thead><tr><th></th><th>Nome</th><th>N. Profissional</th><th>N. Pessoal</th></tr></thead><tbody>");
                foreach ($dados as $entry) {
                    echo ("<tr class=\"alternaCor\"><td><input type=\"radio\" value=\"" . $entry['idContato'] . "\" name=\"contatosRadio\" id=\"contatosRadio" . $entry['idContato'] . "\" " . (($temp == 0) ? "checked" : "") . "></td>");
                    echo ("<td>" . $entry['nomeContato'] . "</td><td>" . $entry['telemovelProfissional'] . "</td>");
                    echo ("<td>" . $entry['telemovelPessoal'] . "</td></tr>");
                    $temp++;
                }
                echo("</tbody></table></form></div>");
                break;
            } else {
                echo ("<h1>De momento, não existem Contatos associados a este utilizador.<br>Utilize a aplicação desktop para descarregar dados para o sistema!</h1>");
                break;
            }
        case 27:
            echo ("<h1 id=\"detalhesContato\">Detalhe Contato</h1>");
            echo ("<table><tbody><tr><td><p>Nome: </p></td><td><p>" . $_SESSION['contatoCarregado']['nomeContato'] . "</p></td></tr>");
            echo ("<tr><td><p>N. Profissional: </p></td><td><p>" . $_SESSION['contatoCarregado']['telemovelProfissional'] . "</p></td></tr>");
            echo ("<tr><td><p>N. Pessoal: </p></td><td><p>" . $_SESSION['contatoCarregado']['telemovelPessoal'] . "</p></td></tr>");
            echo ("<tr><td><p>email: </p></td><td><p>" . $_SESSION['contatoCarregado']['email'] . "</p></td></tr>");
            echo ("<tr><td><p>Contato Profissional? </p></td><td><p><input type=\"checkbox\" disabled=\"disabled\" " . (($_SESSION['contatoCarregado']['profissionalPessoal'] == true) ? ("checked=\"checked\" id=\"true\"") : ("id=\"false\"")) . "</p></td></tr>");
            echo ("</tbody></table>");
            break;
        case 28:
            echo ("<h1>Editar Contato</h1>");
            echo ("<table><tbody><tr><td><p>Nome: </p></td>");
            echo ("<td><input type=\"text\" id=\"descricaoNomeContatoEditar\" name=\"descricaoNomeContatoEditar\" disabled=\"disabled\" maxlength=\"45\" value=\"" . $_SESSION['contatoCarregado']['nomeContato'] . "\"/></td></tr>");
            echo ("<tr><td><p>N. Profissional: </p></td>");
            echo ("<td><input type=\"text\" id=\"descricaoNumProfContatoEditar\" autofocus=\"autofocus\" name=\"descricaoNumProfContatoEditar\" maxlength=\"20\" value=\"" . $_SESSION['contatoCarregado']['telemovelProfissional'] . "\"/></td></tr>");
            echo ("<tr><td><p>N. Pessoal: </p></td>");
            echo ("<td><input type=\"text\" id=\"descricaoNumPessoalContatoEditar\" name=\"descricaoNumPessoalContatoEditar\" maxlength=\"20\" value=\"" . $_SESSION['contatoCarregado']['telemovelPessoal'] . "\"/></td></tr>");
            echo ("<tr><td><p>email: </p></td>");
            echo ("<td><input type=\"text\" id=\"descricaoEmailContatoEditar\" name=\"descricaoEmailContatoEditar\" maxlength=\"100\" value=\"" . $_SESSION['contatoCarregado']['email'] . "\"/></td></tr>");
            echo ("<tr><td><p>Contato Profissional? </p></td>");
            echo ("<td><form name=\"contatoEditar\"><input type=\"checkbox\" id=\"descricaoProfPessoalContatoEditar\" name=\"descricaoProfPessoalContatoEditar\" " . (($_SESSION['contatoCarregado']['profissionalPessoal'] == true) ? ("checked=\"checked\" id=\"true\"") : ("id=\"false\"")) . "></form></td></tr>");
            echo ("<tr><td></td><td><button type=\"button\" id=\"guardarContato\" onclick=\"MakeXMLHTTPCall(61);\">Guardar</button>");
            echo ("<button type=\"button\" id=\"cancelarContato\" onclick=\"MakeXMLHTTPCall(58);\">Cancelar</button></td></tr>");
            echo ("</tbody></table>");
            break;
        case 29:
            echo ("<h1>O contato \"" . $dados . "\" foi actualizado com sucesso.</h1>");
            echo ("<a href=\"#\" onclick=\"MakeXMLHTTPCall(58);\">Voltar</a>");
            break;
    }
}

function iniciaBD() {

// Open a database connection
    try {
        $db = new PDO(DB_INFO, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $erro) {
        echo ("Erro BD: " . $erro->getMessage() . "\n");
        echo ("Código Erro BD: " . $erro->getCode() . "\n");
    }
    return $db;
}

?>
