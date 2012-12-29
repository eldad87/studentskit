<!-- poppup start -->
<div id="order-notice-popup" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3>Info</h3>
    </div> <!-- /modal-header -->
    <div class="modal-body">
        <?php
        if(!empty($user)) {
            echo '<p>Dear ',$user['username'],'</p>';
        }
        ?>

        <p><?php echo $description; ?></p>

    </div> <!-- /modal-body -->

    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('Cancel'); ?></button>
        <button class="btn btn-primary" id="order-popup-button"><?php echo $button[0]['name']; ?></button>
    </div>
</div>
<!-- poppup ends -->
<script type="text/javascript">

  $(document).ready(function() {
      $('#order-popup-button').click(function() {
          window.location = '<?php echo Router::url($button[0]['url']); ?>';
      });

  });

</script>
