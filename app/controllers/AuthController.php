<?php

namespace App\controllers;

use App\models\Usuario;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Controllers;

class AuthController extends Controller
{
    // public static function confirmar_email()
    // {
    //     if (isset($_GET['token'])) {
    //         try {
    //             self::confirma_criacao_conta($_GET['token']);
    //         } catch (\Throwable $th) {
    //             echo $th->getMessage();
    //         }
    //     }
    // }
    // public static function verifica_email_existe($email)
    // {
    //     if (empty(Usuario::buscar_usuario_by_email($email))) {
    //         return true;
    //     } else {
    //         return false;
    //         //    $this->view('error_existe_user');
    //         // echo "Já existe um usuário como o email." . $email;
    //         // exit();
    //     }
    // }
    // public static function autenticar($email, $senha)
    // {
    //     // 1. Captura os dados do formulário (ajuste conforme sua estrutura)
    //     // $email = $_POST['email'] ?? null;
    //     // $senha = $_POST['senha'] ?? null;
    //     // echo($email.''.$senha.'');

    //     // 2. Verifica se o usuário existe
    //     $usuario = Usuario::buscar_usuario_by_email($email);


    //     if (!$usuario || !password_verify($senha, $usuario['senha'])) {
    //         // Usuário ou senha inválidos
    //         // $_SESSION['erro'] = 'Email ou senha inválidos.';
    //         header('Location: /login');
    //         exit;
    //     }

    //     // 3. Gera token para autenticação (por exemplo, 6 dígitos)
    //     $token = rand(100000, 999999);

    //     // 4. Salva token no banco
    //     // Usuario::gravar_token_acesso($usuario['id'], $token);
    //     // echo($token);
    //     //   die;
    //     // 5. Envia token por e-mail

    //     $enviado = self::enviarConfirmacaoEmailAcesso($usuario['email'], $token);

    //     if ($enviado) {
    //         return;
    //         //echo "Cadastro realizado! Verifique seu e-mail para confirmar a conta.";
    //     } else {
    //         echo "Erro ao enviar e-mail de confirmação.";
    //         exit();
    //     }
    //     // 6. Redireciona para página de confirmação de token
    //     // $_SESSION['email_autenticando'] = $email;
    //     // header('Location: /confirmar-acesso');
    //     // exit;
    // }

    // public static function registrar()
    // {
    //     // Recebe os dados do POST "tvax duup iwhr gltw"
    //     $nome  = $_POST['nome-reg'] ?? '';
    //     $email = $_POST['email-reg'] ?? '';
    //     $senha = $_POST['senha-reg'] ?? '';

    //     if (!$nome || !$email || !$senha) {
    //         echo "Preencha todos os campos.";
    //         return;
    //     }

    //     // Hash da senha
    //     $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    //     // Geração do token e expiração
    //     $token  = bin2hex(random_bytes(32));

    //     $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
    //     //  echo($expira);die;

    //     //verifica se usuario existe baseadao no email 

    //     // Salvar no banco
    //     $salvo = Usuario::criar([
    //         'nome'                 => $nome,
    //         'email'                => $email,
    //         'senha'                => $senhaHash,
    //         'email_token'          => $token,
    //         'email_token_expira'  => $expira,
    //     ]);

    //     if (!$salvo) {
    //         echo "Erro ao registrar.";
    //         return;
    //     }

    //     // Enviar e-mail
    //     $enviado = self::enviarConfirmacaoEmail($email, $token);

    //     if ($enviado) {
    //         return;
    //         //echo "Cadastro realizado! Verifique seu e-mail para confirmar a conta.";
    //     } else {
    //         echo "Erro ao enviar e-mail de confirmação.";
    //         exit();
    //     }
    // }

    // private static function enviarConfirmacaoEmailAcesso($email, $token)
    // {


    //     $assunto = 'Confirmação de Acesso';

    //     $mensagem = '
    //     <html>
    //     <head>
    //       <meta charset="UTF-8">
    //       <title>Confirmação de Acesso</title>
    //     </head>
    //     <body>
    //       <div>
    //         <h1>Bem-vindo!</h1>
    //         <p>Obrigado por se cadastrar. Para ativar sua conta, clique no botão abaixo:</p>
    //         <h1>' . $token . '</h1>
    //       </div>
    //     </body>
    //     </html>';
    //     // Instancia o PHPMailer
    //     $mail = new PHPMailer(true);

    //     try {
    //         $mail->isSMTP();
    //         $mail->Host       = 'smtp.gmail.com';                   // <- HOST DO GMAIL
    //         $mail->SMTPAuth   = true;
    //         $mail->Username   = 'cavalcante02@gmail.com';               // <- SEU GMAIL
    //         $mail->Password   = 'tvax duup iwhr gltw';                 // <- SENHA DE APLICATIVO DO GMAIL
    //         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    //         $mail->Port       = 587;
    //         $mail->setFrom('cavalcante02@gmail.com', 'Nome Remetente');
    //         $mail->addAddress("cavalcante02@gmail.com");
    //         $mail->isHTML(true);
    //         $mail->Subject = $assunto;
    //         $mail->Body    = $mensagem;
    //         // Envia o e-mail
    //         $mail->send();
    //         return true;
    //     } catch (Exception $e) {
    //         echo ("Erro ao enviar e-mail: {$mail->ErrorInfo}");
    //         die;
    //         return false;
    //     }
    // }


    // private static function enviarConfirmacaoEmail($email, $token)
    // {
    //     // $host = $_SERVER['HTTP_HOST'];

    //     $link =  $_SERVER['HTTP_HOST'] . '/confirmar?token=' . urlencode($token);

    //     $assunto = 'Confirmação de Cadastro';

    //     $mensagem = '
    //     <html>
    //     <head>
    //       <meta charset="UTF-8">
    //       <title>Confirmação de Cadastro</title>
    //     </head>
    //     <body>
    //       <div>
    //         <h1>Bem-vindo!</h1>
    //         <p>Obrigado por se cadastrar. Para ativar sua conta, clique no botão abaixo:</p>
    //         <p><a href="' . $link . '">Confirmar e-mail</a></p>
    //       </div>
    //     </body>
    //     </html>';
    //     // Instancia o PHPMailer
    //     $mail = new PHPMailer(true);

    //     try {
    //         $mail->isSMTP();
    //         $mail->Host       = 'smtp.gmail.com';                   // <- HOST DO GMAIL
    //         $mail->SMTPAuth   = true;
    //         $mail->Username   = 'cavalcante02@gmail.com';               // <- SEU GMAIL
    //         $mail->Password   = 'tvax duup iwhr gltw';                 // <- SENHA DE APLICATIVO DO GMAIL
    //         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    //         $mail->Port       = 587;
    //         $mail->setFrom('cavalcante02@gmail.com', 'Nome Remetente');
    //         $mail->addAddress("cavalcante02@gmail.com");
    //         $mail->isHTML(true);
    //         $mail->Subject = $assunto;
    //         $mail->Body    = $mensagem;
    //         // Envia o e-mail
    //         $mail->send();
    //         return true;
    //     } catch (Exception $e) {
    //         echo ("Erro ao enviar e-mail: {$mail->ErrorInfo}");
    //         die;
    //         return false;
    //     }
    // }
    // private static function confirma_criacao_conta($token)
    // {
    //     $ret = Usuario::buscarPorTokenDeEmail($token);
    //     if ($ret) {
    //         Usuario::confirmarEmail($ret[0]);
    //         header('Location: /login');
    //         exit;
    //     }
    // }
}
