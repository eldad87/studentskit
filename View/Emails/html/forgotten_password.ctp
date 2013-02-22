<h1 style="margin-top: 0px; color: #555">
    Hi <?php echo $user['username']; ?>
</h1>
<div style="margin-left: 5px">
    <p>Someone (hopefully you) has requested a password reset on your account. In order to reset your password please <?php
        echo $this->Html->link('Click here', array('action' => 'forgotten_password', 'password_reset' => $user['password_reset'], 'full_base'=>true)) ?>
    </p>
</div>