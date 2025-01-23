<?php
defined('MYAAC') or die('Direct access not allowed!');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Players</title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/fontawesome.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        .top1 { color: gold; font-weight: bold; }
        .top2 { color: silver; font-weight: bold; }
        .top3 { color: #cd7f32; font-weight: bold; }
        .fa-trophy { color: #FFD700; margin-right: 5px; } /* Złoty kolor dla ikony pucharu */
    </style>
</head>
<body>
    <div class="well">
        <div class="header">
            <a href="<?= getLink('highscores') ?>">Top Players</a>
        </div>
        <div class="body">
            <table class="table-100">
                <?php
                $i = 0;
                foreach (getTopPlayers(5) as $player) {
                    $i++;

                    $rowClass = '';
                    if ($i == 1) {
                        $rowClass = 'top1';
                    } elseif ($i == 2) {
                        $rowClass = 'top2';
                    } elseif ($i == 3) {
                        $rowClass = 'top3';
                    }


                    $trophyIcon = ($i <= 3) ? '<i class="fa-solid fa-trophy"></i>' : '';
                    ?>
                    <tr class="<?= $rowClass; ?>">
                        <td><?= $i; ?>.</td>
                        <td><?= $trophyIcon . getPlayerLink($player['name']); ?> (<?= $player['level']; ?>)</td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>
