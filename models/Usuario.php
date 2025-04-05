<?php
namespace Model;
class Usuario extends ActiveRecord {
    protected static $tabla= 'usuarios';
    protected static $columnasDB= ['id','nombre','email','password','token','confirmado'];

    /*
    public static $id;
    public static $nombre;
    public static $email;
    public static $password;
    public static $token;
    public static $confirmado;
    */
    public function __construct($args= []){
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }
    
    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][]= 'El email del usuario es obligatorio';
        }
        if(!$this->password){
            self::$alertas['error'][]= 'La contraseña del usuario es obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][]= 'Email no válido';

        }

        return self::$alertas;
    }

    //Validacion para nuevas cuentas
    public function validar(){
        if(!$this->nombre){
            self::$alertas['error'][]= 'El nombre del usuario es obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][]= 'El email del usuario es obligatorio';
        }
        if(!$this->password){
            self::$alertas['error'][]= 'La contraseña del usuario es obligatorio';
        }
        if(strlen($this->password2) < 6){
            self::$alertas['error'][]= 'La contraseña debe contener al menos 6 caracteres';
        }
        if($this->password !== $this->password2){
            self::$alertas['error'][]= 'Las contraseñas no son iguales';
        }

        return self::$alertas;
    }

    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][]= 'El email es obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][]= 'Email no válido';

        }

        return self::$alertas;
    }

    public function validarPassword(){
        if(!$this->password){
            self::$alertas['error'][]= 'La contraseña del usuario es obligatorio';
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][]= 'La contraseña debe contener al menos 6 caracteres';
        }

        return self::$alertas;
    }
        
    //Hashea el password
    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }
    
    public function crearToken(){
        $this->token = uniqid();
    }

}