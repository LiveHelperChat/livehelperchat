<div class="container-fluid overflow-auto fade-in p-3 pb-4 {dev_type}">
    <div class="p-2" id="start-chat-btn" style="cursor: pointer">
        <div class="shadow rounded bg-white nh-background pb-1">
            <button type="button" id="close-need-help-btn" class="close position-absolute" style="right:34px;top:28px;z-index:2" aria-label="Close">
                <span class="px-1" aria-hidden="true">×</span>
            </button>
            <div id="operator-profile-snippet" class="operator-info p-2 d-flex border-bottom">
                {operator_profile}
            </div>
           <div class="bottom-message px-1 position-relative" id="messages-scroll" style="max-height:91px;font-size:14px">
                {msg_body}
            </div>
        </div>
    </div>
</div>