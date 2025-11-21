<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = 'itso.itdesk.ph@gmail.com';
    public string $fromName   = 'ITSO Equipment System';

    public string $protocol = 'smtp';
    public string $SMTPHost = 'smtp.gmail.com';
    public string $SMTPUser = 'itso.itdesk.ph@gmail.com';
    public string $SMTPPass = 'ivkh czre etqp zbop';
    public int    $SMTPPort = 587;
    public string $SMTPCrypto = 'tls';

    public string $mailType = 'html';
    public string $charset = 'UTF-8';

    public string $newline = "\r\n";
    public string $CRLF = "\r\n";
}
