/* Este ficheiro trata os pedidos Ajax e os dados recebidos do servidor PHP. Os dados recebidos sao tratados
 * como texto e não como XML. Do servidor PHP o HTML ja vem formado e pronto a ser inserido na pagina*/

var xmlHttpObj;
var dadosPesquisar;
var flagPlaneamento=0;
var flagAtividade=0;
var dataHoraInicio;
var dataHoraFim;
var aplicacaoSelect;
var chamadaSelect;
var mensagemSelect;
var contatoSelect;
var verificado = false;

function CreateXMLHttpRequestObject( )
{
    // detecção do browser simplificada e sem tratamento de excepções
    xmlHttpObj=null;
    if(window.XMLHttpRequest) // IE 7 e Firefox
    {
        xmlHttpObj=new XMLHttpRequest()
    }
    else if(window.ActiveXObject) // IE 5 e 6
    {
        xmlHttpObj=new ActiveXObject("Microsoft.XMLHTTP")
    }
    return xmlHttpObj;
}

//Variável estado permite distinguir em que tipo de situacoes foi invocada a funcao MakeXMLHTTPCall
function MakeXMLHTTPCall(estado)
{
    switch(estado)
    {
        //Obtem dados do utilizador com sessão iniciada
        case 0:
        {
            carregaMenuOpcoes(0);
            dadosPesquisar="../Dados/utilizadorBD.php?funcao=0";            
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
        }
        break;
        //Carrega todas as tarefas
        case 1:
        {
            carregaMenuOpcoes(1);
            dadosPesquisar="../Dados/tarefasBD.php?funcao=0";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
        }
        break;
        case 2:
        {
            carregaMenuOpcoes(1);
            var tarefaSeleccionada=document.getElementById("listaTarefas").value;
            if(tarefaSeleccionada=="escolha")
            {
                break;
            }
            dadosPesquisar="../Dados/tarefasBD.php?funcao=1&idTarefa="+tarefaSeleccionada;
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
        }
        break;
        case 3:
        {
            carregaMenuOpcoes(2);
            dadosPesquisar="../Dados/tarefasBD.php?funcao=2";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
        }
        break;     
        case 4:
        {
            carregaMenuOpcoes(2);
            var prioridadeSeleccionada=document.getElementById("listaPrioridades").value;
            if(prioridadeSeleccionada=="escolha")
            {
                break;
            }
            dadosPesquisar="../Dados/tarefasBD.php?funcao=3&idPrioridade="+prioridadeSeleccionada;
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
        }
        break;
        case 5:
        {
            carregaMenuOpcoes(1);
            var detalheTarefa=document.getElementById("detalhesTarefa");
            if(detalheTarefa==null)
            {
                alert("Por favor, escolha em 1º lugar uma tarefa para editar");
                break;
            }
            dadosPesquisar="../Dados/tarefasBD.php?funcao=4";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
        }
        break;
        case 6:
        {
            carregaMenuOpcoes(1);
            var nomeTarefaEdita=document.getElementById("nomeTarefaActualizar").value;
            var descricaoTarefaEdita=document.getElementById("descricaoActualizar").value;
            var prioridadeSeleccionadaEdita=document.getElementById("listaPrioridadesEdiTarefa").value;
            if(nomeTarefaEdita=="" || descricaoTarefaEdita=="")
            {
                alert("Os campos 'Nome' e/ou 'Descriçao' não podem ficar em branco");
                break;
            }
            dadosPesquisar="../Dados/tarefasBD.php?funcao=5&nomeTarefa="+nomeTarefaEdita+"&descricaoTarefa="+descricaoTarefaEdita
            +"&idPrioridade="+prioridadeSeleccionadaEdita;
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
        }
        break;
        case 7:
        {
            carregaMenuOpcoes(1);
            dadosPesquisar="../Dados/tarefasBD.php?funcao=6";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
        }
        break;
        case 8:
        {
            carregaMenuOpcoes(1);
            var nomeTarefaCria=document.getElementById("nomeTarefaCriar").value;
            var descricaoTarefaCria=document.getElementById("descricaoCriar").value;
            var prioridadeSeleccionadaCria=document.getElementById("listaPrioridadesCriaTarefa").value;
            if(prioridadeSeleccionadaCria=="escolha")
            {
                alert("Escolha um tipo de prioridade");
                break;
            }         
            if(nomeTarefaCria=="" || descricaoTarefaCria=="")
            {
                alert("Introduza um nome e descrição para a nova tarefa");
                break;
            }                
            dadosPesquisar="../Dados/tarefasBD.php?funcao=7&nomeTarefa="+nomeTarefaCria+"&descricaoTarefa="+descricaoTarefaCria
            +"&idPrioridade="+prioridadeSeleccionadaCria;
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
        }
        break;
        case 9:
        {
            carregaMenuOpcoes(1);
            var detalheTarefaEliminar=document.getElementById("detalhesTarefa");
            if(detalheTarefaEliminar==null)
            {
                alert("Por favor, escolha em 1º lugar uma tarefa para eliminar");
                break;
            }
            dadosPesquisar="../Dados/tarefasBD.php?funcao=8";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
        }
        break;
        case 10:
        {
            carregaMenuOpcoes(1);
            dadosPesquisar="../Dados/tarefasBD.php?funcao=9";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
        }
        break;    
        case 11:
        {
            carregaMenuOpcoes(2);
            var detalhesPrioridade=document.getElementById("detalhesPrioridade");
            if(detalhesPrioridade==null)
            {
                alert("Por favor, escolha em 1º lugar uma prioridade para editar");
                break;
            }            
            dadosPesquisar="../Dados/tarefasBD.php?funcao=10";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
        }
        break;
        case 12:
        {
            carregaMenuOpcoes(2);
            var descricaoPrioridadeEditar=document.getElementById("descricaoPrioridadeEditar").value;
            var pesoPrioridadeEditar=document.getElementById("pesoPrioridadeEditar").value;
            if(descricaoPrioridadeEditar=="")
            {
                alert("O campo 'Descrição' não pode ficar em branco");
                break;
            }
            if(isNaN(pesoPrioridadeEditar)|| pesoPrioridadeEditar > 5 || pesoPrioridadeEditar <1)
            {
                alert("O peso da prioridade deverá estar compreendido entre 1 e 5");
                break;
            }
            dadosPesquisar="../Dados/tarefasBD.php?funcao=11&descricaoPrioridade="+descricaoPrioridadeEditar+"&pesoPrioridade="+pesoPrioridadeEditar;
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
        }
        break;   
        case 13:
        {
            carregaMenuOpcoes(2);
            dadosPesquisar="../Dados/tarefasBD.php?funcao=12";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
        }
        break; 
        case 14:
        {
            carregaMenuOpcoes(2);
            var descricaoPrioridadeCriar=document.getElementById("descricaoPrioridadeCriar").value;
            var pesoPrioridadeCriar=document.getElementById("pesoPrioridadeCriar").value;
            if(isNaN(pesoPrioridadeCriar)|| pesoPrioridadeCriar > 5 || pesoPrioridadeCriar <1)
            {
                alert("O peso da prioridade deverá estar compreendido entre 1 e 5");
                break;
            }
            if(descricaoPrioridadeCriar=="")
            {
                alert("Introduza uma descrição para a nova prioridade");
                break;
            }             
            dadosPesquisar="../Dados/tarefasBD.php?funcao=13&descricaoPrioridade="+descricaoPrioridadeCriar+
            "&pesoPrioridade="+pesoPrioridadeCriar;
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
        }
        break;  
        case 15:
        {
            carregaMenuOpcoes(2);
            var detalhePrioridadeEliminar=document.getElementById("detalhesPrioridade");
            if(detalhePrioridadeEliminar==null)
            {
                alert("Por favor, escolha em 1º lugar uma prioridade para eliminar");
                break;
            }
            dadosPesquisar="../Dados/tarefasBD.php?funcao=14";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
        }
        break;
        case 16:
            carregaMenuOpcoes(2);
            dadosPesquisar="../Dados/tarefasBD.php?funcao=15";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;
        case 17:
            carregaMenuOpcoes(0);
            dadosPesquisar="../Dados/utilizadorBD.php?funcao=1";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;
        case 18:
            carregaMenuOpcoes(0);
            dadosPesquisar="../Dados/utilizadorBD.php?funcao=2";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerCarregaPaginaCompleta);
            refrescaVariaveis();
            break;
        case 19:
            var utilizadorLogin=document.getElementById("username").value;
            var senhaLogin=document.getElementById("password").value;
            if(utilizadorLogin=="" || senhaLogin=="")
            {
                var mensagem=document.getElementById("resultadoLogin");
                mensagem.innerHTML="Preencha ambos os campos";
                break;
            }
            dadosPesquisar="../IUtilizador/login.php";
            var enviar = new Array();
            enviar[0]=utilizadorLogin;
            enviar[1]=senhaLogin;
            efectuaPesquisaPOST(dadosPesquisar,enviar,stateHandlerInjectaHTMLTagResultadoLogin);
            refrescaVariaveis();
            break;
        case 20:
            dadosPesquisar="../Dados/utilizadorBD.php?funcao=3";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagMenuBar);
            refrescaVariaveis();
            break;
        case 21:
            carregaMenuOpcoes(3);
            dadosPesquisar="../Dados/utilizadorBD.php?funcao=4";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;      
        case 22:
            carregaMenuOpcoes(3);
            dadosPesquisar="../Dados/utilizadorBD.php?funcao=5";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;
        case 23:
            carregaMenuOpcoes(3);
            var nomeUtilizadorEditar=document.getElementById("nomeUtilizadorEditar").value;
            var senhaUtilizadorEditar=document.getElementById("senhaUtilizadorEditar").value;
            var senhaConfirmarUtilizadorEditar=document.getElementById("senhaConfirmarUtilizadorEditar").value;
            if(nomeUtilizadorEditar=="" || senhaUtilizadorEditar=="" || senhaConfirmarUtilizadorEditar=="")
            {
                alert("Por favor, preencha todos os campos");
                break;
            }
            if(senhaUtilizadorEditar != senhaConfirmarUtilizadorEditar)
            {
                alert("As senhas não coincidem!");
                break;
            }
            dadosPesquisar="../Dados/utilizadorBD.php?funcao=6&nome="+nomeUtilizadorEditar+"&senha="+senhaUtilizadorEditar;
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break; 
        case 24:
            dadosPesquisar="../Dados/planeamentoBD.php?funcao=2";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagOpcoes);
            break;
        case 25:
            carregaMenuOpcoes(4);
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=0";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;   
        case 26:
            carregaMenuOpcoes(5);
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=1";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;      
        case 27:
            carregaMenuOpcoes(5);
            var sistemaOpSeleccionado=document.getElementById("listaSistemaOperativo").value;
            if(sistemaOpSeleccionado=="escolha")
            {
                break;
            }
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=2&idSistemaOperSelec="+sistemaOpSeleccionado;
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;             
        case 28:
            carregaMenuOpcoes(5);
            var detalheSistemaOperativo=document.getElementById("detalhesSistemaOperativo");
            if(detalheSistemaOperativo==null)
            {
                alert("Por favor, escolha em 1º lugar um S.O. para editar");
                break;
            }
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=3";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();            
            break;
        case 29:
            carregaMenuOpcoes(5);
            var descricaoPlataformaEditar=document.getElementById("descricaoPlataformaEditar").value;
            if(descricaoPlataformaEditar=="")
            {
                alert("O campo 'Descrição' não pode ficar em branco");
                break;
            } 
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=4&descricaoPlataforma="+descricaoPlataformaEditar;
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();            
            break;  
        case 30:
            carregaMenuOpcoes(5);
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=5";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();            
            break; 
        case 31:
            carregaMenuOpcoes(5);
            var descricaoPlataformaCriar=document.getElementById("descricaoPlataformaCriar").value;
            if(descricaoPlataformaCriar=="")
            {
                alert("O campo 'Descrição' não pode ficar em branco");
                break;
            }            
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=6&descricaoPlataforma="+descricaoPlataformaCriar;
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;
        case 32:
            carregaMenuOpcoes(5);
            var detalheSistemaOperativoEliminar=document.getElementById("detalhesSistemaOperativo");
            if(detalheSistemaOperativoEliminar==null)
            {
                alert("Por favor, escolha em 1º lugar um S.Operativo para eliminar");
                break;
            }
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=7";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;
        case 33:
            carregaMenuOpcoes(5);
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=8";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;         
        case 34:
            carregaMenuOpcoes(6);
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=9";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;      
        case 35:
            carregaMenuOpcoes(6);
            var tipoAppSeleccionada=document.getElementById("listaTipoApp").value;
            if(tipoAppSeleccionada=="escolha")
            {
                break;
            }
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=10&idTipoApp="+tipoAppSeleccionada;
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;             
        case 36:
            carregaMenuOpcoes(6);
            var detalheTipoApp=document.getElementById("detalhesTipoApp");
            if(detalheTipoApp==null)
            {
                alert("Por favor, escolha em 1º lugar um tipo de aplicação para editar");
                break;
            }
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=11";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();            
            break;
        case 37:
            carregaMenuOpcoes(6);
            var descricaoTipoAppEditar=document.getElementById("descricaoTipoAppEditar").value;
            if(descricaoTipoAppEditar=="")
            {
                alert("O campo 'Descrição' não pode ficar em branco");
                break;
            } 
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=12&descricaoTipoApp="+descricaoTipoAppEditar;
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();            
            break;  
        case 38:
            carregaMenuOpcoes(6);
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=13";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();            
            break; 
        case 39:
            carregaMenuOpcoes(6);
            var descricaoTipoAppCriar=document.getElementById("descricaoTipoAppCriar").value;
            if(descricaoTipoAppCriar=="")
            {
                alert("O campo 'Descrição' não pode ficar em branco");
                break;
            }            
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=14&descricaoTipoApp="+descricaoTipoAppCriar;
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;
        case 40:
            carregaMenuOpcoes(6);
            var detalheTipoAppEliminar=document.getElementById("detalhesTipoApp");
            if(detalheTipoAppEliminar==null)
            {
                alert("Por favor, escolha em 1º lugar um tipo de aplicação para eliminar");
                break;
            }
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=15";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;
        case 41:
            carregaMenuOpcoes(6);
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=16";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;
        case 42:
            refrescaVariaveis();
            var headerOpcoes = document.getElementById("headerOpcoes");
            headerOpcoes.innerHTML="Legenda";
            dadosPesquisar="../Dados/registoAtividadeBD.php?funcao=1";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagOpcoes);
            break;
        case 43:
            var dataInicioPesquisa = document.getElementById("inicioEventoPesquisa").value;
            var dataFimPesquisa = document.getElementById("fimEventoPesquisa").value;
            var graficoGerar = obtemValorRadioGrafico();
            
            if(dataInicioPesquisa == "" || dataFimPesquisa=="")
            {
                alert ("Por favor, escolha uma data de início e/ou fim");
                break;
            }
            if(dataInicioPesquisa > dataFimPesquisa)
            {
                alert ("Por favor, escolha uma data de início superior ou igual a " + dataInicioPesquisa);
                break;
            }
            dadosPesquisar="../Dados/registoAtividadeBD.php?funcao=2&dataInicio="+dataInicioPesquisa+"&dataFim="+dataFimPesquisa+"&tipoGrafico="+ graficoGerar;
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;
            
        case 44:
            carregaMenuOpcoes(8);
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=17";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            verificado = false;
            refrescaVariaveis();
            break;      
        case 45:
            carregaMenuOpcoes(8);
            if(!verificado)
                aplicacaoSelect=obtemValorRadioAplicacoes();
            
            if(aplicacaoSelect!=""){            
                obtemDatasInicioFim(-7, 0);            
                dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=18&idAplicacao="+aplicacaoSelect+"&dataInicio="+dataHoraInicio+"&dataFim="+dataHoraFim;
                efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
                document.getElementById("historicoPesquisa").style.display="inline";
                refrescaVariaveis();
                break;
            }else{
                alert("De momento, a pesquisa do Histórico não se encontra disponível.\n"+
                    "Actualize o sistema, descarregando dados do smart phone através da aplicação desktop.");
                break;                                                                                                                                                                                                    
            }
        case 46:
            carregaMenuOpcoes(8);
            if(!verificado)
                aplicacaoSelect=obtemValorRadioAplicacoes();
            
            if(aplicacaoSelect!=""){    
                dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=19&idAplicacao="+aplicacaoSelect;
                efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
                refrescaVariaveis();
                break;
            }else{
                alert("De momento, o detalhe da Aplicação não se encontra disponível.\n"+
                    "Actualize o sistema, descarregando dados do smart phone através da aplicação desktop.");
                break;                                                                                                                                                                                                    
            }
        case 47:
            if(aplicacaoSelect!=""){ 
                var dataInicioPesquisaApp = document.getElementById("inicioAplicacaoPesquisa").value;
                var dataFimPesquisaApp = document.getElementById("fimAplicacaoPesquisa").value;
            
                if(dataInicioPesquisaApp == "" || dataFimPesquisaApp=="")
                {
                    alert ("Por favor, escolha uma data de início e/ou fim");
                    break;
                }
                if(dataInicioPesquisaApp > dataFimPesquisaApp)
                {
                    alert ("Por favor, escolha uma data de início superior ou igual a " + dataInicioPesquisaApp);
                    break;
                }
            
                dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=18&idAplicacao="
                +aplicacaoSelect+"&dataInicio="+dataInicioPesquisaApp+"&dataFim="+dataFimPesquisaApp;
                efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
                refrescaVariaveis();
                break;
            }
        case 48:
            carregaMenuOpcoes(8);
            if(!verificado)
                aplicacaoSelect=obtemValorRadioAplicacoes();
            if(aplicacaoSelect!=""){ 
                dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=20&idAplicacao="+aplicacaoSelect;
                efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
                refrescaVariaveis();
                break;
            }else{
                alert("De momento, a edição da Aplicação não se encontra disponível.\n"+
                    "Actualize o sistema, descarregando dados do smart phone através da aplicação desktop.");
                break;   
            }
        case 49:
            carregaMenuOpcoes(8);
            var enderecoAplicacaoEditar=document.getElementById("descricaoEnderecoAplicacaoEditar").value;
            var comentarioAplicacaoEditar=document.getElementById("descricaoComentarioAplicacaoEditar").value;
            var plataformaAplicacaoEditar=document.getElementById("descricaoPlataformaAplicacaoEditar").value; 
            var tipoAplicacaoEditar=document.getElementById("descricaoTipoAplicacaoEditar").value;
            var tempAplicacao=document.aplicacaoEditar.aplicacoesCheckEdita;
            
            var aplicacaoAtiva;
            
            if(tempAplicacao.checked)
                aplicacaoAtiva = 1;
            else
                aplicacaoAtiva = 0;
            

            if(enderecoAplicacaoEditar=="")
            {
                alert("Introduza um endereço para a aplicação");
                break;
            } 
            if(comentarioAplicacaoEditar=="")
            {
                alert("Introduza um comentário/nota sobre a aplicação");
                break;
            }
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=21&idAplicacao="+aplicacaoSelect+"&endereco="+enderecoAplicacaoEditar
            +"&comentario="+comentarioAplicacaoEditar+"&idSistemaOperSelec="+plataformaAplicacaoEditar+"&idTipoApp="+tipoAplicacaoEditar+"&ativa="+aplicacaoAtiva;
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;   
        case 50:
            carregaMenuOpcoes(9);
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=22";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            verificado = false;
            break;
        case 51:
            carregaMenuOpcoes(9);
            if(!verificado)
                chamadaSelect = obtemValorRadioChamadas();
            if(chamadaSelect!=""){
                obtemDatasInicioFim(-7, 0);            
                dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=23&idContato="+chamadaSelect+"&dataInicio="+dataHoraInicio+"&dataFim="+dataHoraFim;
                efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
                document.getElementById("historicoChamadasPesquisa").style.display="inline";
                refrescaVariaveis();
                break;
            }
            else{
                alert("De momento, a pesquisa do Histórico não se encontra disponível.\n"+
                    "Actualize o sistema, descarregando dados do smart phone através da aplicação desktop.");
                break; 
            }
        case 52:
            if(chamadaSelect!=""){
                var dataInicioPesquisaChamadas = document.getElementById("inicioChamadasPesquisa").value;
                var dataFimPesquisaChamadas = document.getElementById("fimChamadasPesquisa").value;
            
                if(dataInicioPesquisaChamadas == "" || dataFimPesquisaChamadas=="")
                {
                    alert ("Por favor, escolha uma data de início e/ou fim");
                    break;
                }
                if(dataInicioPesquisaChamadas > dataFimPesquisaChamadas)
                {
                    alert ("Por favor, escolha uma data de início superior ou igual a " + dataInicioPesquisaApp);
                    break;
                }
            
                dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=23&idContato="
                +chamadaSelect+"&dataInicio="+dataInicioPesquisaChamadas+"&dataFim="+dataFimPesquisaChamadas;
                efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
                refrescaVariaveis();
                break;
            }
        case 53:
            carregaMenuOpcoes(10);
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=24";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            verificado=false;
            break;
        case 54:
            carregaMenuOpcoes(10);
            if(!verificado)
                mensagemSelect = obtemValorRadioMensagens();
            if(mensagemSelect!=""){
                obtemDatasInicioFim(-7, 0);   
                dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=25&idContato="+mensagemSelect+"&dataInicio="+dataHoraInicio+"&dataFim="+dataHoraFim;
                efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
                document.getElementById("historicoMensagensPesquisa").style.display="inline";
                refrescaVariaveis();
                break;
            }else{
                alert("De momento, a pesquisa do Histórico não se encontra disponível.\n"+
                    "Actualize o sistema, descarregando dados do smart phone através da aplicação desktop.");
                break; 
            }
        case 55:
            if(mensagemSelect != ""){
                var dataInicioPesquisaMensagens = document.getElementById("inicioMensagensPesquisa").value;
                var dataFimPesquisaMensagens = document.getElementById("fimMensagensPesquisa").value;
            
                if(dataInicioPesquisaMensagens == "" || dataFimPesquisaMensagens=="")
                {
                    alert ("Por favor, escolha uma data de início e/ou fim");
                    break;
                }
                if(dataInicioPesquisaMensagens > dataFimPesquisaMensagens)
                {
                    alert ("Por favor, escolha uma data de início superior ou igual a " + dataInicioPesquisaMensagens);
                    break;
                }
            
                dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=25&idContato="+mensagemSelect+"&dataInicio="+dataInicioPesquisaMensagens+"&dataFim="+dataFimPesquisaMensagens;
                efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
                refrescaVariaveis();
                break;
            }
        case 56:
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=26";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagOpcoes);
            refrescaVariaveis();
            break;
        case 57:
            var filtrarContacto = document.getElementById("filtraContatos").value;
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=27&utilizador="+filtrarContacto;
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            break;
        case 58:
            carregaMenuOpcoes(11);
            dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=28";
            efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
            refrescaVariaveis();
            verificado = false;
            break;
        case 59:
            carregaMenuOpcoes(11);
            if(!verificado)
                contatoSelect = obtemValorRadioContatos();
            if(contatoSelect!=""){
                dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=29&idContato="+contatoSelect;
                efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
                break;
            }
            else{
                alert("De momento, o detalhe do Contato não se encontra disponível.\n"+
                    "Actualize o sistema, descarregando dados do smart phone através da aplicação desktop.");
                break;
            }
        case 60:
            carregaMenuOpcoes(11);
            if(!verificado)
                contatoSelect = obtemValorRadioContatos();
            if(contatoSelect!=""){
                dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=30&idContato="+contatoSelect;
                efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
                break;
            }else{
                alert("De momento, a edição do Contato não se encontra disponível.\n"+
                    "Actualize o sistema, descarregando dados do smart phone através da aplicação desktop.");
                break;
            }
        case 61:
            carregaMenuOpcoes(11);
            if(contatoSelect!=""){
                var numProfContatoEditar=document.getElementById("descricaoNumProfContatoEditar").value;
                var numPessoalContatoEditar=document.getElementById("descricaoNumPessoalContatoEditar").value;
                var emailContatoEditar=document.getElementById("descricaoEmailContatoEditar").value;
                var temp=document.contatoEditar.descricaoProfPessoalContatoEditar;
            
                var profPessoalContatoEditar;
            
                if(temp.checked)
                    profPessoalContatoEditar = 1;
                else
                    profPessoalContatoEditar = 0;
                

                if(isNaN(numProfContatoEditar)&& numProfContatoEditar!="")
                {
                    alert("Introduza um número profissional válido (código internacional 00)");
                    break;
                } 
            
                if(isNaN(numPessoalContatoEditar)&& numPessoalContatoEditar!="")
                {
                    alert("Introduza um número pessoal válido (código internacional 00)");
                    break;
                } 
                dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=31&idContato="+contatoSelect+"&numProf="+numProfContatoEditar
                +"&numPessoal="+numPessoalContatoEditar+"&email="+emailContatoEditar+"&profPessoal="+profPessoalContatoEditar;
                efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
                refrescaVariaveis();
                break;
            }
        case 62:
            carregaMenuOpcoes(11);
            if(!verificado)
                contatoSelect = obtemValorRadioContatos();
            if(contatoSelect!=""){
                dadosPesquisar="../Dados/smartPhoneGestaoBD.php?funcao=32";                
                efectuaPesquisaGET(dadosPesquisar,stateHandlerInjectaHTMLTagIntro);
                break;
            }else{
                alert("De momento, não existem Contatos associados a este utilizador para serem exportados!\n"+
                    "Actualize o sistema, descarregando dados do smart phone através da aplicação desktop.");
                break;
            }
    }
}

function obtemValorRadioGrafico()
{
    for (var i=0; i < document.graficoForm.graficoRadio.length; i++)
    {
        if (document.graficoForm.graficoRadio[i].checked)
        {
            var rad_val = document.graficoForm.graficoRadio[i].value;
        }
    }
    return rad_val;
}

function obtemValorRadioAplicacoes()
{
    if(typeof document.aplicacoesForm == 'undefined')
        return "";
    if(document.aplicacoesForm.aplicacoesRadio.length==null){
        verificado=true;
        return document.aplicacoesForm.aplicacoesRadio.value;
    }else{
    
        for (var i=0; i < document.aplicacoesForm.aplicacoesRadio.length; i++)
        {
            if (document.aplicacoesForm.aplicacoesRadio[i].checked)
            {
                var rad_val = document.aplicacoesForm.aplicacoesRadio[i].value;
            }
        }
    }
    verificado=true;
    return rad_val;
}

function obtemValorRadioChamadas()
{
    if(typeof document.chamadasForm == 'undefined')
        return "";
    if(document.chamadasForm.chamadasRadio.length==null){
        verificado=true;
        return document.chamadasForm.chamadasRadio.value;
    }else{
        for (var i=0; i < document.chamadasForm.chamadasRadio.length; i++)
        {
            if (document.chamadasForm.chamadasRadio[i].checked)
            {
                var rad_val = document.chamadasForm.chamadasRadio[i].value;
            }
        }
    }
    verificado=true;
    return rad_val;
}

function obtemValorRadioMensagens()
{
    if(typeof document.mensagensForm == 'undefined')
        return "";
    if(document.mensagensForm.mensagensRadio.length==null){
        verificado=true;
        return document.mensagensForm.mensagensRadio.value;
    }else{
        for (var i=0; i < document.mensagensForm.mensagensRadio.length; i++)
        {
            if (document.mensagensForm.mensagensRadio[i].checked)
            {
                var rad_val = document.mensagensForm.mensagensRadio[i].value;
            }
        }
    }
    verificado=true;
    return rad_val;
}

function obtemValorRadioContatos()
{
    if(typeof document.contatosForm == 'undefined')
        return "";
    if(document.contatosForm.contatosRadio.length==null){
        verificado=true;
        return document.contatosForm.contatosRadio.value;
    }else{
        for (var i=0; i < document.contatosForm.contatosRadio.length; i++)
        {
            if (document.contatosForm.contatosRadio[i].checked)
            {
                var rad_val = document.contatosForm.contatosRadio[i].value;
            }
        }
    }
    verificado=true;
    return rad_val;
}

function ordenaLista(funcao){
    var lista = "";
    switch(funcao){
        case 0:
            lista = document.getElementById("listaContatos");
            break;
        case 1:
            lista = document.getElementById("listaAplicacoes");
            break;
        case 2:
            lista = document.getElementById("historicoAplicacoes");
            break;
        case 3:
            lista = document.getElementById("listaChamadas");
            break;
        case 4:
            lista = document.getElementById("historicoChamadas");
            break;     
        case 5:
            lista = document.getElementById("listaMensagens");
            break; 
        case 6:
            lista = document.getElementById("historicoMensagens");
            break; 
    }
    sorttable.makeSortable(lista);
}

function sobre(){
    var menuIntro = document.getElementById("intro");
    var menuOpcoes = document.getElementById("opcoes");
    menuIntro.innerHTML = "<h1>Portal AsynChronus</h1><br><p>Esta página é uma parte integrante do projeto desenvolvido no âmbito da Unidade Curricular PESTI, que pretende ser o interface e elemento aglutinador de outras duas componentes:<br>- Aplicação Desktop.<br>- Ligação a Smart Phone.<br>"+
    "O portal é o local onde se poderá visualizar o planeamento efectuado versus as tarefas de fato executadas, bem como a gestão dos dados obtidos através do Smart Phone.</p>";
    
    menuOpcoes.innerHTML = "";
}
function obtemDatasInicioFim(deltaTInicio,deltaTFim){
    var dataHInicio = new Date();
    dataHInicio.setDate(dataHInicio.getDate()+deltaTInicio);
    var dataHFim = new Date();
    dataHFim.setDate(dataHFim.getDate()+deltaTFim);
    
    dataHoraInicio = dataHInicio.getFullYear()+"-"+(dataHInicio.getMonth()+1)+"-"+dataHInicio.getDate();
    dataHoraFim = dataHFim.getFullYear()+"-"+(dataHFim.getMonth()+1)+"-"+dataHFim.getDate();
    
//    dataHoraInicio = dataHInicio.getFullYear()+"-"+(dataHInicio.getMonth()+1)+"-"+dataHInicio.getDate()+" "
//    +dataHInicio.getHours()+":"+dataHInicio.getMinutes()+":"+dataHInicio.getSeconds();
//    dataHoraFim = dataHFim.getFullYear()+"-"+(dataHFim.getMonth()+1)+"-"+dataHFim.getDate()+" "
//    +dataHFim.getHours()+":"+dataHFim.getMinutes()+":"+dataHFim.getSeconds();
}

function efectuaPesquisaPOST(destino,parametros,funcao) {
    
    var params = "utilizadorLogin="+parametros[0]+"&senhaLogin="+parametros[1];
    
    xmlHttpObj = CreateXMLHttpRequestObject();
    if(xmlHttpObj)
    {
        // Definição do URL para efectuar pedido HTTP - método POST
        xmlHttpObj.open("POST",destino,true);
        xmlHttpObj.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlHttpObj.setRequestHeader("Content-length", parametros.length);
        xmlHttpObj.setRequestHeader("Connection", "close");
        // Registo do EventHandler
        xmlHttpObj.onreadystatechange = funcao;
        xmlHttpObj.send(params);
    }
}

//Recebe o parametro de pesquisa e qual sera a funcao statehandler a ser posteriormente invocada
function efectuaPesquisaGET(dadosP,funcao)
{
    xmlHttpObj = CreateXMLHttpRequestObject();
    if(xmlHttpObj)
    {
        // Definição do URL para efectuar pedido HTTP - método GET
        xmlHttpObj.open("GET",dadosP,true);
        // Registo do EventHandler
        xmlHttpObj.onreadystatechange = funcao;
        xmlHttpObj.send();
    }
}

function stateHandlerInjectaHTMLTagMenuBar()
{
    var temp;
    // resposta do servidor completa
    if(xmlHttpObj.readyState == 4 && xmlHttpObj.status == 200)
    {
        var resposta=xmlHttpObj.responseText;
        var tag=document.getElementById("menuBarNav");
        temp = resposta.substring(0, 3);
        if(temp=="../"){
            location = resposta;
        }
        else{
            tag.innerHTML=resposta;
        }
    }
}

function stateHandlerInjectaHTMLTagIntro()
{
    var temp;
    // resposta do servidor completa
    if(xmlHttpObj.readyState == 4 && xmlHttpObj.status == 200)
    {
        var resposta=xmlHttpObj.responseText;
        var tag=document.getElementById("intro");
        var tag2=document.getElementById("menuBarNav").childNodes;
        temp = resposta.substring(0, 3);
        if(temp=="../"){
            location = resposta;
        }
        else{
            tag.innerHTML=resposta;
        }
        if(tag2.length<2){
            MakeXMLHTTPCall(20);
        }
    }
}

function stateHandlerInjectaHTMLTagResultadoLogin()
{
    var temp;
    // resposta do servidor completa
    if(xmlHttpObj.readyState == 4 && xmlHttpObj.status == 200)
    {
        var resposta=xmlHttpObj.responseText;
        var tag=document.getElementById("resultadoLogin");
        temp = resposta.substring(0, 3);
        if(temp=="../"){
            location = resposta;
        }
        else{
            tag.innerHTML=resposta;
        }
    }
}

function stateHandlerInjectaHTMLTagOpcoes()
{
    var temp;
    // resposta do servidor completa
    if(xmlHttpObj.readyState == 4 && xmlHttpObj.status == 200)
    {
        var resposta=xmlHttpObj.responseText;
        var tag=document.getElementById("opcoes");
        temp = resposta.substring(0, 3);
        if(temp=="../"){
            location = resposta;
        }
        else{
            tag.innerHTML=resposta;
        }
    }
}

function stateHandlerCarregaPaginaCompleta(){
    // resposta do servidor completa
    if(xmlHttpObj.readyState == 4 && xmlHttpObj.status == 200)
    {
        var resposta=xmlHttpObj.responseText;
        location=resposta;
    }
}

function carregaPlaneamento(){
    
    if(flagPlaneamento==0){
        var calendario=document.getElementById("calendar");
        if(calendario==null || flagAtividade !=0)
        {
            var tag=document.getElementById("intro");
            tag.innerHTML="<div id=\"calendar\"></div>";
        }
        flagAtividade=0;
        MakeXMLHTTPCall(24);
        
        var headerOpcoes = document.getElementById("headerOpcoes");
        headerOpcoes.innerHTML="Opções";
        
        $(document).ready(function(){
        
            $("#calendar").fullCalendar({
                 
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                selectable: true,
                editable: true,
                unselectAuto: false,
                selectHelper: true,
                eventSources: [
                {
                    url:"../Dados/planeamentoBD.php?funcao=0"
                }
                ],
                unselect: function(){
                    $('.botaoMenuCriaEvento').unbind();
                },
                select: function(start, end, allDay) {
                    $('#menuApagaEvento').hide();
                    
                    var inicio = $.fullCalendar.formatDate(start,"yyyy-MM-dd HH:mm:ss");
                    var fim = $.fullCalendar.formatDate(end,"yyyy-MM-dd HH:mm:ss");
                    
                    $('#inicioEvento').val(inicio);
                    $('#fimEvento').val(fim);

                    $('#menuCriaEvento').show();
                    
                    $('.botaoMenuCriaEvento').click(function(){
                        var idTarefa    = $('#listaTarefasEvento').val();
                            
                        if(idTarefa=="escolha")
                        {
                            alert("Seleccione por favor um tipo de tarefa");
                            exit;
                        }
                    
                        $.ajax({
                            type: "GET",
                            url: "../Dados/planeamentoBD.php?funcao=5&idTarefa="+idTarefa+"&dataInicio="
                                +inicio+"&dataFim="+fim,
                            success: function(msg){
                                $('#menuCriaEvento').hide();
                                $('#resultadoAlteracoesEvento').html(msg);
                                $('#resultadoAlteracoesEvento').show();
                                $('#resultadoAlteracoesEvento').fadeOut(2500);            
                                $('#calendar').fullCalendar( 'refetchEvents' );
                                $('#listaTarefasEvento').val("escolha");
                                $('#calendar').fullCalendar('unselect');
                            }
                        });
                    }); 

                    $('.botaoMenuCancelaEvento').click(function(){
                        $('#menuCriaEvento').hide();
                        $('#calendar').fullCalendar('unselect');  
                        $('#listaTarefasEvento').val("escolha");
                        $('.botaoMenuCriaEvento').unbind();
                    });
                },
                
                eventClick: function(event) {
                    $.ajax({
                        url: "../Dados/planeamentoBD.php?funcao=1&idPlaneamento="+event.id,
                        success: function(data) {
                            $('#menuCriaEvento').hide();
                            
                            $('#menuApagaEvento').html(data);
                            $('#menuApagaEvento').show();

                            $('.botaoMenuEdicaoSairEvento').click(function(){
                                $('#menuApagaEvento').hide();
                                $('#calendar').fullCalendar('unselect');
                            });

                            $('.botaoMenuEdicaoApagaEvento').click(function(){
                                var answer = confirm("Tem a certeza que deseja eliminar este evento?")

                                if (answer){
                                    $.ajax({
                                        type: "GET",
                                        url: "../Dados/planeamentoBD.php?funcao=3&idPlaneamento="+event.id,
                                        success: function(msg){
                                            $('#menuApagaEvento').hide();
                                            $('#resultadoAlteracoesEvento').html(msg);
                                            $('#resultadoAlteracoesEvento').show();
                                            $('#resultadoAlteracoesEvento').fadeOut(2500);            
                                            $('#calendar').fullCalendar( 'removeEvents', [event.id ] );
                                            $('#calendar').fullCalendar('unselect');
                                        }
                                    });
                                }
                            }); 
                        }
                    });
                },
                
                eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc) {
                    $('#menuCriaEvento').hide();
                    $('#menuCriaEvento').hide();
                    var inicio = $.fullCalendar.formatDate(event.start,"yyyy-MM-dd HH:mm:ss");
                    var fim = $.fullCalendar.formatDate(event.end,"yyyy-MM-dd HH:mm:ss");

                    $.ajax({
                        type: "GET",
                        url: "../Dados/planeamentoBD.php?funcao=4&idPlaneamento="+event.id+"&dataInicio="+inicio+"&dataFim="+fim,
                        success: function(msg){
                            $('#resultadoAlteracoesEvento').html(msg);
                            $('#resultadoAlteracoesEvento').show();
                            $('#resultadoAlteracoesEvento').fadeOut(2500);            
                            $('#calendar').fullCalendar( 'refetchEvents' );
                            $('#calendar').fullCalendar('unselect');
                        }
                    }); 
                },
                
                eventResize: function(event,dayDelta,minuteDelta,revertFunc) {
                    
                    var inicio = $.fullCalendar.formatDate(event.start,"yyyy-MM-dd HH:mm:ss");
                    var fim = $.fullCalendar.formatDate(event.end,"yyyy-MM-dd HH:mm:ss");

                    $.ajax({
                        type: "GET",
                        url: "../Dados/planeamentoBD.php?funcao=4&idPlaneamento="+event.id+"&dataInicio="+inicio+"&dataFim="+fim,
                        success: function(msg){
                            $('#resultadoAlteracoesEvento').html(msg);
                            $('#resultadoAlteracoesEvento').show();
                            $('#resultadoAlteracoesEvento').fadeOut(2500);            
                            $('#calendar').fullCalendar( 'refetchEvents' );
                            $('#calendar').fullCalendar('unselect');
                        }
                    });
                }
            });
        });
        flagPlaneamento++;
    }
}

function carregaRegistoAtividade(){
    
    if(flagAtividade==0){
        var registo=document.getElementById("calendar");
        if(registo==null || flagPlaneamento !=0)
        {
            var tag=document.getElementById("intro");
            tag.innerHTML="<div id=\"calendar\"></div>";
        }
        flagPlaneamento=0;
        
        $(document).ready(function(){
            
            $("#calendar").fullCalendar({
                 
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                selectable: false,
                editable: false,
                slotMinutes:10,
                eventSources: [
                {
                    url:"../Dados/registoAtividadeBD.php?funcao=0"
                   
                }
                ],
                eventRender: function(event, element) {
                    element.qtip({
                        content: event.descricao,
                        position: {
                            corner:{
                                target: "topRight",
                                tooltip:"bottomLeft"
                            }
                        }
                    });
                }
            });
        });
        MakeXMLHTTPCall(42);
        flagAtividade++;
    }
}

function carregaGrafico(){
    carregaMenuOpcoes(7);
    var menuPrincipal = document.getElementById("intro");
    menuPrincipal.innerHTML="<h1>Para gerar um gráfico, selecione as datas e o tipo pretendido</h1>";
    refrescaVariaveis();
}

function carregaMenuOpcoes(opcao)
{
    var menuOpcoes=document.getElementById("opcoes");
    switch(opcao){
        case 0:
            menuOpcoes.innerHTML="<ul><li><a href=\"#\" onclick=\"MakeXMLHTTPCall(21);\">Dados Acesso</a></li>"+
            "<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(17);\">Sair</a></li></ul>";
            break;
        case 1:
            menuOpcoes.innerHTML="<ul><li><a href=\"#\" onclick=\"MakeXMLHTTPCall(1);\">Listar</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(5);\">Editar</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(7);\">Criar</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(9);\">Eliminar</a></li></ul>";
            break;
        case 2:
            menuOpcoes.innerHTML="<ul><li><a href=\"#\" onclick=\"MakeXMLHTTPCall(3);\">Listar</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(11);\">Editar</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(13);\">Criar</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(15);\">Eliminar</a></li></ul>";
            break;
        case 3:
            menuOpcoes.innerHTML="<ul><li><a href=\"#\" onclick=\"MakeXMLHTTPCall(22);\">Editar</a></li></ul>";
            break;     
        case 4:
            menuOpcoes.innerHTML="<ul><li><a href=\"#\" onclick=\"MakeXMLHTTPCall(56);\">Filtrar Contatos</a></li></ul>";
            break;
        case 5:
            menuOpcoes.innerHTML="<ul><li><a href=\"#\" onclick=\"MakeXMLHTTPCall(26);\">Listar</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(28);\">Editar</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(30);\">Criar</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(32);\">Eliminar</a></li></ul>";
            break;
        case 6:
            menuOpcoes.innerHTML="<ul><li><a href=\"#\" onclick=\"MakeXMLHTTPCall(34);\">Listar</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(36);\">Editar</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(38);\">Criar</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(40);\">Eliminar</a></li></ul>";
            break;    
        case 7:
            menuOpcoes.innerHTML="<u>Escolher datas</u><br>"
            +"<label>Data Inicio: <input id=\"inicioEventoPesquisa\" type=\"date\"></label>"
            +"<label>Data Fim: <input id=\"fimEventoPesquisa\" type=\"date\"></label>"     
            +"<form name=\"graficoForm\"><input type=\"radio\" value=\"0\" name=\"graficoRadio\" checked>Total Horas/Tarefa<br>"
            +"<input type=\"radio\" value=\"1\" name=\"graficoRadio\">% / Tarefas<br>"
            +"<input type=\"radio\" value=\"2\" name=\"graficoRadio\">Exec. vs Plan.</form>"
            +"<input type=\"button\" class=\"botaoMenuPesquisa\" onclick=\"MakeXMLHTTPCall(43);\" value=\"Pesquisar\">"
            +"<input type=\"reset\" class=\"botaoMenuSairPesquisa\" onclick=\"MakeXMLHTTPCall(0);\" value=\"Sair\">";
            break;
        case 8:
            menuOpcoes.innerHTML="<ul><li><a href=\"#\" onclick=\"MakeXMLHTTPCall(44);\">Listar</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(45);\">Histórico</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(46);\">Detalhe</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(48);\">Editar</a></li></ul>"
            +"<div id=\"historicoPesquisa\" style=\"display:none;\"><br><u>Escolher datas</u><p></p>"
            +"<label>Data Inicio: <input id=\"inicioAplicacaoPesquisa\" type=\"date\"></label>"
            +"<label>Data Fim: <input id=\"fimAplicacaoPesquisa\" type=\"date\"></label>"
            +"<input type=\"button\" class=\"botaoHistoricoPesquisa\" onclick=\"MakeXMLHTTPCall(47);\" value=\"Pesquisar\">"
            +"<input type=\"button\" class=\"botaoHistoricoSairPesquisa\" onclick=\"MakeXMLHTTPCall(44);\" value=\"Sair\">"
            +"</div>";
            break;
        case 9:
            menuOpcoes.innerHTML="<ul><li><a href=\"#\" onclick=\"MakeXMLHTTPCall(50);\">Listar</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(51);\">Histórico</a></li></ul>"
            +"<div id=\"historicoChamadasPesquisa\" style=\"display:none;\"><br><u>Escolher datas</u><p></p>"
            +"<label>Data Inicio: <input id=\"inicioChamadasPesquisa\" type=\"date\"></label>"
            +"<label>Data Fim: <input id=\"fimChamadasPesquisa\" type=\"date\"></label>"
            +"<input type=\"button\" class=\"botaoHistoricoChamadasPesquisa\" onclick=\"MakeXMLHTTPCall(52);\" value=\"Pesquisar\">"
            +"<input type=\"button\" class=\"botaoHistoricoChamadasSairPesquisa\" onclick=\"MakeXMLHTTPCall(50);\" value=\"Sair\">"
            +"</div>";
            break;            
        case 10:
            menuOpcoes.innerHTML="<ul><li><a href=\"#\" onclick=\"MakeXMLHTTPCall(53);\">Listar</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(54);\">Histórico</a></li></ul>"
            +"<div id=\"historicoMensagensPesquisa\" style=\"display:none;\"><br><u>Escolher datas</u><p></p>"
            +"<label>Data Inicio: <input id=\"inicioMensagensPesquisa\" type=\"date\"></label>"
            +"<label>Data Fim: <input id=\"fimMensagensPesquisa\" type=\"date\"></label>"
            +"<input type=\"button\" class=\"botaoHistoricoMensagensPesquisa\" onclick=\"MakeXMLHTTPCall(55);\" value=\"Pesquisar\">"
            +"<input type=\"button\" class=\"botaoHistoricoMensagensSairPesquisa\" onclick=\"MakeXMLHTTPCall(53);\" value=\"Sair\">"
            +"</div>";
            break;
        case 11:
            menuOpcoes.innerHTML="<ul><li><a href=\"#\" onclick=\"MakeXMLHTTPCall(58);\">Listar</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(59);\">Detalhe</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(60);\">Editar</a></li>"
            +"<li><a href=\"#\" onclick=\"MakeXMLHTTPCall(62);\">Exportar</a></li></ul>"
            break;
    }
}

function refrescaVariaveis(){
    var headerOpcoes = document.getElementById("headerOpcoes");
    headerOpcoes.innerHTML="Opções";
    flagPlaneamento = 0;    
    flagAtividade = 0;
    dataHoraFim = 0;
    dataHoraInicio = 0;
}

