<h2>User Options</h2>
<div>
	<?php echo $this->Html->link('Main', array('controller'=>'Student', 'action'=>'/')); ?><br/>
	<?php echo $this->Html->link('Lessons', array('controller'=>'Student', 'action'=>'lessons')); ?><br/>
	<?php echo $this->Html->link('profile', array('controller'=>'Student', 'action'=>'profile')); ?><br/>
	<?php echo $this->Html->link('Review', array('controller'=>'Student', 'action'=>'awaitingReview')); ?><br/>
</div>
<br />

<h2>Teacher Options</h2>
<div>
	<?php echo $this->Html->link('Main', array('controller'=>'teacher', 'action'=>'/')); ?><br/>
	<?php echo $this->Html->link('Subjects', array('controller'=>'teacher', 'action'=>'subjects')); ?><br/>
	<?php echo $this->Html->link('Lessons', array('controller'=>'teacher', 'action'=>'lessons')); ?><br/>
	<?php echo $this->Html->link('profile', array('controller'=>'teacher', 'action'=>'profile')); ?><br/>
	<?php echo $this->Html->link('Review', array('controller'=>'teacher', 'action'=>'awaitingReview')); ?><br/>
</div>
<br />