<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="wpl-listing-results-links-cnt" id="wpl_listing_results_links_container<?php echo $property_id; ?>">
    <?php if(trim($search_url)): ?><div class="wpl-back-to-search-results"><a href="<?php echo $search_url; ?>"><?php echo __('Back to search results', 'wpl'); ?></a></div><?php endif; ?>
    <ul class="wpl_listing_results_links_list_container clearfix">
        <?php if($previous and $previous != $property_id): ?><li class="wpl-previous-listing"><a href="<?php echo wpl_property::get_property_link(NULL, $previous); ?>"><?php echo __('Prev', 'wpl'); ?></a></li><?php endif; ?>
        <li class="wpl-listing-result-pagination"><span><?php echo __('Result ', 'wpl'); ?><?php echo $position; ?></span><?php echo __(' of ', 'wpl'); ?><span><?php echo $total; ?></span></li>
        <?php if($next and $next != $property_id): ?><li class="wpl-next-listing"><a href="<?php echo wpl_property::get_property_link(NULL, $next); ?>"><?php echo __('Next', 'wpl'); ?></a></li><?php endif; ?>
    </ul>
</div>