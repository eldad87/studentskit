<?php
$this->Html->scriptBlock('Support. FAQ load");', array('inline'=>false));
$this->extend('/Support/common/common');
$this->start('main');
?>

<div class="cont-span6 cbox-space fullwidth">
    <div class="fullwidth pull-left space17">
        <div class="form-first" id="faq">
            <h2 class="space15 "><?php echo __('FAQ'); ?></h2>

            <p class="space6 space15"><strong>For Teachers</strong></p>
            <ul>
                <li>+ How can I become a teacher?
                    <p>We're always on the hunt for new teachers. If you think you have what it takes, just register.<br />
                        Any user can be a teacher and vise versa.
                    </p>
                </li>
                <li>+ 
                    How much does it cost to use Universito to teach?
                    <p>
                        For educators, Universito has ZERO signup fees. We don't charge you anything upfront, so there is no risk involved in using the platform.
                    </p>
                </li>
                <li>+ 
                    What commission percentage does Universito take?
                    <p>
                        Universito takes 1USD MAX commission on revenue generated from any content sales.
                    </p>
                </li>


                <li>+ 
                    If I teach a paid class, when do I receive my class revenues?
                    <p>
                        We send your class revenue to you as soon as your class is successfully starts. Payments are sent via PayPal.
                    </p>
                </li>

                <li>+ 
                    How big are class sessions on Universito?
                    <p>
                        Our Virtual Classroom allows teachers to teach an unlimited amount of students at a time. When building classes and courses, teacher set the maximum number of students that can register.
                    </p>
                </li>

                <li>+ 
                    Why can't I see my private lessons on search?
                    <p>
                        The search shows only Public classes available to all. For Private lessons you must send the link to your private classroom to each student individually.
                    </p>
                </li>


                <li>+ 
                    How will I get paid?
                    <p>
                        Payments are sent via paypal as soon as your class is successfully starts. To set up a PayPal account, visit <?php echo $this->Html->link('Paypal', 'www.paypal.com') ?>
                    </p>
                </li>

                <li>+ 
                    Do I have to download anything in order to run my class?
                    <p>
                        Absolutely not, our platform is fully integrated with all the necessary features and doesn't require you to download anything additional to get your class up and running.
                    </p>
                </li>

                <li>+ 
                    What price should I set for my class?
                    <p>
                        We let our teachers set the price per student for each subject, however reasonable prices will allow more students to sign up.
                    </p>
                </li>

                <li>+ 
                    What do I do if a student didn't show up for my class, even though they registered for it?
                    <p>
                        The onus is on the student to show up on time for a registered class, and if they decide not to show up, the teacher still gets paid.
                    </p>
                </li>
                <li>+ 
                    Should I mute all my students during a class, so that no one is able to disrupt me?
                    <p>
                        We give our teachers full control over their classroom, so they are free to make any decisions in regards to how they teach their classroom. A good technique to have all students muted but unmute each when taking or responding to their questions.
                    </p>
                </li>
                <li>+ 
                    I am a tutor. What will I get if I join Universito?
                    <p>
                        Universito allows you expand your tutoring services on an international level, and build your brand, all while sitting in the comfort of your home. If you have an existing business, we help you save money and time from traveling to and from your student’s locations and we remove the friction of you having to request and collect payments. In short, we let you focus on teaching.
                    </p>
                </li>
                <li>+ 
                    How can I use universito for my tutoring company?
                    <p>
                        We work with businesses as well as individuals. Contact us <?php echo $this->Html->link('here', array('action'=>'contact')); ?> to get the conversation started regarding how you can host your tutoring franchise online:
                    </p>
                </li>

                <li>+ 
                    What happens if I go over the time limit I set for a class?
                    <p>
                        The time limit is set in order to give the students an approximation of how long the class will be, but if necessary you are allowed to go over the time limit.
                    </p>
                </li>
            </ul>


            <p class="space6 space15"><strong>For students</strong></p>
            <ul>

                <li>+ 
                    If I pay for a class and the teacher doesn't show up, what happens?
                    <p>
                        Send us an email (support@universito.com) with the name of the lesson and the name of the teacher. We will refund your money right away, no questions asked.
                    </p>
                </li>


                <li>+ 
                    How do I sign up for a lesson?
                    <p>
                        To sign up for a lessons, you must create a student account and click on order/join button in the relevant lesson page.
                    </p>
                </li>

                <li>+ 
                    How do I get more information about a teacher?
                    <p>
                        Just click on the teacher, and you will be pointed to the teacher's profile page.
                    </p>
                </li>

                <li>+ 
                    How do I get more information about a subject?
                    <p>
                        Just click on the subject you will be pointed to the subject's profile page.
                    </p>
                </li>

                <li>+ 
                    Can I ask a question to the teacher before a class starts?
                    <p>
                        First, sign-in, then click on the relevant teacher. there you'll see an envelop icon. click on it, and a dialog box should open.
                    </p>
                </li>
                <li>+ 
                    A teacher cancelled a class that I signed up for, how do I get my money back?
                    <p>
                        You only get charged when the lesson starts, therefore no refund is needed.
                    </p>
                </li>
                <li>+ 
                    How can I pay?
                    <p>
                        Payment is done using Paypal, to set up a PayPal account, visit <?php echo $this->Html->link('Paypal', 'www.paypal.com') ?>
                    </p>
                </li>
                <li>+ 
                    Can I negotiate a lesson?
                    <p>
                        Yes, after ordering or during the ordering process, you can always negotiate and set new lesson details (duration, max students, price etc).
                        After doing so, your request will be sent to the teacher for approval.
                    </p>
                </li>
            </ul>


            <p class="space6 space15"><strong>General</strong></p>
            <ul>

                <li>+ 
                    Do I have to download anything in order to use Universito?
                    <p>
                        Absolutely not, our platform is fully integrated with all the necessary features and doesn't require you to download anything additional to get your class up and running.
                    </p>
                </li>
                <li>+ 
                    How much do classes & courses cost on Universito?
                    <p>
                        We let our teachers set the per class, so prices will vary depending on the teacher and subject matter.
                    </p>
                </li>

                <li>+ 
                    What is the Virtual Classroom?
                    <p>
                        The Virtual Classroom is an advanced, real-time teaching venue. This is where teacher conduct lessons. Our Virtual Classroom has dynamic functionality like screensharing, video streaming, whiteboarding, document sharing and many more.
                    </p>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php
$this->end();
?>

