<?php

require_once 'DBOperations.php';
require 'PHPMailer/PHPMailerAutoload.php';



class Functions{

private $db;
private $mail;

public function __construct() {

      $this -> db = new DBOperations();
      $this -> mail = new PHPMailer;

}


public function registerUser($name, $email, $password) {

	$db = $this -> db;

	if (!empty($name) && !empty($email) && !empty($password)) {

  		if ($db -> checkUserExist($email)) {

  			$response["result"] = "failure";
  			$response["message"] = "Usuario ja registrado!";
  			return json_encode($response);

  		} else {

  			$result = $db -> insertData($name, $email, $password);

  			if ($result) {

				  $response["result"] = "success";
  				$response["message"] = "Usuario registrado com sucesso!";
  				return json_encode($response);
  						
  			} else {

  				$response["result"] = "failure";
  				$response["message"] = "Falha de registro";
  				return json_encode($response);

  			}
  		}					
  	} else {

  		return $this -> getMsgParamNotEmpty();

  	}
}

public function loginUser($email, $password) {

  $db = $this -> db;

  if (!empty($email) && !empty($password)) {

    if ($db -> checkUserExist($email)) {

       $result =  $db -> checkLogin($email, $password);


       if(!$result) {

        $response["result"] = "failure";
        $response["message"] = "Usuario o senha invalidos";
        return json_encode($response);

       } else {

        $response["result"] = "success";
        $response["message"] = "Login bem sucedido";
        $response["user"] = $result;
        return json_encode($response);

       }

    } else {

      $response["result"] = "failure";
      $response["message"] = "Credenciais de login invalidas";
      return json_encode($response);

    }
  } else {

      return $this -> getMsgParamNotEmpty();
    }

}


public function changePassword($email, $old_password, $new_password) {

  $db = $this -> db;

  if (!empty($email) && !empty($old_password) && !empty($new_password)) {

    if(!$db -> checkLogin($email, $old_password)){

      $response["result"] = "failure";
      $response["message"] = 'Senha antiga invalida';
      return json_encode($response);

    } else {


    $result = $db -> changePassword($email, $new_password);

      if($result) {

        $response["result"] = "success";
        $response["message"] = "Senha alterada com sucesso";
        return json_encode($response);

      } else {

        $response["result"] = "failure";
        $response["message"] = 'Erro ao atualizar a senha';
        return json_encode($response);

      }

    } 
  } else {

      return $this -> getMsgParamNotEmpty();
  }

}

public function resetPasswordRequest($email){

  $db = $this -> db;

  if ($db -> checkUserExist($email)) {

    $result =  $db -> passwordResetRequest($email);

    if(!$result){

      $response["result"] = "failure";
      $response["message"] = "Falha de redefinicao de senha";
      return json_encode($response);

    } else {

      $mail_result = $this -> sendEmail($result["email"],$result["temp_password"]);

      if($mail_result){

        $response["result"] = "success";
        $response["message"] = "Verifique seu e-mail para redefinir o codigo da senha.";
        return json_encode($response);

      } else {

        $response["result"] = "failure";
        $response["message"] = "Falha de redefinicao de senha";
        return json_encode($response);
      }
    }


  } else {

    $response["result"] = "failure";
    $response["message"] = "Email nao existe";
    return json_encode($response);

  }

}

public function resetPassword($email,$code,$password){

  $db = $this -> db;

  if ($db -> checkUserExist($email)) {

    $result =  $db -> resetPassword($email,$code,$password);

    if(!$result){

      $response["result"] = "failure";
      $response["message"] = "Falha de redefinicao de senha";
      return json_encode($response);

    } else {

      $response["result"] = "success";
      $response["message"] = "Senha alterada com sucesso";
      return json_encode($response);

    }


  } else {

    $response["result"] = "failure";
    $response["message"] = "Email nao existe";
    return json_encode($response);

  }

}

public function sendEmail($email,$temp_password){

  $mail = $this -> mail;
  $mail->isSMTP();
  $mail->Host = 'smtp.exemplo.com.br';
  $mail->SMTPAuth ='true';
  $mail->Username = 'email@exemplo.com.br';
  $mail->Password = 'suasenha';
  $mail->SMTPSecure = 'tls';
  $mail->Port = 25;
 
  $mail->From = 'email@exemplo.com.br';
  $mail->FromName = 'Charles Silva';
  $mail->addAddress($email, 'Charles Silva');
 
  $mail->addReplyTo('email@exemplo.com.br', 'Charles Silva');
 
  $mail->WordWrap = 50;
  $mail->isHTML(true);
 
  $mail->Subject = 'Solicitação de redefinição de senha';
  $mail->Body    = 'Olá!, <br> <br> Seu código de redefinição de senha é <b>'.$temp_password.'</b> . Este código expira em 120 segundos. Digite este código dentro de 120 segundos para redefinir sua senha.<br><br>Obrigado,<br>Charles Silva.';

  if(!$mail->send()) {

   return $mail->ErrorInfo;

  } else {

    return true;

  }

}

public function sendPHPMail($email,$temp_password){

  $subject = 'Solicitação de redefinição de senha';
  $message = 'Olá!,\n\n Seu código de redefinição de senha é '.$temp_password.' . Este código expira em 120 segundos. Digite este código dentro de 120 segundos para redefinir sua senha.\n\nObrigado,\nCharles Silva.';
  $from = "email@exemplo.com.br";
  $headers = "From:" . $from;

  return mail($email,$subject,$message,$headers);

}


public function isEmailValid($email){

  return filter_var($email, FILTER_VALIDATE_EMAIL);
}

public function getMsgParamNotEmpty(){


  $response["result"] = "failure";
  $response["message"] = "Os parametros nao devem estar vazios!";
  return json_encode($response);

}

public function getMsgInvalidParam(){

  $response["result"] = "failure";
  $response["message"] = "Parametros invalidos";
  return json_encode($response);

}

public function getMsgInvalidEmail(){

  $response["result"] = "failure";
  $response["message"] = "E-mail inexistente";
  return json_encode($response);

}

}
