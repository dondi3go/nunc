<?php
require_once(__DIR__.'/../Config.php');
require_once(__DIR__.'/../main/IDisplayable.php');
require_once(__DIR__.'/Authentication.php');

//
// A form for User Connection
//
class ConnectUI implements IDisplayable
{
    private $authentication;
    private $loginLabel  = "<span class='glyphicon glyphicon-user' aria-hidden='true'></span>";
    private $passwdLabel = "<span class='glyphicon glyphicon-lock' aria-hidden='true'></span>";
    private $buttonLabel = "log in";

    public function ConnectUI($authentication)
    {
        $this->authentication = $authentication;
    }

    public function setUILabels($loginLabel, $passwdLabel, $buttonLabel)
    {
        $this->loginLabel = $loginLabel;
        $this->passwdLabel = $passwdLabel;
        $this->buttonLabel = $buttonLabel;
    }

    private function addLibraries(IPage $page)
    {
        $page->addCSS(Config::bootstrap_css);
    }

    public function display(IPage $page)
    {
        $this->addLibraries($page);

        $this->displayState($page);
        $this->displayLoginPasswordUI($page);
    }

    public function displayState(IPage $page)
    {
        $state = $this->authentication->getState();
        switch($state)
        {
             case IAuthenticationState::WRONG_CREDENTIALS:
                $this->displayAlertMessage($page, "wrong credentials");
                break;

            case IAuthenticationState::DISCONNECTED_IP:
                $this->displayAlertMessage($page, "ip switch detected");
                break;

            case IAuthenticationState::DISCONNECTED_TIME:
                $this->displayAlertMessage($page, "session time exceeded limit");
                break;
        }
    }

    private function displayAlertMessage(IPage $page, $message)
    {
        $page->addInnerCSSContent( ".auth-alert {margin-top:20px;}\n" );

        $str .= "<div class='alert alert-danger auth-alert text-center'>".$message."</div>\n";

        $page->addBodyContent($str);
    }

    public function displayLoginPasswordUI(IPage $page)
    {
        $page->addInnerCSSContent( ".auth-input {padding-top:10px;}\n" );
        $page->addInnerCSSContent( ".auth-button {margin-top:10px;}\n" );

        $str  = "<form method='post' enctype='multipart/form-data'>\n";

        if( $this->loginLabel != "" )
        {
            $str .= "    <div class='input-group auth-input'>\n";
            $str .= "        <div class='input-group-addon'>".$this->loginLabel."</div>\n";
            $str .= "        <input class='form-control' name='login' type='text' required autofocus/>\n";
            $str .= "    </div>\n";
        }
        else
        {
            $str .= "    <div class='auth-input'>\n";
            $str .= "        <input class='form-control text-center' name='login' type='text' required autofocus/>\n";
            $str .= "    </div>\n";
        }

        if( $this->passwdLabel != "" )
        {
            $str .= "    <div class='input-group auth-input'>\n";
            $str .= "        <div class='input-group-addon'>".$this->passwdLabel."</div>\n";
            $str .= "        <input class='form-control' name='password' type='password' required/>\n";
            $str .= "    </div>\n";
        }
        else
        {
            $str .= "    <div class='auth-input'>\n";
            $str .= "        <input class='form-control text-center' name='password' type='password' required/>\n";
            $str .= "    </div>\n";
        }

        $str .= "    <button class='btn btn-block btn-default auth-button' name='authenticate' type='submit' value='Connect'>\n";
        $str .= "    ".$this->buttonLabel."\n";
        $str .= "    </button>\n";

        $str .= "</form>\n";

        $page->addBodyContent($str);
    }
}



//
// Display Connection info
//
class ConnectionUI implements IDisplayable
{
    private $authentication;

    function ConnectionUI($authentication)
    {
        $this->authentication = $authentication;
    }

    function addLibraries(IPage $page)
    {
        $page->addCSS(Config::bootstrap_css);
    }

    public function display(IPage $page)
    {
        $this->addLibraries($page);

        // User login
        $userlogin = $this->authentication->getUserLogin();
        $str = "<h3 style='text-align:center;'><span class='glyphicon glyphicon-user' aria-hidden='true'></span> ".$userlogin."</h3>";

        // User ip
        $ip = $this->authentication->getUserIp();
        $str.= "<p class='text-muted' style='text-align:center;'>".$ip."</p>";

        // Session duration
        $duration = (int) ($this->authentication->getSessionDuration()/60); // sec -> min
        $str.= "<p class='text-muted' style='text-align:center;'>".$duration." minutes</p>";

        $page->addBodyContent($str);
    }
}



//
// A form for User Disconnection
//
class DisconnectUI implements IDisplayable
{
    private $authentication;
    private $buttonLabel = "log out";

    function DisconnectUI($authentication)
    {
        $this->authentication = $authentication;
    }

    function addLibraries(IPage $page)
    {
        $page->addCSS(Config::bootstrap_css);
    }

    public function display(IPage $page)
    {
        $this->addLibraries($page);

        // Log Out button
        $str = "<form method='post' enctype='multipart/form-data' style='text-align:center;'>\n";
        $str.= "    <button class='btn btn-default' name='disconnect' type='submit' value='Disconnect'/>\n";
        $str.= "    ".$this->buttonLabel."\n";
        $str.= "    </button>\n";
        $str.= "</form>\n";

        $page->addBodyContent($str);
    }
}

?>