<?php
class Event {
    public function __construct(
        private string $event_id,
        private string $club_id,
        private string $event_name,
        private DateTime $event_date,
        private ?DateTime $start_time,
        private ?DateTime $end_time,
        private int $slot,
        private string $location,
        private string $status,
        private string $register_status = 'pending'
    ) {}

    public function getEventId(): string { return $this->event_id; }
    public function getClubId(): string { return $this->club_id; }
    public function getEventName(): string { return $this->event_name; }
    public function getEventDate(): DateTime { return $this->event_date; }
    public function getStartTime(): ?DateTime { return $this->start_time; }
    public function getEndTime(): ?DateTime { return $this->end_time; }
    public function getSlot(): int { return $this->slot; }
    public function getLocation(): string { return $this->location; }
    public function getStatus(): string { return $this->status; }
    public function getRegisterStatus(): string { return $this->register_status; }
}