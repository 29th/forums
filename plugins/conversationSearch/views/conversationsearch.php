<?php defined('APPLICATION') or die; ?>

<div class="SearchForm">
    <?= $this->Form->open(['action' => url('/messages/search'), 'method' => 'get']) ?>
    <?= $this->Form->errors() ?>
    <div class="SiteSearch InputAndButton">
    <?= $this->Form->textBox('Search', ['aria-label' => t('Enter your search term.'), 'title' => t('Enter your search term.')]) ?>
    <?= $this->Form->button('Search', ['aria-label' => t('Search'), 'Name' => '']) ?>
    </div>
    <?= $this->Form->close() ?>
</div>

<?php

/*
Ignore mine [ ]
From Users [____________]
From [_____] To [____]
Search in Unread
 */

$search = $this->data('Search', '');
if ($search != '') {
    include __DIR__.'/conversationsearchresults.php';
}
