<?php

interface DBPropertyType
{
    const ShortText   = 0;
    const MediumText  = 1;
    const LongText    = 2;

    const IntNumber   = 3;
    const FloatNumber = 4;

    const DateTime    = 5; // date(DATE_W3C)
    
    const Url         = 6;
    const ImgUrl      = 7;
    const HostedImg   = 8; // filename only, url should be rebuilt

    const Enum        = 9;

    const Tags        = 10;

    // TODO : Bool
}

?>