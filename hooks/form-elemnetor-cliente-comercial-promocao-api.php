
<?php

/****   CÓDIGO FORM - CONTATO E COMERCIAL WHATSAPP    ****/

function promocao($record){

	$form_name = $record->get_form_settings( 'form_name' );
	
	
    // capturando o formulário pelo nome
    $nome_formulario = "promocao";
    
    //Definir o formulário
    if ( $nome_formulario !== $form_name ) {
        return;
    }

	// PEGANDO TELEFONE COMERCIAL
	$telefone_comercial = "6281762590";
  
	
	//Buscar os campos do formulário
    $raw_fields = $record->get( 'fields' );
    $fields = [];
    foreach ( $raw_fields as $id => $field ) {
        $fields[ $id ] = $field['value'];
    }
    
    $telefoneForm = preg_replace("/\D/","", $fields['telefone']);
	//Pegando DDD do cliente
	$telefone1 = substr($telefoneForm, 0, 2);
	
		// Validando DDD de São Paulo e Rio
	
	if( $telefone1 === "11" || $telefone1 === "12" || $telefone1 === "13" || $telefone1 === "14" || $telefone1 === "15" || $telefone1 === "16" || $telefone1 === "17" || $telefone1 === "18" || $telefone1 === "19" || $telefone1 === "21" || $telefone1 === "22" || $telefone1 === "24"){
		$telefone_cliente = $telefoneForm;
	}else{
		$telefone2 = substr($telefoneForm, -8);
		$telefone = $telefone1 . $telefone2;
		$telefone_cliente =  $telefone; 
	}

	// INFORMANDO TELEFONE COMERCIAL D DO CLIENTE
	$array = array($telefone_comercial, $telefone_cliente);
		

	// Montando requisição e enviando para API.
	foreach ($array as $key => $value) {


			// Montando mensagem para o cliente e o comercial.
			$apimsgsaudacao = "*CUPOM DE DESCONTO*";
			$despedida = array("Muito obrigado, agradecemos a preferencia!","Ficamos a disposição, abraços!","Pode contar com a gente, agradecemos a preferencia!");
			$apimsgdespedida = $despedida[array_rand($despedida)];
			$plinha = "\r\n";
			$texto_cliente = "*PARABÉNS* - Ganhou *50% OFF*.\r\n\r\nAgora digite *1*, para que nossso *atendente virtual* possa informar seu *cupom de desconto*.";
			
				//SEPARANDO MENSAGEM PARA O WHATSAPP COMERCIALE CLIENTE.
	
				if($value === $telefone_comercial){
					// Mensagem para o comercial
					$apimsgCliente = "Novo lead capturado".$plinha.$plinha."Telefone: ".$telefone_cliente ;
				} else{
					
					//Mensagem para o cliente
				
					$apimsgCliente = $apimsgsaudacao.$plinha.$plinha.$texto_cliente.$plinha.$plinha.$apimsgdespedida;
				}	
		

	

		// PEGANDO URL DO CLIENTE PARA VALIDAÇÃO
	$url_atual = "alsweb.com.br"; //str_replace(array('http://','https://'), '', get_site_url());
		
		// PEGAR ENDEREÇO DO INTERMEDIÁRIO DA API
	$url = 'http://116.203.60.247:8000/send-message/';

		// Informando o sender (chip), UR local, numero para disparo e mensagem.
	$data1 = array('sender' => 'primary','idc' => $url_atual,'number' => '55' . $telefone_cliente, 'message' => $apimsgCliente);	

		// Montando array com os dados de envio.
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data1),
				'timeout' => 10
			)
		);
	

		// Montando a requisição
		$context  = stream_context_create($options);
		// Enviando a requisição
		$result = file_get_contents($url, false, $context);

	}

}
add_action( 'elementor_pro/forms/new_record', 'promocao' );




