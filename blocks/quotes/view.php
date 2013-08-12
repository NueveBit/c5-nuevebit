<?php defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<blockquote id="quote-block-<?= intval($bID) ?>" class="quote-block">
    <p><?php print $content; ?></p>
    <cite><?php print $source; ?></cite>
</blockquote>
