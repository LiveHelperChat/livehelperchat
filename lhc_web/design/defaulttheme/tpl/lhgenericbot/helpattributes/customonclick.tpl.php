<?php if ($context == 'customonclick') : ?>
    <p>You can have custom JS execution on click event. In combination with <span class="badge badge-secondary">Hide content on click</span> you can have your own invitation workflow.</p>
    <p>Some examples of JS</p>
    <div class="alert alert-success" role="alert">
        window.parent.document.location = 'https://example.com/go_to_page.html';
    </div>
    <p>Call page function where widget is embedded</p>
    <div class="alert alert-success" role="alert">
        window.parent.parentPageFunction();
    </div>
<?php endif; ?>