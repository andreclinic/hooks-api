
<?php

function promocao($record){

	$form_name = $record->get_form_settings( 'form_name' );
	
	
    // capturando o formulário pelo nome
    $nome_formulario = "promocao";
    
    //Definir o formulário
    if ( $nome_formulario !== $form_name ) {
        return;
    }

  
	
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
		

	$texto = "*PARABÉNS* - Ganhou *50% OFF*.\r\n\r\nAgora digite *1*, para que nossso *atendente virtual* possa informar seu *cupom de desconto*.";

	$url_atual = 'alsweb.com.br';//str_replace(array('http://','https://'), '', get_site_url());
	
	$url = 'http://23.88.58.172:8000/send-message';

		// Informando o sender (chip), UR local, numero para disparo e mensagem.
		$data1 = array('sender' => 'primary','idc' => $url_atual,'number' => '55' . $telefone_cliente, 'message' => $texto);	

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
add_action( 'elementor_pro/forms/new_record', 'promocao' );

