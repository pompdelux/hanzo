<?php
/**
 * this will trigger a clear cache for the prod environment.
 * it wil also ensure that the correct apache permissions is set correctly
 *
 * @see http://www.developly.com/using-symfony2-on-phpfog-now-right-now
 */
if (!empty($_GET['run'])) {
    echo `php ../app/console cache:clear --env=prod`;
}
