<?php


namespace SilexApi;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use mysql_xdevapi\Exception;

class UserDao
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
    $sql = "SELECT user.id, user.username, user.team_id FROM user";
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
        $sql = "SELECT user.id, user.username, user.team_id FROM user WHERE user.id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if($row){
            return $this->buildDomainObject($row);
        } else {
            throw new \Exception("pas de user avec cette id : " .$id);
        }
    }

    public function connexion($username, $password)
    {
        $sql = "SELECT id, username, team_id FROM user WHERE username=? AND password=?";
        $row = $this->getDb()->fetchAssoc($sql, array($username, $password));

        if($row){
            return $this->buildDomainObject($row);
        } else {
            throw new \Exception("nom de compte ou mot de passe incorrect");
        }
    }

    public function save(User $user)
    {
        $userData = array(
            'username' => $user->getUsername(),
            'password' => $user->getPassword(),
            'team_id' => $user->getTeam()
        );

        // TODO CHECK
        if ($user->getId()) {
            $this->getDb()->update('user', $userData, array('id' => $user->getId()));
        } else {
            $this->getDb()->insert('user', $userData);
            $id = $this->getDb()->lastInsertId();
            $user->setId($id);
        }
    }

    public function delete($id)
    {
        try {
            $this->getDb()->delete('user', array('id' => $id));
        } catch (InvalidArgumentException $e) {
            echo $e;
        }
    }

    protected function buildDomainObject($row)
    {
        $user = new User();
        $teamDao = new TeamDao($this->db);
        $user->setId($row['id']);
        $user->setUsername($row['username']);
        //$user->setTeam($row['libelle']);
        $user->setTeam($teamDao->findById($row['team_id']));
        return $user;
    }
}
