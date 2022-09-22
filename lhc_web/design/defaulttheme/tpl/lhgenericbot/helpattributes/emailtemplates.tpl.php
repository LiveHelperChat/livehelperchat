<?php if ($context == 'emailtemplates') : ?>
    <p>HTML E-mail templates at the moment are supported only for closed chat notification e-mail.</p>
    <ul>
        <li><code class="fs12">{head_html}{/head_html}</code> If you are sending HTML e-mail defined this variables. We will automatically start sending HTML e-mails instead of plain ones.</li>
        <li><code class="fs12">{footer_html}{/footer_html}</code> Define footer for e-mail HTML templates.</li>
        <li><code class="fs12">{visitor_name_html}<?php echo htmlspecialchars('<span style="background-color:rgb(244,204,204)">{val}</span>');?>{/visitor_name_html}</code> <b>Visitor name HTML template</b></li>
        <li><code class="fs12">{bot_name_html}<?php echo htmlspecialchars('<span style="background-color:rgb(182,215,168)">{val}</span></b>');?>{/bot_name_html}</code> <b>Bot name HTML template</b></li>
        <li><code class="fs12">{bot_attr_html}<?php echo htmlspecialchars('<span style="color:#857979">{val}</span>');?>{/bot_attr_html}</code> <b>Bot sub-action HTML template (operator typing, text area hidden etc.)</b></li>
        <li><code class="fs12">{operator_name_html}<?php echo htmlspecialchars('<span style="background-color:rgb(170,229,255)">{val}</span>');?>{/operator_name_html}</code> <b>Operator name HTML template</b></li>
        <li><code class="fs12">{system_name_html} echo htmlspecialchars('<span style="background-color:rgb(255,242,204)">{val}</span>');?>{/system_name_html}</code> <b>System name HTML template</b></li>
        <li><code class="fs12">{bot_button_html}[<?php echo htmlspecialchars('<span style="color:#0b5394;font-weight:bold">{val}</span>');?>]{/bot_button_html}</code> <b>Bot button HTML template</b></li>
        <li><code class="fs12">{msg_date_html}<?php echo htmlspecialchars('<small>[{val}]</small>');?>{/msg_date_html}</code> <b>Date attribute HTML wrapper</b></li>
    </ul>
    <label>HTML E-Mail template you can use as sample. Copy it and paste as e-mail template.</label>
    <textarea class="fs11 form-control" rows="15"><?php echo htmlspecialchars('{head_html}
<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Chat was closed</title>
    </head>
  <body>
{/head_html}

{visitor_name_html}<span style="background-color:rgb(244,204,204)">{val}</span>{/visitor_name_html}
{bot_name_html}<span style="background-color:rgb(182,215,168)">{val}</span></b>{/bot_name_html}
{bot_attr_html}<span style="color:#857979">{val}</span>{/bot_attr_html}
{operator_name_html}<span style="background-color:rgb(170,229,255)">{val}</span>{/operator_name_html}
{system_name_html}<span style="background-color:rgb(255,242,204)">{val}</span>{/system_name_html}
{bot_button_html}[<span style="color:#0b5394;font-weight:bold">{val}</span>]{/bot_button_html}
{msg_date_html}<small>[{val}]</small>{/msg_date_html}

Hello,

{operator} has closed a chat
Name: {name}
Email: {email}
Phone: {phone}
Department: {department}
Country: {country}
City: {city}
IP: {ip}
Created:	{created}
User left:	{user_left}
Waited:	{waited}
Chat duration:	{chat_duration}



Message:
{message}


Additional data, if any:
{additional_data}

URL of page from which user has send request:
{url_request}

Survey:
{survey}

Sincerely,
Live Support Team


{footer_html}
 </body>
</html>
{/footer_html}')?></textarea>
<?php endif; ?>










