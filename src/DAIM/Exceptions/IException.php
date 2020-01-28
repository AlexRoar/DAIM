<?php
/**
 * Copyright (c) 2020.
 * Designed and developed by Aleksandr Dremov
 * Use according to the license guidelines.
 * Contact me: dremov.me@gmail.com
 */

namespace DAIM\Exceptions;


interface IException
{
    /* Protected methods inherited from Exception class */
    public function __construct($message = null, $code = 0);                 // Exception message

    public function getMessage();                    // User-defined Exception code

    public function getCode();                    // Source filename

    public function getFile();                    // Source line

    public function getLine();                   // An array of the backtrace()

    public function getTrace();           // Formatted string of trace

    /* Overrideable methods inherited from Exception class */

    public function getTraceAsString();                 // formatted string for display

    public function __toString();
}