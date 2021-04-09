<!--clientes/views/contatos/index.php-->
<h1>CRM - Contatos Registrados</h1>
<?
	$noProgramMenu = true;

	require_once '../mtos/controllers/utils/fastsearch.php';
	require_once 'modules/clientes/controllers/clientes.php';
    require_once 'modules/configuracoes/controllers/configuracoes.php';

	if (($_GET['dtPeriodo'] == '') && ($parametros['financeiro_cobrancasPagar_ignorarPeriodo'] != 'Sim')) {
		$_GET['dtPeriodo'] = date("d-m-Y,d-m-Y");
	} 
	

	$fastsearch = new fastsearch([
		'url' => $controller . '/contatos/index.pesquisar',
		'autoload' => true,
		'hasPrint' => true
    ]);
	

	$filiaisArr = $Configuracoes->filiais_listar([]);	
	$selectFiliais = $fastsearch->convertToSelect($filiaisArr, 'idFilial', 'nmApelidoFilial');
	
	$fastsearch->fastsearch_add([
		'label' => 'Filial',
		'name' => 'idFilial',
		'type' => 'select',
		'size' => 1,
		'array' => $selectFiliais,
		'defaultValue' => $_GET['idFilial']
    ]);

	$fastsearch->fastsearch_add([
		'label' => 'Código Cliente',
		'name' => 'idPessoa',
		'type' => 'number',
		'size' => 1,
		'defaultValue' => $_GET['idPessoa']
	);

	$fastsearch->fastsearch_add([
		'label' => 'Nome do Cliente',
		'name' => 'nmPessoa',
		'b64' => false,
		'type' => 'text',
		'size' => 3,
		'defaultValue' => $_GET['nmProduto']
    ]);

	$fastsearch->fastsearch_add([
		'label' => 'Período',
		'name' => 'dtPeriodo',
		'size' => 1,
		'type' => 'dateInterval',
		'defaultValue' => $_GET['dtPeriodo'],
		'options' => array(
			'hasIgnorar' => 'Sim'
		)
    ]);

	$fastsearch->fastsearch_render();
	$fastsearch->fastsearch_render_container();
?>