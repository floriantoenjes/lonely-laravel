<?php


namespace App\Helpers;


class UserNotification
{
    private $userId = -1;

    private $type = '';

    private $message = '';

    private $senderId = -1;

    private $activityId = -1;

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): UserNotification
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): UserNotification
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): UserNotification
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return int
     */
    public function getSenderId(): int
    {
        return $this->senderId;
    }

    /**
     * @param int $senderId
     */
    public function setSenderId(int $senderId): UserNotification
    {
        $this->senderId = $senderId;
        return $this;
    }

    /**
     * @return int
     */
    public function getActivityId(): int
    {
        return $this->activityId;
    }

    /**
     * @param int $activityId
     */
    public function setActivityId(int $activityId): UserNotification
    {
        $this->activityId = $activityId;
        return $this;
    }




}
