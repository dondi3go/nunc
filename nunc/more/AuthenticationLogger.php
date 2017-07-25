<?php

class AuthenticationMailLogger implements IAuthenticationLogger
{
    private $strEmailAddress;

    private $bNotifyLogInSuccess = true;
    private $bNotifyLogInFailure = true;
    private $bNotifyLogOut = true;

    public function AuthenticationMailLogger(
        $strEmailAddress,
        $bNotifyLogInSuccess = true,
        $bNotifyLogInFailure = true,
        $bNotifyLogOut = true)
    {
        $this->strEmailAddress = $strEmailAddress;
        $this->bNotifyLogInSuccess = $bNotifyLogInSuccess;
        $this->bNotifyLogInFailure = $bNotifyLogInFailure;
        $this->bNotifyLogOut = $bNotifyLogOut;
    }

    public function logInSuccess()
    {
        if($this->bNotifyLogInSuccess)
        {
            $strObject = $this->getDomainName()." : log in success";
            $strMessage = $this->getCurrentDateTime();
            $this->sendMail($strObject, $strMessage);
        }
    }

    public function logInFailure()
    {
        if($this->bNotifyLogInFailure)
        {
            $strObject = $this->getDomainName()." : log in failure";
            $strMessage = $this->getCurrentDateTime();
            $this->sendMail($strObject, $strMessage);
        }
    }

    public function logOut()
    {
        if($this->bNotifyLogOut)
        {
            $strObject = $this->getDomainName()." : log out";
            $strMessage = $this->getCurrentDateTime();
            $this->sendMail($strObject, $strMessage);
        }
    }

    private function getDomainName()
    {
        return $_SERVER['SERVER_NAME'];
    }

    private function getCurrentDateTime()
    {
        return date('Y/m/d H:i:s', time());
    }

    private function sendMail($strObject, $strMessage)
    {
        mail($this->strEmailAddress, $strObject, $strMessage);
    }
}

?>