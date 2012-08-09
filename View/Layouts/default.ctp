<?php
$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
?>
<?php echo $this->Html->docType('xhtml-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo Configure::read('Config.languageDirection'); ?>">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('studentskit');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div id="container">
		<div id="top_strip">
			<div class="container">
				<div class="localize">
					<p>Language</p> <?php echo $this->Html->image('top_strip/lang.jpg'); ?> <?php echo $this->Html->image('top_strip/country.jpg'); ?>
				</div>
				<div class="user">
					<p>Sign in</p> <?php echo $this->Html->image('top_strip/sign_in.jpg', array('class'=>'user_sign_in')); ?>
					<p>Register</p>
				</div>
			</div>
		</div>
		
		<div id="nav">
			<div class="container">
				<div class="logo"><?php echo $this->Html->image('nav/logo.jpg'); ?></div>
				<div class="menu">
					<div id="slidetabsmenu">
						<ul>
							<li class="current"><a href="/" title="Home"><span>Home</span></a></li>
							<li><?php echo $this->Html->link('<span>Forum</span>', array('controller'=>'forum', 'action'=>'/'), array('title'=>'Forum', 'escape'=>false)); ?></li>
							<li><?php echo $this->Html->link('<span>Account</span>', array('controller'=>'Student', 'action'=>'/'), array('title'=>'Account', 'escape'=>false)); ?></li>

							<li><?php echo $this->Html->link('<span>Lesson request</span>', array('controller'=>'Requests', 'action'=>'/'), array('title'=>'Account', 'escape'=>false)); ?></li>
							<li><a href="#" title="How it works"><span>How it works</span></a></li>	
						</ul>
					</div>
					<br style="clear: left;" />

				</div>
			</div>
		</div>
		
		
		
		<div id="content">
			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
