<?php

namespace Shikiryu\Backup\Backup;

class Mysql extends BackupAbstract
{

    /**
    * @var $pdo \PDO
    */
    private $pdo;

    /*
    * from config
    */
    protected $tables;
    protected $host;
    protected $database;
    protected $login;
    protected $pwd;

    /**
    * @param array $config
    */
    public function __construct(array $config = array())
    {
        parent::__construct($config);
    }

    /**
    * @param array $tables
    * @return $this|string
    */
    private function fromTables($tables = array())
    {
        if (!empty($tables)) {
            $this->tables = $tables;
        }
        $this->streams_to_backup[sprintf('db-%s.sql', $this->database)] = $this->getFromTables();
        return $this;
    }

    /**
    * set the list of table to backup to all tables
    *
    * @return $this
    */
    private function everything()
    {
        $this->tables = array();
        foreach ($this->pdo->query('SHOW TABLES') as $table) {
            $this->tables[] = $table;
        }
        $this->streams_to_backup[sprintf('db-%s.sql', $this->database)] = $this->getFromTables();
        return $this;
    }

    /**
    * @return string
    */
    private function getFromTables()
    {
        $return = "";
        foreach ($this->tables as $table) {
            if (is_array($table)) {
                $table = $table[0];
            }
            $result = $this->pdo->prepare('SELECT * FROM ' . $table);
            $result->execute();
            $num_fields = $result->columnCount();
            $return .= 'DROP TABLE IF EXISTS ' . $table . ';';
            $result2 = $this->pdo->prepare('SHOW CREATE TABLE ' . $table);
            $result2->execute();
            $row2 = $result2->fetch();
            $return.= "\n\n" . $row2[1] . ";\n\n";
            foreach ($result as $row) {
                $return.= 'INSERT INTO ' . $table . ' VALUES(';
                for ($j = 0; $j < $num_fields; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    //                        $row[$j] = preg_replace("\n", "\\n", $row[$j]);
                    if (isset($row[$j])) {
                        $return.= '"' . $row[$j] . '"';
                    } else {
                        $return.= '""';
                    }
                    if ($j < ($num_fields - 1)) {
                        $return.= ',';
                    }
                }
                $return.= ");\n";
            }
            $return.="\n\n\n";
        }
        return $return;
    }

    /**
    * @return bool
    */
    public function isValid()
    {
        return !empty($this->streams_to_backup);
    }

    /**
    * Function that can be used to initialize the backup
    */
    protected function preBuild()
    {
        $this->pdo = new \PDO('mysql:host='.$this->host.';dbname='.$this->database, $this->login, $this->pwd);
    }

    /**
    * Function that can be used after the backup
    */
    protected function postBuild()
    {
        // TODO: Implement postBuild() method.
    }

    /**
    * Mandatory function doing the backup
    */
    protected function build()
    {
        empty($this->tables) || $this->tables == '*' ? $this->everything() : $this->fromTables($this->tables);
    }
}
