(function ($) {
    'use strict';

    // The script can be loaded more than once in the same document (chat tabs), bind the handlers only once
    if (window.lhcSendEmailInitialized) {
        return;
    }
    window.lhcSendEmailInitialized = true;

    var RECIPIENT_CONTAINERS = ['#cc-container', '#bcc-container'];

    function formMessages($form) {
        return {
            mailboxRequired: $form.data('msg-mailbox-required') || '',
            signatureFailed: $form.data('msg-signature-failed') || '',
            signatureError: $form.data('msg-signature-error') || ''
        };
    }

    /**
     * Appends a signature to the TinyMCE editor or to the plain textarea if editor is not ready yet.
     */
    function appendToBody(html) {
        var editor = typeof tinymce !== 'undefined' ? tinymce.get('response-template') : null;

        if (editor) {
            editor.setContent((editor.getContent() || '') + html);
            editor.nodeChanged();
            return;
        }

        var $textarea = $('#response-template');
        $textarea.val(($textarea.val() || '') + html);
    }

    function appendSignature($btn) {
        var $form = $btn.closest('form');
        var messages = formMessages($form);
        var mailboxEmail = $('#new-mailbox-id').val();

        if (!mailboxEmail) {
            alert(messages.mailboxRequired);
            return;
        }

        // Disable button and show loading state
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="material-icons lhc-spin">cached</span>');

        $.ajax({
            url: WWW_DIR_JAVASCRIPT + 'mailconv/getsignature',
            type: 'POST',
            dataType: 'json',
            data: {
                mailbox_email: mailboxEmail,
                csrf_token: confLH.csrf_token
            }
        }).done(function (response) {
            if (response.error === false && response.signature) {
                appendToBody(response.signature);
            } else {
                alert(response.msg || messages.signatureFailed);
            }
        }).fail(function () {
            alert(messages.signatureError);
        }).always(function () {
            $btn.prop('disabled', false).html(originalHtml);
        });
    }

    function buildRecipientRow(field) {
        var $row = $('#recipient-row-prototype').children().first().clone();

        $row.find('input[type="text"]').attr('name', field + '[]').val('');

        return $row;
    }

    /**
     * Cc/Bcc block is visible only while it has at least one recipient row.
     */
    function syncRecipientSection($container) {
        if ($container.length === 0) {
            return;
        }

        $container.closest('.recipient-section').toggleClass('d-none', $container.children('.recipient-row').length === 0);
    }

    function addRecipientRow($container) {
        if ($container.length === 0) {
            return;
        }

        var $row = buildRecipientRow($container.data('field')).appendTo($container);

        syncRecipientSection($container);
        $row.find('input[type="text"]').trigger('focus');

        return $row;
    }

    $(function () {
        $.each(RECIPIENT_CONTAINERS, function (index, selector) {
            syncRecipientSection($(selector));
        });

        $('#append-signature-btn').on('click', function () {
            appendSignature($(this));
        });
    });

    $(document).on('click', '.btn-add-recipient', function (e) {
        e.preventDefault();
        addRecipientRow($($(this).data('target')));
    });

    $(document).on('click', '.btn-remove-recipient', function (e) {
        e.preventDefault();

        var $row = $(this).closest('.recipient-row');
        var $container = $row.parent();

        $row.remove();
        syncRecipientSection($container);
    });

})(jQuery);