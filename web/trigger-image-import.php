<?php
require __DIR__.'/../vendor/autoload.php';
use Symfony\Component\Process\Process;

if (!empty($_GET['go'])) {
    $job = __DIR__.'/../cron/productImageImport.php --debug';
    $log = __DIR__.'/../app/logs/php.log';

    $process = new Process('nohup php '.$job.' >> '.$log.' 2>&1 & echo $!');
    $process->run();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Billede importen kører ...</title>
</head>
<body>
<h1>Billede importen kører ...</h1>

<?php if (empty($_GET['go'])): ?>
<p>... eller, det gør det når du lige trykker <a href="?go=1" rel="nofollow">her!</a></p>
<?php else: ?>

<p>Sæt dig bare tilbage</p>
<p>- nyd en kop kaffe</p>
<p>- hyg dig med et "dameblad" - hør lidt musik</p>
<p>Vi klarer ærterne her fra!</p>
<?php
$songs = [
    '<iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F89271792"></iframe>',
    '<iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F12406176"></iframe>',
    '<iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F33870816"></iframe>',
    '<iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F89271790"></iframe>',
];

echo $songs[array_rand($songs)];
?>
<p>Du vil modtage en mail så snart importen er færdig.</p>

<?php endif; ?>
</body>
</html>
