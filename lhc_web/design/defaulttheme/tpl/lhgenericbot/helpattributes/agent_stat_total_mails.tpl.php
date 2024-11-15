Represents number of mail messages assigned to the operator. These includes
<ul>
    <li>0 - Unresponded
        <ul>
            <li>New messages received by system from visitors is set to this status. They remain in this status untill operator. [RESPONSE_UNRESPONDED]
                <ul>
                    <li>Operator replies to message by writing response. In that scenario message changes status to (3) [RESPONSE_NORMAL]</li>
                    <li>Operator replies to message by choosing No reply required option or closes conversation. In that scenario message changes status to (1) [RESPONSE_NOT_REQUIRED]</li>
                </ul>
            </li>
        </ul>
    </li>
    <li>
        (1) - No reply required
        <ul>
            <li>Sometimes visitor replies to our e-mail where visitor response does not require any feedback from us. In that scenario operator marks message as no reply required. So visitor message get's this status. This happens also if operator just closes a conversation [RESPONSE_NOT_REQUIRED]</li>
        </ul>
    </li>
    <li>(2) - We have sent this message as reply or forward
        <ul>
            <li>Messages operator has sent is set with this status. This status indicates it was an operator send message. [RESPONSE_INTERNAL]</li>
        </ul>
    </li>
    <li>(3) - Responded by e-mail
        <ul>
            <li>As soon operator writes response to customer e-mail customer message changes status to this to indicate it was responded. [RESPONSE_NORMAL]</li>
        </ul>
    </li>
</ul>

This statistic is based on mail messages and their status. Not on the conversation itself. Each conversation consists of more than one mail message. If you want to have statistic based on conversation choose `Mail statistic is based on conversation user`.
<br>
<br>
If you choose to generate mail statistic based on conversation statistic can be inaccurate because in one conversation, multiple operators might have written an e-mail.