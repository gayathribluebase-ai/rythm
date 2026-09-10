<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../connect.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatServer implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        echo "New connection: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        global $con;

        $data = json_decode($msg, true);

        if (!$data) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Identify user
        |--------------------------------------------------------------------------
        */

        if ($data['type'] === 'login') {

            $userId = intval($data['user_id']);

            $from->user_id = $userId;

            echo "User {$userId} connected\n";

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Chat message
        |--------------------------------------------------------------------------
        */

        if ($data['type'] === 'message') {

            $senderId = $from->user_id ?? 0;
            $receiverId = intval($data['receiver_id'] ?? 0);
            $message = trim($data['message'] ?? '');

            if ($senderId == 0 || $receiverId == 0 || $message == '') {
                return;
            }


/*
| Save message to database
*/

$stmt = $con->prepare("
    INSERT INTO messages
    (sender_id, receiver_id, message, timestamp, is_read)
    VALUES (?, ?, ?, NOW(), 0)
");

try {

    $stmt->execute([
        $senderId,
        $receiverId,
        $message
    ]);

    echo "Message saved: {$senderId} -> {$receiverId}: {$message}\n";

} catch (\PDOException $e) {

    echo "DATABASE ERROR: " . $e->getMessage() . "\n";
    return;
}

            /*
            | Send message to sender + receiver
            */

            $response = json_encode([
                'type' => 'message',
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'message' => htmlspecialchars(
                    $message,
                    ENT_QUOTES,
                    'UTF-8'
                ),
                'timestamp' => date('h:i A')
            ]);


            foreach ($this->clients as $client) {

                $clientUserId = $client->user_id ?? 0;

                if (
                    $clientUserId == $senderId ||
                    $clientUserId == $receiverId
                ) {
                    $client->send($response);
                }
            }
        }
    }


    public function onClose(ConnectionInterface $conn)
    {
        $userId = $conn->user_id ?? 0;

        $this->clients->detach($conn);

        echo "User {$userId} disconnected\n";
    }


    public function onError(
        ConnectionInterface $conn,
        \Exception $e
    ) {
        echo "Error: {$e->getMessage()}\n";

        $conn->close();
    }
}