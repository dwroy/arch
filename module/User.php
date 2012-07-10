<?php
namespace module;

class User {

    private static $pool = array();

    private $user;

    private $profile;

    public static function instance( $id ){
        if( empty( User::$pool[$id] ) ){
            $user = new \model\User;
            if( $user->load( $id ) )
                User::$pool[$id] = new User( $user );
            else
                return;
        }

        return User::$pool[$id];
    }

    public static function signin( $email , $password ){
        if( isEmail( $email ) ){
            $user = new \model\User;
            if( $user->load( array( 'email' => $email ) ) ){
                if( $user->checkPassword( $password ) )
                    return User::$pool[$user->id] = new User( $user );
                \App::$message->setError( 'password' , 'wrong password' );
            }else
                \App::$message->setError( 'email' , 'email unexist' );
        }else
            \App::$message->setError( 'email' , 'wrong email' );

        return false;
    }

    public static function signup( $email , $password ){
        if( strlen( $password ) < 6 ){
            \App::$message->setError( 'password' , 'password is less than 6' );
            return false;
        }

        if( isEmail( $email ) ){
            $user = new \model\User;
            if( $user->setEmail( $email ) ){
                $user->password = $password;
                $user->save();
                return User::$pool[$user->id] = new User( $user );
            }
            \App::$message->setError( 'email' , 'email exsit' );
        }else
            \App::$message->setError( 'email' , 'wrong email' );

        return false;
    }

    protected function __construct( \model\User $user ){
        $profile = new \model\UserProfile;
        if( $profile->load( array( 'uid' => $user->id ) ) )
            $this->profile = $profile;

        $this->user = $user;
    }

    public function getData(){
        if( $this->profile )
            return array_merge( $this->user->data , $this->profile->data );
        else
            return $this->user->data;
    }
}