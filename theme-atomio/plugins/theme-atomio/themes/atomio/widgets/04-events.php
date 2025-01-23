<?php
defined('MYAAC') or die('Direct access not allowed!');

// setup duration (3600 = 1 hour)
$duration = [
    'event1' => 7200,
    'event2' => 3600,
    'event3' => 1800,
];


$serverStartTime = strtotime('today midnight'); // Poczatek dnia w sekundach UNIX


$events = [];
foreach ($duration as $key => $time) {
    $events[$key] = $serverStartTime + $time * ceil((time() - $serverStartTime) / $time);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Events Countdown</title>
</head>
<body>
<div class="well">
    <div class="header">
        Events
    </div>
    <div class="body">
        <table class="table-100">
            <tr>
                <td>Event 1</td>
                <td><i class="fas fa-clock"></i> <span class="countdown" data-endtime="<?= $events['event1']; ?>" data-duration="<?= $duration['event1']; ?>"></span></td>
            </tr>
            <tr>
                <td>Event 2</td>
                <td><i class="fas fa-clock"></i> <span class="countdown" data-endtime="<?= $events['event2']; ?>" data-duration="<?= $duration['event2']; ?>"></span></td>
            </tr>
            <tr>
                <td>Event 3</td>
                <td><i class="fas fa-clock"></i> <span class="countdown" data-endtime="<?= $events['event3']; ?>" data-duration="<?= $duration['event3']; ?>"></span></td>
            </tr>
        </table>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Funkcja formatująca czas do HH:MM:SS
    function formatTime(seconds) {
        const hrs = Math.floor(seconds / 3600);
        const mins = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        return `${hrs}h ${mins}m ${secs}s`;
    }


    function startCountdown(element) {
        const duration = parseInt(element.getAttribute('data-duration'), 10); // Czas trwania wydarzenia
        let endTime = parseInt(element.getAttribute('data-endtime'), 10); // Pobierz czas końcowy (UNIX timestamp)

        function updateClock() {
            const currentTime = Math.floor(Date.now() / 1000); // Aktualny czas w sekundach (UNIX timestamp)
            let timeLeft = endTime - currentTime; // Oblicz pozostały czas

            if (timeLeft <= 0) {
                // Gdy czas się skończy, ustaw nowy czas końcowy
                endTime += duration;
                timeLeft = endTime - currentTime; // Resetuj odliczanie
            }

            element.innerText = formatTime(timeLeft);
            setTimeout(updateClock, 1000); // Aktualizuj co sekundę
        }

        updateClock();
    }

    const countdowns = document.querySelectorAll('.countdown');
    countdowns.forEach(startCountdown);
});
</script>
</body>
</html>
