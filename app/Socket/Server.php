<?php
namespace App\Socket;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Device;
use App\Services\UserChatService;

class Server implements MessageComponentInterface {
    protected $clients;
    protected $sockets = [];
    protected $service;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->service = new UserChatService;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $res = json_decode($msg, true);
        try {
          switch ($res['type']) {
            case 'init':
              echo "Registered new connection for {$res['payload']}\n";
              $this->sockets[$res['payload']] = $from;
              break;
            case 'message':
              $receiver = $res['payload']['receiver_id'];
              $sender = $res['payload']['sender_id'];
              if (isset($this->sockets[$receiver])) {
                $this->sockets[$receiver]->send(json_encode([
                  'type' => 'newMessage',
                  'data' => $res['payload']
                ]));
              }
              break;
            case 'unread':
              $receiver = $res['payload']['receiver_id'];
              $ids = $res['payload']['ids'];
              if (isset($this->sockets[$receiver])) {
                $this->sockets[$receiver]->send(json_encode([
                  'type' => 'unread',
                  'data' => $ids
                ]));
              }
              break;
            case 'disconnect':
              $userId = $res['payload'];
              echo "Disconnected {$userId}\n";
              if (isset($this->sockets[$userid])) {
                unset($this->sockets[$userid]);
              }
              break;
          }
        } catch (\Exception $e) {
          // @todo
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        foreach ($this->sockets as $userId => $socket) {
          if ($socket == $conn) {
            echo "Disconnected {$userId}\n";
            unset($socket);
          }
        }

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
