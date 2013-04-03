<?php
$this->Html->scriptBlock('mixpanel.track("Support. termsOfUse load");', array('inline'=>false));
$this->extend('/Support/common/common');
$this->start('main');
?>

<div class="cont-span6 cbox-space fullwidth">
    <div class="fullwidth pull-left space17">
        <div class="form-first justify" id="contact-area">
            <p>
                <strong>
                    You are required to agree to the Terms of Use prior to use of the Universito website. We may modify Terms of Use at any time by posting the revised terms at:
                    <?php echo $this->Html->link(Configure::read('public_domain').'/Support/termsAndConditions', Configure::read('public_domain').'/Support/termsAndConditions') ?>
                    All changes are effective immediately when we post them.  We will notify you through the Universito Messaging and/or via email of any material changes to the Terms of Use.  You are required to agree to the changes to the Terms of Use prior to further use of the Universito website.  If you do not indicate your agreement, your access to the website features and functions may be limited or terminated.
                </strong>
            </p>

            <p class="space37">
                PLEASE READ THESE TERMS OF USE ("AGREEMENT" OR "TERMS OF USE") CAREFULLY BEFORE USING THE WEBSITE AND SERVICES OFFERED BY UNIVERSITO, INC. ("UNIVERSITO"). THIS AGREEMENT SETS FORTH THE LEGALLY BINDING TERMS AND CONDITIONS FOR YOUR USE OF THE WEBSITE AT <?php echo $this->Html->link(Configure::read('public_domain'), Configure::read('public_domain')) ?> (THE "SITE"), AND ALL SERVICES PROVIDED BY UNIVERSITO ON THE SITE.
            </p>

            <p class="space37">
                By clicking to accept or agree to the Terms of Use when this option is made available to you or by using the Site in any manner, including but not limited to visiting or browsing the Site, you (the "member," "you," “student,” “tutor,” “buyer,” or “seller”) agree to be bound by this Agreement, including those additional terms and conditions and policies referenced herein and/or available by hyperlink. This Agreement applies to all members of the Site, including without limitation members who are vendors, customers, merchants, contributors of content, information and other materials or services on the Site.
            </p>

            <p class="space37">
                If you have any questions, please refer to the <?php echo $this->Html->link('Contact page', array('controller'=>'Support', 'action'=>'contact')); ?>.
            </p>




            <p class="space32">
                <strong>
                    1. Universito is a Marketplace
                </strong>
            </p>
            <p class="space37">
                Universito acts as a marketplace to allow members who comply with Universito's policies to offer, sell or buy tutoring services at rates set by the tutor. Universito is not directly involved in the transaction between students and tutors. As a result, Universito has no control over the truth and accuracy, quality, safety, morality or legality of any aspect of the services offered by members and member generated content. Universito does not pre-screen members or the content or information provided by members.
            </p>
            <p class="space37">
                Universito cannot guarantee the true identity, age, capabilities, and nationality of a member. Universito encourages members to review a tutors profile, recommendations, and ratings through the tools available on the Site.
            </p>
            <p class="space37">
                You agree that Universito is a marketplace and as such is not responsible or liable for any content, for example, data, text, information, member names, graphics, images, photographs, profiles, audio, video, items, and links posted by members, other members, or outside parties on Universito. You use the Universito service at your own risk.
            </p>



            <p class="space32">
                <strong>
                    2. Membership Eligibility
                </strong>
            </p>
            <p class="space37">
                <strong>Age:</strong> Universito's services are available only to, and may only be used by, individuals who are 18 years and older who can form legally binding contracts under applicable law. You represent and warrant that you are at least 18 years old and that all registration information you submit is accurate and truthful. Universito may, in its sole discretion, refuse to offer access to or use of the Site to any person or entity and change its eligibility criteria at any time. This provision is void where prohibited by law and the right to access the Site is revoked in such jurisdictions.
            </p>
            <p class="space37">
                Individuals under the age of 18 must at all times use Universito's services only in conjunction with and under the supervision of a parent or legal guardian who is at least 18 years of age. In this all cases, members must be at least 18 years, the adult is the member, and is responsible for any and all activities.
            </p>
            <p class="space37">
                <strong>Compliance:</strong> You agree to comply with all local laws regarding online conduct and acceptable content. The Member is responsible for all applicable taxes. In addition, you must abide by Universito's policies as stated in the Agreement and the Universito policy documents listed below (if applicable to your activities on or use of the Site) as well as all other operating rules, policies and procedures that may be published from time to time on the Site by Universito, each of which is incorporated herein by reference and each of which may be updated by Universito from time to time:
            </p>
            <ul class="disc-list space26">
                <li><?php echo $this->Html->link('The DOs & DON\'Ts', array('controller'=>'Support', 'action'=>'dosAndDonts')); ?> of Universito;</li>
                <li><?php echo $this->Html->link('Privacy Policy', array('controller'=>'Support', 'action'=>'privacyAndPolicy')); ?>; and</li>
                <li><?php echo $this->Html->link('Copyright and Intellectual Property Policy', array('controller'=>'Support', 'action'=>'ip')); ?>.</li>
            </ul>
            <p class="space37">
                In addition, some services offered through the Site may be subject to additional terms and conditions promulgated by Universito from time to time; your use of such services is subject to those additional terms and conditions, which are incorporated into this Agreement by this reference.
            </p>
            <p class="space37">
                <strong>Password:</strong> Keep the password you select secure. You are fully responsible for all activity, liability and damage resulting from your failure to maintain password confidentiality. You agree to immediately notify Universito of any unauthorized use of the password you select or any breach of security. You also agree that Universito cannot and will not be liable for any loss or damage arising from your failure to keep the password secure. You agree not to provide the member name and password information in combination to any other party other than Universito without Universito's express written permission.
            </p>

            <p class="space37">
                <strong>Account Information:</strong> You must keep your account information up-to-date and accurate at all times, including a valid email address. To sell items on Universito you must provide and maintain valid payment information such as a valid PayPal account.
            </p>
            <p class="space37">
                <strong>Account Transfer:</strong>  Universito account and Member ID are not transferable by sale, gift, or otherwise to another party. If your account is registered to a business entity, you personally guarantee that you have the authority to bind the entity to this Agreement.
            </p>
            <p class="space37">
                <strong>Right to Refuse Service:</strong> Universito's services are not available to temporarily or indefinitely suspended Universito members. Universito reserves the right, in Universito's sole discretion, to cancel unconfirmed or inactive accounts. Universito reserves the right to refuse service to anyone, for any reason, at any time.
            </p>



            <p class="space32">
                <strong>
                    3. Universito Fees
                </strong>
            </p>
            <p class="space37">
                Universito charges fees based on a percentage of the tutoring fees earned by a tutor when tutoring services are provided. Universito charges the tutor a fee of up to <u>25%</u> of the tutoring fees that tutors earn.  Members have the opportunity to earn IQ points that will make them eligible for reduction in fees. Universito may, at Universito's sole discretion, change some or all of Universito's services at any time. In the event Universito introduces a new service, the fees for that service will be clearly posted and are effective at the launch of the service. Unless otherwise stated, all fees are quoted in US Dollars (USD).
            </p>



            <p class="space32">
                <strong>
                    4.Credit Points
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
                    5. Payment for Tutors
                </strong>
            </p>
            <p class="space37">
                <strong>Live/Scheduled Sessions:</strong>  Right after a student books a session with a tutor, Universito will charge student's account with the appropriate number of Credit Points according to the tutor's rate. This number of Credit Points will be put on hold for at least <u>72 hours</u> after the session finishes.  If there is no refund request within <u>72 hours</u> from the time the session finishes, Universito will credit the tutor the Credit Points after deducting the Universito fee.
            </p>
            <p class="space37">
                <strong>Video/Recorded Sessions:</strong>  Right after a student watch a video session for the first time, Universito will charge student's account with the appropriate number of Credit Points according to the tutor's rate. This number of Credit Points will be put on hold for at least <u>72 hours</u>.  If there is no refund request within <u>72 hours</u> from the time the student watched the video session, Universito will credit the tutor the Credit Points after deducting the Universito fee.
            </p>



            <p class="space32">
                <strong>
                    6. Cash Withdraw Policy
                </strong>
            </p>
            <p class="space37">
                A member can make a withdraw request if his or her account has a balance of greater than 5 Credit Points.  Universito will process the withdraw request for payment within 14 business day from the time a request is received by issuing a check to the requesting member at the mailing address provided by the member.  A confirmation will be sent to the member after the check has been processed and mailed.
            </p>




            <p class="space32">
                <strong>
                    7. Guarantee and Tutor Session Refunds
                </strong>
            </p>
            <p class="space37">
                Universito believes in a Satisfaction Guaranteed policy.  A student's first session with any new tutor is 100% satisfaction guaranteed by the tutor <u>72 hours</u> from the time the first session is completed.   If you sell tutoring services, you agree that Universito is authorized to arbitrate and mediate refund requests related to any tutoring sessions that you provide.  Universito, in its sole discretion, can accept or reject a Tutor Session Refund request.
            </p>

            <p class="space37">
                If a student feels that they did not get help – they have <u>72 hours</u> from the completion of a tutoring session to log a dispute and initiate a Tutor Session Refund.  Within <u>48 hours</u> of a request Universito will review and declare a judgment on the Tutor Session Refund request.  It a refund is granted, Universito will issue a credit to the student for fees related to a Tutor Session Refund and debit the tutor's account if needed for the amount refunded (i.e.The tutor will not be paid for the session.).
            </p>

            <p class="space37">
                Members who use the Tutor Session Refund privilege multiple times  may be subject to review, which can result in suspension of account privileges and/or termination, at Universito's sole discretion.
            </p>

            <p class="space37">
                Tutors and student are responsible for paying all fees and applicable taxes associated with using Universito. Universito keeps accepted payment information on file.  Tutors have access to a report on sessions provided and fees earned.
            </p>



<?php
            /*<p class="space32">
                <strong>
                    8. Reporting obligations
                </strong>
            </p>
            <p class="space37">
                Since the 2011 tax year, Internal Revenue Service (“IRS”) regulations require that U.S. third-party settlement organizations and payment processors, including Universito, file Form 1099-K to report unadjusted annual gross sales information for sellers that meet both of the following thresholds in a calendar year (reporting): 1.) More than $20,000 in gross sales and 2.) More than 200 transactions.
            </p>
            <p class="space37">
                As a result of these regulations, tutors who approach exceed $15,000 in gross sales or 150 tutoring sessions in a calendar year will be required by Universito to provide taxpayer-identification information and will be notified via email and through other communication channels available on Universito.
            </p>*/
?>


            <p class="space32">
                <strong>
                    8. Fees and Termination
                </strong>
            </p>
            <p class="space37">
                If you close your account, have your privileges suspended and/or your account is terminated, you remain obligated to pay Universito for all unpaid fees plus any penalties, if applicable. Universito retains its rights to use all means necessary to collect fees dues including retaining collection agencies and legal counsel. If you have a question or wish to dispute a charge, contact Universito.  Upon closure of a member account or termination of a member account, once all fees are paid, if due, Universito will pay out any remaining account balance to the member.
            </p>



            <p class="space32">
                <strong>
                    9. Prohibited, Questionable and Infringing Items and Activities
                </strong>
            </p>
            <p class="space37">
                You are solely responsible for your conduct and activities on and regarding to Universito and any and all data, text, information, member names, graphics, images, photographs, profiles, audio, video, items, and links (together, "content") that you share, submit, post, and display on Universito.
            </p>
            <p class="space37">
                Restricted Activities: You agree that your content and your use of Universito shall not:
            </p>
            <ul class="decimal-list space26">
                <li>Be false, inaccurate or misleading</li>
                <li>Be fraudulent or involve the sale of illegal, counterfeit or stolen items</li>
                <li>Infringe upon any third-party's copyright, patent, trademark, trade secret or other proprietary or intellectual property rights or rights of publicity or privacy (see also, Universito's <?php echo $this->Html->link('Copyright and Intellectual Property Policy', array('controller'=>'Support', 'action'=>'copyright')); ?>)</li>
                <li>Violate this Agreement, The <?php echo $this->Html->link('The DOs & DON\'Ts', array('controller'=>'Support', 'action'=>'dosAndDonts')); ?> of Universito, any site policy or community guidelines, or any applicable law, statute, ordinance or regulation (including, but not limited to, those governing export control, consumer protection, unfair competition, anti-discrimination or false advertising)</li>
                <li>Be defamatory, trade libelous, unlawfully threatening, unlawfully harassing, impersonate or intimidate any person (including Universito staff or other members), or falsely state or otherwise misrepresent your affiliation with any person, through for example, the use of similar email address, nicknames, or creation of false account(s) or any other method or device</li>
                <li>Be obscene or contain child pornography</li>
                <li>Contain or transmit any code of a destructive nature that may damage, detrimentally interfere with, surreptitiously intercept or expropriate any system, data or personal information</li>
                <li>Modify, adapt or hack Universito or modify another website so as to falsely imply that it is associated with Universito;</li>
                <li>Appear to create liability for Universito or cause Universito to lose (in whole or in part) the services of Universito's ISPs or other suppliers</li>
            </ul>
            <p class="space37">
                Furthermore, you may not consummate any transaction that was initiated using Universito's service that, by paying to Universito the service fee, could cause Universito to violate any applicable law, statute, ordinance or regulation, or that violates the Terms of Use.
            </p>



            <p class="space32">
                <strong>
                    10. Content
                </strong>
            </p>
            <p class="space37">
                <strong>License:</strong> Universito does not claim ownership rights in the content created, submitted or posted by you (hereinafter “Your Content”). You grant Universito the right to use Your Content for the sole purpose and only to the extent necessary for Universito to provide to you services you request and in unidentifiable form in aggregated data format, a license solely to enable Universito to use any information or content you supply Universito with, so that Universito is not violating any rights you might have in that content. You agree to allow Universito to store or re-format your content on Universito and display your content on Universito in any way as Universito chooses, provided no personally identifiable data is displayed with the content. Universito will only use personal information in accordance with Universito's <?php echo $this->Html->link('Privacy & Policy', array('action'=>'privacyAndPolicy')) ?>.
            </p>
            <p class="space37">
                As part of a transaction, you may obtain personal information, including email address and other information, from another Universito member. Without obtaining prior permission from the other member, this personal information shall only be used for that transaction or for Universito-related communications. Neither Universito nor the owner of the information has granted you a license to use the information for unsolicited commercial messages. Further, without express consent from the member, you are not licensed to add any Universito member to your email or physical mail list. For more information, see Universito's <?php echo $this->Html->link('Privacy & Policy', array('action'=>'privacyAndPolicy')) ?>.
            </p>
            <p class="space37">
                <strong>Re-Posting Content:</strong> By posting content on Universito, it is possible for an outside website or a third party to re-post that content. You agree to hold Universito harmless for any dispute concerning this use. If you choose to display your own Universito-hosted image on another website, the image must provide a link back to its listing page on Universito.
            </p>
            <p class="space37">
                <strong>Idea Submissions:</strong> But for information that personally identifies the member, Universito considers any unsolicited suggestions, ideas, proposals or other material submitted to it by members via the Site or otherwise (other than the content and the tangible items sold on the Site by members) (collectively, the "Material") to be non-confidential and non-proprietary, and Universito shall not be liable for the disclosure or use of such Material. If, at Universito's request, any member sends Material to improve the site (for example through the Forums or to customer support), Universito will also consider that Material to be non-confidential and non-proprietary and Universito will not be liable for use or disclosure of the Material. Any communication by you to Universito is subject to this Agreement. You hereby grant and agree to grant Universito, under all of your rights in the Material, a worldwide, non-exclusive, perpetual, irrevocable, royalty-free, fully-paid, sublicensable and transferable right and license to incorporate, use, publish and exploit such Material for any purpose whatsoever, commercial or otherwise, including but not limited to incorporating it in the API, documentation, or any product or service, without compensation or accounting to you and without further recourse by you, provided the material does not include any personally identifying information of the member.
            </p>



            <p class="space32">
                <strong>
                    11. Information Control
                </strong>
            </p>
            <p class="space37">
                Universito does not control the content provided by members that is made available on Universito. You may find some content to be offensive, harmful, inaccurate, or deceptive. There are also risks of dealing with underage persons or people acting under false pretense.
            </p>
            <p class="space37">
                By using Universito, you agree to accept such risks and that Universito (and Universito's officers, directors, agents, subsidiaries, joint ventures and employees) is not responsible for any and all acts or omissions of members on Universito. Please use caution, common sense, and practice safe buying and selling when using Universito.
            </p>
            <p class="space37">
                <strong>Other Resources:</strong> Universito is not responsible for the availability of outside websites or resources linked to or referenced on the Site. Universito does not endorse and is not responsible or liable for any content, advertising, products, or other materials on or available from such websites or resources. You agree that Universito shall not be responsible or liable, directly or indirectly, for any damage or loss caused or alleged to be caused by or in connection with the use of or reliance on any such content, goods or services available on or through any such websites or resources.
            </p>



            <p class="space32">
                <strong>
                    12. Meetings
                </strong>
            </p>
            <p class="space37">
                Members are solely responsible for interactions with others. Universito is not involved with user generated groups, the groups' requirements, or the Meetings.
            </p>
            <p class="space37">
                Universito does not supervise or control any member Meetings, member-initiated online or offline gatherings, or interactions among and between members and other persons or companies. Members are solely responsible for interactions with others. Members understand that Universito does not in any way screen its members. All members are strongly urged to <u>exercise caution and use good judgment</u> in all interactions with others, particularly if meeting offline or in person.
            </p>



            <p class="space32">
                <strong>
                    13. Authorization to Contact You; Recording Calls
                </strong>
            </p>
            <p class="space37">
                You authorize Universito, its affiliates, agents, and independent contractors to contact you at any telephone number (including telephone numbers associated with mobile, cellular, wireless, or similar devices) you provide to us or from which you place a call to us, or any telephone number at which we reasonably believe we may reach you, using any means of communication, including, but not limited to, calls or text messages using an automatic telephone dialing system and/or prerecorded messages, even if you incur charges for receiving such communications.
            </p>
            <p class="space37">
                You understand and agree that Universito may, without further notice or warning and in its discretion, monitor or record telephone conversations you or anyone acting on your behalf has with Universito or its agents for quality control and training purposes or for its own protection.  You acknowledge and understand thatnot all telephone lines or calls may be recorded by Universito, and Universito does not guarantee that recordings of any particular telephone calls will be retained or retrievable.
            </p>



            <p class="space32">
                <strong>
                    14. Resolution of Disputes and Release
                </strong>
            </p>
            <p class="space37">
                In the event a dispute arises between you and Universito, please contact legal@universito.com.
            </p>
            <p class="space37">
                All disputes arising under this Agreement or out of the parties' relationship shall first be submitted to mediation in accordance with the Israeli Arbitration Association's Rules for Commercial Mediation then in effect.  If the parties are unable to resolve their dispute through mediation, the dispute shall be resolved exclusively through final and binding arbitration before a single arbitrator in accordance with the Israeli Arbitration Association's Rules for Commercial Arbitration then in effect.  All arbitrations shall be held in Israel, Tel-Aviv.  The Arbitrator shall award the prevailing party his or its reasonable attorneys' fees, expert witness fees and all other related expenses incurred in connection therewith.  Notwithstanding the foregoing, all disputes arising from breaches of <u>confidentiality or intellectual property infringement</u> shall not be submitted to mediation or arbitration, but instead shall be submitted to a court of competent jurisdiction.  The prevailing party in such litigation shall be awarded his or its reasonable attorneys' fees, expert witness fees and all other related expenses incurred in connection therewith.
            </p>
            <p class="space37">
                Judgment upon any award so rendered may be entered in a court having jurisdiction or application may be made to such court for judicial acceptance of any award and an order of enforcement, as the case may be. Notwithstanding the foregoing, each party shall have the right to institute an action in a court of proper jurisdiction for injunctive or other equitable relief pending a final decision by the arbitrator. For all purposes of this Agreement, the parties consent to exclusive jurisdiction and venue in the District Court of Tel-Aviv, Israel. Use of the Site is not authorized in any jurisdiction that does not give effect to all provisions of the Agreement, including without limitation, this section. You and Universito agree that any cause of action arising out of or related to the Site (including, but not limited to, any services provided or made available therein) or this Agreement must commence within one (1) year after the cause of action arose; otherwise, such cause of action is permanently barred.
            </p>
            <p class="space37">
                Should you have a dispute with one or more members, or an outside party, you release Universito (and Universito's officers, directors, agents, subsidiaries, joint ventures and employees) from any and all claims, demands and damages (actual and consequential) of every kind and nature, known and unknown, suspected and unsuspected, disclosed and undisclosed, arising out of or in any way connected with such disputes. Universito encourages members to report member-to-member disputes to your local law enforcement or a certified mediation or arbitration entity, as applicable.
            </p>
            <p class="space37">
                Universito, for the benefit of members, may try to help members resolve disputes. Universito does so in Universito's sole discretion, and Universito has no obligation to resolve disputes between members or between members and outside parties. To the extent that Universito attempts to resolve a dispute, Universito will do so in good faith based solely on Universito's policies. Universito will not make judgments regarding legal issues or claims.
            </p>



            <p class="space32">
                <strong>
                    15. Universito's Intellectual Property
                </strong>
            </p>
            <p class="space37">
                UNIVERSITO, and other Universito graphics, logos, designs, page headers, button icons, scripts, and service names are registered trademarks, trademarks or trade dress of UNIVERSITO, INC. in the U.S. and/or other countries. Universito's trademarks and trade dress may not be used, including as part of trademarks and/or as part of domain names or email addresses, in connection with any product or service in any manner that is likely to cause consumer confusion.  Any effort to decompile, disassemble or reverse engineer all or any part of Universito, its websites or applications in order to identify, acquire, copy or emulate any source code or object code is expressly prohibited.
            </p>



            <p class="space32">
                <strong>
                    16. Access and Interference
                </strong>
            </p>
            <p class="space37">
                Universito may contain robot exclusion headers which contain internal rules for software usage. Much of the information on Universito is updated on a real-time basis and is proprietary or is licensed to Universito by Universito's members or third-parties. You agree that you will not use any robot, spider, scraper or other automated means to access Universito for any purpose whatsoever.  Additionally, you agree that you will not:
            </p>
            <ul class="decimal-list space26">
                <li>Take any action that imposes, or may impose, in Universito's sole discretion, an unreasonable or disproportionately large load on Universito's infrastructure;</li>
                <li>Copy, reproduce, modify, create derivative works from, distribute or publicly display any member content (except for your content) or other allowed uses as set out in The <?php echo $this->Html->link('The DOs & DON\'Ts', array('controller'=>'Support', 'action'=>'dosAndDonts')); ?> of Universito from the Site or otherwise without the prior express written permission of Universito and the appropriate third party, as applicable;</li>
                <li>Interfere or attempt to interfere with the proper working of the Site or any activities conducted on the Site; and</li>
                <li>Bypass Universito's robot exclusion headers or other measures Universito may use to prevent or restrict access to Universito.</li>
            </ul>


            <p class="space32">
                <strong>
                    17. Breach
                </strong>
            </p>
            <p class="space37">
                Without limiting any other remedies, Universito may, without notice, and without refunding any fees, delay or immediately remove content, warn Universito's community of a member's actions, issue a warning to a member, temporarily suspend a member, temporarily or indefinitely suspend a member's account privileges, terminate a member's account, prohibit access to the Site, and take technical and legal steps to keep a member off the Site and refuse to provide services to a member if any of the following apply:
            </p>
            <p class="space37">
                Universito suspects (by information, investigation, conviction, settlement, insurance or escrow investigation, or otherwise) a member has breached this Agreement, the Privacy Policy, The <?php echo $this->Html->link('The DOs & DON\'Ts', array('controller'=>'Support', 'action'=>'dosAndDonts')); ?> Policy, or other policy documents and community guidelines incorporated herein; Universito is unable to verify or authenticate any of your personal information or content; or Universito believes that a member is acting inconsistently with the letter or spirit of Universito's policies, has engaged in improper or fraudulent activity in connection with Universito or the actions may cause legal liability or financial loss to Universito's members or to Universito.
            </p>
            <p class="space37">
                Funds available in a member's account and any Credit Points account balance may be forfeited upon breach of the Terms of Use.
            </p>



            <p class="space32">
                <strong>
                    18. Privacy
                </strong>
            </p>
            <p class="space37">
                Except as provided in Universito's Privacy Policy Universito will not sell or disclose your personal information, (as defined in the Privacy Policy) to third parties without your explicit consent. Universito stores and processes content on computers that are commercially reasonably protected by physical as well as technological security.
            </p>



            <p class="space32">
                <strong>
                    19. No Warranty
                </strong>
            </p>
            <p class="space37">
                UNIVERSITO, UNIVERSITO'S SUBSIDIARIES, OFFICERS, DIRECTORS, EMPLOYEES, AND UNIVERSITO'S SUPPLIERS PROVIDE UNIVERSITO'S WEB SITE AND SERVICES "AS IS" AND WITHOUT ANY WARRANTY OR CONDITION, EXPRESS, IMPLIED OR STATUTORY. UNIVERSITO, UNIVERSITO'S SUBSIDIARIES, OFFICERS, DIRECTORS, EMPLOYEES AND UNIVERSITO'S SUPPLIERS SPECIFICALLY DISCLAIM ANY IMPLIED WARRANTIES OF TITLE, MERCHANTABILITY, PERFORMANCE, FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT. IN ADDITION, NO ADVICE OR INFORMATION (ORAL OR WRITTEN) OBTAINED BY YOU FROM UNIVERSITO SHALL CREATE ANY WARRANTY. SOME STATES DO NOT ALLOW THE DISCLAIMER OF IMPLIED WARRANTIES, SO THE FOREGOING DISCLAIMER MAY NOT APPLY TO YOU. THIS WARRANTY GIVES YOU SPECIFIC LEGAL RIGHTS AND YOU MAY ALSO HAVE OTHER LEGAL RIGHTS THAT VARY FROM STATE TO STATE.
            </p>



            <p class="space32">
                <strong>
                    20. Liability Limit
                </strong>
            </p>
            <p class="space37">
                IN NO EVENT SHALL UNIVERSITO, AND (AS APPLICABLE) UNIVERSITO'S SUBSIDIARIES, OFFICERS, DIRECTORS, EMPLOYEES OR UNIVERSITO'S SUPPLIERS BE LIABLE FOR ANY DAMAGES WHATSOEVER, WHETHER DIRECT, INDIRECT, GENERAL, SPECIAL, COMPENSATORY, CONSEQUENTIAL, AND/OR INCIDENTAL, ARISING OUT OF OR RELATING TO THE CONDUCT OF YOU OR ANYONE ELSE IN CONNECTION WITH THE USE OF THE SITE, UNIVERSITO'S SERVICES, OR THIS AGREEMENT, INCLUDING WITHOUT LIMITATION, LOST PROFITS, BODILY INJURY, EMOTIONAL DISTRESS, OR ANY SPECIAL, INCIDENTAL OR CONSEQUENTIAL DAMAGES.
            </p>
            <p class="space37">
                UNIVERSITO'S LIABILITY, AND (AS APPLICABLE) THE LIABILITY OF UNIVERSITO'S SUBSIDIARIES, OFFICERS, DIRECTORS, EMPLOYEES, AND SUPPLIERS, TO YOU OR ANY THIRD PARTIES IN ANY CIRCUMSTANCE IS LIMITED TO THE GREATER OF (A) THE AMOUNT OF FEES YOU PAY TO UNIVERSITO IN THE 12 MONTHS PRIOR TO THE ACTION GIVING RISE TO LIABILITY. SOME STATES DO NOT ALLOW THE EXCLUSION OR LIMITATION OF INCIDENTAL OR CONSEQUENTIAL DAMAGES, SO THE ABOVE LIMITATION OR EXCLUSION MAY NOT APPLY TO YOU.
            </p>



            <p class="space32">
                <strong>
                    21. Indemnity
                </strong>
            </p>
            <p class="space37">
                YOU AGREE TO INDEMNIFY AND HOLD UNIVERSITO AND (AS APPLICABLE) UNIVERSITO'S PARENT, SUBSIDIARIES, AFFILIATES, OFFICERS, DIRECTORS, AGENTS, AND EMPLOYEES, HARMLESS FROM ANY CLAIM OR DEMAND, INCLUDING REASONABLE ATTORNEYS' FEES, MADE BY ANY THIRD PARTY DUE TO OR ARISING OUT OF YOUR BREACH OF THIS AGREEMENT OR THE DOCUMENTS IT INCORPORATES BY REFERENCE, OR YOUR VIOLATION OF ANY LAW OR THE RIGHTS OF A THIRD PARTY.
            </p>



            <p class="space32">
                <strong>
                    22. No Guarantee
                </strong>
            </p>
            <p class="space37">
                Universito does not guarantee continuous, uninterrupted access to the Site, and operation of the Site may be interfered with by numerous factors outside Universito's control.
            </p>



            <p class="space32">
                <strong>
                    23. Legal Compliance; Taxes
                </strong>
            </p>
            <p class="space37">
                You shall comply with all applicable domestic and international laws, statutes, ordinances and regulations regarding your use of the Site and any Universito service and, if applicable, the purchase of tutoring services and sale and the delivery of tutoring services. In addition, you shall be responsible for paying any and all taxes applicable to any purchases or sales of tutoring services you make on the Site (excluding any taxes on Universito's net income).
            </p>



            <p class="space32">
                <strong>
                    24. Severability
                </strong>
            </p>
            <p class="space37">
                If any provision of this Agreement is held unenforceable, then such provision will be modified to reflect the parties' intention. All remaining provisions of this Agreement shall remain in full force and effect.
            </p>



            <p class="space32">
                <strong>
                    25. No Agency
                </strong>
            </p>
            <p class="space37">
                You and Universito are independent contractors, and no agency, partnership, joint venture, employee-employer or franchiser-franchisee relationship is intended or created by this Agreement.
            </p>



            <p class="space32">
                <strong>
                    26. Universito Service
                </strong>
            </p>
            <p class="space37">
                Universito reserves the right to modify or terminate the Universito service for any reason, without notice, at any time. Universito reserves the right to alter these Terms of Use or other Site policies at any time. If Universito makes a material change Universito will notify you, by a system message, by email, by means of a notice on our website, or other places Universito deems appropriate. What constitutes a "material change" will be determined at Universito's sole discretion, in good faith, and using common sense and reasonable judgment.
            </p>



            <p class="space32">
                <strong>
                    27. Choice of Law
                </strong>
            </p>
            <p class="space37">
                This Agreement shall in all respects be interpreted and construed with and by the laws of the State of Israel, excluding its conflicts of laws rules. Venue of any action brought to enforce or relating to this Agreement shall be brought exclusively in the District Court of Tel-Aviv, Israel.
            </p>



            <p class="space32">
                <strong>
                    28. Survival
                </strong>
            </p>
            <p class="space37">
                Sections 3 (Universito Fees), 8 (Fees and Termination), 10 (Content, License), 11 (Information Control), 12 (Meetings), 14 (Resolution of Dispute and Release), 15 (Universito's Intellectual Property), 17 (Breach), 18 (Privacy), 19 (No Warranty), 20 (Liability Limit), 21 (Indemnity), 24 (Severability), 25 (No Agency), 27 (Choice of Law) shall survive any termination or expiration of this Agreement.
            </p>



            <p class="space32">
                <strong>
                    29. Notices
                </strong>
            </p>
            <p class="space37">
                Except as explicitly stated otherwise, any notices shall be given by postal mail to Universito; Attn: Legal; Emma Tauber, 8/11 Herzliya, Israel (in the case of Universito) or, in your case, to the email address you provide to Universito (either during the registration process or when your email address changes). Notice shall be deemed given 24 hours after email is sent, unless the sending party is notified that the email address is invalid. Alternatively, Universito may give you notice by certified mail, postage prepaid and return receipt requested, to the address provided to Universito. In such case, notice shall be deemed given three days after the date of mailing.
            </p>
            <p class="space37">
                For issues with intellectual property, please provide the notice as specified in Universito's <?php echo $this->Html->link('Copyright and Intellectual Property Policy', array('controller'=>'Support', 'action'=>'copyright')); ?>.
            </p>



            <p class="space32">
                <strong>
                    30. Entire Agreement
                </strong>
            </p>
            <p class="space37">
                The Terms of Use, Policy Documents and any link pages constitute the sole and entire agreement between you and Universito with respect to the Website and supersede all prior and contemporaneous understandings, agreements, representations and warranties, both written and oral, with respect to the Website.
            </p>


        </div>
    </div>
</div>

<?php
$this->end();
?>

