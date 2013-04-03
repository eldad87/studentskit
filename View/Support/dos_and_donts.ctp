<?php
$this->Html->scriptBlock('Support. dosAndDonts load");', array('inline'=>false));
$this->extend('/Support/common/common');
$this->start('main');
?>

<div class="cont-span6 cbox-space fullwidth">
    <div class="fullwidth pull-left space17">
        <div class="form-first justify" id="contact-area">
            <p>
                <strong>
                    By agreeing to the Terms of Use, you agree to this Dos and Don’ts Policy.  We may modify Dos and Don'ts Policy at any time by posting the revised terms at:
                    <?php echo $this->Html->link(Configure::read('public_domain').'/Support/dosAndDonts', Configure::read('public_domain').'/Support/dosAndDonts') ?>.
                    All changes are effective immediately when we post them.  We will notify you through the Universito Messaging and/or via email of any material changes to the Dos and Don’ts Policy.  You are required to agree to the changes to the Dos and Don’ts Policy prior to further use of the Universito website.  If you do not indicate your agreement, your access to the website features and functions may be limited or terminated.
                </strong>
            </p>


            <p class="space37">
                Please read this page with care, as it explains the Dos and Don'ts, of being a member of the Universito community. These policies only apply to Universito.com. If you still have questions after reading these policies, please <?php echo $this->Html->link('contact us here', array('controller'=>'Support', 'action'=>'contact')); ?>.
            </p>



            <p class="space32">
                <strong>
                    Membership
                </strong>
            </p>
            <p class="space37">
                We expect all members of Universito (including Universito's staff) to treat each other with respect and kindness. Your name on Universito is your identity. You are responsible for your conduct and all content submitted under your name on Universito.  Keep the following in mind:
            </p>
            <ul class="decimal-list space26">
                <li>By joining Universito, you agree to abide by the policies outlined here and in our <?php echo $this->Html->link('Terms of Use', array('controller'=>'Support', 'action'=>'termsOfUse')); ?>;</li>
                <li>You may not use the word "Universito" in your member name;</li>
                <li>You must be at least 18 years of age to hold an account on Universito;</li>
                <li>If you are under 18, you must have the permission and supervision of a parent or legal guardian who is at least 18 years of age; that adult is responsible for the account; and</li>
                <li>If you are under the age of 18, you may not utilize the community features on Universito. When using Universito, those under 18 must, at all times, have the permission and supervision of a parent or legal guardian who is at least 18 years of age.</li>
                <li>You may not use mature, profane or racist language or images in the public areas of your account (for example: username, avatar, or Profile);</li>
                <li>You may not use the public areas of your account to demonstrate or discuss disputes with others or with Universito;</li>
                <li>You may not transfer ownership or sell your Universito account to another party;</li>
                <li>You may not use Universito to direct shoppers to another online selling venue to purchase, as this may constitute fee avoidance. This includes posting links/URLs or providing information sufficient to locate the other online venue(s);</li>
                <li>A Universito account may not be used for the purpose of redirecting traffic to another web location; and</li>
                <li>Keep your account information updated and accurate. Your account must have a valid email address at all times. Universito will use the email address on file in your account information to contact you when necessary.</li>
            </ul>
            <p class="space37">
                Members who do not comply with Universito's policies may be subject to review, which can result in suspension of account privileges and/or termination. This includes all of your accounts by virtue of association. In other words, Universito reserves the right to suspend the use of the site for a person and all username(s) he/she operates under. Suspended or terminated members remain obligated to pay Universito for all unpaid fees per our <?php echo $this->Html->link('Terms of Use', array('controller'=>'Support', 'action'=>'termsOfUse')); ?>.
            </p>



            <p class="space32">
                <strong>
                    Messaging
                </strong>
            </p>
            <p class="space37">
                Universito provides a messaging system as a way to communicate privately with other Universito members. Think of it like email just for Universito. Messaging is used to communicate about tutoring or to build friendly relationships with other members. Please use common sense when giving out personal information to others.
            </p>
            <ul class="disc-list space26">
                <li>You must not use messaging to send unsolicited advertising or promotions, requests for donations or "spam;"</li>
                <li>You must not knowingly harass or abuse another member;</li>
                <li>If someone explicitly tells you not to contact him or her, you must not contact him or her again; and</li>
                <li>Sending too many messages too quickly may auto-disable your messaging. You must contact Support to have your ability to send messages are reinstated.</li>
            </ul>
            <p class="space37">
                Misuse of messaging may result in suspension of account privileges and/or termination of your Universito account(s). Suspended or terminated members remain obligated to pay Universito for all unpaid fees per our <?php echo $this->Html->link('Terms of Use', array('controller'=>'Support', 'action'=>'termsOfUse')); ?>.
            </p>



            <p class="space32">
                <strong>
                    Credit Points
                </strong>
            </p>
            <p class="space37">
                In order to purchase tutor services through Universito, members must purchase Credit Points.  Credit Points are what students pay tutors with for sessions. 1 Credit Point = 1 US Dollar.
                Universito accepts PayPal payment only.
            </p>
            <p class="space37">
                You understand and agree that Universito may earn interest or other compensation from the balances in our bank accounts that result from the timing difference between Credit Points purchases and Credit Points withdraw request that have been processed.
            </p>



            <p class="space32">
                <strong>
                    Refund
                </strong>
            </p>
            <p class="space37">
                Universito believes in a Satisfaction Guaranteed policy.  A student’s first session with any new tutor is 100% satisfaction guaranteed. If the student feels that they did not get help – they have 72 hours from the completion of a tutoring session to log a dispute and initiate a Tutor Session Refund.  Within 48 hours of a request Universito will review and declare a judgment on the Tutor Session Refund request.  If a refund is granted, Universito will issue a credit to the student for fees related to a Tutor Session Refund and debit the tutor account for the amount refunded.
            </p>
            <p class="space37">
                Members who abuse the Tutor Session Refund privilege (at Universito’s sole discretion) may be subject to review, which can result in suspension of account privileges and/or termination.
            </p>



            <p class="space32">
                <strong>
                    Rate/Feedback
                </strong>
            </p>
            <p class="space37">
                Feedback is a way to establish a reputation of trust for students, tutors and the Universito marketplace. For every tutoring session, the student has the opportunity to submit a 5 star rating and an optional comment. Consistency, fairness and honesty are critical to the integrity of the feedback system. Feedback directly affects a Tutor's reputation, so we ask that members take it seriously. Please consider the following:
            </p>
            <ul class="decimal-list space26">
                <li>
                    Rated feedback cannot be edited or removed after submission.
                </li>
                <li>
                    Feedback ratings, comments or images removed by Universito cannot be reinstated or resubmitted.  Members may askUniversito to review feedback for removal. Contact Support to request a feedback review. Universito does not investigate the validity of opinions or statements made in feedback or mediate feedback disputes. At Universito's sole discretion and without notice to both parties involved, feedback may be removed or altered by Universito in the following circumstances:
                    <ul class="disc-list space26">
                        <li>Personally identifying or private information was published (for example: a phone number, real name, email address, physical address, content of a private Universito Conversation, details regarding an Universito investigation);</li>
                        <li>Feedback contains mature, profane or racist language and/or images;</li>
                        <li>Feedback contains spam, links, scripts or advertising;</li>
                        <li>Shilling is evident (fraudulent inflation of feedback rating by use of an alternate account);</li>
                        <li>Feedback is given for a transaction created for the sole purpose of leaving feedback;</li>
                        <li>Negative or neutral feedback was mistakenly submitted for a different transaction;</li>
                        <li>Negative or neutral feedback refers to an unrelated transaction;</li>
                        <li>Negative or neutral feedback comments about using Universito or other services (for example: payment processors);</li>
                        <li>A member is confused about how to use the Feedback system, resulting in unintended negative or neutral feedback; and</li>
                    </ul>
                </li>
            </ul>
            <p class="space37">
                Members with low overall feedback scores or members who have violated the above policies for feedback may be subject to review, which can result in suspension of account privileges and/or termination. Suspended or terminated members remain obligated to pay Universito for all unpaid fees per our Terms of Use.
            </p>



            <p class="space32">
                <strong>
                    Profiles
                </strong>
            </p>
            <p class="space37">
                We encourage you to provide information about yourself and/or your expertise in your Profile.
            </p>
            <p class="space37">
                Please keep the following things in mind as you set up your profile:
            </p>
            <ul class="disc-list space26">
                <li>Do not make illegal use of photographs or written text. This is in violation of our Terms of Use;</li>
                <li>You may not use mature, profane or racist language or images in your profile;</li>
                <li>Set a minimum rate for your tutoring services if you are a tutor; and</li>
                <li>You (student or tutor) may not engage in any activity to avoid Universito's fees ("fee avoidance"). This includes but is not limited to: completing a transaction off-Universito once it has been initiated on the site.</li>
            </ul>
            <p class="space37">
                Members who do not comply with Universito's policies may be subject to review, which can result in suspension of account privileges and/or termination. Suspended or terminated members remain obligated to pay Universito for all unpaid fees per our Terms of Use.
            </p>



            <p class="space32">
                <strong>
                    Intellectual Property Infringement
                </strong>
            </p>
            <p class="space37">
                Universito may act expeditiously to respond to a proper notice by (1) removing or disabling access to material claimed to be subject of infringing activity; and (2) removing and discontinuing service to repeat offenders. If Universito removes or disables access in response to such a notice, Universito will make a good-faith attempt to contact the allegedly infringing party ("Member") so that they may make a counter notification.
            </p>
            <p class="space37">
                Infringing of intellectual property may result in suspension of account privileges and/or termination of your Universito account(s). Suspended or terminated members remain obligated to pay Universito for all unpaid fees per our Terms of Use.
            </p>



            <p class="space32">
                <strong>
                    Flagging
                </strong>
            </p>
            <p class="space37">
                Flagging is like Universito's neighborhood watch. It's your way to alert Universito of potential problems. Email support with "RE: Report this item to Universito" and describe the issue. This is a confidential process.  Additionally, Universito's investigation will be handled privately; you will not receive a personal response to your flagging message.  Please consider the following:
            </p>
            <ul class="disc-list space26">
                <li>Flagging should be used to report any behavior or content that violates any of Universito's policies;</li>
                <li>Do not flag a single violation multiple times; and</li>
                <li>Do not flag for intellectual property matters. Instead, please follow Universito's <?php echo $this->Html->link('Copyright and Intellectual Property Policy', array('controller'=>'Support', 'action'=>'ip')); ?>.</li>
            </ul>
            <p class="space37">
                In most cases, Universito will work with a member privately to remedy the problem. If a seller does not respond to Universito's communication or requests in a timely manner, the item may be removed, and the shop's selling privileges may be suspended and/or terminated. In some extreme cases, listings will be removed immediately. Abuse of the flagging system by means of raising repeated, unjustified flags may result in the suspension and/or termination of your account. If Universito removes an item listing for violating Universito policy, the seller is still obligated to pay the listing fee for that item. Suspended or terminated members remain obligated to pay Universito for all unpaid fees per our Terms of Use.
            </p>



            <p class="space32">
                <strong>
                    Community
                </strong>
            </p>
            <p class="space37">
                All registered members are part of our community. Universito has established multiple ways for members to interact with one another on the site. Please remember that these are public spaces, so use common sense when sharing personal information. Universito's role is to facilitate open discussion and support our community through constructive communication. We have some general rules for community conduct, and some spaces have additional rules.  Please consider the following:
            </p>
            <ul class="disc-list space26">
                <li>Treat one another with respect. There is a real person behind each name;</li>
                <li>If your account involves a person under the age of 18, you may not utilize the community features on Universito unless otherwise specified by Universito. (When using Universito, those under 18 must, at all times, must have the permission and supervision of a parent or legal guardian who is at least 18 years of age.);</li>
                <li>Knowingly harassing, insulting or abusing other members is unacceptable;</li>
                <li>The community spaces are not the appropriate channel to express disputes with others;</li>
                <li>Discussing a specific member in a negative way is not allowed; and</li>
                <li>Do not publicly post another person's private information without their explicit consent (for example: email, Conversations, letters, phone numbers, addresses, full names or business transactions).</li>
            </ul>
            <p class="space37">
                Violating community policies may result in suspension of community or other privileges and/or account termination.
            </p>



            <p class="space32">
                <strong>
                    Forums
                </strong>
            </p>
            <p class="space37">
                The Universito Forums are a place to interact with a broad range of Universito community members worldwide. On our Forums you can read staff announcements, share questions and answers, and have discussions on various topics of interest.  Please consider the following:
            </p>
            <ul class="disc-list space26">
                <li>When using the Forums, follow the general rules for Universito community conduct;</li>
                <li>Solicitation for direct donations or other fund-raising is not allowed in the Forums;</li>
                <li>Universito does not allow spam in the Forums; for this reason, unsolicited promotion or advertisement from representatives or affiliates of outside services, websites or other products is not allowed;</li>
                <li>Keep your posts on-topic. Do not derail a constructive discussion thread;</li>
                <li>Start threads in the appropriate section of the Forums; and</li>
                <li>Consider carefully what information you post in the Forums. In general, the Forums serve as a permanent record. In certain circumstances, at Universito's sole discretion, Universito may remove content from Forums.</li>
            </ul>
            <p class="space37">
                Universito reserves the right to close any Forum thread for any reason. Threads may be moved to a more appropriate section without notice. Violating community policies may result in suspension of community or other privileges and/or account termination.
            </p>


        </div>
    </div>
</div>

<?php
$this->end();
?>

