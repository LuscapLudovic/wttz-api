<?php


namespace SilexApi;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\InvalidArgumentException;

class TeamDao
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
        $sql = "SELECT * FROM TEAM";
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
        $sql = "SELECT id, libelle FROM TEAM WHERE id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));
        if($row){
            return $this->buildDomainObject($row);
        } else {
            throw new \Exception("pas de team avec cette id : " .$id);
        }
    }

    public function save(Team $team)
    {
        $teamData = array(
            'libelle' => $team->getLibelle(),
        );

        // TODO CHECK
        if ($team->getId()) {
            $this->getDb()->update('team', $teamData, array('id' => $team->getId()));
        } else {
            $this->getDb()->insert('team', $teamData);
            $id = $this->getDb()->lastInsertId();
            $team->setId($id);
        }
    }

    public function delete($id)
    {
        try {
            $this->getDb()->delete('team', array('id' => $id));
        } catch (InvalidArgumentException $e) {
            echo $e;
        }
    }

    protected function buildDomainObject($row)
    {
        $team = new Team();
        $team->setId($row['id']);
        $team->setLibelle($row['libelle']);
        return $team;
    }
}
