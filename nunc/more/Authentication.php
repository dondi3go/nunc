<?php
require_once(__DIR__.'/../core/Session.php');

session_start();

//
// Interface used by Authentication
//
interface IUser
{
    // Return true or false
    public function matchLoginPassword( $login, $password );
}


//
// The most basic implementation of IUser
// Please do not use this class in production environment
//
class BasicUser
{
    private $login;
    private $password;
    
    public function BasicUser( $login, $password )
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function matchLoginPassword( $login, $password )
    {
        if( ( $this->login == $login )&&( $this->password == $password ) )
        {
            return true;
        }
        return false;
    }
}


interface IAuthenticationState
{
    const NOT_CONNECTED = 1;
    const WRONG_CREDENTIALS = 2;
    const CONNECTED = 3;
    const DISCONNECTED_USER = 4;
    const DISCONNECTED_IP = 5;
    const DISCONNECTED_TIME = 6;
}


interface IAuthenticationLogger
{
    public function logInSuccess(); // login / ip
    public function logInFailure(); // login / ip
    public function logOut();       // login / ip
}


//
//  Class handling user login
//  Just test userConnected() in your page, that should be enough
//
class Authentication
{
    // Array of Users
    private $users = array();

    // Logger
    private $logger;

    // State
    private $state = IAuthenticationState::NOT_CONNECTED;

    //
    //
    //
    public function addUser( $login, $password )
    {
        $user = new BasicUser( $login, $password );
        $this->users[] = $user;
    }

    //
    //
    //
    public function getUserLogin()
    {
        $user = Session::getVariable('AUTHENTICATION_LOGIN');
        if ((isset($user)) && (!empty($user)))
        {
            return $user;
        }
        return "no connection";
    }

    //
    //
    //
    public function getUserIp()
    {
        $ip = Session::getVariable('AUTHENTICATION_IP');
        if ((isset($ip)) && (!empty($ip)))
        {
            return $ip;
        }
        return "no connection";
    }


    //
    // Return result in seconds
    //
    public function getSessionDuration()
    {
        $start = Session::getVariable('AUTHENTICATION_START');
        if ((isset($start)) && (!empty($start)))
        {
            $duration = time() - $start;
            return $duration;
        }
        return 0;
    }


    //
    // Set a logger to handle authentication notifications
    //
    public function setLogger(IAuthenticationLogger $logger)
    {
        $this->logger = $logger;
    }


    //
    // Returns true or false
    //
    public function isUserConnected()
    {
        return $this->isUserConnectedViaLoginPassword();
    }


    //
    // Return IAuthenticationState
    //
    public function getState()
    {
        return $this->state;
    }


    //
    // Return true or false
    //
    private function isUserConnectedViaLoginPassword()
    {
        $this->state = IAuthenticationState::NOT_CONNECTED;

        // Handle user action
        if( isset($_POST['login'])&&isset($_POST['password']) )
        {
            $login = $_POST['login'];
            $password = $_POST['password'];
            
            foreach ($this->users as $user)
            {
                if( $user->matchLoginPassword( $login, $password ) )
                {
                    Session::setVariable('AUTHENTICATION_LOGIN', $login);
                    Session::setVariable('AUTHENTICATION_IP', $_SERVER['REMOTE_ADDR']);
                    Session::setVariable('AUTHENTICATION_START', time());
                    $this->notifyLogInSuccess();
                }
            }

            $user = Session::getVariable('AUTHENTICATION_LOGIN');
            if ((!isset($user)) or (empty($user)))
            {
                $this->state = IAuthenticationState::WRONG_CREDENTIALS;
                $this->notifyLogInFailure();
            }
        }

        if( isset($_POST['disconnect']) ) // what if not connected ?
        {
            Session::removeAllVariables();
            $this->state = IAuthenticationState::DISCONNECTED_USER;
            $this->notifyLogOut();
        }
        
        // Check current state
        $user = Session::getVariable('AUTHENTICATION_LOGIN');
        if ((isset($user)) && (!empty($user)))
        {
            if( $this->isIPCorrect() )
            {
                $this->state = IAuthenticationState::CONNECTED;
                return true;
            }

            // IP change during session ... what to do ?
            Session::removeAllVariables();
            $this->state = IAuthenticationState::DISCONNECTED_IP;
            $this->notifyLogOut();
        }
        return false;
    }


    //
    // Compare current IP with IP used at login 
    //
    private function isIpCorrect()
    {
        $currentIP = $_SERVER['REMOTE_ADDR'];
        $storedIP = Session::getVariable('AUTHENTICATION_IP');
        if($currentIP == $storedIP)
        {
            return true;
        }
        return false;
    }
    
    //
    // Notifications to logger
    //
    private function notifyLogInSuccess()
    {
        if( isset($this->logger) )
        {
            $this->logger->logInSuccess();
        }
    }

    private function notifyLogInFailure()
    {
        if( isset($this->logger) )
        {
            $this->logger->logInFailure();
        }
    }

    private function notifyLogOut()
    {
        if( isset($this->logger) )
        {
            $this->logger->logOut();
        }
    }
}

?>