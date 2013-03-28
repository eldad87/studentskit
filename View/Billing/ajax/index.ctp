



<script type="text/javascript" xmlns="http://www.w3.org/1999/html">
    $(document).ready(function(){
        //Activate tooltip
        initToolTips();
    });
</script>

<?php
////////////// Page 1 - start
if($page==1) {
?>
    <div class="fullwidth pull-left cont-span15">
        <?php
        if(!$response['response']['billingHistory']) {
            echo $this->Layout->flashMessage(__('Info'), __( 'You didn\'t received any payments yet.'), 'alert-info');
            die;
        }
        ?>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            var url = '/Billing/index/{limit}/{page}';
            lmObj.loadMoreButton('#billing-history-load-more', 'click', '#billing-history', url, {}, 'get', <?php echo $limit; ?>);
            lmObj.setItemsCountSelector('#billing-history-load-more', '#billing-history li.history' );
        });
    </script>



    <div class="fullwidth pull-left cont-span15" id="billing-history">
        <h2 class="space2"><strong><?php echo __('Billing History'); ?></strong></h2>

<?php
}
$i = $page*$limit;
foreach($response['response']['billingHistory'] AS $bh) {
    $i++;
?>

        <div class="fullwidth history pull-left  bg-color<?php echo $i%2==0 ? '2' : '1'; ?>">
            <div class="headeruser space20 space23 space15">
                <?php
                    echo $this->Html->image($this->Layout->image($bh['Subject']['image_source'], 38, 38), array('alt' => 'Subject image'));
                ?>
            </div>
            <div class="pull-left space11 space4">
                <p><?php echo sprintf(__('On %s'), $bh['BillingHistory']['created']) ;?>, <?php echo $bh['BillingHistory']['message'] ;?></p>
            </div>
        </div>

<?php
}
////////////// Page 1 - start
if($page==1) {
?>
    </div>
    <div class="fullwidth pull-left cont-span15 space4">
        <?php
        if(count($response['response']['billingHistory'])>=$limit) {
            echo '<a href="#" class="more radius3 gradient2 space8" id="billing-history-load-more"><strong>', __('Load More') ,'</strong><i class="iconSmall-more-arrow"></i></a>';
        }
        ?>
    </div>
<?php
}
?>