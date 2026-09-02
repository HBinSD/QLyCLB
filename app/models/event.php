<?php 
    class Event {
        public function __construct(
            private string $event_id,
            private string $club_id,
            private string $event_name,
            private DateTime $event_date,
            private int $slot,
            private string $location,
            private string $status
        ) {}

        // --- GETTERS ---

        public function getEventId(): string {
            return $this->event_id;
        }

        public function getClubId(): string {
            return $this->club_id;
        }

        public function getEventName(): string {
            return $this->event_name;
        }

        public function getEventDate(): DateTime {
            return $this->event_date;
        }

        public function getSlot(): int {
            return $this->slot;
        }

        public function getLocation(): string {
            return $this->location;
        }

        public function getStatus(): string {
            return $this->status;
        }

        // --- SETTERS ---

        public function setEventId(string $event_id): void {
            $this->event_id = $event_id;
        }

        public function setClubId(string $club_id): void {
            $this->club_id = $club_id;
        }

        public function setEventName(string $event_name): void {
            $this->event_name = $event_name;
        }

        public function setEventDate(DateTime $event_date): void {
            $this->event_date = $event_date;
        }

        public function setSlot(int $slot): void {
            $this->slot = $slot;
        }

        public function setLocation(string $location): void {
            $this->location = $location;
        }

        public function setStatus(string $status): void {
            $this->status = $status;
        }
    }
?>