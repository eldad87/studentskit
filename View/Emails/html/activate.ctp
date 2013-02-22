<h1 style="margin-top: 0px; color: #555">
   Welcome!
</h1>
<div style="margin-left: 5px">
    <p>Dear <?php echo $user['username']; ?>,</p>
    <p>In order to get started, please <?php
        echo $this->Html->link('Click here', array('action' => 'activate', 'activation_code' => $user['activation_code'], 'full_base'=>true))
        ?> to activate your account.
    </p>
</div>