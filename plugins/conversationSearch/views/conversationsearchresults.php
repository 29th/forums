<?php defined('APPLICATION') or die;

$results = $this->data('Results');
$resultCount = $this->data('ResultCount');
?>
<?php if ($resultCount == 0): ?>
    <p class="NoResults"><?php printf(t('No results for %s.', 'No results for <b>%s</b>.'), $search); ?></p>
<?php else: ?>
    <ol id="search-results" class="DataList DataList-Search">
    <?php foreach ($results as $result): ?>
        <li class="Item Item-Search">
            <?php
                if ($result['Title'] == '') {
                    $date = Gdn_Format::date($result['DateInserted'], t('conversationSearch.NoSubject.DateFormat', t('Date.DefaultDateTimeFormat')));
                    $subject = sprintf(t('conversationSearch.NoSubject.Text', '<Message written on %2$s, no subject>'), $result['Name'], $date);
                } else {
                    $subject = $result['Title'];
                }
            ?>
            <h3><?= anchor(htmlspecialchars($subject), $result['Url']); ?></h3>
            <div class="Item-Body Media">
                <?php
                $Photo = userPhoto($result, array('LinkClass' => 'Img'));
                if ($Photo) {
                    echo $Photo;
                }
                ?>
                <div class="Media-Body">
                    <div class="Meta">
                        <?php
                        echo ' <span class="MItem-Author">'.
                            sprintf(t('by %s'), userAnchor($result)).
                            '</span>';

                        echo bullet(' ');
                        echo ' <span class="MItem-DateInserted">'.
                            Gdn_Format::date($result['DateInserted'], 'html').
                            '</span> ';
                        ?>
                    </div>
                    <div class="Summary">
                        <?php echo $result['Summary']; ?>
                    </div>
                </div>
            </div>
        </li>
    <?php endforeach; ?>
    </ol>
    <div class="PageControls Bottom">
    <?php if ($resultCount): ?>
        <!-- <span class="Gloss"><?php echo plural($resultCount, '%s result', '%s results'); ?></span> -->
        <?php PagerModule::write(array('CurrentRecords' => $resultCount)); ?>
    <?php endif; ?>
    </div>
<?php endif; ?>
