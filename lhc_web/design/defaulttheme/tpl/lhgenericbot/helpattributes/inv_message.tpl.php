Sample how to use uploaded image from Design section
<div class="alert alert-success" role="alert">
    <?php echo nl2br(htmlspecialchars('[html]
<img src="{proactive_img_1}" alt="" />
[/html]')); ?>
</div>

Supported replaceable variables. They are taken from online visitor passed variables.
<ul>
    <li><span class="badge badge-secondary">{nick}</span> - visitor nick if we know it from previous chats</li>
    <li><span class="badge badge-secondary">{lhc.var.&lt;variable key&gt;}</span> - invisible by operator</li>
    <li><span class="badge badge-secondary">{lhc.add.&lt;variable key&gt;}</span> - visible by operator</li>
</ul>
