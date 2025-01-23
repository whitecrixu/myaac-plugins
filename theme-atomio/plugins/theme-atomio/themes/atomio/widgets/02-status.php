<?php
// Ensure access is restricted
defined('MYAAC') or die('Direct access not allowed!');

// Function to get the current server time in the Europe/London timezone
function getServerTime() {
    $datetime = new DateTime('now', new DateTimeZone('Europe/London'));
    return $datetime->format('Y-m-d H:i:s');
}

// Load common.php
require_once 'common.php';

// Fetch Boosted Creature and Boosted Boss
$boostedData = getBoostedData($db); // Function available in common.php
$boostedCreature = $boostedData['boostedCreature'] ?? 'None';
$boostedBoss = $boostedData['boostedBoss'] ?? 'None';

// Function to persist and retrieve uptime from a file or session
function getPersistentUptime($status) {
    $file = '/tmp/server_uptime.json'; // Path to file storing uptime

    if ($status['online']) {
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if (isset($data['startTime'])) {
                $startTime = $data['startTime'];
            } else {
                $startTime = time();
                file_put_contents($file, json_encode(['startTime' => $startTime]));
            }
        } else {
            $startTime = time();
            file_put_contents($file, json_encode(['startTime' => $startTime]));
        }

        $uptimeSeconds = time() - $startTime;
        $status['uptimeSeconds'] = $uptimeSeconds;
        $status['uptimeReadable'] = gmdate("H:i", $uptimeSeconds);
    } else {
        if (file_exists($file)) {
            unlink($file); // Delete the file when the server is offline
        }
        $status['uptimeSeconds'] = 0;
        $status['uptimeReadable'] = '00:00';
    }

    return $status;
}

$status = getPersistentUptime($status);

// Function to calculate time until the next Server Save at 6:00 AM
function getNextServerSaveCountdown() {
    $now = new DateTime('now', new DateTimeZone('Europe/London'));
    $nextSave = new DateTime('today 6:00', new DateTimeZone('Europe/London'));

    if ($now > $nextSave) {
        $nextSave->modify('+1 day');
    }

    $interval = $now->diff($nextSave);
    return $interval->format('%H:%I:%S');
}

$nextServerSave = getNextServerSaveCountdown();
?>

<div class="well widget loginContainer" id="loginContainer">
    <div class="header">
        <a href="<?= getLink('online') ?>">Server Status</a>
    </div>
    <div class="body">
        <div style="text-align: center">
            <p style="font-weight: bold;">
                Server Status: 
                <span style="color: <?= $status['online'] ? '#1ebc30' : '#ff0000'; ?>; font-weight: bold;">
                    <?= $status['online'] ? 'Online' : 'Offline'; ?>
                </span>
            </p>
            <?php if ($status['online']) { ?>
                <p>Uptime: <strong id="uptime" data-uptime="<?= $status['uptimeSeconds'] ?>">
                    <?= $status['uptimeReadable'] ?></strong>
                </p>
                <p>Next Server Save: <strong id="serverSaveCountdown">
                    <?= $nextServerSave ?></strong>
                </p>
                <p>Server Time: <strong id="serverTime" data-server-time="<?= getServerTime() ?>">
                    <?= getServerTime() ?></strong>
                </p>
                <a href="<?= getLink('online') ?>">
                    <p style="color: #1ebc30; font-weight: bold;">Players: <strong><?= $status['players'] ?? 0 ?></strong></p>
                </a>
                <p>Boosted Creature: <strong><?= htmlspecialchars($boostedCreature) ?></strong></p>
                <p>Boosted Boss: <strong><?= htmlspecialchars($boostedBoss) ?></strong></p>
            <?php } else { ?>
                <p>Boosted Creature: <strong>None</strong></p>
                <p>Boosted Boss: <strong>None</strong></p>
                <script>
                    // Start countdown when the status changes to online
                    function checkServerStatus() {
                        fetch('<?= getLink("status") ?>') // API or endpoint returning server status
                            .then(response => response.json())
                            .then(data => {
                                if (data.online) {
                                    location.reload();
                                }
                            })
                            .catch(error => console.error('Error checking server status:', error));
                    }

                    // Check status every 5 seconds
                    setInterval(checkServerStatus, 5000);
                </script>
            <?php } ?>
        </div>
    </div>
</div>

<?php if ($status['online']) { ?>
<script>
    function updateServerTime() {
        const serverTimeElement = document.getElementById('serverTime');
        const uptimeElement = document.getElementById('uptime');
        const serverSaveElement = document.getElementById('serverSaveCountdown');

        try {
            // Update server time
            if (serverTimeElement) {
                let serverTime = new Date(serverTimeElement.getAttribute('data-server-time'));
                if (!isNaN(serverTime.getTime())) {
                    serverTime.setSeconds(serverTime.getSeconds() + 1);
                    serverTimeElement.setAttribute('data-server-time', serverTime.toISOString());
                    serverTimeElement.innerText = serverTime.toISOString().replace('T', ' ').slice(0, 19);
                }
            }

            // Update uptime
            if (uptimeElement) {
                let uptimeSeconds = parseInt(uptimeElement.getAttribute('data-uptime'));
                if (!isNaN(uptimeSeconds)) {
                    uptimeSeconds += 1;
                    uptimeElement.setAttribute('data-uptime', uptimeSeconds);

                    const hours = Math.floor(uptimeSeconds / 3600);
                    const minutes = Math.floor((uptimeSeconds % 3600) / 60);

                    uptimeElement.innerText = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
                }
            }

            // Update time until the next server save
            if (serverSaveElement) {
                let now = new Date();
                let nextSave = new Date();
                nextSave.setHours(6, 0, 0, 0);
                if (now > nextSave) {
                    nextSave.setDate(nextSave.getDate() + 1);
                }
                let diff = nextSave - now;

                let hours = Math.floor(diff / (1000 * 60 * 60));
                let minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((diff % (1000 * 60)) / 1000);

                serverSaveElement.innerText = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
        } catch (error) {
            console.error('Error updating server time, uptime or server save countdown:', error);
        }
    }

    setInterval(updateServerTime, 1000);
</script>
<?php } ?>
