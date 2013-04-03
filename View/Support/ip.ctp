<?php
$this->Html->scriptBlock('mixpanel.track("Support. ip load");', array('inline'=>false));
$this->extend('/Support/common/common');
$this->start('main');
?>

<div class="cont-span6 cbox-space fullwidth">
    <div class="fullwidth pull-left space17">
        <div class="form-first justify" id="contact-area">
            <p>
                <strong>
                    By agreeing to the Terms of Use, you agree to this Copyright and Intellectual Property Policy.  We may modify Copyright and Intellectual Property Policy at any time by posting the revised terms at:
                    <?php echo $this->Html->link(Configure::read('public_domain').'/Support/copyright', Configure::read('public_domain').'/Support/copyright') ?>
                    .  All changes are effective immediately when we post them.  We will notify you through the Universito Messaging and/or via email of any material changes to the Copyright and Intellectual Property Policy.  You are required to agree to the changes to the Copyright and Intellectual Property Policy prior to further use of the Universito website.  If you do not indicate your agreement, your access to the website features and functions may be limited or terminated.
                </strong>
            </p>

            <p class="space37">
                The address of Universito's Designated Agent to Receive Notification of Claimed Infringement ("Designated Agent") is listed at the end of this policy.
            </p>
            <p class="space37">
                Universito may act expeditiously to respond to a proper notice by (1) removing or disabling access to material claimed to be subject of infringing activity; and (2) removing and discontinuing service to repeat offenders. If Universito removes or disables access in response to such a notice, Universito will make a good-faith attempt to contact the allegedly infringing party ("Member") so that they may make a counter notification.
            </p>



            <p class="space32">
                <strong>
                    A. Procedure for Reporting Copyright or Intellectual Property Infringements:
                </strong>
            </p>
            <p class="space37">
                If you materially misrepresent that content or tutoring activity is infringing your intellectual property, you may be liable for damages (including costs and attorneys' fees). Therefore, if you are not sure whether the material infringes upon your intellectual property, please contact an attorney before contacting Universito.
            </p>
            <p class="space37">
                If you believe that material residing on or accessible through the Universito web site or service infringes a copyright or other intellectual property right, to provide Universito of notice of such infringement, you must send a written notice of the infringement to the Designated Agent listed below. Please specify the type of infringement at issue and the notice must include the following information:
            </p>
            <ul class="decimal-list space26">
                <li>A physical or electronic signature of a person authorized to act on behalf of the owner of the copyright or intellectual property right that has been allegedly infringed upon (by fax or regular mail – not by email, except by prior agreement);</li>
                <li>Identification in sufficient detail of the material being infringed upon (for an allegation of a patent infringement, please provide a patent number);</li>
                <li>Identification of the material that is claimed to be infringing upon the intellectual property. Include information regarding the location of the infringing material with sufficient detail so that Universito is capable of finding and verifying its existence;</li>
                <li>Contact information about the notifier including the name of the intellectual property owner, the name and title of the person contacting Universito on the owner's behalf, the address, telephone number and, if available, e-mail address;</li>
                <li>A statement that the notifier has a good faith belief that the material is not authorized by the intellectual property or copyright owner, its agent, or the law; and</li>
                <li>A statement made under penalty of perjury that the information provided is accurate and the notifying party is authorized to make the complaint on behalf of the intellectual property or copyright owner.</li>
            </ul>



            <p class="space32">
                <strong>
                    B. Removal of Allegedly Infringing Material
                </strong>
            </p>
            <p class="space37">
                When removing material from the site, Universito will make reasonable attempts to inform the Member of the removal, the reason for the removal.
            </p>
            <p class="space37">
                Once Proper Bona Fide Infringement Notification is received by the Designated Agent, Universito may remove or disable access to the material infringing upon the intellectual property. If Universito removes or disables access to content in response to an infringement notice, Universito will make reasonable attempts to notify the Member that Universito has removed or disabled access to the material. Repeat offenders will have all material removed from the system and Universito will terminate such Members’ access to the service.
            </p>



            <p class="space37">
                Please contact Universito’s Designated Agent to submit Notifications of Claimed Infringement and Copyright Counter-Notices at the following address below:
            </p>
            <p class="space37">
                <u>Designated Agent to Receive Notification of Claimed Infringement.</u>
            </p>
            <p class="space37">
                Universito, Inc.
                Attn: Legal;
                Emma Tauber, 8/11 Herzliya,
                Israel
                E-mail: legal@universito.com
            </p>
        </div>
    </div>
</div>

<?php
$this->end();
?>

