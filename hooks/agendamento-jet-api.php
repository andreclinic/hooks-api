<?php

/****   CÓDIGO AGENDAMENTO WHATSAPP    ****/

	function agendamento_cliente($data ){
		// PEGANDO TELEFONE COMERCIAL
		$telefone_empresa = "6281762590";
		// Pegando telefone do Cliente
		$nome_cliente =  $data['user_name'];
		//Pegando e-mail do cliente
		$email_cliente =  $data['user_email'];
		//Pegando telefone do cliente e removendo caracteres especiais
		$telefoneForm = preg_replace("/\D/","", $data['user_phone']);
		//Pegando DDD do cliente
		$telefone1 = substr($telefoneForm, 0, 2);
		
		// VALIDANDO DDD DE SÃO PAULO E RIO
	
		if( $telefone1 === "11" || $telefone1 === "12" || $telefone1 === "13" || $telefone1 === "14" || $telefone1 === "15" || $telefone1 === "16" || $telefone1 === "17" || $telefone1 === "18" || $telefone1 === "19" || $telefone1 === "21" || $telefone1 === "22" || $telefone1 === "24"){
			$telefone_cliente = $telefoneForm;
		}else{
			$telefone2 = substr($telefoneForm, -8);
			$telefone = $telefone1 . $telefone2;
			$telefone_cliente =  $telefone; 
		}

		
		// Pegando e Tratando as datas No banco do jet apointiment
		global $wpdb;
		$id_agendamento = $data['appointment_id_list'][0];
		$result = $wpdb->get_results ( "SELECT * FROM wp_jet_appointments  WHERE ID = $id_agendamento ",ARRAY_A );
		$data_dia = date('d/m/Y', $result[0]['date']);
		$data_hi = date('H:i', $result[0]['slot']);
		$data_hs = date('H:i', $result[0]['slot_end']);
		function agendamento_cliente);	
		

		// INFORMANDO TELEFONE COMERCIAL D DO CLIENTE
		$array = array($telefone_empresa, $telefone_cliente);
		

		// Montando requisição e enviando para API.
		foreach ($array as $key => $value) {
			
		// Montando mensagem para o cliente e o comercial.
		$apimsgsaudacao = "*AGENDAMENTO*";
		$despedida = array("Muito obrigado, agradecemos a preferencia!","Ficamos a disposição, abraços!","Pode contar com a gente, agradecemos a preferencia!");
		$apimsgdespedida = $despedida[array_rand($despedida)];
		$plinha = "\r\n";
		
			//SEPARANDO MENSAGEM PARA O WHATSAPP COMERCIALE CLIENTE.

			if($value === $telefone_empresa){
				// Mensagem para o comercial
				$apimsgCliente = $apimsgsaudacao.$plinha.$plinha."*Nome:* ".$nome_cliente.".".$plinha.$plinha."*E-mail:* ".$email_cliente.".".$plinha.$plinha."*Telefone:* ".$telefone_cliente.".".$plinha.$plinha."*Data:* ".$data_dia." Início as ".$data_hi." e finaliza as".$data_hs.".".$plinha.$plinha."*Tratamento:* ".$tratamento->post_title.".".$plinha.$plinha."*Dr:* ".$profissional->post_title.".";
			} else{
				
				//Mensagem para o cliente
			
				$apimsgCliente = $apimsgsaudacao.$plinha.$plinha."Obrigado *". $nome_cliente."*".".".$plinha.$plinha."Seu agendamento foi feito com sucesso!".$plinha.$plinha."*Tratamento:* ".$tratamento->post_title.".".$plinha.$plinha."*Data:* ".$data_dia." Início as ".$data_hi." e finaliza as".$data_hs.".".$plinha.$plinha."*Dr:* ".$profissional->post_title.".".$plinha.$plinha.$apimsgdespedida;
			}
		
		// PEGANDO URL DO CLIENTE PARA VALIDAÇÃO
		$url_atual = str_replace(array('http://','https://'), '', get_site_url());
		
		// PEGAR ENDEREÇO DO INTERMEDIÁRIO DA API
		$url = 'http://157.90.27.54:8000/send-message';
		
		// Informando o sender (chip), UR local, numero para disparo e mensagem.
		$data1 = array('sender' => 'primary','idc' => $url_atual,'number' => '55' . $value, 'message' => $apimsgCliente);	
		
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
	// HOOK DO FORMULÁRIO DE JETENGINE
	add_action( 'jet-engine-booking/jet-engine-booking/disparo_whatsapp', 'agendamento_cliente', 10, 3);
				



