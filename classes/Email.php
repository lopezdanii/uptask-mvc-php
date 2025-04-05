<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email{
    protected $email;
    protected $nombre;
    protected $token;

    public function __construct($email, $nombre, $token){
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion(){
        $mail = new PHPMailer();
        $mail->isSMTP();

        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com');
        $mail->Subject = 'Confirma tu cuenta';

        $mail->isHTML(TRUE);
        $mail->CharSet= 'UTF-8';

        //Contenido que se muestra en el email
        $contenido= '<html>';
        $contenido.="<p><strong>Hola " . $this->nombre . "</strong>";
        $contenido.="<p> Para confirmar la creación de tu cuenta en UpTask, pulsa el siguiente enlace </p>";
        
        $contenido.="<p>Presiona aqui: <a href='" . $_ENV['APP_URL'] . "/confirmar?token=". $this->token . "'>Confirmar cuenta</a></p>";
        $contenido.="<p> Si tu no creaste esta cuenta, puedes ignorar este mensaje. </p>";

        $contenido.= '</html>';

        $mail->Body = $contenido;
        $mail->send();
    }

    public function enviarInstrucciones(){

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com');
        $mail->Subject = 'Restablece tu contraseña';

        $mail->isHTML(TRUE);
        $mail->CharSet= 'UTF-8';

        //Contenido que se muestra en el email
        $contenido= '<html>';
        $contenido.="<p><strong>Hola " . $this->nombre . "</strong>";
        $contenido.="<p> Has solicitado restablecer tu contraseña en UpTask. </p>";
        
        $contenido.="<p>Presiona aqui para crear una nueva contraseña: <a href='" . $_ENV['APP_URL'] . "/restablecer?token=". $this->token . "'>Restablecer contraseña</a></p>";
        $contenido.="<p> Si tu no solicitaste este cambio, puedes ignorar este mensaje. </p>";

        $contenido.= '</html>';

        $mail->Body = $contenido;
        $mail->send();
    
    }

}

