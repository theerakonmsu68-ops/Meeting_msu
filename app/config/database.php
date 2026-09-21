<?php

class Database
{
    private string $host;
    private string $db_name;
    private string $username;
    private string $password;
    private string $port;
    private bool $ssl;
    private string $sslCa;

    public ?PDO $conn = null;


    public function __construct()
    {
        // Local XAMPP fallback / Render Environment
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->db_name = getenv('DB_NAME') ?: 'meeting_msu';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
        $this->port = getenv('DB_PORT') ?: '3306';


        // เปิด SSL เมื่อ DB_SSL=true
        $this->ssl = filter_var(
            getenv('DB_SSL') ?: 'false',
            FILTER_VALIDATE_BOOLEAN
        );


        // TiDB Cloud / Render Docker
        $this->sslCa = getenv('DB_SSL_CA')
            ?: '/etc/ssl/certs/ca-certificates.crt';
    }


    public function connect(): ?PDO
    {
        $this->conn = null;

        try {

            $dsn =
                "mysql:host={$this->host};" .
                "port={$this->port};" .
                "dbname={$this->db_name};" .
                "charset=utf8mb4";


            $options = [

                PDO::ATTR_ERRMODE =>
                    PDO::ERRMODE_EXCEPTION,

                PDO::ATTR_DEFAULT_FETCH_MODE =>
                    PDO::FETCH_ASSOC,

                PDO::ATTR_EMULATE_PREPARES =>
                    false,
            ];


            // TiDB Cloud TLS
            if ($this->ssl) {

                $options[
                    PDO::MYSQL_ATTR_SSL_CA
                ] = $this->sslCa;


                if (
                    defined(
                        'PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT'
                    )
                ) {

                    $options[
                        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT
                    ] = true;

                }
            }


            $this->conn = new PDO(
                $dsn,
                $this->username,
                $this->password,
                $options
            );


        } catch (PDOException $e) {

            error_log(
                'Database Connection Error: '
                . $e->getMessage()
            );


            die(
                'ระบบขัดข้องชั่วคราว กรุณาลองใหม่อีกครั้งในภายหลัง'
            );
        }


        return $this->conn;
    }
}