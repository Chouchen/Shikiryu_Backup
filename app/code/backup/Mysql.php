<?php

namespace Shikiryu\Backup\Backup;

class Mysql extends BackupAbstract
{

    /**
     * @var $_pdo PDO
     */
    private $_pdo;
    private $_tables;

    /**
     * @param $host
     * @param $login
     * @param $pwd
     * @param $db
     */
    public function __construct($host, $login, $pwd, $db) {
        parent::__construct();
        $this->_tables = array();
        $this->_pdo = new PDO('mysql:host='.$host.';dbname='.$db, $login, $pwd);
    }

    /**
     * set the list of table to backup
     *
     * @param array $tables
     *
     * @return $this
     */
    public function setTables(array $tables)
    {
        if(is_array($tables) && !empty($tables)) {
            $this->_tables = $tables;
        }
        return $this;
    }
    
//    function withSQL($sql) {
//        $statement = $this->_pdo->query($sql);
//    }

    /**
     * @param array $tables
     * @return $this|string
     */
    public function fromTables($tables = array())
    {
        if(!empty($tables)) {
            $this->_tables = $tables;
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
        $this->_tables = array();
        foreach($this->_pdo->query('SHOW TABLES') as $table) {
            $this->_tables[] = $table;
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
        foreach ($this->_tables as $table) {
            if(is_array($table)) {
                $table = $table[0];
            }
            $result = $this->_pdo->prepare('SELECT * FROM ' . $table);
            $result->execute();
            $num_fields = $result->columnCount();
            $return .= 'DROP TABLE IF EXISTS ' . $table . ';';
            $result2 = $this->_pdo->prepare('SHOW CREATE TABLE ' . $table);
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

}

?>
