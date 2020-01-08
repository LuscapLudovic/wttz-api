<?php


namespace SilexApi;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\InvalidArgumentException;

class MessageDao
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
        $sql = "SELECT * FROM MESSAGE";
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
        $sql = "SELECT id, text, posted_at, team_id, user_id FROM MESSAGE WHERE id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if($row){
            return $this->buildDomainObject($row);
        } else {
            throw new \Exception("pas de message avec cette id : " .$id);
        }
    }

    public function save(Message $message){
        $messageData = array(
            'text' => $message->getText(),
            'posted_at' => date('Y-m-d H:i:s'),
            'team_id' => $message->getTeam(),
            'user_id' => $message->getUser()
        );

        if($message->getId())
        {
            $this->getDb()->update('message', $messageData, array('id' => $message->getId()));
        } else {
            $this->getDb()->insert('message', $messageData);
            $id = $this->getDb()->lastInsertId();
            $message->setId($id);
        }
    }

    public function delete($id)
    {
        try {
            $this->getDb()->delete('message', array('id' => $id));
        } catch (InvalidArgumentException $e) {
            echo $e;
        }
    }

    protected function buildDomainObject($row)
    {
        $message = new Message();
        $teamDao = new TeamDao($this->db);
        $userDao = new UserDao($this->db);
        $message->setId($row['id']);
        $message->setText($row['text']);
        $message->setPostedAt($row['posted_at']);
        $message->setTeam($teamDao->findById($row['team_id']));
        $message->setUser($userDao->findById($row['user_id']));

        return $message;
    }
}
