<?php

namespace Riconas\RiconasApi\Mailing;

use Riconas\RiconasApi\Integrations\Mailgun\MailgunClient;

class MailingService
{
    private MailgunClient $client;

    private string $mailTemplatesPath = __DIR__ . '/../../mail_templates';

    public function __construct(MailgunClient $client)
    {
        $this->client = $client;
    }

    public function sendPasswordRecoveryEmail(
        string $recipientEmailAddress,
        string $languageCode,
        string $passwordResetLink
    ): void {
        // TODO TMP replace recipient email address with hardcoded one. This will be removed with account upgrade
        $recipientEmailAddress = 'developer.hovakimyan@gmail.com';

        $dirPath = "{$this->mailTemplatesPath}/password_recovery/{$languageCode}";

        $this->client->sendEmail(
            $recipientEmailAddress,
            'Recover your password',
            file_get_contents("{$dirPath}/template.txt"),
            file_get_contents("{$dirPath}/template.html"),
            [
                'resetPasswordLink' => $passwordResetLink,
            ],
        );
    }

    public function sendCoworkerInvitationEmail(
        string $recipientEmailAddress,
        string $languageCode,
        string $invitationLink
    ): void {
        // TODO TMP replace recipient email address with hardcoded one. This will be removed with account upgrade
        $recipientEmailAddress = 'developer.hovakimyan@gmail.com';

        $dirPath = "{$this->mailTemplatesPath}/coworker_invitation/{$languageCode}";

        $this->client->sendEmail(
            $recipientEmailAddress,
            'Invitation to collaborate on Riconas',
            file_get_contents("{$dirPath}/template.txt"),
            file_get_contents("{$dirPath}/template.html"),
            [
                'invitationLink' => $invitationLink,
            ],
        );
    }
}