<?php
namespace Database\Repositories;

use Database\PDORepository;
use Models\IpBan;

class IpBanRepository extends PDORepository
{
    public function save(IpBan $ipBan)
    {
        $statement = $this->connection->prepare(
            "INSERT INTO `ip_bans` (`banned_by_id`, `expires`, `ip`, `reason`, `created_at`) VALUES (?, ?, ?, ?, NOW())"
        );
        $result = $statement->execute([
            $ipBan->banned_by_id,
            $ipBan->expires,
            $ipBan->ip,
            $ipBan->reason
        ]);

        $this->insertedIds[] = $this->connection->lastInsertId();

        return $result;
    }
}
