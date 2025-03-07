<?php

class MapadoNotification
{
    /* CONSTANTS
     *************************************************************************/
    const SUCCESS = 'updated';
    const ERROR = 'error';
    /* PUSH METHODS
     *************************************************************************/
    public static function success($message)
    {
        static::push($message, static::SUCCESS);
    }
    public static function error($message)
    {
        static::push($message, static::ERROR);
    }
    public static function push($message, $type = 'info')
    {
        if ($message) {
            $info = ['message' => $message, 'type' => $type];
            if (!isset($_SESSION['MapadoNotification'])) {
                $_SESSION['MapadoNotification'] = [];
            }
            $_SESSION['MapadoNotification'][] = $info;
        }
    }
    /* PULL METHODS
     *************************************************************************/
    public static function pull()
    {
        $messages = [];
        if (isset($_SESSION['MapadoNotification'])) {
            $messages = $_SESSION['MapadoNotification'];
            $_SESSION['MapadoNotification'] = [];
        }
        return $messages;
    }
}