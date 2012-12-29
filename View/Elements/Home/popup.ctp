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

        <div class="control  control1 pull-right">
            <button class="btn-blue" type="button"><?php echo $button[0]['name']; ?></button>
        </div>
    </div> <!-- /modal-body -->
</div>
<!-- poppup ends -->