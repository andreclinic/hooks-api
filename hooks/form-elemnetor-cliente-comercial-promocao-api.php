
<?php



/*****************************************************************************************************/
/*********************************** API Whatsapp Form Elementor *************************************/
/*****************************************************************************************************/

 
// Pegando dados para o enviar para API do WhatsApp

function pegando_dados_api($record){
	
	$form_name = $record->get_form_settings( 'form_name' );
	
	// capturando o formulário pelo nome
	$nome_formulario = 'cupom';
	
	
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
		// Recuperando os campos do formulário (telefone)
    	$telefone = $fields['nwa'];
		
		$telComercial = "62986062592";
		
		$msgCliente = "*PARABÉNS* - Ganhou *50% OFF*.\r\n\r\nAgora digite *1*, para que nossso *atendente virtual* possa informar seu *cupom de desconto*.";
	

		$msgComercial = "Lead captado na landingpage *".$telefone."*";
	
	
		// Nome do sender de envio
		$sender = "primary";
	
		// URL da API ou Sistema Intermediário
		$urlApi = "http://23.88.57.118:9000/send-message/";
	
		// Função para enviar os dados para API
		preparando_envio_mensagem_api($telefone, $telComercial, $msgCliente, $msgComercial, $sender, $urlApi);
	
}

	/************************** Hooks dos formulários do Jet Engine **************************/

	add_action('elementor_pro/forms/new_record', 'pegando_dados_api');

	
	/************************** Preparando dados para envio da Mensagem para API **************************/

	function preparando_envio_mensagem_api($telefone, $telComercial, $msgCliente, $msgComercial, $sender, $urlApi){

    $telefoneCliente = valida_telefone($telefone);
    $telComercial = valida_telefone($telComercial);
    $telefones = array('telComercial' => $telComercial, 'telCliente' => $telefone);
    $msgCompleta = $msgCliente;


    foreach ($telefones as $value):
        $telefoneArray = $value;
        if($telefoneArray == $telComercial){
            
            $msgCompleta = $msgComercial;
            $telefone = $telComercial;

            $r = enviar_mensagem_whatsapp_texto_api($sender,$telefone,$msgCompleta, $urlApi);


        
            if($r['status'] == "sucesso"){
                //echo "Mensagem Enviada com sucesso";
            }else{
                //echo "Houve um erro";
            }

        }else{
            $msgCompleta = $msgCliente;
            $telefone = $telefoneCliente;
 
            $r = enviar_mensagem_whatsapp_texto_api($sender,$telefone,$msgCompleta, $urlApi);agendamento com Jet Apintment

            if($r['status'] == "sucesso"){
                //echo "Mensagem Enviada com sucesso";
            }else{
                //echo "Houve um erro";
            }
        }
 
    
    endforeach;

}


/************************** Executa o Envio da Mensagem para API **************************/

function enviar_mensagem_whatsapp_texto_api($sender,$telefone,$msgCompleta, $urlApi){
    
    
	$dominio = str_replace(array('http://','https://'), '', get_site_url());

    $data1 = array('sender'=> $sender, 'idc' => $dominio,  'number' => '+55' . $telefone, 'message' => $msgCompleta);
    // use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data1),
            'timeout' => 10,
        ),
    );
    // Montando a requisição
    $context = stream_context_create($options);
    // Enviando a requisição
    $result = file_get_contents($urlApi, false, $context);

    $resultado = json_decode($result);

    $status_resultado = $resultado->status;

    if($status_resultado == 1){
        $r = array('status' => 'sucesso');
    }else{
        $r = array('status'=> 'Houve um erro ao tentar enviar sua mensagem');
    }

    return $r;
    
}


/************************** Função validar validar o telefone **************************/

function valida_telefone($telefone)
{
    
    $telefone = preg_replace("/\D/", "", $telefone); 
    $ddd = substr($telefone, 0, 2); 

    if ($ddd === "11" || $ddd === "12" || $ddd === "13" || $ddd === "14" || $ddd === "15" || $ddd === "16" || $ddd === "17" || $ddd === "18" || $telefone1 === "19" || $telefone1 === "21" || $telefone1 === "22" || $telefone1 === "24") {
        $telefone = $telefone;
    } else {
        $telefone = substr($telefone, -8); 
        $telefone = $ddd . $telefone; 
        $telefone = $telefone;
    }

    return $telefone;
}






