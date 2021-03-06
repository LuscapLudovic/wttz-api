<?php


namespace SilexApi;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use mysql_xdevapi\Exception;

class CryptageDao
{
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    protected function getDb()
    {
        return $this->db;
    }

    public function findAll()
    {
        $sql = "SELECT * FROM CRYPTAGE";
        $result = $this->getDb()->fetchAll($sql);
        $entities = array();
        foreach ($result as $row){
            $id = $row['id'];
            $entities[$id] = $this->buildDomainObject($row);
        }
        return $entities;
    }

    public function findById($id)
    {
        $sql = "SELECT id, text, team_id FROM CRYPTAGE WHERE id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if($row){
            return $this->buildDomainObject($row);
        } else {
            throw new \Exception("pas de cryptage avec cette id : " .$id);
        }
    }

    public function cryptage($id)
    {
        $sql = "SELECT id, text, team_id FROM CRYPTAGE WHERE team_id = ?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if($row){
            return $this->buildDomainObject($row);
        } else {
            throw new \Exception("pas de cryptage pour cette team : ".$id);
        }
    }

    private function buildDomainObject($row)
    {
        $cryptage = new Cryptage();
        $teamDao = new TeamDao($this->db);

        $cryptage->setId($row["id"]);
        $cryptage->setText($row["text"]);
        $cryptage->setTeam($teamDao->findById($row["team_id"]));

        return $cryptage;
    }
}
