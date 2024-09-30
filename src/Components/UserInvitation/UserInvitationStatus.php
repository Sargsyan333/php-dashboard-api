<?php

namespace Riconas\RiconasApi\Components\UserInvitation;

enum UserInvitationStatus: string {
    case NOT_SENT = 'not_sent';
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
}