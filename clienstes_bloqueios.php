<!--[BLOCO 01-inicio]-->
    <?
        // clientes/views/index/bloqueios.php
        if ($_GET['idPessoa'] == '') {
            $generate->message("danger", "Escolha uma pessoa válida");
            die();
        }

        $cliParams['idPessoa'] = $_GET['idPessoa'];
        $Clientes->carregar($cliParams);
        $cliFields = $Clientes->fields['Clientes'];
        
        if (!is_array($cliFields)) {
            $generate->message("danger", "Escolha uma pessoa válida");
            die();
        }
    ?>
<!--[BLOCO 01-inicio]-->


<h1>Clientes - Desbloqueios</h1>
<h2>Cliente: <?=$_GET['idPessoa']; ?> - <?=$cliFields['nmPessoa']; ?></h2>

<!--[BLOCO 02-inicio]-->
    <style>
        .btn-toggle.btn-lg.focus,
        .btn-toggle.btn-lg.focus.active,
        .btn-toggle.btn-lg:focus,
        .btn-toggle.btn-lg:focus.active,
        .btn-toggle.focus,
        .btn-toggle.focus.active,
        .btn-toggle:focus,
        .btn-toggle:focus.active{
            outline:0
        }
        .btn-toggle{
            margin:0 4rem;
            padding:0;
            position:relative;
            border:none;
            height:1.5rem;
            width:3rem;
            border-radius:1.5rem;
            color:#6b7381;
            background:#bdc1c8
        }
        .btn-toggle:after,
        .btn-toggle:before{
            line-height:1.5rem;
            width:4rem;
            text-align:center;
            font-weight:600;
            font-size:.75rem;
            text-transform:uppercase;
            letter-spacing:2px;
            position:absolute;
            bottom:0;
            transition:opacity .25s
        }
        .btn-toggle>
        .handle{
            position:absolute;
            top:.1875rem;
            left:.1875rem;
            width:1.125rem;
            height:1.125rem;
            border-radius:1.125rem;
            background:#fff;
            transition:left .25s        
        }
        .btn-toggle.active{
            transition:background-color .25s;
            background-color:#2F5279
        }
        .btn-toggle.active>.handle{
            left:1.6875rem;
            transition:left .25s
        }
        .btn-toggle.active:before{
            opacity:.5;
            content:'Sim';
            right:9px
        }
        .btn-toggle.active:after{
            opacity:1
        }
        .btn-toggle.btn-lg{
            margin:0 5rem;
            padding:0;
            position:relative;
            border:none;
            height:2.5rem;
            width:5rem;
            border-radius:2.5rem
        }
        .btn-toggle:not(.active):after{
            content:'Não';
            left:10px
        }
        .btn-toggle.btn-lg:after,
        .btn-toggle.btn-lg:before{
            color:#FFF;
            line-height:2.5rem;
            width:5rem;
            text-align:center;
            font-weight:600;
            font-size:.9rem;
            text-transform:uppercase;
            letter-spacing:1px;
            position:absolute;
            bottom:0;
            transition:opacity .25s
        }
        .btn-toggle.btn-lg>.handle{
            position:absolute;
            top:.3125rem;
            left:.3125rem;
            width:1.875rem;
            height:1.875rem;
            border-radius:1.875rem;
            background:#fff;
            transition:left .25s
        }
        .btn-toggle.btn-lg.active{
            transition:background-color .25s
        }
        .btn-toggle.btn-lg.active>.handle{
            left:2.8125rem;
            transition:left .25s
        }
        .btn-toggle.btn-lg.active:before{
            opacity:.5
        }
        .btn-toggle.btn-lg.active:after{
            opacity:1
        }
        .btn-toggle.btn-lg.btn-sm:after,
        .btn-toggle.btn-lg.btn-sm:before{
            line-height:.5rem;
            color:#fff;
            letter-spacing:.75px;
            left:.6875rem;
            width:3.875rem
        }
        .btn-toggle.btn-lg.btn-sm:before{
            text-align:right
        }
        .btn-toggle.btn-lg.btn-sm:after{
            text-align:left;
            opacity:0
        }
        .btn-toggle.btn-lg.btn-sm.active:before{
            opacity:0
        }
        .btn-toggle.btn-lg.btn-sm.active:after{
            opacity:1
        }
        .btn-toggle.btn-lg.btn-xs:after,
        .btn-toggle.btn-lg.btn-xs:before{
            display:none
        }
    </style>
<!--[BLOCO 02-fim]-->

<!--[BLOCO 03-inicio]-->
    <table id="parametrosGerais123" class="table smart-form">
        <tr>
            <td>Bloqueado Definitivo:</td>
            <td align="right">        
                <button type="button" onclick="atualizarParametroRadio('isBloqueadoDefinitivo')" valor="<?=($cliFields['isBloqueadoDefinitivo'] == '' ? 'Não' : $cliFields['isBloqueadoDefinitivo']);?>" id="isBloqueadoDefinitivo" class="btn btn-lg btn-toggle <?=($cliFields['isBloqueadoDefinitivo'] == 'Sim' ? 'active' : '' ) ?>" data-toggle="button" aria-pressed="false" autocomplete="sim">
                    <div class="handle"></div>
                </button>        
            </td>
        </tr>
        <tr>
            <td>Cliente Bloqueado:</td>
            <td align="right">        
                <button type="button" onclick="atualizarParametroRadio('isBloqueado')" valor="<?=($cliFields['isBloqueado'] == '' ? 'Não' : $cliFields['isBloqueado']);?>" id="isBloqueado" class="btn btn-lg btn-toggle <?=($cliFields['isBloqueado'] == 'Sim' ? 'active' : '' ) ?>" data-toggle="button" aria-pressed="false" autocomplete="sim">
                    <div class="handle"></div>
                </button>        
            </td>
        </tr>
        <tr>
            <td>Credito de Cliente:</td>
            <td align="right">        
                <button type="button" onclick="atualizarParametroRadio('isCreditoBloqueado')" valor="<?=($cliFields['isCreditoBloqueado'] == '' ? 'Não' : $cliFields['isCreditoBloqueado']);?>" id="isCreditoBloqueado" class="btn btn-lg btn-toggle <?=($cliFields['isCreditoBloqueado'] == 'Sim' ? 'active' : '' ) ?>" data-toggle="button" aria-pressed="false" autocomplete="sim">
                    <div class="handle"></div>
                </button>        
            </td>
        </tr>
        <tr>
            <td>Condição Especial:</td>
            <td align="right">        
                <button type="button" onclick="atualizarParametroRadio('hasCondicaoEspecial')" valor="<?=($cliFields['hasCondicaoEspecial'] == '' ? 'Não' : $cliFields['hasCondicaoEspecial']);?>" id="hasCondicaoEspecial" class="btn btn-lg btn-toggle <?=($cliFields['hasCondicaoEspecial'] == 'Sim' ? 'active' : '' ) ?>" data-toggle="button" aria-pressed="false" autocomplete="sim">
                    <div class="handle"></div>
                </button>        
            </td>
        </tr>
        <tr>
            <td>Bloqueio na Entrega:</td>
            <td align="right">        
                <button type="button" onclick="atualizarParametroRadio('isBloqueadoEntrega')" valor="<?=($cliFields['isBloqueadoEntrega'] == '' ? 'Não' : $cliFields['isBloqueadoEntrega']);?>" id="isBloqueadoEntrega" class="btn btn-lg btn-toggle <?=($cliFields['isBloqueadoEntrega'] == 'Sim' ? 'active' : '' ) ?>" data-toggle="button" aria-pressed="false" autocomplete="sim">
                    <div class="handle"></div>
                </button>        
            </td>
        </tr>
        <tr>
            <td>Exige ordem de compra:</td>
            <td align="right">        
                <button type="button" onclick="atualizarParametroRadio('isClienteExigeOrdemDeCompra')" valor="<?=($cliFields['isClienteExigeOrdemDeCompra'] == '' ? 'Não' : $cliFields['isClienteExigeOrdemDeCompra']);?>" id="isClienteExigeOrdemDeCompra" class="btn btn-lg btn-toggle <?=($cliFields['isClienteExigeOrdemDeCompra'] == 'Sim' ? 'active' : '' ) ?>" data-toggle="button" aria-pressed="false" autocomplete="sim">
                    <div class="handle"></div>
                </button>        
            </td>
        </tr>
        <tr>
            <td>Permite tirar boleto sem nota:</td>
            <td align="right">        
                <button type="button" onclick="atualizarParametroRadio('hasBoletoTipo1')" valor="<?=($cliFields['hasBoletoTipo1'] == '' ? 'Não' : $cliFields['hasBoletoTipo1']);?>" id="hasBoletoTipo1" class="btn btn-lg btn-toggle <?=($cliFields['hasBoletoTipo1'] == 'Sim' ? 'active' : '' ) ?>" data-toggle="button" aria-pressed="false" autocomplete="sim">
                    <div class="handle"></div>
                </button>        
            </td>
        </tr>
        <?
            if (($_SESSION['idFilial'] == $cliFields['idFilial']) || ($cliFields['idFilial'] == 0) || ($_SESSION['tpUsuario'] == 'AD')) {
                ?>
                <tr>
                    <td>Faturar somente na filial <?=$cliFields['idFilial']; ?>:</td>
                    <td align="right">
                        <button type="button" onclick="atualizarParametroRadio('isBloqueadoMultifilial')" valor="<?=($cliFields['isBloqueadoMultifilial'] == '' ? 'Não' : $cliFields['isBloqueadoMultifilial']);?>" id="isBloqueadoMultifilial" class="btn btn-lg btn-toggle <?=($cliFields['isBloqueadoMultifilial'] == 'Sim' ? 'active' : '' ) ?>" data-toggle="button" aria-pressed="false" autocomplete="sim">
                            <div class="handle"></div>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>Filial do Cliente:</td>
                    <td align="right">
                        <input onchange="atualizarParametroText('idFilial', $(this).val())" type="text" value="<?=$cliFields['idFilial']; ?>">
                    </td>
                </tr>
                <?
            }
        ?>			
    </table>
<!--[BLOCO 03-fim]-->

<!--[BLOCO 04-inicio]-->
    <br><br>
    <div class="text-center">
    <button onclick="zerarLimite()" class="btn btn-xs btn-danger" >Zerar Limite</button>
    </div>
    <script>
        function zerarLimite() {
            var url = defaultDOM + '/scripts/clientes/index/actions/zerarLimite&idPessoa=<?=$_GET['idPessoa']; ?>';
            $.get(url, function(data) {
                criar_areaformulario_buscar();
                fecharAssistente('');
                return false;			
            });
        }

        function atualizarParametroText(nmParametro,vrParamAtual) {	
            if (vrParamAtual) {
                var url = defaultDOM + '/scripts/clientes/index/actions/atualizarParametro&idPessoa=<?=$_GET['idPessoa']; ?>&nmParametro='+nmParametro+'&vrParametro='+vrParamAtual;
                $.get(url, function(data) {
                    console.log(url);
                    return false;

                });
            }
        }
        function atualizarParametroRadio(nmParametro) {
            var vrParamAtual = '';		
            var ValorAntigo = $('#'+nmParametro).attr('valor');		
            if (ValorAntigo == 'Sim') {
                vrParamAtual = 'Não';
                $('#'+nmParametro).attr('valor', vrParamAtual);
            } else if(ValorAntigo == 'Não') {
                vrParamAtual = 'Sim';
                $('#'+nmParametro).attr('valor', vrParamAtual);
            }
            var url = defaultDOM + '/scripts/clientes/index/actions/atualizarParametro&idPessoa=<?=$_GET['idPessoa']; ?>&nmParametro='+nmParametro+'&vrParametro='+vrParamAtual;
            $.get(url, function(data) {
                console.log(url);
                return false;			
            });
        }
        
    </script>
<!--[BLOCO 03-inicio]-->
