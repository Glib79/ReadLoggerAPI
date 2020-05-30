<?php
declare(strict_types=1);

namespace App\DataTransformer;

use App\DTO\BaseDto;
use App\DTO\LogDto;
use DateTime;
use Ramsey\Uuid\Uuid;

class LogDataTransformer extends BaseDataTransformer implements DataTransformerInterface
{
    /**
     * Creates new LogDto
     * @param Uuid $userId
     * @param string $action
     * @param string $table
     * @param object $newDto object after update, set to null for delete action
     * @param array $old normalized old object
     * @param array $removeFields fields to remove from log
     * @return LogDto
     */
    public function prepareLog(
        Uuid $userId,
        string $action,
        string $table,
        object $newDto = null, //it's null for delete action
        array $old = null,
        array $removeFields = []
    ): LogDto
    {
        $dto = new LogDto($this->serializer, $this->validator);
        $dto->userId = $userId;
        $dto->action = $action;
        $dto->table = $table;
        $dto->recordId = $newDto ? $newDto->id : Uuid::fromString($old['id']);
        $dto->value = $this->prepareValue(
            $newDto ? $newDto->normalize([BaseDto::GROUP_LOG]) : $old, 
            $newDto ? $old : null,
            $removeFields
        );
        
        return $dto;
    }
    
    /**
     * Transform array from database to array ready for output
     * @param array $log
     * @param array $groups
     * @return array
     */
    public function transformOutput(array $log, array $groups): array
    {
        $dto = new LogDto($this->serializer, $this->validator);
        $dto->id = Uuid::fromString($log['id']);
        $dto->userId = $log['user_id'] ? Uuid::fromString($log['user_id']) : null;
        $dto->happenedAt = !empty($log['happened_at']) ? new DateTime($log['happened_at']) : null;
        $dto->action = $log['action'] ?? null;
        $dto->table = $log['table'] ?? null;
        $dto->recordId = !empty($log['record_id']) ? Uuid::fromString($log['record_id']) : null;
        $dto->value = !empty($log['value']) ? json_decode($log['value'], true) : null;

        return $dto->normalize($groups);
    }
    
    /**
     * Prepare value for log
     * @param array $newDto
     * @param array|null $oldDto
     * @param array $removeFields
     * @return array
     */
    private function prepareValue(array $newDto, ?array $oldDto, array $removeFields): array
    {
        if ($oldDto === null) {
            return $newDto;
        }
        
        foreach ($newDto as $key => $val) {
            if (in_array($key, $removeFields)) {
                unset($newDto[$key]);
                continue;
            }
            
            if (is_array($val)) {
                if (!empty($val['id']) && (string) $val['id'] === (string) $oldDto[$key]['id']) {
                    unset($newDto[$key]);
                    continue;
                }
                
                foreach($val as $key1 => $val1) {
                    if (!empty($val1['id']) && (string) $val1['id'] === (string) $oldDto[$key][$key1]['id']) {
                        unset($newDto[$key][$key1]);
                    }
                }
                
                if (!$newDto[$key]) {
                    unset($newDto[$key]);
                }
                continue;
            }
            
            if ((string) $val === (string) $oldDto[$key]) {
                unset($newDto[$key]);
            }
        }
        
        return $newDto;
    }
}
