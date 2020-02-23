<?php
class UnreadOnIndexPlugin extends Gdn_Plugin {
    public function categoriesController_afterCategoryTitle_handler($sender, $args) {
        if( ! $args['Category']['Read']) {
            echo ' <strong class="HasNew JustNew NewCommentCount" title="You haven\'t read this yet.">new</strong>';
        }
    }
}
