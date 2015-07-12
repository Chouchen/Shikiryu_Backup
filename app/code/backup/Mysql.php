<?php

namespace Shikiryu\Backup\Backup;

class Mysql extends BackupAbstract
{

    /**
     * @var $pdo \PDO
     */
    private $pdo;
    private $tables;
    private $host;
    private $db;
    private $login;
    private $pwd;

    /**
     * @param array $config
     */
    public function __construct(array $config = array()) {
        parent::__construct($config);
        empty($this->tables) || $this->tables == '*' ? $this->everything() : $this->fromTables($this->tables);
        $this->pdo    = new \PDO('mysql:host='.$this->host.';dbname='.$this->db, $this->login, $this->pwd);
    }

    /**
     * @param array $tables
     * @return $this|string
     */
    public function fromTables($tables = array())
    {
        if(!empty($tables)) {
            $this->tables = $tables;
        }
        $this->_streamsToBackup[] = $this->getFromTables();
        return $this;
    }

    /**
     * set the list of table to backup to all tables
     *
     * @return $this
     */
    public function everything() {
        $this->tables = array();
        foreach($this->pdo->query('SHOW TABLES') as $table) {
            $this->tables[] = $table;
        }
        $this->_streamsToBackup[] = $this->getFromTables();
        return $this;
    }

    /**
     * @return string
     */
    private function getFromTables()
    {
        $return = "";
        foreach ($this->tables as $table) {
            if(is_array($table)) {
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
            foreach($result as $i=>$row){
                    $return.= 'INSERT INTO ' . $table . ' VALUES(';
                    for ($j = 0; $j < $num_fields; $j++) {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = preg_replace("\n", "\\n", $row[$j]);
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
        // TODO: Implement isValid() method.
    }
}

?>
