<span @class(['flap', 'flap-go' => $job->daysLeft() === null, 'flap-stop' => $job->isClosingSoon()]) data-flap>{{ $job->availabilityLabel() }}</span>
